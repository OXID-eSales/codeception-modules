<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Codeception\Module;

use Codeception\Exception\ElementNotFound;
use Codeception\Lib\Interfaces\DependsOnModule;
use Codeception\Module;
use Codeception\Module\WebDriver;

class OxideshopAdmin extends Module implements DependsOnModule
{
    /**
     * Admin interface frame IDs
     */
    private const FRAME_LIST = 'list';
    private const FRAME_NAVIGATION = 'navigation';
    private const FRAME_BASE = 'basefrm';
    private const FRAME_EDIT = 'edit';
    private const FRAME_HEADER = 'header';
    private const FRAME_ADMINNAV = 'adminnav';

    /**
     * Admin interface frame dependency structure. One level supported.
     *
     * Frame -> Parent frame
     */
    private array $frameParents = [
        self::FRAME_ADMINNAV => self::FRAME_NAVIGATION,
        self::FRAME_LIST => self::FRAME_BASE,
        self::FRAME_EDIT => self::FRAME_BASE
    ];

    private Oxideshop $oxideshop;

    private WebDriver $webdriver;

    public function _depends(): array
    {
        return [
            WebDriver::class => 'Codeception\Module\WebDriver is required',
            Oxideshop::class => 'Codeception\Module\Oxideshop is required'
        ];
    }

    public function _inject(WebDriver $webDriver, Oxideshop $oxideshop)
    {
        $this->webdriver = $webDriver;
        $this->oxideshop = $oxideshop;
    }

    /**
     * Select Header frame in Admin panel to be active now
     */
    public function selectHeaderFrame(): void
    {
        $this->selectFrameInAdmin(self::FRAME_HEADER);
    }


    /**
     * Select Base frame in Admin panel to be active now
     */
    public function selectBaseFrame(): void
    {
        $this->selectFrameInAdmin(self::FRAME_BASE);
    }

    /**
     * Select Edit frame in Admin panel to be active now
     */
    public function selectEditFrame(): void
    {
        $this->selectFrameInAdmin(self::FRAME_EDIT);
    }

    /**
     * Select Navigation frame in Admin panel to be active now
     */
    public function selectNavigationFrame(): void
    {
        $this->selectFrameInAdmin(self::FRAME_ADMINNAV);
    }

    /**
     * Select List frame in Admin panel to be active now
     */
    public function selectListFrame(): void
    {
        $this->selectFrameInAdmin(self::FRAME_LIST);
    }

    /**
     * Selects the frame by current OXID eShop admin frame dependency structure
     *
     * @param string $desiredFrame
     */
    private function selectFrameInAdmin(string $desiredFrame): void
    {
        $desiredParent = $this->frameParents[$desiredFrame] ?? '';

        $this->switchToFrame();

        if ($desiredParent) {
            $this->webdriver->waitForElement("#{$desiredParent}");
            $this->switchToFrame($desiredParent);
            $this->oxideshop->waitForDocumentReadyState();
        }

        $this->webdriver->waitForElement("#{$desiredFrame}");
        $this->switchToFrame($desiredFrame);
        $this->oxideshop->waitForDocumentReadyState();
    }

    /**
     * Switch to frame
     *
     * Method is temporary until webdriver will provide working solution for switching the frames
     *
     * @param $elementId
     */
    private function switchToFrame($elementId = null): void
    {
        if (is_null($elementId)) {
            $this->webdriver->webDriver->switchTo()->defaultContent();
            return;
        }

        $els = $this->webdriver->_findElements("frame[id='{$elementId}']");
        if (!count($els)) {
            throw new ElementNotFound($elementId, "Frame was not found by CSS or XPath");
        }

        $this->webdriver->webDriver->switchTo()->frame($els[0]);
    }
}
