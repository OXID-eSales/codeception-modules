<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Codeception\Module;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\Exception\ModuleException;

class Oxideshop extends \Codeception\Module
{
    /**
     * Reset context before test
     */
    public function _before(TestInterface $I)
    {
        Context::setActiveUser(null);
    }

    /**
     * Clear browser cache
     */
    public function clearShopCache()
    {
        $this->getModule('WebDriver')->_restart();
    }

    /**
     * Clean up database
     */
    public function cleanUp()
    {
        /** @var \Codeception\Module\Db $db */
        $db = $this->getModule('Db');
        $config = $db->_getConfig();
        $config['cleanup'] = true;

        try {
            $db->_cleanup(null, $config);
        } catch (\Exception $e) {
            throw new ModuleException(__CLASS__, $e->getMessage());
        }
    }

    /**
     * Removes \n signs and it leading spaces from string. Keeps only single space in the ends of each row.
     *
     * @param string $line Not formatted string (with spaces and \n signs).
     *
     * @return string Formatted string with single spaces and no \n signs.
     */
    public function clearString($line)
    {
        return trim(preg_replace("/[ \t\r\n]+/", ' ', $line));
    }

}
