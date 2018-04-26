<?php namespace Atyagi\Elasticache;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\ServiceProvider;

class ElasticacheServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     * @return void
     */
    public function register()
    {
        /** @var Repository $config */
        $config = $this->app->make('config');

        $servers = $config->get('cache.stores.memcached.servers');

        // No servers defined
        if ($servers === null) {
            return;
        }

        $elasticache = new ElasticacheConnector();
        $memcached = $elasticache->connect($servers);

        // memcached extension not loaded
        if ($memcached) {

            $this->app->registerDeferredProvider('Illuminate\Cache\CacheServiceProvider');

            $this->app->make('session')->extend('elasticache', function () use ($memcached) {
                return new ElasticacheSessionHandler($memcached, $this->app);
            });

            $this->app->make('cache')->extend('elasticache', function () use ($memcached, $config) {
                /** @noinspection PhpUndefinedNamespaceInspection */
                /** @noinspection PhpUndefinedClassInspection */
                return new \Illuminate\Cache\Repository(
                    new \Illuminate\Cache\MemcachedStore($memcached, $config->get('cache.prefix')));
            });
        }
    }

    /**
     * Get the services provided by the provider.
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
