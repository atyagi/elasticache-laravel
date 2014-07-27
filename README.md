Elasticache Laravel
===================
[![Build Status](https://travis-ci.org/atyagi/elasticache-laravel.svg?branch=master)](https://travis-ci.org/atyagi/elasticache-laravel) 
[![Coverage Status](https://img.shields.io/coveralls/atyagi/elasticache-laravel.svg)](https://coveralls.io/r/atyagi/elasticache-laravel?branch=master)

AWS Elasticache Session and Cache Drivers for Laravel (Memcached specifically)

## Setup

This package requires the memcached extension for PHP. Please see [this link](http://php.net/manual/en/book.memcached.php) for installation instructions.

With composer, simply add `"atyagi/elasticache-laravel": "dev-master"` to your composer.json.

Once `composer update` is ran, add

`'Atyagi\Elasticache\ElasticacheServiceProvider',`

to the providers array in `app/config.php`.

At this point, inside of `app/session.php` and `app/cache.php`, you can use `elasticache` as a valid driver.

#### Versions
- dev-master -- Stable release version
- dev-dev -- Generally stable, but still the main development branch
- tags -- see Packagist (https://packagist.org/packages/atyagi/elasticache-laravel) for specific tagged versions. Most releases to master get tagged.

## Configuration

All configuration lives within `app/session.php` and `app/cache.php`. The key ones are below:

#### Session.php
- lifetime -- the session lifetime within the Memcached environment
- cookie -- this is the prefix for the session ID to prevent clashing

#### Cache.php
- memcached -- follow the Laravel defaults for host/port information


