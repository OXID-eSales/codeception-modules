<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Codeception\Module;

use Codeception\Lib\Interfaces\DependsOnModule;
use Codeception\Module;
use Codeception\TestInterface;
use Codeception\Module\Db;
use Error;

/**
 * Class SelectTheme
 * @package OxidEsales\Codeception\Module
 */
class SelectTheme extends Module implements DependsOnModule
{
    private Database $database;

    protected $requiredFields = ['themeId'];

    private Db $db;

    public function _depends(): array
    {
        return [
            Database::class => 'OxidEsales\Codeception\Module\Database is required',
            Db::class => 'Codeception\Module\Db is required'
        ];
    }

    public function _inject(Database $database, Db $db): void
    {
        $this->database = $database;
        $this->db = $db;
    }

    public function _before(TestInterface $test): void
    {
        $this->database->updateConfigInDatabase('sTheme', $this->config['themeId'], 'str');
        $this->database->updateConfigInDatabase('sCustomTheme', '', 'str');

        if ($this->config['themeId'] === 'twig') {
            $this->importTwigThemeConfigIntoDatabase();
        }
    }

    private function importTwigThemeConfigIntoDatabase(): void
    {
        $dbh = $this->db->_getDbh();
        $sth = $dbh->prepare(
            file_get_contents(
                __DIR__ . '/../../../twig-theme/setup.sql'
            )
        );
        $sth->execute();
    }
}
