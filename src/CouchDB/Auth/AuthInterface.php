<?php
namespace CouchDB\Auth;
use CouchDB\Http\ClientInterface;

interface AuthInterface {

    public function authorize(ClientInterface $client);

    public function getHeaders();
}
