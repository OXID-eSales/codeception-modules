<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Codeception\Module\Database;

use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Codeception\Module\Database\DatabaseDefaultsFileGenerator;
use OxidEsales\Facts\Facts;

trait DatabaseTrait
{
    /**
     * @param string $query
     */
    public function executeSqlQuery(string $query): void
    {
        DatabaseProvider::getDb()->execute($query);
    }

    public function setupShopDatabase()
    {
        $facts = new \OxidEsales\Facts\Facts();
        $resetDatabaseService = new \OxidEsales\DeveloperTools\Framework\Database\Service\ResetDatabaseService(
            new \OxidEsales\DeveloperTools\Framework\Database\Service\DatabaseChecker(),
            new \OxidEsales\DeveloperTools\Framework\Database\Service\DatabaseCreator(),
            new \OxidEsales\DeveloperTools\Framework\Database\Service\DatabaseInitiator(new \OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext()),
            new \OxidEsales\DeveloperTools\Framework\Database\Service\DropDatabaseService(),
            new \OxidEsales\DatabaseViewsGenerator\ViewsGenerator()
        );
        $resetDatabaseService->resetDatabase(
            $facts->getDatabaseHost(),
            $facts->getDatabasePort(),
            $facts->getDatabaseUserName(),
            $facts->getDatabasePassword(),
            $facts->getDatabaseName()
        );
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