<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Codeception\Module;

use Codeception\Lib\ModuleContainer;
use Codeception\Module;
use OxidEsales\EshopCommunity\Tests\TestUtils\Traits\DatabaseTestingTrait;
use OxidEsales\EshopCommunity\Tests\TestUtils\Traits\SetupTrait;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;

class ShopSetup extends Module
{
    use SetupTrait;

    public function __construct(ModuleContainer $moduleContainer, $config = null)
    {
        parent::__construct($moduleContainer, $config);
    }


    public function _beforeSuite($settings = [])
    {
        parent::_beforeSuite($settings);
        $this->backupConfigInc();
        $this->configureTestDatabaseInConfigInc();
        $this->initializeDatabase();
        $this->createViews();
    }

    public function _afterSuite()
    {
        $this->restoreConfigInc();
        parent::_afterSuite();
    }
}