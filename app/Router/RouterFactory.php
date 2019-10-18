<?php

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;
use Ublaboo\ApiRouter\ApiRoute;


final class RouterFactory
{
    use Nette\StaticClass;

    public static function createRouter(): RouteList
    {
        $router = new RouteList;

//        $router[] = new ApiRoute('/hello', 'Users');

        $router[] = new ApiRoute('/hello', 'ApiRouter', [
            'methods' => ['GET' => 'superRead', 'POST']
        ]);
        $router->addRoute('<presenter>/<action>[/<id>]', 'Homepage:default');
        return $router;
    }
}
