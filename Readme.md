# CouchDB #
A CouchDB Client for >=PHP 5.3 with event system.

> __Please Note__
> This is library is currently in development! Do not use it in __production__.

Inspired by [Doctrine/MongoDB](https://github.com/Doctrine/mongodb) and [Doctrine/CouchDB](https://github.com/Doctrine/couchdb-odm)

[![Build Status](https://secure.travis-ci.org/Baachi/CouchDB.png)](http://travis-ci.org/Baachi/CouchDB)

[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/Baachi/CouchDB/badges/quality-score.png?s=5c5013c398de5097793c1210a87a6d94493006f2)](https://scrutinizer-ci.com/g/Baachi/CouchDB/)

## Installation ##
__Composer (recommend)__

You can install CouchDB Client over composer. Add the following line into your ```composer.json``` file.

```json
"require": {
    "bachi/couchdb": "dev-master"
}
```

__Git__

You can clone the repository:

```
git clone https://github.com/Baachi/CouchDB.git
```

or if you already use git in your project

```
git submodule add https://github.com/Baachi/CouchDB.git
```


## Usage ##

### Initialize ###

```php
$client = new \CouchDB\Http\StreamClient('localhost', 5984);
$connection = new \CouchDB\Connection($client);
```

### Creates a database ###

```php
$database = $conn->createDatabase('foobar');
// or with magic method
$database = $conn->foobar;
```

### Get a database instance ###

```php
$database = $conn->selectDatabase('foobar');
// or with magic method
$database = $conn->foobar;
```
### Delete a database ###

```php
if (true === $conn->hasDatabase('foobar')) {
    $conn->dropDatabase('foobar');
}
// or with magic methods
if (isset($conn->foobar)){
    unset($conn->foobar);
}
```

## Unit Tests (PHPUnit) ##
The testsuite can you find in the ```tests``` folder.

Run the testsuite:

```
phpunit
```

It is green?

## Credits ##

 * Markus Bachmann <markus.bachmann@bachi.biz>
 * [All contributors] (https://github.com/Baachi/Alien/contributors)

## License ##
CouchDB Client is released under the MIT License. See the bundled LICENSE file for details.
