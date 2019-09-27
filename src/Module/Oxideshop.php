<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Codeception\Module;

require_once __DIR__.'/../../../../oxid-esales/testing-library/base.php';

use Codeception\Lib\Interfaces\DependsOnModule;
use Codeception\Module\WebDriver;
use Codeception\Module\Db;

/**
 * Class Oxideshop
 * @package OxidEsales\Codeception\Module
 */
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
     * Reset context and activate modules before test
     */
    public function _before(\Codeception\TestInterface $test)
    {
        \OxidEsales\Codeception\Module\Context::resetActiveUser();
        // Activate modules
        $this->activateModules();
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
    public function clearString(string $line)
    {
        return trim(preg_replace("/[ \t\r\n]+/", ' ', $line));
    }

    /**
     * @param int $timeout
     */
    public function waitForAjax(int $timeout = 60)
    {
        $this->webDriver->waitForJS('y=(window.jQuery|$); return !y || y.active == 0;', $timeout);
        $this->webDriver->wait(1);
    }

    /**
     * @param int $timeout
     */
    public function waitForPageLoad(int $timeout = 60)
    {
        $this->waitForDocumentReadyState($timeout);
        $this->waitForAjax($timeout);
    }

    /**
     * @param int $timeout
     */
    public function waitForDocumentReadyState(int $timeout = 60)
    {
        $this->webDriver->waitForJs('return document.readyState == "complete"', $timeout);
    }

    /**
     * Activates modules
     */
    private function activateModules()
    {
        $testConfig = new \OxidEsales\TestingLibrary\TestConfig();
        $modulesToActivate = $testConfig->getModulesToActivate();

        if ($modulesToActivate) {
            $serviceCaller = new \OxidEsales\TestingLibrary\ServiceCaller();
            $serviceCaller->setParameter('modulestoactivate', $modulesToActivate);
            $serviceCaller->callService('ModuleInstaller', 1);
        }
    }
    
    /**
     * Check if element exists on currently loaded page
     */
    public function seePageHasElement($element)
    {
        return count($this->getModule('WebDriver')->_findElements($element)) > 0;
    }
}
