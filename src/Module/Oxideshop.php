<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Codeception\Module;

#require_once __DIR__ . '/../../../../../tests/bootstrap.php';

use Codeception\Exception\ElementNotFound;
use Codeception\Lib\Interfaces\DependsOnModule;
use Codeception\Module;
use Codeception\Module\Db;
use Codeception\Module\WebDriver;
use Facebook\WebDriver\Exception\ElementNotVisibleException;
use Facebook\WebDriver\Exception\NoSuchElementException;
use OxidEsales\Facts\Facts;
use Webmozart\PathUtil\Path;

class Oxideshop extends Module implements DependsOnModule
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
     * @var array
     */
    protected $config = ['screen_shot_url' => ''];

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
     * Reset context
     */
    public function _before(\Codeception\TestInterface $test)
    {
        \OxidEsales\Codeception\Module\Context::resetActiveUser();
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
     * @param \Codeception\TestInterface $test
     * @param $fail
     */
    public function _failed(\Codeception\TestInterface $test, $fail)
    {
        $report = $test->getMetadata()->getReports();
        if (isset($report['png']) && $this->config['screen_shot_url']) {
            $fileName = basename($report['png']);
            $fullUrl = rtrim($this->config['screen_shot_url'],'/') . '/' . $fileName;
            $test->getMetadata()->addReport('png', $fullUrl);
        }
    }

    /**
     * Check if element exists on currently loaded page
     */
    public function seePageHasElement($element)
    {
        return count($this->getModule('WebDriver')->_findElements($element)) > 0;
    }

    /**
     * Clicks on first visible element
     * @param string $locator
     * @throws ElementNotVisibleException
     * @throws NoSuchElementException
     */
    public function seeAndClick(string $locator): void
    {
        $elements = $this->webDriver->_findElements($locator);
        if (!$elements) {
            throw new NoSuchElementException($locator);
        }
        foreach ($elements as $el) {
            if ($el->isDisplayed()) {
                $el->click();
                return;
            }
        }
        throw new ElementNotVisibleException($locator);
    }

    public function regenerateDatabaseViews(): void
    {
        $vendorPath = (new Facts())->getVendorPath();
        exec($vendorPath. '/bin/oe-eshop-db_views_regenerate');
    }
}
