<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require 'app.php';

$app->get('/', function (Request $req, Response $res) {
    return $this->get('view')->render($res, 'home.twig');
});

$app->post('/', function (Request $req, Response $res) {
    $data = $req->getParsedBody();
    $lead = new \App\Models\Lead();
    foreach ($data as $key => $value) {
        try {
            $lead->set($key, $value);
            $lead->save();
        } catch (\Exception $e) {
            //return $res->withStatus(500, "Invalid attribute {$key}");
        }
    }
    return $res;
});

$app->get('/about', function (Request $req, Response $res) {
    return $this->get('view')->render($res, 'about.twig');
});

$app->get('/blog', function (Request $req, Response $res) {
    return $this->get('view')->render($res, 'blog.twig');
});

$app->get('/contact', function (Request $req, Response $res) {
    return $this->get('view')->render($res, 'contact.twig');
});

$app->get('/faq', function (Request $req, Response $res) {
    return $this->get('view')->render($res, 'faq.twig');
});