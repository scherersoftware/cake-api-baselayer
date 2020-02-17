<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin(
    'CakeApiBaselayer',
    ['path' => '/scherersoftware/cake-api-baselayer'],
    function (RouteBuilder $routes) {
        $routes->fallbacks('DashedRoute');
    }
);
