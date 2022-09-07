<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Codeception\Module;

use Codeception\Module;
use OxidEsales\Codeception\Module\Database\DatabaseDefaultsFileGenerator;
use OxidEsales\Codeception\Module\Exception\FixtureFileNotFoundException;
use OxidEsales\Facts\Config\ConfigFile;
use OxidEsales\Facts\Facts;
use Symfony\Component\Filesystem\Filesystem;

class ShopSetup extends Module
{
    use CommandTrait;

    private Facts $facts;

    /**
     * @var array
     */
    protected array $config = [
        'dump' => '',
        'fixtures' => '',
        'license' => ''
        ];

    public function _beforeSuite($settings = array())
    {
        $this->facts = new Facts();
        $this->setupShopDatabase();
        $this->addLicenseKey();
        $this->importSqlFile($this->facts->getDatabaseName(), $this->getFixturesSqlFile());
        $this->createSqlDump($this->facts->getDatabaseName(), $this->getDumpFilePath());
    }

    private function setupShopDatabase(): void
    {
        $this->debug('Setup shop database');
        $command = ' oe:database:reset' .
            ' --db-host=' . $this->facts->getDatabaseHost() .
            ' --db-port=' . $this->facts->getDatabasePort() .
            ' --db-name=' . $this->facts->getDatabaseName() .
            ' --db-user=' . $this->facts->getDatabaseUserName() .
            ' --db-password=' . $this->facts->getDatabasePassword() .
            ' --force';
        $this->debug($this->processConsoleCommand($command));
    }

    private function addLicenseKey(): void
    {
        if ($this->config['license']) {
            $this->debug('Add license key');
            $this->debug($this->processConsoleCommand(' oe:license:add ' . $this->config['license']));
        }
    }

    private function createSqlDump(string $databaseName, string $dumpFile): void
    {
        $this->debug('Create mysqldump file: ' . $dumpFile);
        $this->debug($this->processMysqldumpCommand($databaseName, $dumpFile));
    }

    private function importSqlFile(string $databaseName, string $sqlFile): void
    {
        $this->debug('Import mysql file: ' . $sqlFile);
        $this->debug($this->processMysqlCommand($databaseName, $sqlFile));
    }

    private function processMysqldumpCommand(string $databaseName, string $dumpFile): string
    {
        $command = 'mysqldump --defaults-file="$file" --default-character-set=utf8 "$name" > $dump';
        $parameter = [
            'file' => $this->getMysqlConfigPath() ,
            'name' => $databaseName,
            'dump' => $dumpFile
        ];
        return $this->processCommand($command, $parameter);
    }

    private function getMysqlConfigPath(): string
    {
        return (new DatabaseDefaultsFileGenerator(new ConfigFile()))->generate();
    }

    private function processMysqlCommand(string $databaseName, string $sqlFile): string
    {
        $command = 'mysql --defaults-file="$file" --default-character-set=utf8 "$name" < $sql';
        $parameter = [
            'file' => $this->getMysqlConfigPath() ,
            'name' => $databaseName,
            'sql' => $sqlFile
        ];
        return $this->processCommand($command, $parameter);
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
}
