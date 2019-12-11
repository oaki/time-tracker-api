<?php

namespace App\Model;

use Tracy\Debugger;

class LogImageModel
{

    /**
     * @property-read \Dibi\Connection $connection
     */
    protected $connection;

    public function __construct(\Dibi\Connection $connection)
    {
        $this->connection = $connection;
    }

    public function save($params)
    {
        $this->connection->insert('image', $params)->execute();
    }

    public function getImage($id)
    {
        return $this->connection->fetchSingle('SELECT data FROM [image] WHERE id=%i', $id);
    }
}