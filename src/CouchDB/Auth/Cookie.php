<?php
namespace CouchDB\Auth;
use CouchDB\Http;

/**
 * @author Maxim Gnatenko <mgnatenko@gmail.com>
 */
class Cookie implements AuthInterface
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
     * @param Http\ClientInterface $client
     * @return AuthInterface|Cookie
     */
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

    /**
     * @return array
     */
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
