<?php

namespace Aego\OAuth2\Client\Test\Provider;

use Aego\OAuth2\Client\Provider\Odnoklassniki;
use League\OAuth2\Client\Token\AccessToken;

class OdnoklassnikiTest extends \PHPUnit_Framework_TestCase
{
    public function testGetResourceOwnerDetailsUrl()
    {
        $provider = new Odnoklassniki([
            'clientId' => 'mock',
            'clientPublic' => 'mock_public',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none',
        ]);

        $token = new AccessToken([
            'access_token' => 'mock_token',
        ]);

        $query = parse_url($provider->getResourceOwnerDetailsUrl($token), PHP_URL_QUERY);
        parse_str($query, $param);

        $this->assertEquals($token->getToken(), $param['access_token']);
        $this->assertEquals($provider->clientPublic, $param['application_key']);
        $this->assertNotEmpty($param['sig']);
    }
}
