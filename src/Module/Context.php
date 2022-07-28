<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Codeception\Module;

class Context
{
    /**
     * @var string
     */
    protected static $activeUser;

    /**
     * @return bool
     */
    public static function isUserLoggedIn()
    {
        return isset(self::$activeUser);
    }

    /**
     * @param string $activeUser
     */
    public static function setActiveUser(string $activeUser)
    {
        self::$activeUser = $activeUser;
    }

    /**
     * Reset active user
     */
    public static function resetActiveUser()
    {
        self::$activeUser = null;
    }
}
