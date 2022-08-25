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
use Symfony\Component\Process\Process;

class ShopSetup extends Module
{
    use DatabaseTrait;

    /**
     * @var array
     */
    protected $config = [
        'dump' => '',
        'fixtures' => '',
        'license' => ''
        ];

    public function _beforeSuite($settings = array())
    {
        $this->setupShopDatabase();
        $this->addLicenseKey();
        $this->importSqlFile($this->getDatabaseName(), $this->getFixturesSqlFile());
        $this->createSqlDump($this->getDatabaseName(), $this->getDumpFilePath());
    }

    private function addLicenseKey()
    {
        if ($this->config['license']) {
            $this->debug('Add license key');
            $command = $this->getConsolePath() .
                ' oe:license:add ' . $this->config['license'];
            $this->debug($this->processCommand($command, []));
        }
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

    private function processCommand(string $command, array $parameter): string
    {
        $process = Process::fromShellCommandline($command);
        $process->run(null, $parameter);
        return $process->getOutput();
    }
}
