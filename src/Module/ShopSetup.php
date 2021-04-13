<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Codeception\Module;

use Codeception\Module;
use OxidEsales\Codeception\Module\Database\DatabaseTrait;
use OxidEsales\Codeception\Module\Exception\FixtureFileNotFoundException;
use OxidEsales\Facts\Facts;
use Symfony\Component\Filesystem\Filesystem;

class ShopSetup extends Module
{
    use DatabaseTrait;

    /**
     * @var array
     */
    protected $config = [
        'dump' => '',
        'fixtures' => ''
        ];

    public function _beforeSuite($settings = array())
    {
        $this->setupShopDatabase();
        $this->importSqlFile($this->getDatabaseName(), $this->getFixturesSqlFile());
        $this->createSqlDump($this->getDatabaseName(), $this->getDumpFilePath());
    }

    /**
     * @return string
     * @throws FixtureFileNotFoundException
     */
    private function getFixturesSqlFile(): string
    {
        $sqlFilePath = $this->config['fixtures'];
        $fileSystem = new Filesystem();
        if (!$fileSystem->exists($sqlFilePath)) {
            $this->debug('No fixtures file found');
            throw new FixtureFileNotFoundException();
        }
        return $sqlFilePath;
    }

    /**
     * @return string
     */
    private function getDumpFilePath(): string
    {
        $shopDumpFile = $this->config['dump'];
        $pathDir = dirname($shopDumpFile);
        $fileSystem = new Filesystem();
        if (!$fileSystem->exists($pathDir)) {
            $this->debug('Create dump directory');
            $fileSystem->mkdir($pathDir);
        }
        return $shopDumpFile;
    }

    /**
     * @return string
     */
    private function getDatabaseName(): string
    {
        return (new Facts())->getDatabaseName();
    }
}
