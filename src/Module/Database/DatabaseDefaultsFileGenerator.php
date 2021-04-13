<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Codeception\Module\Database;

use OxidEsales\Facts\Config\ConfigFile;
use Symfony\Component\Filesystem\Filesystem;

class DatabaseDefaultsFileGenerator
{
    /**
     * @var ConfigFile
     */
    private $config;

    /**
     * @param ConfigFile $config
     */
    public function __construct(ConfigFile $config)
    {
        $this->config = $config;
    }

    /**
     * @return string File path.
     */
    public function generate(): string
    {
        $file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('testing_codeception', true) . '.cnf';
        $fileContents = "[client]"
            . "\nuser=" . $this->config->getVar('dbUser')
            . "\npassword=" . $this->config->getVar('dbPwd')
            . "\nhost=" . $this->config->getVar('dbHost')
            . "\nport=" . $this->config->getVar('dbPort')
            . "\n";
        $fileSystem = new Filesystem();
        $fileSystem->dumpFile($file, $fileContents);
        return $file;
    }
}
