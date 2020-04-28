<?php
use Slim\Factory\AppFactory;

$container = new \DI\Container();
AppFactory::setContainer($container);
$app = AppFactory::create();
$container = $app->getContainer();

$settings = require __DIR__ . '/../config/settings.php';
$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($settings['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container->set('db', function () use ($capsule){
    return $capsule;
});
