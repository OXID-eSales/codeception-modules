<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Codeception\Module;

use Codeception\Lib\Interfaces\DependsOnModule;
use Codeception\Module;
use Codeception\Module\WebDriver;

class OxideshopAdmin extends Module implements DependsOnModule
{
    private const FRAME_LIST = 'list';
    private const FRAME_NAVIGATION = 'navigation';
    private const FRAME_BASE = 'basefrm';
    private const FRAME_EDIT = 'edit';
    private const FRAME_HEADER = 'header';
    private const FRAME_ADMINNAV = 'adminnav';
    private const FRAME_DYNEXPORT_DO = 'dynexport_do';
    private const FRAME_DYNEXPORT_MAIN = 'dynexport_main';

    private array $frameParents = [
        self::FRAME_ADMINNAV => self::FRAME_NAVIGATION,
        self::FRAME_LIST => self::FRAME_BASE,
        self::FRAME_EDIT => self::FRAME_BASE,
        self::FRAME_DYNEXPORT_DO => self::FRAME_BASE,
        self::FRAME_DYNEXPORT_MAIN => self::FRAME_BASE,
    ];
    private WebDriver $webdriver;

    public function _depends(): array
    {
        return [
            WebDriver::class => WebDriver::class . ' is required',
        ];
    }

    public function _inject(WebDriver $webDriver): void
    {
        $this->webdriver = $webDriver;
    }

    public function selectHeaderFrame(): void
    {
        $this->selectFrame(self::FRAME_HEADER);
    }

    public function selectBaseFrame(): void
    {
        $this->selectFrame(self::FRAME_BASE);
    }

    public function selectEditFrame(): void
    {
        $this->selectFrame(self::FRAME_EDIT);
    }

    public function selectNavigationFrame(): void
    {
        $this->selectFrame(self::FRAME_ADMINNAV);
    }

    public function selectListFrame(): void
    {
        $this->selectFrame(self::FRAME_LIST);
    }

    public function selectGenericExportStatusFrame(): void
    {
        $this->selectFrame(self::FRAME_DYNEXPORT_DO);
    }

    public function selectGenericExportMainFrame(): void
    {
        $this->selectFrame(self::FRAME_DYNEXPORT_MAIN);
    }

    private function selectFrame(string $frame): void
    {
        $this->webdriver->switchToFrame();
        if (isset($this->frameParents[$frame])) {
            $this->webdriver->switchToFrame($this->frameParents[$frame]);
        }
        $this->webdriver->switchToFrame($frame);
        $this->webdriver->waitForJS('return window.document.readyState === "complete";');
    }
}
