<?php


namespace Opencart;

use Facebook\WebDriver\Exception\NoAlertOpenException;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\StaleElementReferenceException;
use Facebook\WebDriver\Exception\TimeOutException;
use Facebook\WebDriver\Exception\UnexpectedTagNameException;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\WebDriverExpectedCondition;

class OpencartRunner extends OpencartTestHelper {

	/**
	 * @param $args
	 *
	 * @throws NoSuchElementException
	 * @throws TimeOutExceptionOpencart
	 * @throws UnexpectedTagNameException
	 */
	public function ready( $args ) {
		$this->set( $args );
		$this->go();
	}

	/**
	 * @throws NoSuchElementException
	 * @throws TimeOutException
	 */
	public function loginAdmin() {
		$this->goToPage( 'admin/index.php?route=common/login', '#input-username' );
		while ( ! $this->hasValue( '#input-username', $this->user ) ) {
			$this->typeLogin();
		}
		$this->click( '.btn-primary' );
		$this->waitForElement( '#menu' );

	}

	/**
	 *  Insert user and password on the login screen
	 */
	private function typeLogin() {
		$this->type( '#input-username', $this->user );
		$this->type( '#input-password', $this->pass );
	}

	/**
	 * @param $args
	 */
	private function set( $args ) {
		foreach ( $args as $key => $val ) {
			$name = $key;
			if ( isset( $this->{$name} ) ) {
				$this->{$name} = $val;
			}
		}
	}

	/**
	 * @throws NoSuchElementException
	 * @throws TimeOutException
	 */
	public function changeCurrency() {
		$this->click( '#form-currency' );
		$this->click( "//*[contains(@name, '" . $this->currency . "')]" );

	}


	/**
	 * @throws NoSuchElementException
	 * @throws TimeOutException
	 */
	public function disableEmail() {
		if ( $this->stop_email === true ) {
			$this->click( "#menu-system" );
			$this->waitForElement( ".collapse.in li" );
			$this->click( ".collapse.in li" );
			$this->click( "//*[contains(@data-original-title, 'Edit')]" );
			$this->click( "//*[contains(@href, '#tab-mail')]" );
			$this->click( "//*[contains(@value, 'order')]" );
			$this->click( '#button-save' );
		}
	}

	/**
	 * @throws NoSuchElementException
	 * @throws TimeOutException
	 */

	public function changeMode() {
		$this->goToPage( 'admin/index.php?route=extension/payment/paylike' );
		while ( ! $this->hasValue( '#input-username', $this->user ) ) {
			$this->typeLogin();
		}
		$this->click( '.btn-primary' );
		$this->captureMode();
	}

	/**
	 * @throws NoSuchElementException
	 * @throws TimeOutException
	 */
	private function settingsCheck() {
		$this->outputVersions();
	}

	/**
	 * @throws NoSuchElementException
	 * @throws TimeOutException
	 */

	private function logVersionsRemotly() {
		$versions = $this->getVersions();
		$this->wd->get( getenv( 'REMOTE_LOG_URL' ) . '&key=' . $this->get_slug( $versions['ecommerce'] ) . '&tag=opencart&view=html&' . http_build_query( $versions ) );
		$this->waitForElement( '#message' );
		$message = $this->getText( '#message' );
		$this->main_test->assertEquals( 'Success!', $message, "Remote log failed" );
	}

	/**
	 * @throws NoSuchElementException
	 * @throws TimeOutException
	 */
	private function getVersions() {
		$opencart = $this->getText( '#footer' );
		$this->goToPage( "admin/index.php?route=extension/payment/paylike", null );
		$this->waitForElement( ".module-item-list" );
		$paylike = "";
		if ( $this->hasValue( ".module-item", "data-name" ) ) {
			$paylike = $this->getText( ".col-md-2 .small-text" );
		}


		return [ 'ecommerce' => $opencart, 'plugin' => $paylike ];
	}

	/**
	 * @throws NoSuchElementException
	 * @throws TimeOutException
	 */
	private function outputVersions() {
		$this->waitForElement( "#shop_version" );
		$this->main_test->log( "Opencart Version: ", $this->getText( '#shop_version' ) );
		$this->click( "#subtab-AdminParentModulesSf" );
		$this->click( "#subtab-AdminModulesSf" );
		$this->waitForElement( ".module-item" );
		$this->main_test->log( "Paylike Version:", $this->getElementData( "//*[contains(@data-name, 'Paylike')]", "version" ) );

	}


	/**
	 * @throws NoSuchElementException
	 * @throws TimeOutException
	 */
	public function changeDecimal() {
		$this->goToPage( 'wp-admin/admin.php?page=wc-settings', '#select2-opencart_currency-container' );
		$this->type( '#opencart_price_decimal_sep', '.' );
	}

	/**
	 *
	 */
	public function submitAdmin() {
		$this->click( '#module_form_submit_btn' );
	}

	/**
	 * @throws NoSuchElementException
	 * @throws TimeOutException
	 * @throws UnexpectedTagNameException
	 */
	private function directPayment() {
		$this->goToPage( 'index.php?route=account/login', '.col-sm-6 #input-email' );
		$this->loginShop();
		$this->goToPage( "upload", "#form-currency" );
		$this->clearCartItem();
		$this->changeCurrency();
		$this->addToCart();
		$this->proceedToCheckout();
		// $this->choosePaylike();
		$this->finalPaylike();
		$this->waitElementDisappear( ".paylike.pending" );
		$this->waitElementDisappear( ".paylike.done" );
		$this->waitElementDisappear( ".btn-primary.disabled" );
		$this->waitForElement( "//*[contains(text(), 'Confirm Order')]" );
		$this->click( "#button-confirm" );
		$this->finalPaylike();
		$this->selectOrder();
		if ( $this->capture_mode == 'delayed' ) {
			$this->checkNoCaptureWarning();
			$this->capture();
		} else {
			$this->refund();
		}

	}

	private function loginShop() {
		$this->waitForElement( ".col-sm-6 #input-email" );
		$this->click( ".col-sm-6 #input-email" );
		$this->type( ".col-sm-6 #input-email", "stefan.calara@gmail.com" );
		$this->click( ".col-sm-6 #input-password" );
		$this->type( ".col-sm-6 #input-password", "admin#522" );
		$this->click( "//*[contains(@value, 'Login')]" );

	}

	/**
	 * @throws NoSuchElementException
	 * @throws TimeOutException
	 * @throws UnexpectedTagNameException
	 */
	public function checkNoCaptureWarning() {
		$this->selectValue( "#input-order-status", "5" );
		$this->click( '#button-history' );
		$this->waitForElement( ".alert-success " );
		$text = $this->pluckElement( '.tab-content #history tbody tr td', 2 )->getText();
		if ( $text == 'Complete' || $text == 'Complete' ) {
			$text = $this->pluckElement( '.tab-content #history tbody tr td', 2 )->getText();
		}
		$messages = explode( "\n", $text );
		$this->main_test->assertEquals( 'Complete', $messages[0], "Complete" );
	}

	/**
	 * @throws NoSuchElementException
	 * @throws TimeOutException
	 * @throws UnexpectedTagNameException
	 */
	public function capture() {
		$this->selectValue( "#input-order-status", "5" );
		$this->click( '#button-history' );
		$this->waitElementDisappear( ".alert-success " );
		$text = $this->pluckElement( '.tab-content #history tbody tr td', 2 )->getText();
		if ( $text == 'Shipped' || $text == 'Shipped' ) {
			$text = $this->pluckElement( '.tab-content #history tbody tr td', 2 )->getText();
		}
		$messages = explode( "\n", $text );
		$this->main_test->assertEquals( 'Shipped', $messages[0], "Shipped" );
	}

	/**
	 *
	 */
	public function captureMode() {
		$this->click( '#input_capture_mode' );
		$this->click( "//*[contains(@value, '" . $this->capture_mode . "')]" );
		$this->click( '.fa-save' );;
	}

	/**
	 * @throws NoSuchElementException
	 * @throws TimeOutException
	 */
	public function clearCartItem() {

		$this->click( "#cart-total" );
		if ( $this->getText( ".container .row .col-sm-3 .dropdown-menu .text-center" ) != "Your shopping cart is empty!" ) {
			$this->waitForElement( "//*[contains(@title, 'Remove')]" );
			$this->click( "//*[contains(@title, 'Remove')]" );
		}

	}

	/**
	 *
	 */
	public function addToCart() {
		$this->waitForElement( '.product-layout' );
		$this->click( '.product-layout .fa-shopping-cart' );
	}

	/**
	 *
	 */
	public function proceedToCheckout() {
		$this->click( "//*[contains(@title, 'Checkout')]" );

		$this->waitForElement( "#button-payment-address" );
		$this->click( "#button-payment-address" );
		$this->waitForElement( "#button-payment-method" );
		$this->click( "//*[contains(@name, 'agree')]" );
		$this->click( "#button-payment-method" );
		$this->waitForElement( "#button-confirm" );
		$this->click( "#button-confirm" );


	}

	/**
	 * @throws NoSuchElementException
	 * @throws TimeOutException
	 */
	public function choosePaylike() {
		$this->click( '#payment-option-3' );
		$this->click( "conditions_to_approve[terms-and-conditions]" );
		$this->click( "#pay-by-paylike" );
	}

	/**
	 * @throws NoSuchElementException
	 * @throws TimeOutException
	 */
	public function finalPaylike() {
		$this->popupPaylike();
	}

	/**
	 * @throws NoSuchElementException
	 * @throws TimeOutException
	 */
	public function popupPaylike() {
		try {
			$this->waitForElement( '.paylike.overlay .payment form #card-number' );
			$this->type( '.paylike.overlay .payment form #card-number', 41000000000000 );
			$this->type( '.paylike.overlay .payment form #card-expiry', '11/22' );
			$this->type( '.paylike.overlay .payment form #card-code', '122' );
			$this->click( '.paylike.overlay .payment form button' );
		} catch ( NoSuchElementException $exception ) {
			$this->confirmOrder();
			$this->popupPaylike();
		}

	}

	/**
	 * @throws NoSuchElementException
	 * @throws TimeOutException
	 */
	public function selectOrder() {
		$this->goToPage( "admin/index.php?route=sale/order" );
		while ( ! $this->hasValue( '#input-username', $this->user ) ) {
			$this->typeLogin();
		}
		$this->click( '.btn-primary' );
		$this->waitForElement( '#form-order' );
		$this->click( "//*[contains(@data-original-title, 'View')]" );
		$this->waitForElement( "#input-order-status" );
	}

	/**
	 * @throws NoSuchElementException
	 * @throws TimeOutException
	 * @throws UnexpectedTagNameException
	 */
	public function refund() {
		$this->selectValue( "#input-order-status", "11" );
		$this->waitForElement( "#history" );
		$this->click( "#button-history" );
		$this->waitForElement( ".alert-success " );
		$text = $this->pluckElement( '.tab-content #history tbody tr td', 2 )->getText();
		if ( $text == 'Refunded' || $text == 'Refunded' ) {
			$text = $this->pluckElement( '.tab-content #history tbody tr td', 2 )->getText();
		}
		$messages = explode( "\n", $text );
		$this->main_test->assertEquals( 'Refunded', $messages[0], "Refunded" );
	}

	/**
	 * @throws NoSuchElementException
	 * @throws TimeOutException
	 */
	public function confirmOrder() {
		$this->waitForElement( '#paylike-payment-button' );
		$this->click( '#paylike-payment-button' );
	}

	/**
	 * @throws NoSuchElementException
	 * @throws TimeOutException
	 */
	private function settings() {

		$this->disableEmail();

		$this->changeMode();

	}

	/**
	 * @throws NoSuchElementException
	 * @throws TimeOutException
	 * @throws UnexpectedTagNameException
	 */
	private function go() {
		$this->changeWindow();
		$this->loginAdmin();

		if ( $this->log_version ) {
			$this->logVersionsRemotly();

			return $this;
		}


		if ( $this->settings_check ) {
			$this->settingsCheck();

			return $this;
		}


		$this->settings();


		$this->directPayment();

	}

	/**
	 *
	 */
	private function changeWindow() {
		$this->wd->manage()->window()->setSize( new WebDriverDimension( 1600, 996 ) );
	}


}

