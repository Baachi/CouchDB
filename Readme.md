# CouchDB #
A CouchDB Client for >=PHP 5.3 with event system.

Inspired by [Doctrine/MongoDB](https://github.com/Doctrine/mongodb) and [Doctrine/CouchDB](https://github.com/Doctrine/couchdb-odm)

[![Build Status](https://secure.travis-ci.org/Baachi/CouchDB.png)](http://travis-ci.org/Baachi/CouchDB)


## Installation ##

__GitHub:__

You can clone the Repository:
```
git clone --recursive https://github.com/Baachi/CouchDB.git
```

or if you already use a git
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

And now, if you are not use a autoloader, include the ``` autoload.php.dist``` in your project.
```php
require_once '/path/to/couchdb/autoload.php.dist';
```

## Credits ##

 * Markus Bachmann <markus.bachmann@bachi.biz>
 * [All contributors] (https://github.com/Baachi/Alien/contributors)

## Licence ##
CouchDB Client is released under the MIT License. See the bundled LICENSE file for details.