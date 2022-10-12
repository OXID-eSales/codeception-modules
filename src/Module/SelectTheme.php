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
use OxidEsales\Eshop\Core\Theme;

class SelectTheme extends Module implements DependsOnModule
{
    private Database $database;

    protected array $requiredFields = ['theme_id'];

    public function _depends(): array
    {
        return [
            Database::class => Database::class . ' is required',
        ];
    }

    public function _inject(Database $database): void
    {
        $this->database = $database;
    }

    public function _before(TestInterface $test): void
    {
        $this->database->updateConfigInDatabase('sTheme', $this->config['theme_id'], 'str');
        $this->database->updateConfigInDatabase('sCustomTheme', '', 'str');

        $theme = new Theme();
        $theme->load($this->config['theme_id']);
        $theme->activate();
    }
}
