<?php
namespace App\Model;

class BaseDbModel
{

    /**
     * @property-read \Dibi\Connection $connection
     */
    protected $connection;

    protected $table = '';

    protected $convention = 'id';

    protected $id_name = false;

    public function __construct(\Dibi\Connection $connection)
    {
        $this->connection = $connection;
        }

    public function getTableRows()
    {
        return $this->connection->query("SHOW COLUMNS FROM " . $this->table)->fetchPairs('Field', 'Field');
    }

    public function getConnection()
    {
        return $this->connection;
    }

    function insertAndReturnLastId($values)
    {
        $this->insert($values);

        return $this->connection->insertId();
    }

    function insert($values = [])
    {
        $this->connection->insert($this->table, $values)->execute();
    }

    function delete($id)
    {
        $this->connection->delete($this->table)->where($this->getTableIdName() . '=%i', $id)->execute();
    }

    function update($values, $id)
    {
        $this->connection->update($this->table, $values)->where($this->getTableIdName() . '=%i', $id)->execute();
    }

    function getFluent($select_collums = '*')
    {
        return $this->connection->select($select_collums)->from($this->table);
    }

    function fetchAll()
    {
        return $this->getFluent()->fetchAll();
    }

    function fetch($id)
    {
        return $this->getFluent()->where($this->getTableIdName() . '=%i', $id)->fetch();
    }

    public function getTableIdName()
    {
        return ($this->id_name) ? $this->id_name : str_replace('_TABLENAME__', $this->table, $this->convention);
    }

    public function getTableName()
    {
        return $this->table;
    }
}