<?php namespace CoandaCMS\Coanda\Pages;

use Route, App, Config, Coanda, View;

use CoandaCMS\Coanda\Exceptions\PageTypeNotFound;
use CoandaCMS\Coanda\Exceptions\PageAttributeTypeNotFound;
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

    private $home_page_types = [];

    /**
     * @var array
     */
    private $publish_handlers = [];

    private $theme;

    private $layouts_by_page_type = [];

    /**
     * @param \CoandaCMS\Coanda\Coanda $coanda
     */
    public function boot(\CoandaCMS\Coanda\Coanda $coanda)
	{
		$this->loadRouter($coanda);
		$this->loadPageTypes($coanda);
		$this->loadPublishHandlers($coanda);
		$this->loadPermissions($coanda);
		$this->loadLayouts($coanda);
	}

	private function loadLayouts($coanda)
	{
		$layouts = $coanda->layouts();

		foreach ($layouts as $layout)
		{
			foreach ($layout->pageTypes() as $page_type)
			{
				$this->layouts_by_page_type[$page_type][] = $layout;
			}			
		}
	}

	public function layoutsByPageType($page_type)
	{
		if (array_key_exists($page_type, $this->layouts_by_page_type))
		{
			return $this->layouts_by_page_type[$page_type];
		}

		return [];
	}

	private function loadRouter($coanda)
	{
		// Add the router to handle slug views
		$coanda->addRouter('page', function ($url) use ($coanda) {

			$pageRepository = App::make('CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface');

			try
			{
				$page = $pageRepository->findById($url->urlable_id);	

				return $this->renderPage($page);
			}
			catch(\CoandaCMS\Coanda\Exceptions\PageNotFound $exception)
			{
				App::abort('404');
			}

		});
	}

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
	 * Returns the available page types
	 * @return Array
	 */
	public function availablePageTypes($page = false)
	{
		$user_permissions = \Coanda::currentUserPermissions();

		if (isset($user_permissions['everything']) && in_array('*', $user_permissions['everything']))
		{
			return $this->page_types;
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
					$page_types = [];

					foreach ($user_permissions['pages']['page_types'] as $permissioned_page_type)
					{
						if (isset($this->page_types[$permissioned_page_type]))
						{
							$page_types[$permissioned_page_type] = $this->page_types[$permissioned_page_type];
						}
					}

					return $page_types;
				}
				else
				{
					return $this->page_types;
				}
			}
		}

		return [];
	}

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

    public function getHomePageType	($type)
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

	public function checkAccess($permission, $parameters, $user_permissions = [])
	{
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

	private function getTheme()
	{
		if (!$this->theme)
		{
			$this->theme = Coanda::theme();
		}

		return $this->theme;
	}

	private function templateDirectory()
	{
		$directory = '';

		$theme = $this->getTheme();

		if (method_exists($theme, 'themeDirectory'))
		{
			$directory = $theme->themeDirectory() . '.';
		}

		return $directory;
	}

	private function preRender($render_data)
	{
		$theme = $this->getTheme();

		if (method_exists($theme, 'preRender'))
		{
			$render_data = $theme->preRender($render_data);
		}

		return $render_data;
	}

	private function getLayout($page)
	{
		if ($page->currentVersion()->layout)
		{
			$layout = Coanda::layoutByIdentifier($page->currentVersion()->layout);

			if ($layout)
			{
				return $layout->template();
			}
		}

		$theme = $this->getTheme();

		if (method_exists($theme, 'defaultLayoutTemplate'))
		{
			return $theme->defaultLayoutTemplate();
		}

		// A sensible default - an exception will be thrown if it doesn't exist anyway
		return 'layouts.default';
	}

	public function renderHome()
	{
		$pageRepository = App::make('CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface');
		
		$home_page = $pageRepository->getHomePage();

		if ($home_page)
		{
			return $this->renderPage($home_page);
		}

		throw new \Exception('Home page not created yet!');
	}

	private function renderPage($page)
	{
		if ($page->is_trashed)
		{
			App::abort('404');
		}

		if (!$page->is_visible)
		{
			App::abort('404');
		}

		$data = [
			'name' => $page->present()->name,
			'type' => $page->type,
			'meta' => $this->buildMeta($page),
			'attributes' => $this->buildAttributes($page),
			'page' => $page,
			'template' => $this->templateDirectory() . 'pagetypes.' . $page->type
		];

		// Let the theme provider have a look at (and potentially change) the render data...
		$data = $this->preRender($data);

		// Make the view and pass all the render data to it...
		$rendered_page = View::make($data['template'], $data);

		// Get the layout template...
		$layout = $this->getLayout($page);

		// $blocks = Coanda::module('blocks')->get($layout_identifier, $page->id);

		// Give the layout the rendered page and the data, and it can work some magic to give us back a complete page...
		$layout_data = [
			'content' => $rendered_page,
			'data' => $data,
			'layout' => $layout,
			'module' => 'pages',
			'module_identifier' => $page->id
		];

		return View::make($layout, $layout_data);
	}

	private function buildAttributes($page)
	{
		$attributes = [];

		foreach ($page->attributes as $attribute)
		{
			$attributes[$attribute->identifier] = [
				'identifier' => $attribute->identifier,
				'type' => $attribute->type,
				'order' => $attribute->order,
				'content' => $attribute->typeData(),
				'attribute_data' => $attribute->attribute_data,
			];
		}

		return $attributes;
	}

	private function buildMeta($page)
	{
		return [
			'title' => 'XXXXX',
			'description' => 'XXXXX'
		];
	}
}