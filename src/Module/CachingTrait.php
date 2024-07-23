<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Codeception\Module;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use Symfony\Component\Filesystem\Filesystem;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

trait CachingTrait
{
    public function cleanUpCompilationDirectory(): void
    {
        $this->cleanUpDirectory((new BasicContext())->getCacheDirectory());
    }

    private function cleanUpDirectory($directory): void
    {
        $fileSystem = new Filesystem();
        if ($fileSystem->exists($directory)) {
            $recursiveIterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::LEAVES_ONLY,
                RecursiveIteratorIterator::CATCH_GET_CHILD
            );

            $fileSystem->remove($recursiveIterator);
        }
    }
}
