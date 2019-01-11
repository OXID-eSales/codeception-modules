<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Codeception\Module;

class Context
{
    protected static $activeUser;

    public static function isUserLoggedIn()
    {
        return isset(self::$activeUser);
    }

    public static function setActiveUser($activeUser)
    {
        self::$activeUser = $activeUser;
    }
}
