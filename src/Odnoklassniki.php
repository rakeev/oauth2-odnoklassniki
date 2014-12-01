<?php
namespace Aego\OAuth2\Client\Provider;

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
        $param = 'application_key='.$this->clientPublic
            .'&fields=uid,name,first_name,last_name,location,pic_3,gender,locale'
            .'&method=users.getCurrentUser';
        $sign = md5(str_replace('&', '', $param).md5($token.$this->clientSecret));
        return 'http://api.odnoklassniki.ru/fb.do?'.$param.'&access_token='.$token.'&sig='.$sign;
    }

    public function userDetails($response, AccessToken $token)
    {
        $user = new User;
        $user->exchangeArray((array) $response);
        $user->location = $response->location->city;
        $user->firstName = $response->first_name;
        $user->lastName = $response->last_name;
        $user->imageUrl = $response->pic_3;
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
