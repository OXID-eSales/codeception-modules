<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Codeception\Module;

use Codeception\Module;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Yaml\Yaml;

class ProjectConfiguration extends Module
{
    protected array $requiredFields = [
        'config_path',
        'parameters',
        'services'
    ];

    public function _beforeSuite(array $settings = []): void
    {
        parent::_beforeSuite();
        $this->dumpProjectConfigurations();
    }

    public function dumpProjectConfigurations(): void
    {
        $filesystem = new Filesystem();

        $filesystem->dumpFile(
            Path::join($this->config['config_path'], 'parameters.yaml'),
            Yaml::dump(['parameters' => $this->config['parameters']])
        );
        $filesystem->dumpFile(
            Path::join($this->config['config_path'], 'services.yaml'),
            Yaml::dump(['services' => $this->config['services']])
        );
    }
}
