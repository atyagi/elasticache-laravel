<?php namespace Atyagi\Elasticache;

use Illuminate\Support\ServiceProvider;

class ElasticacheServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $servers = $this->app['config']->get('cache.memcached');
        $elasticache = new ElasticacheConnector();
        $memcached = $elasticache->connect($servers);

        $this->app['cache']->extend('elasticache', function() use ($memcached) {
            /** @noinspection PhpUndefinedNamespaceInspection */
            /** @noinspection PhpUndefinedClassInspection */
            return new Illuminate\Cache\Repository(
                new Illuminate\Cache\MemcachedStore($memcached, $this->app['config']->get('cache.prefix')));
        });

        $this->app['session']->extend('elasticache', function() use ($memcached) {
            return new ElasticacheSessionHandler($memcached, $this->app);
        });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
