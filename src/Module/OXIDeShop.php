<?php
namespace OxidEsales\Codeception\Module;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class OXIDeShop extends \Codeception\Module
{
    public function clearShopCache()
    {
        $this->getModule('WebDriver')->_restart();
    }

    public function cleanUp()
    {
        $this->getModule('Db')->_beforeSuite();
        $this->getModule('Db')->_cleanup();
    }


    /**
     * Removes \n signs and it leading spaces from string. Keeps only single space in the ends of each row.
     *
     * TODO: duplicate?
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
