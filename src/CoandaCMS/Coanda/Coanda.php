<?php namespace CoandaCMS\Coanda;

use App, Route, Config, Redirect, Request, Session;

class Coanda {

	/**
	 * Our user implementation
	 * @var [type]
	 */
	private $user;

	private $modules = [];

	/**
	 * @param CoandaCMSCoandaAuthenticationUser $user [description]
	 */
	public function __construct(\CoandaCMS\Coanda\Authentication\User $user)
	{
		$this->user = $user;
	}

	/**
	 * Takes the path and prepends the current admin_path from the config
	 * @param  string $path
	 * @return string
	 */
	public static function adminUrl($path)
	{
		return url(Config::get('coanda::coanda.admin_path') . '/' . $path);
	}

	/**
	 * Checks to see if we have a user
	 * @return boolean
	 */
	public function isLoggedIn()
	{
		return $this->user->isLoggedIn();
	}

	/**
	 * Returns the current user
	 * @return boolean
	 */
	public function currentUser()
	{
		return $this->user->currentUser();
	}

	/**
	 * Get all the enabled modules from the config and boots them up. Also adds to modules array for future use.
	 */
	public function loadModules()
	{
		$enabled_modules = Config::get('coanda::coanda.enabled_modules');

		foreach ($enabled_modules as $enabled_module)
		{
			$module = new $enabled_module($this);
			$module->boot();

			$this->modules[] = $module;
		}
	}

	/**
	 * Creates all the required filters
	 * @return
	 */
	public function filters()
	{
		Route::filter('admin_auth', function()
		{
		    if (!App::make('coanda')->isLoggedIn())
		    {
		    	Session::put('pre_auth_path', Request::path());

		    	return Redirect::to('/' . Config::get('coanda::coanda.admin_path') . '/login');
		    }

		});
	}

	/**
	 * Outputs all the routes, including those added by modules
	 * @return 
	 */
	public function routes()
	{
		Route::group(array('prefix' => Config::get('coanda::coanda.admin_path')), function()
		{
			// All module admin routes should be wrapper in the auth filter
			Route::group(array('before' => 'admin_auth'), function()
			{
				foreach ($this->modules as $module)
				{
					$module->adminRoutes();
				}
			});

			// We will put the main admin controller outside the group so it can handle its own filters
			Route::controller('/', 'CoandaCMS\Coanda\Controllers\Admin');

		});

		// Let the module output any front end 'user' routes
		foreach ($this->modules as $module)
		{
			$module->userRoutes();
		}
	}

	/**
	 * Runs through all the bindings
	 * @param  Illuminate\Foundation\Application $app
	 */
	public function bindings($app)
	{
		$app->bind('CoandaCMS\Coanda\Authentication\User', 'CoandaCMS\Coanda\Authentication\Eloquent\User');

		// Let the module output any front end 'user' routes
		foreach ($this->modules as $module)
		{
			$module->bindings($app);
		}
	}

}