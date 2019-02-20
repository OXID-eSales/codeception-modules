<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Codeception\Module;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\Lib\Interfaces\DependsOnModule;
use Codeception\Module\WebDriver;
use Codeception\Module\Db;

class Oxideshop extends \Codeception\Module implements DependsOnModule
{
    /**
     * @var WebDriver
     */
    private $webDriver;

    /**
     * @var Db
     */
    private $db;

    /**
     * @return array
     */
    public function _depends()
    {
        return [
            WebDriver::class => 'Codeception\Module\WebDriver is required',
            Db::class => 'Codeception\Module\Db is required'
        ];
    }

    /**
     * @param WebDriver $driver
     */
    public function _inject(WebDriver $driver, Db $db)
    {
        $this->webDriver = $driver;
        $this->db = $db;
    }

    /**
     * Reset context before test
     */
    public function _before(\Codeception\TestInterface $test)
    {
        \OxidEsales\Codeception\Module\Context::setActiveUser(null);
    }

    /**
     * Clear browser cache
     */
    public function clearShopCache()
    {
        $this->webDriver->_restart();
    }

    /**
     * Clean up database
     */
    public function cleanUp()
    {
        $this->db->_beforeSuite([]);
    }

    /**
     * Removes \n signs and it leading spaces from string. Keeps only single space in the ends of each row.
     *
     * @param string $line Not formatted string (with spaces and \n signs).
     *
     * @return string Formatted string with single spaces and no \n signs.
     */
    public function clearString($line)
    {
        return trim(preg_replace("/[ \t\r\n]+/", ' ', $line));
    }

    /**
     * @param int $timeout
     */
    public function waitForAjax($timeout = 60)
    {
        $this->webDriver->waitForJS('return !!window.jQuery && window.jQuery.active == 0;', $timeout);
        $this->webDriver->wait(1);
    }


    /**
     * @param int $timeout
     */
    public function waitForPageLoad($timeout = 60)
    {
        $this->webDriver->waitForJs('return document.readyState == "complete"', $timeout);
        $this->waitForAjax($timeout);
    }
}
