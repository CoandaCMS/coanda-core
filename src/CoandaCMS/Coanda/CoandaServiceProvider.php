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
		$this->app->bind('coanda', function () {

			return new Coanda(new \CoandaCMS\Coanda\Authentication\Eloquent\User);

		});

		// Let the main class handles the bindings
		$this->app->make('coanda')->bindings($this->app);
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
