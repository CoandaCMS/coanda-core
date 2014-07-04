<?php namespace CoandaCMS\Coanda\Pages;

use Route, App, Config, Coanda, View, Cache;

use CoandaCMS\Coanda\Pages\Exceptions\PageNotFound;
use CoandaCMS\Coanda\Pages\Exceptions\PageTypeNotFound;
use CoandaCMS\Coanda\Pages\Exceptions\PageAttributeTypeNotFound;
use CoandaCMS\Coanda\Exceptions\PermissionDenied;

/**
 * Class PagesModuleProvider
 * @package CoandaCMS\Coanda\Pages
 */
class PagesModuleProvider implements \CoandaCMS\Coanda\CoandaModuleProvider {

    /**
     * @var string
     */
    public $name = 'pages';

    /**
     * @var array
     */
    private $page_types = [];

    /**
     * @var array
     */
    private $home_page_types = [];

    /**
     * @var array
     */
    private $publish_handlers = [];

    /**
     * @param CoandaCMS\Coanda\Coanda $coanda
     */
    public function boot(\CoandaCMS\Coanda\Coanda $coanda)
	{
		$this->loadRouter($coanda);
		$this->loadPageTypes($coanda);
		$this->loadPublishHandlers($coanda);
		$this->loadPermissions($coanda);
	}

    /**
     * @param $coanda
     */
    private function loadRouter($coanda)
	{
		// Add the router to handle slug views
		$coanda->addRouter('pagelocation', function ($url) use ($coanda) {

			$cache_key = $this->generateCacheKey($url->type_id);

			if (Cache::has($cache_key))
			{
				return Cache::get($cache_key);
			}

			try
			{
				$location = $this->getPageRepository()->locationById($url->type_id);	

				return $this->renderPage($location->page, $location);
			}
			catch(PageNotFound $exception)
			{
				App::abort('404');
			}

		});
	}

    /**
     * @param $coanda
     */
    private function loadPageTypes($coanda)
	{
		// load the page types
		$page_types = Config::get('coanda::coanda.page_types');

		foreach ($page_types as $page_type)
		{
			if (class_exists($page_type))
			{
				$type = new $page_type($this);

				$this->page_types[$type->identifier()] = $type;
			}
		}

		// load the home page types
		$home_page_types = Config::get('coanda::coanda.home_page_types');

		foreach ($home_page_types as $home_page_type)
		{
			if (class_exists($home_page_type))
			{
				$type = new $home_page_type($this);

				$this->home_page_types[$type->identifier()] = $type;
			}
		}
	}

    /**
     * @param $coanda
     */
    private function loadPublishHandlers($coanda)
	{
		// Load the publish handlers
		$core_publish_handlers = [
			'CoandaCMS\Coanda\Pages\PublishHandlers\Immediate' // Make sure this one is always added (TODO: Consider removing this as 'core')
		];

		$enabled_publish_handlers = Config::get('coanda::coanda.publish_handlers');

		$publish_handlers = array_merge($core_publish_handlers, $enabled_publish_handlers);

		foreach ($publish_handlers as $publish_handler)
		{
			if (class_exists($publish_handler))
			{
				$handler = new $publish_handler;

				$this->publish_handlers[$handler->identifier] = $handler;
			}
		}
	}

    /**
     * @param $coanda
     */
    private function loadPermissions($coanda)
	{
		$publish_handler_options = [];

		foreach ($this->publish_handlers as $publish_handler)
		{
			$publish_handler_options[$publish_handler->identifier] = $publish_handler->name;
		}

		$page_type_options = [];

		foreach ($this->page_types as $page_type)
		{
			$page_type_options[$page_type->identifier()] = $page_type->name();
		}

		// Add the permissions
		$permissions = [
			'create' => [
				'name' => 'Create',
				'options' => []
			],
			'edit' => [
				'name' => 'Edit',
				'options' => []
			],
			'remove' => [
				'name' => 'Remove',
				'options' => []
			],
			'publish_options' => [
				'name' => 'Publish options',
				'options' => $publish_handler_options
			],
			'page_types' => [
				'name' => 'Available page types',
				'options' => $page_type_options
			]
		];

		$coanda->addModulePermissions('pages', 'Pages', $permissions);		
	}

    /**
     * @param bool $page
     * @return array
     */
    public function availablePageTypes($page = false)
	{
		$page_types = $this->page_types;

		if ($page)
		{
			$allowed_page_types = $page->pageType()->allowedSubPageTypes();

			if (count($allowed_page_types) > 0)
			{
				$page_types = [];

				foreach ($allowed_page_types as $allowed_page_type)
				{
					if (isset($this->page_types[$allowed_page_type]))
					{
						$page_types[$allowed_page_type] = $this->page_types[$allowed_page_type];
					}
				}
			}
		}

		$user_permissions = \Coanda::currentUserPermissions();

		if (isset($user_permissions['everything']) && in_array('*', $user_permissions['everything']))
		{
			return $page_types;
		}

		if (isset($user_permissions['pages']))
		{
			if (in_array('*', $user_permissions['pages']))
			{
				return $this->page_types;
			}

			if (in_array('create', $user_permissions['pages']))
			{
				if (isset($user_permissions['pages']['page_types']))
				{
					$new_page_types = [];

					foreach ($user_permissions['pages']['page_types'] as $permissioned_page_type)
					{
						if (isset($page_types[$permissioned_page_type]))
						{
							$new_page_types[$permissioned_page_type] = $page_types[$permissioned_page_type];
						}
					}

					return $new_page_types;
				}
				else
				{
					return $page_types;
				}
			}
		}

		return [];
	}

	public function getPageRepository()
	{
		return App::make('CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface');
	}

    /**
     * @param bool $page
     * @return array
     */
    public function availableHomePageTypes($page = false)
	{
		return $this->home_page_types;
	}

    /**
     * @param $type
     * @return mixed
     * @throws \CoandaCMS\Coanda\Exceptions\PageTypeNotFound
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
     * @param $type
     * @return mixed
     * @throws \CoandaCMS\Coanda\Exceptions\PageTypeNotFound
     */
    public function getHomePageType($type)
	{
		if (array_key_exists($type, $this->home_page_types))
		{
			return $this->home_page_types[$type];
		}

		throw new PageTypeNotFound;
	}

    /**
     * @return array
     */
    public function publishHandlers()
	{
		return $this->publish_handlers;
	}

    /**
     * @param $identifier
     * @return mixed
     */
    public function getPublishHandler($identifier)
	{
		return $this->publish_handlers[$identifier];
	}

    /**
     *
     */
    public function adminRoutes()
	{
		// Load the pages controller
		Route::controller('pages', 'CoandaCMS\Coanda\Controllers\Admin\PagesAdminController');
	}

    /**
     *
     */
    public function userRoutes()
	{
		// Front end routes for Pages (preview etc)
		Route::controller('pages', 'CoandaCMS\Coanda\Controllers\PagesController');
	}

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return mixed
     */
    public function bindings(\Illuminate\Foundation\Application $app)
	{
		$app->bind('CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface', 'CoandaCMS\Coanda\Pages\Repositories\Eloquent\EloquentPageRepository');
	}

    /**
     * @param $permission
     * @param $parameters
     * @param array $user_permissions
     * @throws \CoandaCMS\Coanda\Exceptions\PermissionDenied
     */
    public function checkAccess($permission, $parameters, $user_permissions = [])
	{
		// $user_permissions['paths'] = [
		// 	'/0/30/',
		// 	'/0/42/',
		// ];

		// $pass_path_check = true;

		// if (isset($parameters['page_location_id']))
		// {
		// 	if (isset($user_permissions['paths']) && count($user_permissions['paths']) > 0)
		// 	{
		// 		$pass_path_check = false;

		// 		foreach ($user_permissions['paths'] as $allowed_path)
		// 		{
		// 			$path_parts = explode('/', $allowed_path);

		// 			if (count($path_parts) > 0)
		// 			{
		// 				foreach ($path_parts as $path_part)
		// 				{
		// 					if ($path_part == $parameters['page_location_id'])
		// 					{
		// 						$pass_path_check = true;
		// 					}
		// 				}
		// 			}
		// 		}
		// 	}
		// }

		// if (!$pass_path_check)
		// {
		// 	throw new PermissionDenied('Your are not allowed access to this location');
		// }

		if (in_array('*', $user_permissions))
		{
			return;
		}

		// If we anything in pages, we allow view
		if ($permission == 'view')
		{
			return;
		}

		// If we have create, but not edit, then add edit
		if (in_array('create', $user_permissions) && !in_array('edit', $user_permissions))
		{
			$user_permissions[] = 'edit';
		}

		// If we don't have this permission in the array, the throw right away
		if (!in_array($permission, $user_permissions))
		{
			throw new PermissionDenied('Access denied by pages module: ' . $permission);
		}

		// Page type check
		if ($permission == 'create' || $permission == 'edit')
		{
			if (isset($user_permissions['page_types']) && count($user_permissions['page_types']) > 0)
			{
				if (!in_array($parameters['page_type'], $user_permissions['page_types']))
				{
					throw new PermissionDenied('Access denied by pages module for page type: ' . $parameters['page_type']);
				}
			}
		}
	}

    /**
     * @param $coanda
     */
    public function buildAdminMenu($coanda)
	{
		if ($coanda->canViewModule('pages'))
		{
			$coanda->addMenuItem('pages', 'Pages');	
		}
	}

    /**
     * @param $page
     * @return mixed
     */
    private function getLayout($version)
	{
		if ($version->layout_identifier)
		{
			$layout = Coanda::module('layout')->layoutByIdentifier($version->layout_identifier);

			if ($layout)
			{
				return $layout;
			}
		}

		return Coanda::module('layout')->defaultLayout();
	}

    /**
     * @return mixed
     * @throws \Exception
     */
    public function renderHome()
	{
		// if (Cache::has('home_page'))
		// {
		// 	return Cache::get('home_page');
		// }

		$home_page = $this->getPageRepository()->getHomePage();

		if ($home_page)
		{
			$content = $this->renderPage($home_page);

			// Cache::put('home_page', $content, 2);

			return $content;
		}

		throw new \Exception('Home page not created yet!');
	}

	private function renderAttributes($page, $pagelocation)
    {
		$attributes = new \stdClass;

		foreach ($page->attributes as $attribute)
		{
			$attributes->{$attribute->identifier} = $attribute->render($page, $pagelocation);
		}

		// Add any attributes which are on the definition, but not in the object..
		$pageType = $page->pageType();
		$attribute_definition_list = $pageType->attributes();

		foreach ($attribute_definition_list as $attribute_definition_identfier => $attribute_definition)
		{
			if (!property_exists($attributes, $attribute_definition_identfier))
			{
				$attributes->{$attribute_definition_identfier} = '';
			}
		}

        return $attributes;
	}

    /**
     * @param $page
     * @param bool $pagelocation
     * @return mixed
     */
    private function renderPage($page, $pagelocation = false)
	{
		if ($page->is_trashed)
		{
			App::abort('404');
		}

		if (!$page->is_visible)
		{
			App::abort('404');
		}

		if ($page->is_hidden)
		{
			App::abort('404');
		}

		$meta = $this->buildMeta($page);

		$data = [
			'page_id' => $page->id,
			'version' => $page->current_version,
			'location_id' => ($pagelocation ? $pagelocation->id : false),
			'page' => $page,
			'attributes' => $this->renderAttributes($page, $pagelocation),
			'meta' => $meta,
			'slug' => ($pagelocation ? $pagelocation->slug : ''),
		];

		// Does the page type want to do anything before we carry on with the rendering?
		// e.g. Redirect, set some additional data variables
		$data = $page->pageType()->preRender($data);

		// Lets check if we got a redirect request back...
		if (is_object($data) && get_class($data) == 'Illuminate\Http\RedirectResponse')
		{
			return $data;
		}

		// The page type works out the template to be used. The default is pretty simple, but more complex things could be done if required.
		$template = $page->pageType()->template($page->currentVersion(), $data);

		// Make the view and pass all the render data to it...
		$rendered_page = View::make($template, $data);

		// Get the layout template...
		$layout = $this->getLayout($page->currentVersion());

		// Give the layout the rendered page and the data, and it can work some magic to give us back a complete page...
		$layout_data = [
			'layout' => $layout,
			'content' => $rendered_page,
			'meta' => $meta,
			'page_data' => $data,
			'breadcrumb' => ($pagelocation ? $pagelocation->breadcrumb() : []),
			'module' => 'pages',
			'module_identifier' => $page->id . ':' . $page->current_version
		];

		$content = View::make($layout->template(), $layout_data)->render();

		if ($page->pageType()->canStaticCache() && $pagelocation)
		{
			$cache_key = $this->generateCacheKey($pagelocation->id);

			// $content = str_replace('</body>', '<!-- cached: ' . date('r', time()) . ' --></body>', $content);
			Cache::put($cache_key, $content, 10);
		}

		return $content;
	}

	private function generateCacheKey($location_id)
	{
		$cache_key = 'location-' . $location_id;

		$all_input = \Input::all();

		// If we are viewing ?page=1 - then this is cached the same as without it...
		if (isset($all_input['page']) && $all_input['page'] == 1)
		{
			unset($all_input['page']);
		}

		$cache_key .= '-' . md5(var_export($all_input, true));

		return $cache_key;
	}

    /**
     * @param $page
     * @return array
     */
    private function buildMeta($page)
	{
		$meta_title = $page->currentVersion()->meta_page_title;

		return [
			'title' => $meta_title !== '' ? $meta_title : $page->present()->name,
			'description' => $page->currentVersion()->meta_description
		];
	}

    /**
     * @param $version
     * @return mixed
     */
    public function renderVersion($version)
	{
		$page = $version->page;
		$pagelocation = false;

		$meta_title = $version->meta_page_title;

		$meta = [
			'title' => $meta_title !== '' ? $meta_title : $version->present()->name,
			'description' => $version->meta_description
		];

		$attributes = new \stdClass;

		foreach ($version->attributes as $attribute)
		{
			$attributes->{$attribute->identifier} = $attribute->render($page, $pagelocation);
		}

		$first_location = $version->slugs()->first();
		$temp_location = $first_location->tempLocation();

		// Add 'dummy' versions of these to simulate viewing a location
		$location_id = $first_location->page_location_id;

		$breadcrumb = $temp_location->breadcrumb();

		// We need to take the last item off and replace it with the version name...
		array_pop($breadcrumb);

		$breadcrumb[] = [
			'url' => false,
			'identifier' => '',
			'name' => $version->present()->name
		];

		$data = [
			'page' => $version->page,
			'location_id' => $temp_location->id,
			'meta' => $meta,
			'attributes' => $attributes
		];

		// Make the view and pass all the render data to it...
		$rendered_version = View::make($page->pageType()->template($version, $data), $data);

		// Get the layout template...
		$layout = $this->getLayout($version);

		// Give the layout the rendered page and the data, and it can work some magic to give us back a complete page...
		$layout_data = [
			'layout' => $layout,
			
			'content' => $rendered_version,
			'meta' => $meta,
			
			'page_data' => $data,
			'breadcrumb' => $breadcrumb,
			
			'module' => 'pages',
			'module_identifier' => $page->id . ':' . $version->version
		];

		$content = View::make($layout->template(), $layout_data)->render();

		return $content;
	}

    /**
     * @param $page_id
     * @return bool
     */
    public function getPage($page_id)
	{
		try
		{
			return $this->getPageRepository()->findById($page_id);
		}
		catch (PageNotFound $exception)
		{
			return false;
		}
	}

    /**
     * @param $location_id
     */
    public function getLocation($location_id)
	{
		try
		{
			return $this->getPageRepository()->locationById($location_id);
		}
		catch (PageNotFound $exception)
		{
			return false;
		}
	}

    /**
     * @param $remote_id
     */
    public function getLocationByRemoteId($remote_id)
	{
		try
		{
			return $this->getPageRepository()->getLocationByRemoteId($remote_id);
		}
		catch (PageNotFound $exception)
		{
			return false;
		}
	}

	private function getQueryBuilder()
	{
		return new \CoandaCMS\Coanda\Pages\PageQuery($this->getPageRepository());
	}

	public function query()
	{
		return $this->getQueryBuilder();
	}
}