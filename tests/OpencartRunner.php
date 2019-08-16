<?php

namespace Opencart;

use Facebook\WebDriver\Exception\ElementNotVisibleException;
use Facebook\WebDriver\Exception\NoAlertOpenException;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\StaleElementReferenceException;
use Facebook\WebDriver\Exception\TimeOutException;
use Facebook\WebDriver\Exception\UnexpectedTagNameException;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\WebDriverExpectedCondition;

class OpencartRunner extends OpencartTestHelper
{

    /**
     * @param $args
     *
     * @throws NoSuchElementException
     * @throws TimeOutException
     * @throws UnexpectedTagNameException
     */
    public function ready($args)
    {
        $this->set($args);
        $this->go();
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    public function loginAdmin()
    {
        $this->goToPage('index.php?route=common/login', '#input-username', true);
        while (! $this->hasValue('#input-username', $this->user)) {
            $this->typeLogin();
        }
        $this->click('.btn-primary');
        $this->waitForElement('#menu');
    }

    /**
     *  Insert user and password on the login screen
     */
    private function typeLogin()
    {
        $this->type('#input-username', $this->user);
        $this->type('#input-password', $this->pass);
    }

    /**
     * @param $args
     */
    private function set($args)
    {
        foreach ($args as $key => $val) {
            $name = $key;
            if (isset($this->{$name})) {
                $this->{$name} = $val;
            }
        }
    }

    /**
     */
    public function changeCurrency()
    {
        $this->click('#form-currency');
        $this->click("//*[contains(@name, '" . $this->currency . "')]");
    }


    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    public function disableEmail()
    {
        if ($this->stop_email === true) {
            $this->click("#menu-system");
            $this->waitForElement(".collapse.in li");
            $this->click(".collapse.in li");
            $this->click("//*[contains(@data-original-title, 'Edit')]");
            $this->click("//*[contains(@href, '#tab-mail')]");
            $this->uncheck("//*[contains(@value, 'order')]");
            $this->click('#button-save');
        }
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    public function changeMode()
    {
        $this->goToPage('index.php?route=extension/payment/paylike', null, true);
        $this->captureMode();
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    private function settingsCheck()
    {
        $this->outputVersions();
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    private function logVersionsRemotly()
    {
        $versions = $this->getVersions();
        $this->wd->get(getenv('REMOTE_LOG_URL') . '&key=' . $this->get_slug($versions['ecommerce']) .
            '&tag=opencart3&view=html&' . http_build_query($versions));
        $this->waitForElement('#message');
        $message = $this->getText('#message');
        $this->main_test->assertEquals('Success!', $message, "Remote log failed");
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    private function getVersions()
    {
        $opencart = $this->getText('#footer');
        $opencart_array = explode("Version ", $opencart);
        $opencart_version = $opencart_array[1];
        $this->goToPage("index.php?route=extension/payment/paylike", null, true);
        $this->waitForElement(".panel-title");
        $paylike = $this->getElementData(".panel-title", 'paylike-version');

        return [ 'ecommerce' => $opencart_version, 'plugin' => $paylike ];
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    private function outputVersions()
    {
        $versions = $this->getVersions();
        $this->main_test->log("Opencart Version %s", $versions['ecommerce']);
        $this->main_test->log("Paylike Version %s", $versions['plugin']);
    }

    /**
     *
     */
    public function submitAdmin()
    {
        $this->click('#module_form_submit_btn');
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     * @throws UnexpectedTagNameException
     */
    private function directPayment()
    {
        $this->loginShop();
        $this->goToPage("/", "#form-currency");
        $this->clearCartItem();
        $this->changeCurrency();
        $this->addToCart();
        $this->proceedToCheckout();
        $this->choosePaylike();
        $this->finalPaylike();
        //wait for payment redirect
        $this->wd->wait(20, 500)->until(
            WebDriverExpectedCondition::titleIs('Your order has been placed!')
        );
        $this->main_test->assertEquals(
            'Your order has been placed!',
            $this->getText('.col-sm-12 h1'),
            "Checking success message for purchase"
        );
        $this->selectOrder();
        if ($this->capture_mode == 'delayed') {
            $this->capture();
        } else {
            $this->refund();
        }
    }

    private function loginShop()
    {
        $this->goToPage('index.php?route=account/login', '.col-sm-6 #input-email');
        $this->click(".col-sm-6 #input-email");
        $this->type(".col-sm-6 #input-email", $this->client_user);
        $this->click(".col-sm-6 #input-password");
        $this->type(".col-sm-6 #input-password", $this->client_pass);
        $this->click("//*[contains(@value, 'Login')]");
    }



    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     * @throws UnexpectedTagNameException
     */
    public function capture()
    {
        $this->click('.fa-pencil');
        $this->waitForElement(".runtransaction");
        $this->click(".runtransaction");
        $this->waitForElement("#plt-result");
        $text     = $this->getText('#plt-result');
        $messages = explode(".", $text);
        $this->main_test->assertEquals(
            'Successful Capture transaction',
            $messages[0],
            "Successful Capture transaction"
        );
    }

    /**
     *
     */
    public function captureMode()
    {
        $this->click('#input_capture_mode');
        $this->click("//*[contains(@value, '" . $this->capture_mode . "')]");
        $this->click('.fa-save');
        ;
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    public function clearCartItem()
    {

        $this->click("#cart-total");
        if ($this->getText(".container .row .col-sm-3 .dropdown-menu .text-center") != "Your shopping cart is empty!") {
            $this->waitForElement("//*[contains(@title, 'Remove')]");
            $this->click("//*[contains(@title, 'Remove')]");
        }
    }

    /**
     *
     */
    public function addToCart()
    {
        $this->waitForElement('.product-layout');
        $this->click('.product-layout .fa-shopping-cart');
    }

    /**
     *
     */
    public function proceedToCheckout()
    {
        $this->click("//*[contains(@title, 'Checkout')]");
        try {
            $this->waitForElement("#button-payment-address");
        } catch (NoSuchElementException $exception) {
            $this->loginShop();
            $this->click("//*[contains(@title, 'Checkout')]");
            $this->waitForElement("#button-payment-address");
        }
        $this->click("#button-payment-address");
        $this->waitForElement("#button-payment-method");
        $this->click("//*[contains(@name, 'agree')]");
        $this->click("#button-payment-method");
        $this->waitForElement("#button-confirm");
        $this->click("#button-confirm");
    }

    /**
     */
    public function choosePaylike()
    {
        try {
            $this->click('.panel input[value="paylike"]');
        } catch (ElementNotVisibleException $exception) {
            // only element visible, so its fine
        }
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    public function finalPaylike()
    {
        $this->popupPaylike();
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    public function popupPaylike()
    {
        try {
            $this->waitForElement('.paylike.overlay .payment form #card-number');
            $this->type('.paylike.overlay .payment form #card-number', 41000000000000);
            $this->type('.paylike.overlay .payment form #card-expiry', '11/22');
            $this->type('.paylike.overlay .payment form #card-code', '122');
            $this->click('.paylike.overlay .payment form button');
        } catch (NoSuchElementException $exception) {
            $this->confirmOrder();
            $this->popupPaylike();
        }
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    public function selectOrder()
    {
        $this->loginAdmin();
        $this->goToPage("index.php?route=extension/payment/paylike/payments", null, true);
        $this->click(".caret");
        $this->waitForElement(".fa-trash-o");
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     * @throws UnexpectedTagNameException
     */
    public function refund()
    {

        $this->click(".fa-trash-o");
        $this->waitForElement(".runtransaction");
        $this->click(".runtransaction");
        $this->waitForElement("#plt-result");
        $text     = $this->getText('#plt-result');
        $messages = explode(".", $text);
        $this->main_test->assertEquals('Successful Refund transaction', $messages[0], "Successful Refund transaction.");
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    public function confirmOrder()
    {
        $this->waitForElement('#paylike-payment-button');
        $this->click('#paylike-payment-button');
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     */
    private function settings()
    {
        $this->disableEmail();
        $this->changeMode();
    }

    /**
     * @throws NoSuchElementException
     * @throws TimeOutException
     * @throws UnexpectedTagNameException
     */
    private function go()
    {
        $this->changeWindow();
        $this->loginAdmin();
        if ($this->log_version) {
            $this->logVersionsRemotly();

            return $this;
        }
        if ($this->settings_check) {
            $this->settingsCheck();

            return $this;
        }
        $this->settings();
        $this->directPayment();
    }

    /**
     *
     */
    private function changeWindow()
    {
        $this->wd->manage()->window()->setSize(new WebDriverDimension(1600, 996));
    }
}
