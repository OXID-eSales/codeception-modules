<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Codeception\Module;

#require_once __DIR__.'/../../../../oxid-esales/testing-library/base.php';

use Codeception\Lib\Interfaces\ConflictsWithModule;
use Codeception\Lib\ModuleContainer;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ModuleSetupException;
use OxidEsales\Facts\Facts;
use Symfony\Component\Console\Command\Command;

/**
 * Class Oxideshop
 * @package OxidEsales\Codeception\Module
 */
class OxideshopModules extends \Codeception\Module implements ConflictsWithModule
{
    /** @var string */
    private $consoleRunner;

    public function __construct(
        ModuleContainer $moduleContainer,
        $config = null
    ) {
        $shopRoot = (new Facts())->getCommunityEditionRootPath();
        $this->consoleRunner = "$shopRoot/bin/oe-console";

        parent::__construct($moduleContainer, $config);
    }

    public function _conflicts()
    {
        return 'OxidEsales\Codeception\Module\Oxideshop';
    }

    public function _before($settings = []): void
    {
        $this->activateModules();
    }

    /** @param string $modulePath */
    public function installModule(string $modulePath): void
    {
        exec("$this->consoleRunner oe:module:install $modulePath", $output, $return);
        if ($return !== Command::SUCCESS) {
            throw new \RuntimeException("Module '$modulePath' installation failed: $output");
        }
    }

    /** @param string $moduleId */
    public function uninstallModule(string $moduleId): void
    {
        exec("$this->consoleRunner oe:module:uninstall $moduleId", $output, $return);
        if ($return !== Command::SUCCESS) {
            throw new \RuntimeException("Module '$moduleId' uninstallation failed: $output");
        }
    }

    /** @param string $moduleId */
    public function activateModule(string $moduleId): void
    {
        exec("$this->consoleRunner oe:module:activate $moduleId");
    }

    /** @param string $moduleId */
    public function deactivateModule(string $moduleId): void
    {
        exec("$this->consoleRunner oe:module:deactivate $moduleId");
    }

    /**
     * Activates modules
     */
    private function activateModules(): void
    {
        // TODO: Create new mechanism for loading modules

    }
}
