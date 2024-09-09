<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Codeception\Module\ShopSetup;

use Codeception\Lib\Interfaces\DependsOnModule;
use Codeception\Module;
use Codeception\TestInterface;
use OxidEsales\Codeception\Module\CommandTrait;
use OxidEsales\Codeception\Module\Database;
use OxidEsales\Codeception\ShopSetup\DataObject\UserInput;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use OxidEsales\EshopCommunity\Setup\Utilities;
use OxidEsales\Facts\Facts;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class SetupEnvironment extends Module implements DependsOnModule
{
    use CommandTrait;

    private Database $database;
    private Filesystem $fileSystem;
    private string $htaccessFile;
    private string $databaseSchemaFile;
    private string $configFile;
    private UserInputPopulator $userInputPopulator;

    protected array $requiredFields = [
        'theme_id',
        'db_host',
        'db_port',
        'db_name',
        'db_user_name',
        'db_user_password',
    ];

    public function _depends(): array
    {
        return [
            Database::class => Database::class . ' is required',
        ];
    }

    public function _initialize(): void
    {
        parent::_initialize();

        $shopSourcePath = (new BasicContext())->getSourcePath();
        $this->fileSystem = new FileSystem();
        $this->htaccessFile = Path::join(
            $shopSourcePath,
            '.htaccess'
        );
        $this->databaseSchemaFile = Path::join(
            $shopSourcePath,
            'Setup/Sql/database_schema.sql'
        );
        $this->configFile = (new BasicContext())->getConfigFilePath();
        $this->userInputPopulator = new UserInputPopulator($this->config);
    }

    public function _inject(Database $database): void
    {
        $this->database = $database;
    }

    public function _before(TestInterface $test): void
    {
        $this->dropDatabaseIfAlreadyExists();
        $this->prepareConfigIncFileForSetup();
    }

    public function getDataForUserInput(): UserInput
    {
        return $this->userInputPopulator
            ->populate(
                new UserInput()
            );
    }

    public function activateTheme(string $themeId): void
    {
        $this->processConsoleCommand(
            "oe:theme:activate $themeId"
        );
        $this->processConsoleCommand(
            'oe:cache:clear'
        );
    }

    public function hasActiveDemoDataPackage(): bool
    {
        return $this->fileSystem->exists((new Utilities())->getActiveEditionDemodataPackagePath());
    }

    public function isCommunityEdition(): bool
    {
        return (new Facts())->isCommunity();
    }

    public function backupHtaccessFile(): void
    {
        $this->backupFile($this->htaccessFile);
    }

    public function removeHtaccessFile(): void
    {
        $this->fileSystem->remove($this->htaccessFile);
    }

    public function restoreHtaccessFile(): void
    {
        $this->restoreFileFromBackup($this->htaccessFile);
    }

    public function backupDatabaseSchemaFile(): void
    {
        $this->backupFile($this->databaseSchemaFile);
    }

    public function removeDatabaseSchemaFile(): void
    {
        $this->fileSystem->remove($this->databaseSchemaFile);
    }

    public function corruptDatabaseSchemaFile(): void
    {
        $this->fileSystem->dumpFile(
            $this->databaseSchemaFile,
            uniqid('this-is-not-sql-', true) . ';'
        );
    }

    public function restoreDatabaseSchemaFile(): void
    {
        $this->restoreFileFromBackup($this->databaseSchemaFile);
    }

    public function createDatabaseStub(string $databaseName): void
    {
        $this->database->executeQuery(
            "CREATE DATABASE IF NOT EXISTS `$databaseName`",
            []
        );
        $this->database->executeQuery(
            "CREATE TABLE IF NOT EXISTS `$databaseName`.`oxconfig`(`id` SMALLINT)",
            []
        );
    }

    public function dropDatabaseStub(string $databaseName): void
    {
        $this->database->executeQuery(
            "DROP DATABASE IF EXISTS `$databaseName`",
            []
        );
    }

    private function backupFile(string $filename): void
    {
        $this->fileSystem->copy(
            $filename,
            "$filename.bak",
        );
    }

    private function restoreFileFromBackup(string $filename): void
    {
        $this->fileSystem->copy(
            "$filename.bak",
            $filename,
            true
        );
    }

    private function prepareConfigIncFileForSetup(): void
    {
        if ($this->fileSystem->exists($this->configFile)) {
            $this->fileSystem->remove($this->configFile);
        }
        $this->fileSystem->copy($this->configFile . '.dist', $this->configFile);
        $this->fileSystem->chmod($this->configFile, 0777);
        $this->fileSystem->appendToFile($this->configFile, '$this->blDelSetupDir = false;');
        $this->fileSystem->chmod($this->configFile, 0777);
    }

    private function dropDatabaseIfAlreadyExists(): void
    {
        $this->database->executeQuery(
            "DROP DATABASE IF EXISTS `{$this->config['db_name']}`",
            []
        );
    }
}
