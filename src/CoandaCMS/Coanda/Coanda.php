<?php namespace CoandaCMS\Coanda;

use App, Route, Redirect, Request, Session, View, Config;

use CoandaCMS\Coanda\Core\Attributes\Exceptions\AttributeTypeNotFound;
use CoandaCMS\Coanda\Exceptions\PermissionDenied;
use CoandaCMS\Coanda\Exceptions\ModuleNotFound;
use CoandaCMS\Coanda\Urls\Exceptions\UrlNotFound;

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
     * @var array
     */
    private $admin_menu = [];
    /**
     * @var
     */
    private $urlRepository;

    /**
     * @var array
     */
    private $routers = [];

    /**
     * @var array
     */
    private $attribute_types = [];

    /**
     * @var
     */
    private $search_provider;

    /**
     * @var
     */
    private $config;

    /**
     * @param $app
     */
    public function __construct($app)
    {
		$this->config = $app['config'];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    public function boot($app)
	{
		$this->urlRepository = $app->make('CoandaCMS\Coanda\Urls\Repositories\UrlRepositoryInterface');
		$this->user = $app->make('CoandaCMS\Coanda\Users\UserManager');
		
		$this->loadAttributes();
		$this->loadSearchProvider();
		$this->loadHistoryListener();
	}

    /**
     *
     */
    private function loadAttributes()
	{
		// Load the attributes
		$attribute_types = $this->config->get('coanda::coanda.attribute_types');

		foreach ($attribute_types as $attribute_type_class)
		{
			if (class_exists($attribute_type_class))
			{
				$attribute_type = new $attribute_type_class;

				$this->attribute_types[$attribute_type->identifier()] = $attribute_type;				
			}
		}
	}

    /**
     * @param $type_identifier
     * @return mixed
     * @throws Core\Attributes\Exceptions\AttributeTypeNotFound
     */
    public function getAttributeType($type_identifier)
	{
		if (array_key_exists($type_identifier, $this->attribute_types))
		{
			return $this->attribute_types[$type_identifier];
		}

		throw new AttributeTypeNotFound('Attribute type: ' . $type_identifier . ' could not be found.');
	}

	/**
	 * Takes the path and prepends the current admin_path from the config
	 * @param  string $path
	 * @return string
	 */
	public function adminUrl($path)
	{
		return url($this->config->get('coanda::coanda.admin_path') . '/' . $path);
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
     * @param $module
     * @return bool
     */
    public function canViewModule($module)
	{
		$user_permissions = $this->currentUserPermissions();

		if (isset($user_permissions['everything']) && in_array('*', $user_permissions['everything']))
		{
			return true;
		}

		// Do we have some permissions for this module? If not, then they can not pass!
		return isset($user_permissions[$module]);
	}

    /**
     * @param $module
     * @param $permission
     * @param array $parameters
     * @return bool
     */
    public function canView($module, $permission = '', $parameters = [])
	{
		try
		{
			$this->checkAccess($module, $permission, $parameters);

			return true;
		}
		catch (PermissionDenied $exception)
		{
			return false;
		}
	}

    /**
     * @return mixed
     */
    public function currentUserPermissions()
	{
		return $this->user->currentUserPermissions();
	}

    /**
     * @param $module
     * @param $permission
     * @param array $parameters
     * @throws Exceptions\PermissionDenied
     */
    public function checkAccess($module, $permission, $parameters = [])
	{
		$user_permissions = $this->currentUserPermissions();

		if (isset($user_permissions['everything']) && in_array('*', $user_permissions['everything']))
		{
			return;
		}

		// Do we have some permissions for this module? If not, then they can not pass!
		if (!isset($user_permissions[$module]))
		{
			throw new PermissionDenied('No access to module: ' . $module);
		}

		$this->module($module)->checkAccess($permission, $parameters, $user_permissions[$module]);
	}

	/**
	 * Get all the enabled modules from the config and boots them up. Also adds to modules array for future use.
	 */
	public function loadModules()
	{
		$core_modules = [
			'CoandaCMS\Coanda\Pages\PagesModuleProvider',
			'CoandaCMS\Coanda\Media\MediaModuleProvider',
			'CoandaCMS\Coanda\Users\UsersModuleProvider',
			'CoandaCMS\Coanda\Layout\LayoutModuleProvider',
			'CoandaCMS\Coanda\Urls\UrlModuleProvider'
		];

		$enabled_modules = $this->config->get('coanda::coanda.enabled_modules');

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
		Route::group(array('prefix' => $this->config->get('coanda::coanda.admin_path')), function()
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

			Route::match(['GET', 'POST'], 'search', function () {
				
				// Pass this route to the search provider, it can do whatever it likes!
				return Coanda::search()->handleSearch();

			});

			Route::match(['GET', 'POST'], '{slug}', function ($slug)
			{
				return Coanda::route($slug);

			})->where('slug', '[\.\/_\-\_A-Za-z0-9]+');

			Route::match(['GET', 'POST'], '/', function ()
			{
				return Coanda::routeHome();

			});

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

		$search_provider = $this->config->get('coanda::coanda.search_provider');

		if (class_exists($search_provider))
		{
			$app->bind('CoandaCMS\Coanda\Search\CoandaSearchProvider', $search_provider);
		}
		else
		{
			$app->bind('CoandaCMS\Coanda\Search\CoandaSearchProvider', 'CoandaCMS\Coanda\Search\Basic\CoandaBasicSearchProvider');
		}

		// Let the module output any bindings
		foreach ($this->modules as $module)
		{
			$module->bindings($app);
		}
	}

    /**
     * @param $module
     * @return mixed
     * @throws Exceptions\ModuleNotFound
     */
    public function module($module)
	{
		if (array_key_exists($module, $this->modules))
		{
			return $this->modules[$module];	
		}

		throw new ModuleNotFound('Module ' . $module . ' does not exist or has not been loaded.');		
	}

    /**
     * @param $url
     * @param $name
     */
    public function addMenuItem($url, $name)
	{
		$this->admin_menu[] = ['url' => $url, 'name' => $name];
	}

    /**
     * @return array
     */
    public function adminMenu()
	{
		foreach ($this->modules as $module)
		{
			$module->buildAdminMenu($this);
		}

		return $this->admin_menu;
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
     * @return mixed
     */
    public function routeHome()
	{
		return $this->module('pages')->renderHome();
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
				$route_method = camel_case('route_' . $url->type);

				if (method_exists($this, $route_method))
				{
					return $this->$route_method($url);
				}
				elseif(array_key_exists($url->type, $this->routers))
				{
					return $this->routers[$url->type]($url);
				}
				else
				{
					throw new \Exception('No method exists to route this type of URL: "' . $url->type . '"');
				}
			}
		}
		catch(UrlNotFound $exception)
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
		$url = $this->urlRepository->findById($url->type_id);

		if ($url)
		{
			return Redirect::to(url($url->slug), 301);
		}
	}

    /**
     * @param $wildcard_url
     * @return mixed
     */
    public function routeWildcard($wildcard_url)
	{
		$url = $this->urlRepository->findById($wildcard_url->type_id);

		if ($url)
		{
			$wildcard_slug = '/' . $wildcard_url->slug . '/';
			$new_slug = '/' . $url->slug . '/';

			$requested_url = str_replace($wildcard_slug, $new_slug, '/' . Request::path() . '/');

			return Redirect::to(url($requested_url));
		}
	}

    /**
     *
     */
    private function loadSearchProvider()
	{
		$this->search_provider = App::make('CoandaCMS\Coanda\Search\CoandaSearchProvider');
	}

    /**
     *
     */
    private function loadHistoryListener()
	{
		\Event::listen('history.log', function($module, $module_identifier, $user_id, $action, $data = '')
		{
			$history_repository = \App::make('CoandaCMS\Coanda\History\Repositories\HistoryRepositoryInterface');
			$history_repository->add($module, $module_identifier, $user_id, $action, $data);

		});
	}

    /**
     * @param $module
     * @param $module_identifier
     * @param $limit
     * @return mixed
     */
    public function getHistory($module, $module_identifier, $limit)
	{
		$history_repository = \App::make('CoandaCMS\Coanda\History\Repositories\HistoryRepositoryInterface');
		
		return $history_repository->get($module, $module_identifier, $limit);
	}

    /**
     * @return mixed
     */
    public function search()
	{
		return $this->search_provider;
	}

	public function handleAdminSearch($query)
	{
		// Ideally this would allow other modules to add results and some how collate them, but for the moment lets just pass it to the pages module
		return $this->modules['pages']->adminSearch($query);
	}

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
	{
		if (array_key_exists($name, $this->modules))
		{
			return $this->modules[$name];	
		}

		throw new \InvalidArgumentException('Method "' . $name . '" not found.');
	}

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
	{
		$method = 'get_' . $name . '_attribute';

		if (method_exists($this, camel_case($method)))
		{
			return $this->$method;
		}
	}

	/**
     * @return string
     */
	public function siteName()
	{
		return Config::get('coanda::coanda.site_name');
	}

	/**
     * @return string
     */
    public function adminLogo()
    {
    	return Config::get('coanda::coanda.admin_logo');
    }
}