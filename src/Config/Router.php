<?php

namespace App\Config;

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

class Router
{
    public static function getDispatcher()
    {
        return simpleDispatcher(function (RouteCollector $r) {
            $r->post('/graphql', [\App\Controller\GraphQLController::class, 'handle']);
        });
    }
}
