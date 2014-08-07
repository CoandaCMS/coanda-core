<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Admin path (e.g. yoursite.com/admin)
	|--------------------------------------------------------------------------
	|
	*/
	'admin_path' => 'admin',

	'site_name' => 'Coanda CMS',

	'site_admin_email' => 'admin@yoursite.com',

	/*
	|--------------------------------------------------------------------------
	| Some default settings
	|--------------------------------------------------------------------------
	|
	*/
	'datetime_format' => 'd/m/Y H:i',

	'date_format' => 'd/m/Y',

	/*
	|--------------------------------------------------------------------------
	| Available attributes, used by page types and layout blocks
	|--------------------------------------------------------------------------
	|
	*/
	'attribute_types' => [
		'CoandaCMS\Coanda\Core\Attributes\Types\Textline',
		'CoandaCMS\Coanda\Core\Attributes\Types\HTML',
		'CoandaCMS\Coanda\Core\Attributes\Types\Boolean',
		'CoandaCMS\Coanda\Core\Attributes\Types\Image',
		'CoandaCMS\Coanda\Core\Attributes\Types\Date',
		'CoandaCMS\Coanda\Core\Attributes\Types\Dropdown',
		'CoandaCMS\Coanda\Core\Attributes\Types\Integer',
		'CoandaCMS\Coanda\Core\Attributes\Types\Checkboxes',
		'CoandaCMS\Coanda\Core\Attributes\Types\DateTime',
	],

	/*
	|--------------------------------------------------------------------------
	| Modules
	|--------------------------------------------------------------------------
	|
	*/
	'enabled_modules' => [
		// 'CoandaCMS\CoandaWebForms\WebFormsModuleProvider',
		// 'MySite\Modules\Example\ExampleModuleProvider',
	],


	/*
	|--------------------------------------------------------------------------
	| Page settings - available types, home page types and publish handlers
	|--------------------------------------------------------------------------
	|
	*/
	'page_types' => [
		// 'MySite\PageTypes\Page',
		// 'CoandaCMS\CoandaWebForms\PageTypes\Form',
	],

	'home_page_types' => [
		// 'MySite\PageTypes\HomePage'
	],

	'publish_handlers' => [
		'CoandaCMS\Coanda\Pages\PublishHandlers\Delayed',
	],

	/*
	|--------------------------------------------------------------------------
	| Design & Layout settings
	|--------------------------------------------------------------------------
	|
	*/
	'layouts' => [
		// 'MySite\Layouts\DefaultLayout',
	],

	'default_layout' => 'default',

	/*
	|--------------------------------------------------------------------------
	| Media settings
	|--------------------------------------------------------------------------
	|
	*/
	'uploads_directory' => 'uploads',

	'image_cache_directory' => 'i',

	'file_cache_directory' => 'f',


	/*
	|--------------------------------------------------------------------------
	| Search settings
	|--------------------------------------------------------------------------
	|
	*/
	'search_provider' => 'CoandaCMS\Coanda\Search\Basic\CoandaBasicSearchProvider',

);
