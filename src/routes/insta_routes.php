<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use App\Instagram\InstagramLoader;

$app->get('/insta/callback', function (Request $req, Response $res) {
    $code = $req->getQueryParams()['code'];
    $instaLoader = new InstagramLoader();
    var_dump($instaLoader->getToken($code));
    die;
    return $res->getBody()->write($req->getQueryParams()['code']);
});

$app->get('/insta/loadAuth', function (Request $req, Response $res) {
    $instaLoader = new InstagramLoader();
    $data = $instaLoader->getAuth();

//    var_dump();
//    die;
    return $res->withHeader('Location', 'https://api.instagram.com/oauth/authorize/?' . http_build_query($data));
});

//$app->get('insta/getPosts', function(Request $req, ))