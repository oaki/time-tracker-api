<?php

namespace App\Model;

use Tracy\Debugger;

class LogModel
{

    /**
     * @property-read \Dibi\Connection $connection
     */
    protected $connection;

    public function __construct(\Dibi\Connection $connection)
    {
        $this->connection = $connection;
    }

    public function save($params): int
    {
//        if($params['image']){
//            $image = $params['image'];
//            unset($params['image']);
//        }

        Debugger::log($params);
        return $this->connection->insert('log', $params)->execute('n');

    }
}