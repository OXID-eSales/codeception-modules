<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Codeception\Module\Setup;

use Codeception\Module;

class UserInput extends Module
{
    protected array $requiredFields = [
        'theme_id',
        'db_host',
        'db_port',
        'db_name',
        'db_user_name',
        'db_user_password',
    ];

    public function getThemeId(): string
    {
        return $this->config['theme_id'];
    }

    public function getDbHost(): string
    {
        return $this->config['db_host'];
    }

    public function getDbPort(): string
    {
        return $this->config['db_port'];
    }

    public function getDbName(): string
    {
        return $this->config['db_name'];
    }

    public function getDbUserName(): string
    {
        return $this->config['db_user_name'];
    }

    public function getDbUserPassword(): string
    {
        return $this->config['db_user_password'];
    }
}
