<?php

namespace CouchDB\Authentication;

use CouchDB\Http\ClientInterface;

/**
 * @author Maxim Gnatenko <mgnatenko@gmail.com>
 */
class BasicAuthentication implements AuthenticationInterface
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
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getHeaders()
    {
        if (!$this->login) {
            return array();
        }

        $auth = base64_encode("{$this->login}:{$this->password}");

        return array(
            'Authorization' => 'Basic '.$auth
        );
    }
}
