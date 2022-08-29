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
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use Psr\Container\ContainerInterface;

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
        $this->debug('Activate Modules');
        $this->activateModules();
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
        $modulesToActivate = $this->getModuleIds();

        foreach ($modulesToActivate as $moduleId) {
            $this->activateModule($moduleId);
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