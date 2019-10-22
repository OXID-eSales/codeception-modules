<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Codeception\Module;

require_once __DIR__.'/../../../../oxid-esales/testing-library/base.php';

use Codeception\Lib\Interfaces\ConflictsWithModule;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ModuleSetupException;

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
}
