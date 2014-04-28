<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Admin path (e.g. yoursite.com/admin)
	|--------------------------------------------------------------------------
	|
	*/
	'admin_path' => 'admin',

	/*
	|--------------------------------------------------------------------------
	| Available attributes, used by page types and templates
	|--------------------------------------------------------------------------
	|
	*/
	'attribute_types' => [
		'CoandaCMS\Coanda\Core\Attributes\Types\Textline',
		'CoandaCMS\Coanda\Core\Attributes\Types\HTML',
	],

	'enabled_modules' => [

		],

	'page_types' => [
		'MySite\PageTypes\LandingPage',
		'MySite\PageTypes\Page',
	],

	'publish_handlers' => [
		'CoandaCMS\Coanda\Pages\PublishHandlers\Delayed',
	],

	/*
	|--------------------------------------------------------------------------
	| Theme settings (Theme provider class)
	|--------------------------------------------------------------------------
	|
	*/
	'theme_provider' => 'CoandaCMS\Coanda\Themes\Simple\SimpleThemeProvider',

	/*
	|--------------------------------------------------------------------------
	| Media settings
	|--------------------------------------------------------------------------
	|
	*/
	'uploads_directory' => 'uploads',

	'image_cache_directory' => 'i',

	'file_cache_directory' => 'f',

);
