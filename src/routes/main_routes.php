<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \App\Notifications\TelegramNotification;
use \App\Instagram\InstagramLoader;

// TODO: why?!
require '/var/www/html/src/app.php';

// TODO: add Controler for that
function saveLead(Request $req) {
    $data = $req->getParsedBody();
//    \App\Models\Lead::updateOrCreate(
//        ['phone' => $data['phone']],
//        [
//            'name' => $data['name'],
//            'session_type' => $data['session_type'],
//        ]
//    );
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

// TODO: add Controler for that
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
        saveLead($req);
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
    $instagramLoader = new InstagramLoader();
    $posts = $instagramLoader->getPosts();
    if ($posts === null) {
        return $res->withHeader('Location', '/insta/loadAuth');
    }
    return $this->get('view')->render($res, 'blog.twig', compact('posts'));
});

$app->group('', function () use($app) {
    $app->get('/contact', function (Request $req, Response $res) {
        return $this->get('view')->render($res, 'contact.twig');
    })->setName('contact');

    $app->post('/contact', function (Request $req, Response $res) {
        saveLead($req);
        sendTelegranNotification($req);
        return $this->get('view')->render($res, 'contact.twig');
    });
});

$app->get('/faq', function (Request $req, Response $res) {
    return $this->get('view')->render($res, 'faq.twig');
});