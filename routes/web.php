<?php

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('/auth/{provider}', ['as' => 'authenticate', 'uses' => 'AuthController@postAuthenticate']);
