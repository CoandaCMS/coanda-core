<?php namespace CoandaCMS\Coanda;

use Illuminate\Support\ServiceProvider;

/**
 * Class CoandaServiceProvider
 * @package CoandaCMS\Coanda
 */
class CoandaServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		\Event::listen('auth.login', function ($user) {

			$user->last_login = new \DateTime;
			$user->save();

		});

		$this->package('coandacms/coanda-core', 'coanda');

		$coanda = $this->app->make('coanda');

		// Let the main class load any modules prior to handling the bindings
		$coanda->loadModules();

		// Let the main class handles the bindings
		$coanda->bindings($this->app);

		// Add any filters
		$coanda->filters();

		// Add the routes
		$coanda->routes();

		// Boot up coanda...
		$coanda->boot($this->app);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// Add the blade @set operator, which is used in this package...
		$this->app->register(new \Alexdover\BladeSet\BladeSetServiceProvider($this->app));

		// Bind our main facade
		$this->app->singleton('coanda', function ($app) {

			return new Coanda($app);

		});

        // Add an exception handler for PageNotFound - this will always be a 404
        $this->app->error(function(\CoandaCMS\Coanda\Pages\Exceptions\PageNotFound $exception, $code, $fromConsole)
        {
            $this->app->abort('404');
        });

		$this->app['coanda.setup'] = $this->app->share(function($app)
		{
		    return new \CoandaCMS\Coanda\Artisan\SetupCommand($app);
		});
		
		$this->commands('coanda.setup');

		$this->app['coanda.delayedpublish'] = $this->app->share(function($app)
		{
		    return new \CoandaCMS\Coanda\Artisan\DelayedPublishCommand($app);
		});

		$this->commands('coanda.delayedpublish');
		
		$this->app['coanda.reindex'] = $this->app->share(function($app)
		{
		    return new \CoandaCMS\Coanda\Pages\Artisan\Reindex($app);
		});

		$this->commands('coanda.reindex');

		$this->app['coanda.historydigest'] = $this->app->share(function($app)
		{
			return new \CoandaCMS\Coanda\History\Artisan\SendDailyDigest($app);
		});

		$this->commands('coanda.historydigest');

		$this->app->error(function(\CoandaCMS\Coanda\Exceptions\PermissionDenied $exception)
		{
			return \View::make('coanda::admin.permissiondenied');

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
