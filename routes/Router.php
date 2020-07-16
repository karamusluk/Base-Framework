<?php

namespace SlimRouteInit;
use AppInit\App;

class Router
{
    static function getAllRoutes($app){
        $middlewares = (array) glob(__DIR__ . DIRECTORY_SEPARATOR . 'middlewares'.DIRECTORY_SEPARATOR.'*middleware.php');
        foreach($middlewares as $middleware) {
            include_once $middleware;
        }
        $routeFiles = (array) glob(__DIR__ . DIRECTORY_SEPARATOR . '*routes.php');
        foreach($routeFiles as $routeFile) {
            include_once $routeFile;
        }


    }

}