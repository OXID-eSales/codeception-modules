<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Codeception\Module;

use Codeception\Module;
use OxidEsales\Codeception\Module\Exception\FixtureFileNotFoundException;
use OxidEsales\Facts\Facts;
use Symfony\Component\Filesystem\Filesystem;

use function dirname;

class ShopSetup extends Module
{
    use CommandTrait;

    protected array $config = [
        'dump' => '',
        'fixtures' => '',
        'mysql_config' => '',
        'db_name' => '',
        'license' => '',
        'out_directory_fixtures' => '',
    ];

    public function _beforeSuite($settings = []): void
    {
        $this->createEmptyDatabase();
        $this->addLicenseKey();
        $this->loadDatabaseFixtures();
        $this->dumpDatabaseToFile();

        $this->copyFileFixturesIntoShopsOutDirectory();
    }

    private function createEmptyDatabase(): void
    {
        $this->debug('Setup shop database');
        $this->debug($this->processConsoleCommand(' oe:database:reset --force'));
    }

    private function addLicenseKey(): void
    {
        if ($this->config['license']) {
            $this->debug('Add license key');
            $this->debug($this->processConsoleCommand(' oe:license:add ' . $this->config['license']));
        }
    }

    private function loadDatabaseFixtures(): void
    {
        $testFixturesSql = $this->getFixturesSqlFile();
        $this->debug("Import MySQL file: $testFixturesSql");
        $this->debug($this->loadDump($testFixturesSql));
    }

    /**
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

    private function loadDump(string $dumpFile): string
    {
        return $this->processCommand(
            'mysql --defaults-file="$optionFile" --default-character-set=utf8 "$name" < $dump',
            [
                'optionFile' => $this->config['mysql_config'],
                'name' => $this->config['db_name'],
                'dump' => $dumpFile
            ]
        );
    }

    private function dumpDatabaseToFile(): void
    {
        $dumpPath = $this->getPathForDatabaseDump();
        $this->debug("Create MySQL dump file: $dumpPath");
        $this->debug($this->dump($dumpPath));
    }

    private function getPathForDatabaseDump(): string
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

    private function dump(string $dumpFile): string
    {
        return $this->processCommand(
            'mysqldump --defaults-file="$optionFile" --default-character-set=utf8 --complete-insert "$name" > $dump',
            [
                'optionFile' => $this->config['mysql_config'],
                'name' => $this->config['db_name'],
                'dump' => $dumpFile
            ]
        );
    }

    private function copyFileFixturesIntoShopsOutDirectory(): void
    {
        $outDirectoryFixtures = $this->config['out_directory_fixtures'];
        $filesystem = new Filesystem();
        if (empty($outDirectoryFixtures)) {
            return;
        }
        if ($filesystem->exists($outDirectoryFixtures)) {
            $filesystem->mirror(
                $outDirectoryFixtures,
                (new Facts())->getOutPath()
            );
        } else {
            $this->debug(
                "Invalid configuration: 'out_directory_fixtures': '$outDirectoryFixtures' - directory does not exist!"
            );
        }
    }
}
