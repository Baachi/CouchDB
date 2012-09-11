<?php
namespace CouchDB\Auth;
use CouchDB\Http;

class Basic implements AuthInterface
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
     * @param Http\ClientInterface $client
     * @return AuthInterface|Basic
     */
    public function authorize(Http\ClientInterface $client)
    {
        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->login ?
            array('Authorization' => 'Basic ' . base64_encode($this->login . ':' . $this->password))
            :
            array();
    }
}
