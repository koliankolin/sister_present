<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require 'app.php';

$app->get('/', function (Request $req, Response $res) {
//    $lead = new \App\Models\Lead();
//    $lead->phone = 77823432;
//    $lead->name = 'name';
//    $lead->session_type = 'online';
//    $lead->save();
//    $res->getBody()->write('Created');

    return $this->get('view')->render($res, 'home.html');
});

$app->post('/', function (Request $req, Response $res) {
    $data = $req->getParsedBody();
    $lead = new \App\Models\Lead();
    foreach ($data as $key => $value) {
        try {
            $lead->set($key, $value);
            $lead->save();
        } catch (\Exception $e) {
            return $res->withStatus(500, "Invalid attribute {$key}");
        }
    }
    return $res;
});

$app->post('/users', function (Request $req, Response $res) {
    $res->getBody()->write('users');
    return $res;
});