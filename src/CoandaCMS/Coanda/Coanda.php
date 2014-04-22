<?php namespace CoandaCMS\Coanda;

use App, Route, Config, Redirect, Request, Session;

use CoandaCMS\Coanda\Exceptions\PageTypeNotFound;
use CoandaCMS\Coanda\Exceptions\PageAttributeTypeNotFound;

/**
 * Class Coanda
 * @package CoandaCMS\Coanda
 */
class Coanda {

    /**
     * @var
     */
    private $user;
    /**
     * @var array
     */
    private $modules = [];
    /**
     * @var array
     */
    private $permissions = [];
    /**
     * @var
     */
    private $urlRepository;

    private $routers = [];

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    public function boot(\Illuminate\Foundation\Application $app)
	{
		$this->urlRepository = $app->make('CoandaCMS\Coanda\Urls\Repositories\UrlRepositoryInterface');
		$this->user = $app->make('CoandaCMS\Coanda\Users\Repositories\UserRepositoryInterface');
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
     * @return array
     */
    public function availablePermissions()
	{
		return $this->permissions;
	}

    /**
     * @param $module
     * @param $module_name
     * @param $views
     */
    public function addModulePermissions($module, $module_name, $views)
	{
		$this->permissions[$module] = [
										'name' => $module_name,
										'views' => $views
									];
	}

    /**
     * @param $permission
     * @param bool $permission_id
     * @return mixed
     */
    public function canAccess($permission, $permission_id = false)
	{
		return $this->user->hasAccessTo($permission, $permission_id);
	}

	/**
	 * Get all the enabled modules from the config and boots them up. Also adds to modules array for future use.
	 */
	public function loadModules()
	{
		$core_modules = [
			'CoandaCMS\Coanda\Users\UsersModuleProvider',
			'CoandaCMS\Coanda\Pages\PagesModuleProvider',
			'CoandaCMS\Coanda\Media\MediaModuleProvider'
		];

		$enabled_modules = Config::get('coanda::coanda.enabled_modules');

		$modules = array_merge($core_modules, $enabled_modules);

		foreach ($modules as $module)
		{
			if (class_exists($module))
			{
				$enabled_module = new $module($this);
				$enabled_module->boot($this);

				$this->modules[$enabled_module->name] = $enabled_module;
			}
		}
	}

	/**
	 * Creates all the required filters
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
			Route::controller('/', 'CoandaCMS\Coanda\Controllers\AdminController');

		});

		// Let the module output any front end 'user' routes
		foreach ($this->modules as $module)
		{
			$module->userRoutes();
		}

		App::before( function () {

			Route::match(array('GET', 'POST'), '{slug}', function($slug)
			{
				return Coanda::route($slug);

			})->where('slug', '[\/_\-\_A-Za-z0-9]+');

		});

	}

	/**
	 * Runs through all the bindings
	 * @param  Illuminate\Foundation\Application $app
	 */
	public function bindings($app)
	{
		$app->bind('CoandaCMS\Coanda\Urls\Repositories\UrlRepositoryInterface', 'CoandaCMS\Coanda\Urls\Repositories\Eloquent\EloquentUrlRepository');
		$app->bind('CoandaCMS\Coanda\History\Repositories\HistoryRepositoryInterface', 'CoandaCMS\Coanda\History\Repositories\Eloquent\EloquentHistoryRepository');

		// Let the module output any bindings
		foreach ($this->modules as $module)
		{
			$module->bindings($app);
		}
	}

    /**
     * @param $module
     * @return mixed
     */
    public function module($module)
	{
		return $this->modules[$module];
	}

    /**
     * @param $for
     * @param $closure
     */
    public function addRouter($for, $closure)
	{
		$this->routers[$for] = $closure;
	}

    /**
     * @param $slug
     * @return mixed
     * @throws \Exception
     */
    public function route($slug)
	{
		try
		{
			$url = $this->urlRepository->findBySlug($slug);

			if ($url)
			{
				$route_method = camel_case('route_' . $url->urlable_type);

				if (method_exists($this, $route_method))
				{
					return $this->$route_method($url);
				}
				else if(array_key_exists($url->urlable_type, $this->routers))
				{
					return $this->routers[$url->urlable_type]($url);
				}
				else
				{
					throw new \Exception('No method exists to route this type of URL: "' . $url->urlable_type . '"');
				}
			}			
		}
		catch(\CoandaCMS\Coanda\Urls\Exceptions\UrlNotFound $exception)
		{
			App::abort('404');
		}
	}

    /**
     * @param $url
     * @return mixed
     */
    public function routeRedirect($url)
	{
		$url = $this->urlRepository->findById($url->urlable_id);

		if ($url)
		{
			return Redirect::to(url($url->slug));
		}
	}

    /**
     * @param $wildcard_url
     * @return mixed
     */
    public function routeWildcard($wildcard_url)
	{
		$url = $this->urlRepository->findById($wildcard_url->urlable_id);

		if ($url)
		{
			return Redirect::to(url(str_replace($wildcard_url->slug, $url->slug, Request::path())));
		}
	}
}