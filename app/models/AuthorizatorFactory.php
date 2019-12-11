<?php

namespace App\Model;

use Nette;

class AuthorizatorFactory
{
    public static function create(): Nette\Security\Permission
    {
        $acl = new Nette\Security\Permission;
        $acl->addRole('admin');
        $acl->addResource('backend');
        $acl->allow('admin', 'backend');

        return $acl;
    }
}