<?php

namespace App\Model;

class LogModel
{

    /**
     * @property-read \DibiConnection $connection
     */
    protected $connection;

    public function __construct(\DibiConnection $connection)
    {
        $this->connection = $connection;
    }

    public function save($params)
    {
        $this->connection->insert('log', $params)->execute();
    }
}