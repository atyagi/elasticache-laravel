Elasticache Laravel
===================
[![Build Status](https://travis-ci.org/atyagi/elasticache-laravel.svg?branch=master)](https://travis-ci.org/atyagi/elasticache-laravel)
[![Coverage Status](https://img.shields.io/coveralls/atyagi/elasticache-laravel.svg?style=flat)](https://coveralls.io/r/atyagi/elasticache-laravel?branch=master)
[![Packagist](http://img.shields.io/packagist/v/atyagi/elasticache-laravel.svg?style=flat)](https://packagist.org/packages/atyagi/elasticache-laravel)

AWS Elasticache Session and Cache Drivers for Laravel (Memcached specifically)

## Setup

This package requires the memcached extension for PHP. Please see [this link](http://php.net/manual/en/book.memcached.php) for installation instructions.

With composer, simply add `"atyagi/elasticache-laravel": "~2.0"` to your composer.json. (or `"atyagi/elasticache-laravel": "~1.1"` for Laravel 4 installations)

Once `composer update` is ran, add

`'Atyagi\Elasticache\ElasticacheServiceProvider',`

to the providers array in `app/config.php`.

At this point, inside of `app/session.php` and `app/cache.php`, you can use `elasticache` as a valid driver.

#### Versions
- 2.* represents all versions for Laravel 5
- 1.* represents all versions for Laravel 4

## Configuration

All configuration lives within `app/session.php` and `app/cache.php`. The key ones are below:

#### session.php
- lifetime -- the session lifetime within the Memcached environment
- cookie -- this is the prefix for the session ID to prevent clashing

#### cache.php
Note: for Laravel 5, make sure to add this info to the stores array as follows:
````php
'stores' => [
  ...
  'elasticache' => [
    'driver' => 'memcached',
    'servers' => [
      [
        'host' => '<YOUR HOST>',
        'port' => '<YOUR_PORT>',
        'weight' => '<YOUR_WEIGHT>'
        ]
      ]
    ]
    ...
  ]
````
