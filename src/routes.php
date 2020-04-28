<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require 'app.php';

$app->get('/', function (Request $req, Response $res) {
    $lead = new \App\Models\Lead();
    $lead->phone = 77823432;
    $lead->name = 'name';
    $lead->session_type = 'online';
    $lead->save();
    $res->getBody()->write('Created');
    return $res;
});
$app->get('/users', function (Request $req, Response $res) {
    $res->getBody()->write('users');
    return $res;
});