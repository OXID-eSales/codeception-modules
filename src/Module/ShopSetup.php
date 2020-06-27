<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Codeception\Module;

use Codeception\Module;
use Codeception\TestInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\TestUtils\Database\FixtureLoader;
use OxidEsales\EshopCommunity\Tests\TestUtils\Database\TestDatabaseHandler;
use OxidEsales\EshopCommunity\Tests\TestUtils\Traits\CachingTrait;
use OxidEsales\EshopCommunity\Tests\TestUtils\Traits\ContainerTrait;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;

class ShopSetup extends Module
{
    use ContainerTrait;
    use CachingTrait;

    public function _beforeSuite($settings = [])
    {
        parent::_beforeSuite($settings);
        $this->cleanupCaching();
        FixtureLoader::getInstance()->loadFixtures(
            [Path::join(OX_TESTS_PATH, 'Codeception', '_data', 'db_fixture.yml')]);
        $this->setupCodeceptionContainer();
    }

    public function _before(TestInterface $test)
    {
        parent::_before($test);
        /** @var ContextInterface $context */
        $context = $this->get(ContextInterface::class);
        $varDir = dirname($context->getConfigurationDirectoryPath());
        if (file_exists($varDir))
        {
            if (file_exists($varDir . '.bak')) {
                # Some error happend and the backup directory has not been reset
                # so we probably should just clean up the var dir and hope for the best
                $fileSystem = new Filesystem();
                $fileSystem->remove($varDir);
                return;
            }
            rename($varDir, $varDir . '.bak');
        }
    }

    public function _after(TestInterface $test)
    {
        /** @var ContextInterface $context */
        $context = $this->get(ContextInterface::class);
        $varDir = dirname($context->getConfigurationDirectoryPath());
        if (file_exists($varDir . '.bak')) {
            if (file_exists($varDir)) {
                $fileSystem = new Filesystem();
                $fileSystem->remove($varDir);
            }
            rename($varDir . '.bak', $varDir);
        }
        parent::_after($test);
    }

    public function _afterSuite()
    {
        TestDatabaseHandler::cleanupTestConfigInc();
        parent::_afterSuite();
    }
}