<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Codeception\Module;

use Codeception\Lib\ModuleContainer;
use Codeception\Module;
use Codeception\TestInterface;

/**
 * Class Oxideshop
 * @package OxidEsales\Codeception\Module
 */
class OxideshopModules extends Module
{
    use CommandTrait;

    public function __construct(
        ModuleContainer $moduleContainer,
        $config = null
    )
    {
        parent::__construct($moduleContainer, $config);
    }

    public function _before(TestInterface $test): void
    {
        $activateModules = $this->_getConfig('activate_before_test');
        if (in_array($activateModules, [true, null])) {
            $this->activateModules();
        }
    }

    public function _after(TestInterface $test): void
    {
        if ($this->_getConfig('deactivate_after_test') == true) {
            $this->deactivateModules();
        }
    }

    public function _beforeSuite(array $settings = [])
    {
        if ($this->_getConfig('activate_before_suite') == true) {
            $this->activateModules();
        }
        parent::_beforeSuite($settings);
    }

    public function _afterSuite()
    {
        if ($this->_getConfig('deactivate_after_suite') == true) {
            $this->deactivateModules();
        }
    }

    public function installModule(string $modulePath): void
    {
        $this->processConsoleCommand('oe:module:install ' . $modulePath);
    }

    public function uninstallModule(string $moduleId): void
    {
        $this->processConsoleCommand('oe:module:uninstall ' . $moduleId);
    }

    public function activateModule(string $moduleId): void
    {
        $this->processConsoleCommand('oe:module:activate ' . $moduleId);
    }

    public function deactivateModule(string $moduleId): void
    {
        $this->processConsoleCommand('oe:module:deactivate ' . $moduleId);
    }

    public function activateModules(): void
    {
        $this->debug('Activate Modules');
        $modulesToActivate = $this->getModuleIds();

        foreach ($modulesToActivate as $moduleId) {
            $this->activateModule($moduleId);
        }
    }

    public function deactivateModules(): void
    {
        $this->debug('Deactivate Modules');
        $modulesToDeactivate = $this->getModuleIds();

        foreach ($modulesToDeactivate as $moduleId) {
            $this->deactivateModule($moduleId);
        }
    }

    private function getModuleIds(): array
    {
        if (getenv('MODULE_IDS')) {
            $ids = array_map('trim', explode(',', getenv('MODULE_IDS')));
        }
        return $ids ?? [];
    }
}
