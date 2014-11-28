<?php
namespace Aego\Oauth2\Client\Provider;

use League\OAuth2\Client\Entity\User;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Provider\AbstractProvider;

class Odnoklassniki extends AbstractProvider
{
    public $clientPublic = '';

    public function urlAuthorize()
    {
        return 'https://www.odnoklassniki.ru/oauth/authorize';
    }

    public function urlAccessToken()
    {
        return 'https://api.odnoklassniki.ru/oauth/token.do';
    }

    public function urlUserDetails(AccessToken $token)
    {
        $param = [
            'method' => 'users.getCurrentUser',
            'application_key' => $this->clientPublic,
        ];
        ksort($param);
        $param['sig'] = md5($this->httpBuildQuery($param, 0, '').md5($token.$this->clientSecret));
        $param['access_token'] = $token->accessToken;
        return 'http://api.odnoklassniki.ru/fb.do?'.$this->httpBuildQuery($param);
    }

    public function userDetails($response, AccessToken $token)
    {
        $user = new User;
        $user->exchangeArray((array) $response);
        $user->location = $response->location->city;
        $user->firstName = $response->first_name;
        $user->lastName = $response->last_name;
        return $user;
    }

    public function userUid($response, AccessToken $token)
    {
        return $response->uid;
    }

    public function userEmail($response, AccessToken $token)
    {
        return null;
    }

    public function userScreenName($response, AccessToken $token)
    {
        return [$response->first_name, $response->last_name];
    }
}
