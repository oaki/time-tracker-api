<?php

namespace App\Model;

class UserModel
{

    /**
     * @property-read \DibiConnection $connection
     */
    protected $connection;

    public function __construct(\DibiConnection $connection)
    {
        $this->connection = $connection;
    }

    public function findUser($password)
    {
        return $this->connection->select('*')
            ->from('user')
            ->where('password = %s', $password)
            ->fetch();
    }
}