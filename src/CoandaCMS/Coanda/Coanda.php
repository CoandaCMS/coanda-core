<?php namespace CoandaCMS\Coanda;

use App, Route, Config, Redirect, Request, Session;

use CoandaCMS\Coanda\Exceptions\PageTypeNotFound;
use CoandaCMS\Coanda\Exceptions\PageAttributeTypeNotFound;

class Coanda {

	private $user;

	private $modules = [];

	private $page_types = [];

	public function getUser()
	{
		$this->user = App::make('CoandaCMS\Coanda\Users\Repositories\UserRepositoryInterface');
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

	public function availablePermissions()
	{
		return [
			'pages' => [
				'name' => 'Pages',
				'views' => [
					'create',
					'edit',
					'remove',
					'move'
				]
			],
			'users' => [
				'name' => 'Users',
				'views' => [
					'create',
					'edit',
					'remove'
				]
			]
		];
	}

	public function canAccess($permission, $permission_id = false)
	{
		return $this->user->hasAccessTo($permission, $permission_id);
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
				// Load the pages controller
				Route::controller('pages', 'CoandaCMS\Coanda\Controllers\Admin\PagesAdminController');

				// Load the users controller
				Route::controller('users', 'CoandaCMS\Coanda\Controllers\Admin\UsersAdminController');

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

		Route::match(array('GET', 'POST'), '{slug}', function($slug)
		{
			return Coanda::route($slug);

		})->where('slug', '[\/_\-\_A-Za-z]+');
	}

	/**
	 * Runs through all the bindings
	 * @param  Illuminate\Foundation\Application $app
	 */
	public function bindings($app)
	{
		$app->bind('CoandaCMS\Coanda\Users\Repositories\UserRepositoryInterface', 'CoandaCMS\Coanda\Users\Repositories\Eloquent\EloquentUserRepository');
		$app->bind('CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface', 'CoandaCMS\Coanda\Pages\Repositories\Eloquent\EloquentPageRepository');
		$app->bind('CoandaCMS\Coanda\Urls\Repositories\UrlRepositoryInterface', 'CoandaCMS\Coanda\Urls\Repositories\Eloquent\EloquentUrlRepository');

		// Let the module output any bindings
		foreach ($this->modules as $module)
		{
			$module->bindings($app);
		}
	}

	/**
	 * Loads all the avaibla page types from the config
	 * @return void
	 */
	public function loadPageTypes()
	{
		$page_types = Config::get('coanda::coanda.page_types');

		foreach ($page_types as $page_type)
		{
			if (class_exists($page_type))
			{
				$type = new $page_type($this);

				// TODO: validate the definition to ensure all the specified page attribute types are available.

				$this->page_types[$type->identifier] = $type;				
			}
		}
	}

	/**
	 * Returns the available page types
	 * @return Array
	 */
	public function availablePageTypes()
	{
		return $this->page_types;
	}

	/**
	 * Gets a specific page type by identifier
	 * @param  string $type The identifier of the page type
	 * @return CoandaCMS\Coanda\Pages\PageTypeInterface
	 */
	public function getPageType($type)
	{
		if (array_key_exists($type, $this->page_types))
		{
			return $this->page_types[$type];
		}

		throw new PageTypeNotFound;
	}

	/**
	 * Loads the attributes from the config file
	 * @return void
	 */
	public function loadPageAttributeTypes()
	{
		$page_attribute_types = Config::get('coanda::coanda.page_attribute_types');

		foreach ($page_attribute_types as $page_attribute_type)
		{
			$attribute_type = new $page_attribute_type;

			$this->page_attribute_types[$attribute_type->identifier] = $attribute_type;
		}
	}

	/**
	 * Get a specific attribute by identifier
	 * @param  string $type_identifier
	 * @return CoandaCMS\Coanda\Pages\PageAttributeTypeInterface
	 */
	public function getPageAttributeType($type_identifier)
	{
		if (array_key_exists($type_identifier, $this->page_attribute_types))
		{
			return $this->page_attribute_types[$type_identifier];
		}

		throw new PageAttributeTypeNotFound;
	}

	public function route($slug)
	{
		try
		{
			$urlRepository = App::make('CoandaCMS\Coanda\Urls\Repositories\UrlRepositoryInterface');
			$url = $urlRepository->findBySlug($slug);

			if ($url)
			{
				$route_method = camel_case('route_' . $url->urlable_type);

				if (method_exists($this, $route_method))
				{
					return $this->$route_method($url);
				}
			}			
		}
		catch(\CoandaCMS\Coanda\Urls\Exceptions\UrlNotFound $exception)
		{
			App::abort('404');
		}
	}

	public function routeUrl($url)
	{
		$urlRepository = App::make('CoandaCMS\Coanda\Urls\Repositories\UrlRepositoryInterface');
		$url = $urlRepository->findById($url->urlable_id);

		if ($url)
		{
			return Redirect::to(url($url->slug));
		}
	}

	public function routePage($url)
	{
		return 'Page #' . $url->urlable_id;
	}

	public function routeRedirect($url)
	{
		return 'Redirect #' . $url->urlable_id;
	}

}