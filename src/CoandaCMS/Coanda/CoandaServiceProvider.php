<?php namespace CoandaCMS\Coanda;

use Illuminate\Support\ServiceProvider;

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
		$this->package('dover/coanda');

		// Let the main class load any modules prior to handling the bindings
		$this->app->make('coanda')->loadModules();

		// Let the main class handles the bindings
		$this->app->make('coanda')->bindings($this->app);

		$this->app->make('coanda')->filters();
		$this->app->make('coanda')->routes();
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{		
		// Bind our main facade
		$this->app->singleton('coanda', function () {

			return new Coanda(new \CoandaCMS\Coanda\Authentication\Eloquent\User);

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
