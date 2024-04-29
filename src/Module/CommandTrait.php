<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Codeception\Module;

use OxidEsales\Facts\Facts;
use Symfony\Component\Process\Process;

trait CommandTrait
{
    public function processCommand(string $command, array $parameter): string
    {
        $process = Process::fromShellCommandline($command, null, null, null, 600);
        $process->run(null, $parameter);
        return $process->getOutput();
    }

    public function processConsoleCommand(string $command): string
    {
        return $this->processCommand($this->getConsolePath() . ' ' . $command, []);
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
