<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Codeception\Module;

use Codeception\Module;
use InvalidArgumentException;
use Symfony\Component\Filesystem\Filesystem;

use function dirname;
use function sprintf;

class ShopSetup extends Module
{
    use CommandTrait;

    protected array $requiredFields = ['db_name', 'dump', 'fixtures', 'theme_id'];

    public function _beforeSuite($settings = []): void
    {
        $this->createEmptyDatabase();
        $this->addLicenseKey();
        $this->loadDatabaseFixtures();
        $this->activateFrontendTheme();
        $this->backupDatabaseToTheDumpFile();

        $this->copyFileFixturesIntoShopsOutDirectory();
    }

    protected function validateConfig(): void
    {
        parent::validateConfig();
        if (!(new Filesystem())->exists($this->config['fixtures'])) {
            throw new InvalidArgumentException(
                'Fixtures file does not exist'
            );
        }
        if (!empty($this->config['out_directory_fixtures'])) {
            if (empty($this->config['out_directory'])) {
                throw new InvalidArgumentException(
                        'out_directory can not be empty if out_directory_fixtures is set.'
                );
            }
            if (!(new Filesystem())->exists($this->config['out_directory_fixtures'])) {
                throw new InvalidArgumentException(
                    'out_directory_fixtures directory does not exist'
                );
            }
        }
    }

    private function createEmptyDatabase(): void
    {
        $this->debug('Setup shop database');
        $this->debug(
                $this->processConsoleCommand(' oe:database:reset --force')
        );
    }

    private function addLicenseKey(): void
    {
        if (!empty($this->config['license'])) {
            $this->debug('Add license key');
            $this->debug(
                    $this->processConsoleCommand(' oe:license:add ' . $this->config['license'])
            );
        }
    }

    private function loadDatabaseFixtures(): void
    {
        $this->debug('Import MySQL file');
        $this->debug(
            $this->processCommand(
                sprintf(
                    'mysql %s --default-character-set=utf8 "%s" < "%s"',
                    $this->getDefaultsFileMysqlCommandOption(),
                    $this->config['db_name'],
                    $this->config['fixtures']
                ),
                []
            )
        );
    }

    private function getDefaultsFileMysqlCommandOption(): string
    {
        $mysqlConfigFile = $this->config['mysql_config'] ?? '';
        return $mysqlConfigFile ? "--defaults-file=\"$mysqlConfigFile\"" : '';
    }

    private function backupDatabaseToTheDumpFile(): void
    {
        $this->preparePathForDatabaseDumpFile();
        $this->debug('Backup DB to dump file');
        $this->debug(
            $this->processCommand(
                sprintf(
                    'mysqldump %s --default-character-set=utf8 --complete-insert "%s" > "%s"',
                    $this->getDefaultsFileMysqlCommandOption(),
                    $this->config['db_name'],
                    $this->config['dump']
                ),
                []
            )
        );
    }

    private function preparePathForDatabaseDumpFile(): void
    {
        $pathDir = dirname($this->config['dump']);
        $fileSystem = new Filesystem();
        if (!$fileSystem->exists($pathDir)) {
            $this->debug('Create directories for DB dump');
            $fileSystem->mkdir($pathDir);
        }
    }

    private function copyFileFixturesIntoShopsOutDirectory(): void
    {
        if (!empty($this->config['out_directory_fixtures'])) {
            $this->debug('Copy file fixtures into shops out directory');
            (new Filesystem())->mirror(
                    $this->config['out_directory_fixtures'],
                    $this->config['out_directory'] ?? ''
                );
        }
    }

    private function activateFrontendTheme(): void
    {
        $this->processConsoleCommand(
            sprintf(
                'oe:theme:activate %s',
                $this->config['theme_id']
            )
        );
    }
}
