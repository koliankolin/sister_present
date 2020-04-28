<?php
require '../vendor/autoload.php';
require 'dependencies.php';

$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);




