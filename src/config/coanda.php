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
		'MySite\PageTypes\Page',
		'MySite\PageTypes\LandingPage',
	],

	'home_page_types' => [
		'MySite\PageTypes\HomePage'
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
	'theme_provider' => 'MySite\Theme\MySiteThemeProvider',

	'layouts' => [
		'MySite\Theme\Layouts\SingleColumn',
		'MySite\Theme\Layouts\TwoColumn',
	],

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
