<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Codeception\Module;

use Codeception\Lib\Interfaces\DependsOnModule;
use Codeception\Module;
use Codeception\Module\Db;
use Codeception\Module\WebDriver;
use Codeception\TestInterface;
use Facebook\WebDriver\Exception\ElementNotVisibleException;
use Facebook\WebDriver\Exception\NoSuchElementException;
use OxidEsales\Facts\Facts;

class Oxideshop extends Module implements DependsOnModule
{
    use CachingTrait;
    use CommandTrait;

    private WebDriver $webDriver;

    private Db $database;

    protected array $config = ['screen_shot_url' => ''];

    public function _depends(): array
    {
        return [
            WebDriver::class => 'Codeception\Module\WebDriver is required',
            Db::class => 'Codeception\Module\Db is required'
        ];
    }

    public function _inject(WebDriver $driver, Db $database)
    {
        $this->webDriver = $driver;
        $this->database = $database;
    }

    public function _before(TestInterface $test)
    {
        Context::resetActiveUser();
        $this->cleanUpCompilationDirectory();
    }

    public function _failed(TestInterface $test, $fail)
    {
        $report = $test->getMetadata()->getReports();
        if (isset($report['png']) && $this->config['screen_shot_url']) {
            $fileName = basename($report['png']);
            $fullUrl = rtrim($this->config['screen_shot_url'],'/') . '/' . $fileName;
            $test->getMetadata()->addReport('png', $fullUrl);
        }
    }

    public function clearShopCache(): void
    {
        $this->webDriver->_restart();
    }

    public function cleanUp(): void
    {
        $this->database->_beforeSuite([]);
    }

    /**
     * Removes \n signs and it leading spaces from string. Keeps only single space in the ends of each row.
     *
     * @param string $line Not formatted string (with spaces and \n signs).
     *
     * @return string Formatted string with single spaces and no \n signs.
     */
    public function clearString(string $line): string
    {
        return trim(preg_replace("/[ \t\r\n]+/", ' ', $line));
    }

    public function waitForAjax(int $timeout = 60): void
    {
        $this->webDriver->waitForJS('y=(window.jQuery|$); return !y || y.active == 0;', $timeout);
        $this->webDriver->wait(1);
    }

    public function waitForPageLoad(int $timeout = 60): void
    {
        $this->waitForDocumentReadyState($timeout);
        $this->waitForAjax($timeout);
    }

    public function waitForDocumentReadyState(int $timeout = 60): void
    {
        $this->webDriver->waitForJs('return document.readyState == "complete"', $timeout);
    }

    /**
     * Check if element exists on currently loaded page
     */
    public function seePageHasElement($element): bool
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
        $this->processCommand((new Facts())->getVendorPath() . '/bin/oe-eshop-db_views_generate', []);
    }
}
