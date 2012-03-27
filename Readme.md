# CouchDB #
A CouchDB Client for >=PHP 5.3 with event system.

Inspired by [Doctrine/MongoDB](https://github.com/Doctrine/mongodb) and [Doctrine/CouchDB](https://github.com/Doctrine/couchdb-odm)

[![Build Status](https://secure.travis-ci.org/Baachi/CouchDB.png)](http://travis-ci.org/Baachi/CouchDB)


## Installation ##

__GitHub:__

You can clone the repository:

```
git clone --recursive https://github.com/Baachi/CouchDB.git
```

or if you already use git in your project

```
git submodule add https://github.com/Baachi/CouchDB.git
```

__Composer:__

You can install CouchDB Client over composer. Add the following line into your ```composer.json``` file.

```
"requires": {
    "bachi/couchdb": "*"
}
```

And now, if you not use a autoloader, include the ``` autoload.php.dist``` in your project.

``` php
<?php
require_once '/path/to/couchdb/autoload.php.dist';
```

## Usage ##

### Initialize ###

``` php
<?php
require_once '/path/to/couchdb/autoload.php.dist';
$client = new \CouchDB\Http\StreamClient('localhost', 5984');
$connection = new \CouchDB\Connection($client);
```

### Creates a database ###

``` php
<?php
$database = $conn->createDatabase('foobar');
// or with magic method
$database = $conn->foobar;
```

### Get a database instance ###

``` php
<?php
$database = $conn->selectDatabase('foobar');
// or with magic method
$database = $conn->foobar;
```
### Delete a database ###

```php
<?php
if (true === $conn->hasDatabase('foobar') {
    $conn->dropDatabase('foobar');
}
// or with magic methods
if ( isset($conn->foobar) ){
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

## Licence ##
CouchDB Client is released under the MIT License. See the bundled LICENSE file for details.