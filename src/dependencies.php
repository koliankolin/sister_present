<?php
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

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

$container->set('view', function() use($settings) {
    return Twig::create($settings['renderer']['template_path']);
});

$app->add(TwigMiddleware::createFromContainer($app));