<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Codeception\Module;

use Codeception\Lib\Interfaces\DependsOnModule;
use Codeception\Module\Db;

/**
 * Class Database
 * @package OxidEsales\Codeception\Module
 */
class Database extends \Codeception\Module implements DependsOnModule
{
    /**
     * @var Db
     */
    private $db;

    /**
     * @return array
     */
    public function _depends()
    {
        return [Db::class => 'Codeception\Module\Db is required'];
    }

    /**
     * @param Db $db
     */
    public function _inject(Db $db)
    {
        $this->db = $db;
    }

    /**
     * Delete entries from $table where $criteria conditions
     * Use: $I->deleteFromDatabase('users', ['id' => '111111', 'banned' => 'yes']);
     *
     * @param string $table    The name of table.
     * @param array  $criteria The conditions. See seeInDatabase() method.
     */
    public function deleteFromDatabase(string $table, array $criteria)
    {
        $this->db->_getDriver()->deleteQueryByCriteria($table, $criteria);
    }

    /**
     * Update values in the config table.
     *
     * @param string $name  The name of config parameter.
     * @param mixed  $value The value of config parameter.
     * @param string $type  The type of config parameter.
     */
    public function updateConfigInDatabase(string $name, $value, string $type='bool')
    {
        /** @var \Codeception\Module\Db $dbModule */
        $record = $this->db->grabNumRecords('oxconfig', ['oxvarname' => $name]);
        $dbh = $this->db->_getDbh();
        if ($record > 0) {
            $query = "update oxconfig set oxvarvalue= :value where oxvarname=:name";
            $parameters = [
                'name' => $name,
                'value' => $value,
            ];
        } else {
            $query = "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue)
                       values(:oxid, 1, :name, :type, :value)";
            $parameters = [
                'oxid' => md5($name.$type),
                'name' => $name,
                'type' => $type,
                'value' => $value,
            ];
        }
        $sth = $dbh->prepare($query);
        $sth->execute($parameters);
    }

    /**
     * select a value from config table.
     *
     * @param string $name  The name of config parameter.
     * @param int $shopId  The shopId of config parameter.
     * @param string $module  The module of config parameter.
     *
     * @return mixed Returns array[value, type] or false
     */
    public function grabConfigValueFromDatabase(string $name, int $shopId, string $module = "")
    {
        $query = "select decode(oxvarvalue, :key) as value, oxvartype as type from oxconfig
                   where oxvarname= :name and oxshopid= :shopId and oxmodule= :module";

        $parameters = [
            'shopId' => $shopId,
            'name'   => $name,
            'key'    => $this->config['config_key'],
            'module' => $module
        ];

        $db = $this->db->_getDbh();
        $queryResult = $db->prepare($query);
        $queryResult->execute($parameters);

        return $queryResult->fetch();
    }
}
