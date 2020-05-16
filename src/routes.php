<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \App\Notifications\TelegramNotification;

require 'app.php';

function sendLead(Request $req) {
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
}

function sendTelegranNotification(Request $req) {
    $data = $req->getParsedBody() ?? $req->getQueryParams();
    $telegramNotifier = new TelegramNotification();
    return $telegramNotifier->sendLeadNotification($data['name'], $data['phone']);
}

$app->group('', function () use($app) {
    $app->get('/', function (Request $req, Response $res) {
        return $this->get('view')->render($res, 'home.twig');
    })->setName('home');

    $app->post('/', function (Request $req, Response $res) {
        sendLead($req);
        while (true) {
            $dataRes = json_decode(sendTelegranNotification($req));
            if (key_exists('error', $dataRes)) {
                continue;
            }
            if (key_exists('ok', $dataRes) && $dataRes['ok']) {
                break;
            }
        }
        return $res;
    });
});

//$app->get('/telegram', function (Request $req, Response $res) {
//    var_dump(sendTelegranNotification($req));
//    return $res;
//});

$app->get('/about', function (Request $req, Response $res) {
    return $this->get('view')->render($res, 'about.twig');
});

$app->get('/blog', function (Request $req, Response $res) {
    return $this->get('view')->render($res, 'blog.twig');
});

$app->group('', function () use($app) {
    $app->get('/contact', function (Request $req, Response $res) {
        return $this->get('view')->render($res, 'contact.twig');
    })->setName('contact');

    $app->post('/contact', function (Request $req, Response $res) {
        sendLead($req);
        sendTelegranNotification($req);
        return $this->get('view')->render($res, 'contact.twig');
    });
});

$app->get('/faq', function (Request $req, Response $res) {
    return $this->get('view')->render($res, 'faq.twig');
});

$app->get('/insta/callback', function (Request $req, Response $res) {
   var_dump($req->getQueryParams());
   return $res;
});