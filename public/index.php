<?php

use Phalcon\Di;
use Phalcon\Escaper;
use Phalcon\Loader;
use Phalcon\Mvc\Application;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Router;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;
use Phalcon\Db\Adapter\Pdo\Mysql as Database;
use Phalcon\Mvc\Model\Manager as ModelManager;
use Phalcon\Mvc\Model\Metadata\Memory as ModelMetadata;

error_reporting(E_ALL | E_STRICT) ;
ini_set('display_errors', 'On');

/**
 * Very simple MVC structure
 */

$loader = new Loader();

$loader->registerFiles(['../vendor/autoload.php']);

$loader->registerNamespaces(
    [
        'Apps\Controllers' => '../apps/controllers/',
        'Apps\Forms' => '../apps/forms/',
        'Apps\Components' => '../apps/components/',
    ]
);

$loader->register();

$di = new Di();

// Registering a router
$di->set("router", Router::class);

// Registering a dispatcher
$di->set(
    'dispatcher',
    function () {
        $dispatcher = new MvcDispatcher();

        $dispatcher->setDefaultNamespace(
            'Apps\Controllers'
        );

        return $dispatcher;
    }
);

// Registering a Http\Response
$di->set("response", Response::class);

// Registering a Http\Request
$di->set("request", Request::class);

// Registering the view component
$di->set(
    "view",
    function () {
        $view = new View();

        $view->setViewsDir("../apps/views/");

        return $view;
    }
);

$di->set(
    "db",
    function () {
        return new Database(
            [
                "host"     => "localhost",
                "username" => "root",
                "password" => "",
                "dbname"   => "invo",
            ]
        );
    }
);

// Start the session the first time when some component request the session service
$di->setShared(
    'session',
    function () {
        $session = new \Phalcon\Session\Adapter\Files();
        $session->start();
        return $session;
    }
);

$di->set(
    'flash',
    function () {
        return new \Phalcon\Flash\Session();
    }
);

$di->set('escaper', function (){

    $escaper = new Escaper();
    $escaper->setEncoding('utf-8');

    return $escaper;
});

$di->setShared('api', function (){
    return new \Apps\Components\Api('http://api.phalcon.local/');
});

//Registering the Models-Metadata
$di->set("modelsMetadata", ModelMetadata::class);

//Registering the Models Manager
$di->set("modelsManager", ModelManager::class);

try {
    $application = new Application($di);

    $response = $application->handle();

    $response->send();
} catch (Exception $e) {
    echo $e->getMessage();
}
