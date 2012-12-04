<?php

namespace CouchDB\Authentication;

use CouchDB\Http\ClientInterface;

/**
 * @author Maxim Gnatenko <mgnatenko@gmail.com>
 */
interface AuthenticationInterface
{

    /**
     * @param  ClientInterface $client
     *
     * @return AuthenticationInterface
     */
    public function authorize(ClientInterface $client);

    /**
     * Get additional headers
     *
     * @return array
     */
    public function getHeaders();
}
