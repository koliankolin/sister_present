<?php

namespace App\Instagram;
use PHLAK\Config\Config;
use Requests;

class InstagramLoader {
    private $configService;
    private $clientId;
    private $clientSecrete;
    private $accessToken;
    private $callbackUrl = 'http://localhost:8888/insta/callback';
    private $authUrl = 'https://api.instagram.com/oauth/authorize';
    private $tokenUrl = 'https://api.instagram.com/oauth/access_token';
    private $mediaUrl = 'https://api.instagram.com/v1/users/self/media/recent/';
    private $tokenGrantType = 'authorization_code';
    private $refreshRokenGrantType = 'ig_refresh_token';
    private $refreshTokenUrl = 'https://graph.instagram.com/refresh_access_token';
    private $codeResponseType = 'code';

    public function __construct() {
        $this->configService = new Config('../config/settings.php');
        $this->clientId = $this->configService->get('insta')['client_id'];
        $this->clientSecrete = $this->configService->get('insta')['client_secrete'];
        $this->accessToken = $this->configService->get('insta')['access_token'];
    }

    public function getAuth() {
        return $this->_getAuthCode();
    }

    public function getToken(string $code) {
        $headers = [
            'multipart' => 'form-data',
        ];

        $data = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecrete,
            'grant_type' => $this->tokenGrantType,
            'redirect_uri' => $this->callbackUrl,
            'code' => $code,
        ];

        $res = Requests::post($this->tokenUrl, $headers, $data);
        return json_decode($res->body)['access_token'];
    }

    //TODO: refresh token

    public function getPosts() {
        $res = Requests::get($this->mediaUrl . "?access_token={$this->accessToken}");
        $posts = json_decode($res->body)->data;
        $postPrepared = [];
        foreach ($posts as $post) {
            $postPrepared[] = $this->_preparePost($post);
        }
        return $postPrepared;
    }

    private function _removeTags($text, $tags) {
        foreach ($tags as $tag) {
            $text = str_replace("#{$tag}", '', $text);
        }
        return $text;
    }

    private function _preparePost($post) {
        $image = $post->images->standard_resolution->url;
        $text = $this->_removeTags($post->caption->text, $post->tags);
        $likesCount = $post->likes->count;
        $link = $post->link;
        return [
            'image' => $image,
            'text' => $text,
            'likes_count' => $likesCount,
            'link' => $link,
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
