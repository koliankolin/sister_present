<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use App\Instagram\InstagramLoader;
use App\Models\Token;


$app->get('/insta/callback', function (Request $req, Response $res) {
    $code = $req->getQueryParams()['code'];
    $instaLoader = new InstagramLoader();
    Token::updateOrCreate(
      ['type' => 'insta'],
      ['token' => $instaLoader->getToken($code)]
    );
    return $res->withHeader('Location', '/blog');
});

$app->get('/insta/loadAuth', function (Request $req, Response $res) {
    $instaLoader = new InstagramLoader();
    $authData = $instaLoader->getAuthData();
    return $res->withHeader('Location', 'https://api.instagram.com/oauth/authorize/?' . http_build_query($authData));
});