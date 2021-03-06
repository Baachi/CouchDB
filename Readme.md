# CouchDB #
A CouchDB Client for >=PHP 5.5 with event system.

Inspired by [Doctrine/MongoDB](https://github.com/Doctrine/mongodb) and [Doctrine/CouchDB](https://github.com/Doctrine/couchdb-odm)

| Service           | Status                                                                                                                                                                                        |
| ----------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Travis CI         | [![Build Status](https://secure.travis-ci.org/Baachi/CouchDB.png)](http://travis-ci.org/Baachi/CouchDB)                                                                                       |
| Scrutinizer       | [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/Baachi/CouchDB/badges/quality-score.png?s=5c5013c398de5097793c1210a87a6d94493006f2)](https://scrutinizer-ci.com/g/Baachi/CouchDB/) |
| Code Coverage     | [![Code Coverage](https://scrutinizer-ci.com/g/Baachi/CouchDB/badges/coverage.png?s=61e18d0b5850e702442bef89fe66aee830a4ecd4)](https://scrutinizer-ci.com/g/Baachi/CouchDB/)                  |
| SensioLabsInsight | [![SensioLabsInsight](https://insight.sensiolabs.com/projects/9a96763c-c938-48a6-a9fa-ac77efb7197d/mini.png)](https://insight.sensiolabs.com/projects/9a96763c-c938-48a6-a9fa-ac77efb7197d)   |
| Style CI          | [![StyleCI](https://styleci.io/repos/3046372/shield)](https://styleci.io/repos/3046372)                                                                                                       |

## Installation ##
__Composer__

You can install CouchDB Client over composer. Add the following line into your ```composer.json``` file.

```
$ composer require bachi/couchdb
```

__Don't use composer?__

Start to disover composer now! https://getcomposer.org

## Usage ##

### Initialize ###

```php
$client = new \GuzzleHttp\Client(['base_uri' => 'http://localhost:5984', 'http_errors' => false]);
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
 * [All contributors] (https://github.com/Baachi/CouchDB/contributors)

## License ##
CouchDB Client is released under the MIT License. See the bundled LICENSE file for details.
