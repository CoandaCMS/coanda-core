<?php namespace CoandaCMS\Coanda\Pages;

use Route, App, Config;

use CoandaCMS\Coanda\Exceptions\PageTypeNotFound;
use CoandaCMS\Coanda\Exceptions\PageAttributeTypeNotFound;

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
    private $page_attribute_types = [];

    /**
     * @var array
     */
    private $publish_handlers = [];

    /**
     * @param \CoandaCMS\Coanda\Coanda $coanda
     */
    public function boot(\CoandaCMS\Coanda\Coanda $coanda)
	{
		// Add the permissions
		$views = [
			'create',
			'edit',
			'remove',
			'move'
		];

		$coanda->addModulePermissions('pages', 'Pages', $views);

		// Ad the router to handle slug views
		$coanda->addRouter('page', function ($url) {

			$pageRepository = App::make('CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface');

			try
			{
				$page = $pageRepository->findById($url->urlable_id);	

				if ($page->is_trashed)
				{
					App::abort('404');
				}

				if (!$page->is_visible)
				{
					App::abort('404');
				}

				return 'View page: ' . $page->present()->name;
			}
			catch(\CoandaCMS\Coanda\Exceptions\PageNotFound $exception)
			{
				App::abort('404');
			}

		});

		// load the page types
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

		// Load the attributes
		$page_attribute_types = Config::get('coanda::coanda.page_attribute_types');

		foreach ($page_attribute_types as $page_attribute_type)
		{
			$attribute_type = new $page_attribute_type;

			$this->page_attribute_types[$attribute_type->identifier] = $attribute_type;
		}

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
	 * Returns the available page types
	 * @return Array
	 */
	public function availablePageTypes()
	{
		return $this->page_types;
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
     * @param $type_identifier
     * @return mixed
     * @throws \CoandaCMS\Coanda\Exceptions\PageAttributeTypeNotFound
     */
    public function getPageAttributeType($type_identifier)
	{
		if (array_key_exists($type_identifier, $this->page_attribute_types))
		{
			return $this->page_attribute_types[$type_identifier];
		}

		throw new PageAttributeTypeNotFound;
	}

	public function publishHandlers()
	{
		return $this->publish_handlers;
	}

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
}