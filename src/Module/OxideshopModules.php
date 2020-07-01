<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Codeception\Module;

require_once __DIR__.'/../../../../oxid-esales/testing-library/base.php';

use Codeception\Lib\Interfaces\ConflictsWithModule;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ModuleSetupException;
use OxidEsales\Facts\Facts;

/**
 * Class Oxideshop
 * @package OxidEsales\Codeception\Module
 */
class OxideshopModules extends \Codeception\Module implements ConflictsWithModule
{

    public function _conflicts()
    {
        return 'OxidEsales\Codeception\Module\Oxideshop';
    }

    /**
     * Reset context and activate modules before test
     */
    public function _beforeSuite($settings = [])
    {
        $this->activateModules();
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
            try {
                $serviceCaller->callService('ModuleInstaller', 1);
            } catch (ModuleSetupException $e) {
                // this may happen if the module is already active,
                // we can ignore this
            }
        }
    }

    private $shopRootPath = null;
    
    private function shopRootPath()
    {
        if (!$this->shopRootPath) {
            $this->shopRootPath = (new Facts())->getShopRootPath();
        }

        return $this->shopRootPath;
    }
    
    public function getShopModulePath(string $modulePath): string
    {
        return $this->shopRootPath() . '/source/modules' . substr($modulePath, strrpos($modulePath, '/'));
    }

    public function installModule($modulePath)
    {
        //first Copy
        exec('cp ' . $modulePath . ' ' . $this->shopRootPath() . '/source/modules/ -R');
        //now activate
        exec(
            $this->shopRootPath() .
            '/bin/oe-console oe:module:install-configuration ' .
            $this->getShopModulePath($modulePath)
        );
    }

    public function uninstallModule($modulePath, $moduleId)
    {
        exec(
            $this->shopRootPath() .
            '/bin/oe-console oe:module:remove-configuration ' .
            $moduleId
        );
        $path = $this->getShopModulePath($modulePath);
        if (file_exists($path) && is_dir($path)) {
            exec('rm ' . $path . ' -R');
        }
    }

    public function activateModule($moduleId)
    {
        exec($this->shopRootPath() . '/bin/oe-console oe:module:activate ' . $moduleId);
    }

    public function deactivateModule($moduleId)
    {
        exec($this->shopRootPath() . '/bin/oe-console oe:module:deactivate ' . $moduleId);
    }
}
