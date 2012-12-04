<?php

namespace CouchDB\Authentication;

use CouchDB\Http\ClientInterface;
use CouchDB\Http\Response\ResponseInterface;

/**
 * @author Maxim Gnatenko <mgnatenko@gmail.com>
 */
class CookieAuthentication implements AuthenticationInterface
{
    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $authCookie;

    /**
     * @param string $login
     * @param string $password
     */
    public function __construct($login, $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    /**
     * {@inheritDoc}
     */
    public function authorize(ClientInterface $client)
    {
        $response = $client->request(
            '/_session',
            ClientInterface::METHOD_POST,
            http_build_query(array('name' => $this->login, 'password' => $this->password)),
            array('Content-Type' => 'application/x-www-form-urlencoded')
        );

        $this->authCookie = self::extractCookie($response);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getHeaders()
    {
        if (!$this->authCookie) {
            return array();
        }

        return array('Cookie' => 'AuthSession='.$this->authCookie);
    }

    private static function extractCookie(ResponseInterface $response = null)
    {
        if (!$response) {
            return false;
        }

        if (!$response->isSuccessful()) {
            return false;
        }

        if (!$value = $response->getHeader('set-cookie')) {
            return false;
        }

        if (!preg_match('/AuthSession=([^;]+);/i', $value, $match)) {
            return false;
        }

        return $match[1];
    }
}
