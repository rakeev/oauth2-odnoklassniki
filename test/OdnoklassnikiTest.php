<?php
namespace Aego\OAuth2\Client\Test\Provider;

class OdnoklassnikiTest extends \PHPUnit_Framework_TestCase
{
    protected $response;
    protected $provider;
    protected $token;

    protected function setUp()
    {
        $this->response = json_decode('{"uid":"12345678901","first_name":"First","last_name":"Last",'
            .'"name":"First Last","locale":"ru","gender":"male","pic_3":"http://mock.ph/oto.jpg","location":'
            .'{"city":"Тольятти","country":"RUSSIAN_FEDERATION","countryCode":"RU","countryName":"Россия"}}');
        $this->provider = new \Aego\OAuth2\Client\Provider\Odnoklassniki([
            'clientId' => 'mock',
            'clientPublic' => 'mock_public',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none',
        ]);
        $this->token = new \League\OAuth2\Client\Token\AccessToken([
            'access_token' => 'mock_token',
        ]);
    }

    public function testUrlUserDetails()
    {
        $query = parse_url($this->provider->urlUserDetails($this->token), PHP_URL_QUERY);
        parse_str($query, $param);

        $this->assertEquals($this->token->accessToken, $param['access_token']);
        $this->assertEquals($this->provider->clientPublic, $param['application_key']);
        $this->assertNotEmpty($param['sig']);
    }

    public function testUserDetails()
    {
        $user = $this->provider->userDetails($this->response, $this->token);
        $this->assertInstanceOf('League\\OAuth2\\Client\\Entity\\User', $user);
        foreach (['uid', 'name', 'locale', 'gender'] as $fld) {
            $this->assertEquals($this->response->$fld, $user->$fld);
        }
        $this->assertEquals($this->response->location->city, $user->location);
        $this->assertEquals($this->response->first_name, $user->firstName);
        $this->assertEquals($this->response->last_name, $user->lastName);
        $this->assertEquals($this->response->pic_3, $user->imageUrl);
    }

    public function testUserUid()
    {
        $uid = $this->provider->userUid($this->response, $this->token);
        $this->assertEquals($this->response->uid, $uid);
    }

    public function testUserScreenName()
    {
        $name = $this->provider->userScreenName($this->response, $this->token);
        $this->assertEquals([$this->response->first_name, $this->response->last_name], $name);
    }
}
