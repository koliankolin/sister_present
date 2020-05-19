<?php

namespace App\Instagram;
use PHLAK\Config\Config;
use Requests;
use App\Models;

class InstagramLoader {
    private $configService;
    private $clientId;
    private $clientSecrete;
    private $callbackUrl = 'http://localhost:8888/insta/callback';
    private $tokenUrl = 'https://api.instagram.com/oauth/access_token';
    private $mediaUrl = 'https://api.instagram.com/v1/users/self/media/recent/';
    private $tokenGrantType = 'authorization_code';
    private $codeResponseType = 'code';

    public function __construct() {
        $this->configService = new Config('../config/settings.php');
        $this->clientId = $this->configService->get('insta')['client_id'];
        $this->clientSecrete = $this->configService->get('insta')['client_secrete'];
    }

    public function getAuthData() {
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
        return (json_decode($res->body))->access_token;
    }

    public function _refreshToken() {

    }

    //TODO: make script for refresh token by cron

    public function getPosts() {
        $lastPostFromDb = (new Models\Post())->latest('dt_inst')->first();
        $lastPostDate = $lastPostFromDb->dt_inst ?? '1970-01-01';
        $accessToken = Models\Token::where('type', 'insta')->first();
        $res = json_decode(Requests::get($this->mediaUrl . "?access_token={$accessToken->token}")->body);
        if (!$res || $res->meta->error_type === 'OAuthAccessTokenException') {
            return null;
        }
        $posts = $res->data;
        foreach ($posts as $post) {
            $preparedPost = $this->_preparePost($post);
            if (new \DateTime($preparedPost['dt_inst']->format('Y-m-d')) > new \DateTime($lastPostDate)) {
                Models\Post::create(
                    $preparedPost
                );
            }
        }

        return Models\Post::orderBy('dt_inst', 'desc')->get();
    }

    private function _removeTags($text, $tags) {
        foreach ($tags as $tag) {
            $text = str_replace("#{$tag}", '', $text);
        }
        return $text;
    }

    private function _preparePost($post) {
        $createdAt = (new \DateTime())->setTimestamp($post->created_time);
        $image = $post->images->standard_resolution->url;
        $text = $this->_removeTags($post->caption->text, $post->tags);
        $likesCount = $post->likes->count;
        $link = $post->link;
        return [
            'img' => $image,
            'text' => $text,
            'link' => $link,
            'likes' => $likesCount,
            'dt_inst' => $createdAt,
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
