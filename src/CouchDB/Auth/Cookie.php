<?php
namespace CouchDB\Auth;
use CouchDB\Http;

class Cookie implements AuthInterface
{

    private $login;
    private $password;

    private $authCookie;

    public function __construct($login, $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    public function authorize(Http\ClientInterface $client)
    {
        $response = $client->request(
            '/_session',
            Http\ClientInterface::METHOD_POST,
            http_build_query(array('name' => $this->login, 'password' => $this->password)),
            array('Content-Type' => 'application/x-www-form-urlencoded')
        );

        $this->authCookie = self::extractCookie($response);
        return $this;
    }

    public function getHeaders()
    {
        return $this->authCookie ?
            array('Cookie' => 'AuthSession=' . $this->authCookie) : array();
    }

    private static function extractCookie(Http\Response\ResponseInterface $response = null)
    {
        if (
            $response
            && ($response->getStatusCode() == 200)
            && $response->getHeader('set-cookie')
            && preg_match('/AuthSession=([^;]+);/i', $response->getHeader('set-cookie'), $regs)) {
            return $regs[1];
        }
        return null;
    }
}
