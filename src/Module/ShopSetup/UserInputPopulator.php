<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Codeception\Module\ShopSetup;

use OxidEsales\Codeception\ShopSetup\DataObject\UserInput;

class UserInputPopulator
{
    public function __construct(private array $config)
    {
    }

    public function populate(UserInput $userInput): UserInput
    {
        $userInput->setThemeId($this->config['theme_id']);
        $userInput->setDbHost($this->config['db_host']);
        $userInput->setDbPort($this->config['db_port']);
        $userInput->setDbName($this->config['db_name']);
        $userInput->setDbUserName($this->config['db_user_name']);
        $userInput->setDbUserPassword($this->config['db_user_password']);

        return $userInput;
    }
}
