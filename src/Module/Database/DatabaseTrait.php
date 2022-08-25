<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Codeception\Module\Database;

use OxidEsales\Facts\Config\ConfigFile;
use OxidEsales\Facts\Facts;
use Symfony\Component\Process\Process;
use Webmozart\PathUtil\Path;

trait DatabaseTrait
{
    public function setupShopDatabase(): void
    {
        $this->debug('Setup shop database');
        $facts = new Facts();
        $command = $this->getConsolePath() .
            ' oe:database:reset' .
            ' --db-host=' . $facts->getDatabaseHost() .
            ' --db-port=' . $facts->getDatabasePort() .
            ' --db-name=' . $facts->getDatabaseName() .
            ' --db-user=' . $facts->getDatabaseUserName() .
            ' --db-password=' . $facts->getDatabasePassword() .
            ' --force';
        $this->debug($this->processCommand($command, []));
    }

    public function createSqlDump(string $databaseName, string $dumpFile): void
    {
        $this->debug('Create mysqldump file: ' . $dumpFile);
        $this->debug($this->processMysqldumpCommand($databaseName, $dumpFile));
    }

    public function importSqlFile(string $databaseName, string $sqlFile)
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

    private function processCommand(string $command, array $parameter): string
    {
        $process = Process::fromShellCommandline($command);
        $process->run(null, $parameter);
        return $process->getOutput();
    }

    private function getMysqlConfigPath(): string
    {
        $generator = new DatabaseDefaultsFileGenerator(new ConfigFile());
        return $generator->generate();
    }

    private function getConsolePath(): string
    {
        $rootPath      = (new Facts())->getShopRootPath();
        $possiblePaths = [
            '/bin/oe-console',
            '/vendor/bin/oe-console',
        ];

        foreach ($possiblePaths as $path) {
            if (is_file($rootPath . $path)) {
                return $rootPath . $path;
            }
        }

        throw new \Exception('error: console not found');
    }
}