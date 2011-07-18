<?php

define('START_TIME', microtime(true));

use Europa\Di\Container;

// can be overridden in bootstrap if need be
error_reporting(E_ALL ^ E_STRICT);
ini_set('display_errors', 'on');
date_default_timezone_set('Australia/Sydney');

// bootstrap the app
require dirname(__FILE__) . '/../app/Bootstrapper.php';
$boot = new Bootstrapper;
$boot();

// any exceptions will routed to the error controller
$container  = Container::get();
$request    = $container->request->get();
$response   = $container->response->get();
$router     = $container->router->get();
$dispatcher = $container->dispatcher->get();
try {
    $request->setParams($params);//request self configures 
    $dispatcher->dispatch($request, $response);//off to the dispatcher to start... dispatching
} catch (\Exception $e) {
    echo $request->setController('error')->dispatch()->render();
}