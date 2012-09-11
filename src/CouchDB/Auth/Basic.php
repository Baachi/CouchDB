<?php
namespace CouchDB\Auth;
use CouchDB\Http;

class Basic implements AuthInterface
{

    private $login;
    private $password;

    public function __construct($login, $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    public function authorize(Http\ClientInterface $client)
    {
        return $this;
    }

    public function getHeaders()
    {
        return $this->login ?
            array(
                'Authorization' => 'Basic ' . base64_encode($this->login . ':' . $this->password)
            )
            : array();
    }
}
