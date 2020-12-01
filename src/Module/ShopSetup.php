<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Codeception\Module;

use Codeception\Module;
use OxidEsales\EshopCommunity\Tests\Utils\Database\DatabaseDefaultsFileGenerator;
use OxidEsales\EshopCommunity\Tests\Utils\Traits\DatabaseTrait;
use OxidEsales\Facts\Facts;
use Symfony\Component\Filesystem\Filesystem;


class ShopSetup extends Module
{
    use DatabaseTrait;

    /**
     * @var array
     */
    protected $config = [
        'shop_dump' => '',
        'fixtures' => ''
        ];

    public function _beforeSuite($settings = array())
    {
        $this->debug('Setup shop database');
        $this->setupShopDatabase();

        $this->debug('Add test fixtures');
        $this->executeSqlQueryFromFile($this->config['fixtures']);

        $this->createDump($this->getDumpFilePath());
    }

    /**
     * @param string $sqlFilePath
     */
    private function executeSqlQueryFromFile(string $sqlFilePath): void
    {
        $fileSystem = new Filesystem();
        if (!$fileSystem->exists($sqlFilePath)) {
            $this->debug('No fixtures file found');
            return;
        }
        $queries = file_get_contents($sqlFilePath);
        if (!$queries) {
            $this->debug('No fixtures found');
            return;
        }
        $this->executeSqlQuery($queries);
    }

    private function getDumpFilePath()
    {
        $shopDumpFile = $this->config['shop_dump'];
        $pathDir = dirname($shopDumpFile);
        $fileSystem = new Filesystem();
        if (!$fileSystem->exists($pathDir)) {
            $this->debug('Create dump directory');
            $fileSystem->mkdir($pathDir);
        }
        return $shopDumpFile;
    }

    public function createDump($pathDump)
    {
        $this->debug('Create mysqldump file');
        $facts = new Facts();
        exec(
            'mysqldump --defaults-file=' . $this->getMysqlConfigPath() .
            ' --default-character-set=utf8 ' . $facts->getDatabaseName() . ' > '.$pathDump,
            $output
        );
        $this->debug($output);
    }

    private function getMysqlConfigPath()
    {
        $generator = new DatabaseDefaultsFileGenerator(new \OxidEsales\Facts\Config\ConfigFile());
        return $generator->generate();
    }
}