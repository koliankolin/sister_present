<?php

namespace App\Instagram;
use PHLAK\Config\Config;
use Requests;
use App\Models;

class InstagramLoader {
    private $configService;
    private $clientId;
    private $clientSecrete;
    private $accessToken;
    private $callbackUrl = 'http://localhost:8888/insta/callback';
    private $refreshTokenUrl = 'https://graph.instagram.com/refresh_access_token';
    private $apiUrl = 'https://graph.instagram.com';
    private $fields = 'caption,media_type,media_url,permalink,like_count,timestamp';
    private $refreshTokenGrantType = 'ig_refresh_token';
    private $codeResponseType = 'code';

    public function __construct() {
        $this->configService = new Config('../config/settings.php');
        $this->clientId = $this->configService->get('insta')['client_id'];
        $this->clientSecrete = $this->configService->get('insta')['client_secrete'];
        $this->accessToken = $this->_getToken();
    }


    public function getPosts() {
        $lastPostFromDb = (new Models\Post())->latest('dt_inst')->first();
        $lastPostDate = new \DateTime($lastPostFromDb->dt_inst ?? '1970-01-01');
        $res = $this->_getPostsData();
        if (!$res || $res->error->type === 'OAuthException') {
            $token = $this->_refreshToken();
            Models\Token::where('type', 'insta')->update(['token' => $token]);
            $this->accessToken = $token;
            $res = $this->_getPostsData();
        }
        $postsFromDb = Models\Post::orderBy('dt_inst', 'desc')->get();
        $posts = (count($postsFromDb) > 0) ? array_slice($res->data, 5) : $res->data;
        foreach ($posts as $post) {
            $preparedPost = $this->_preparePost($post);
            if ($preparedPost['dt_inst'] > $lastPostDate) {
                Models\Post::create(
                    $preparedPost
                );
            }
        }

        return Models\Post::orderBy('dt_inst', 'desc')->get();
    }

    private function _getPostsData() {
        return json_decode(Requests::get( "{$this->apiUrl}/me/media?fields={$this->fields}&access_token={$this->accessToken}")->body);
    }

    private function _getToken() {
        return Models\Token::where('type', 'insta')->first()->token;
    }

    private function _refreshToken() {
        $data = [
            'grant_type' => $this->refreshTokenGrantType,
            'access_token' => $this->_getToken(),
        ];
        $query = http_build_query($data);
        $res = json_decode(Requests::get("{$this->refreshTokenUrl}?{$query}")->body);
        return $res->access_token;
    }

    private function _createParagraphs($text) {
        $brailleTab = json_decode("\0x2800");
        return  preg_replace("/ {$brailleTab} /u", "<br><br>", $text);
    }

    private function _preparePost($post) {
        return [
            'img' => $post->media_url,
            'text' => $post->caption,
            'link' => $post->permalink,
            'likes' => 0,
            'dt_inst' => new \DateTime($post->timestamp),
        ];
    }

    private function _getAuthCode() {
        $data = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->callbackUrl,
            'response_type' => $this->codeResponseType,
        ];

        return $data;
    }

}
