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

/**
 * Class Oxideshop
 * @package OxidEsales\Codeception\Module
 */
class OxideshopModules extends \Codeception\Module
{
    /** @var string */
    private $shopRootPath;

    /**
     * @var array
     */
    protected $config = ['modules' => ''];

    /**
     * @var string
     */
    private $communityEditionRootPath;

    public function __construct(ModuleContainer $moduleContainer, $config = null)
    {
        $this->shopRootPath = (new Facts())->getShopRootPath();
        $this->communityEditionRootPath = (new Facts())->getCommunityEditionRootPath();

        parent::__construct($moduleContainer, $config);
    }

    /**
     * Reset context and activate modules before test
     */
    public function _beforeSuite($settings = [])
    {
        $this->activateModules();
    }

    /**
     * Reset context and activate modules before test
     */
    public function _afterSuite($settings = [])
    {
        $this->deactivateModules();
    }

    /**
     * Activates modules
     */
    private function activateModules()
    {
        foreach ($this->getModuleIds() as $moduleId) {
            $this->activateModule($moduleId);
        }
    }

    /**
     * Deactivates modules
     */
    private function deactivateModules()
    {
        foreach ($this->getModuleIds() as $moduleId) {
            $this->deactivateModule($moduleId);
        }
    }

    public function activateModule($moduleId)
    {
        exec($this->communityEditionRootPath . '/bin/oe-console oe:module:activate ' . $moduleId);
    }

    public function deactivateModule($moduleId)
    {
        exec($this->communityEditionRootPath . '/bin/oe-console oe:module:deactivate ' . $moduleId);
    }

    private function getModuleIds(): array
    {
        if (!empty($this->config['modules'])) {
            return explode(',', $this->config['modules']);
        }
        return [];
    }
}
