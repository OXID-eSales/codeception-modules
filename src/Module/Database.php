<?php
namespace OxidEsales\Codeception\Module;

use Codeception\Module\Db;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Database extends \Codeception\Module
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
     * @param  string $table    tablename
     * @param  array $criteria conditions. See seeInDatabase() method.
     */
    public function deleteFromDatabase($table, $criteria)
    {
        $this->db->driver->deleteQueryByCriteria($table, $criteria);
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @param string $type
     */
    public function updateConfigInDatabase($name, $value, $type='bool')
    {
        /** @var \Codeception\Module\Db $dbModule */
        $dbModule = $this->db;
        $record = $dbModule->grabNumRecords('oxconfig', ['oxvarname' => $name]);
        if ($record > 0) {
            $query = "update oxconfig set oxvarvalue=ENCODE( :value, 'fq45QS09_fqyx09239QQ') where oxvarname=:name";
            $params = ['name' => $name, 'value' => $value];
        } else {
            $query = "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue)
                       values(:oxid, 1, :name, :type, ENCODE( :value, 'fq45QS09_fqyx09239QQ'))";
            $params = [
                'oxid' => md5($name.$type),
                'name' => $name,
                'type' => $type,
                'value' => $value
            ];
        }
        $this->db->driver->executeQuery($query, $params);
    }

}
