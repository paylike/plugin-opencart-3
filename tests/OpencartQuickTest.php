<?php

namespace Opencart;

use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Lmc\Steward\Test\AbstractTestCase;

/**
 * @group opencart_quick_test
 */
class OpencartQuickTest extends AbstractTestCase
{

    public $runner;

    /**
     * @throws NoSuchElementException
     * @throws \Facebook\WebDriver\Exception\TimeOutException
     * @throws \Facebook\WebDriver\Exception\UnexpectedTagNameException
     */
    public function testUsdPaymentBeforeOrderInstant()
    {
        $this->runner = new OpencartRunner($this);
        $this->runner->ready(array(
            'capture_mode' => 'instant',
            'currency'     => 'USD',
        ));
    }
}
