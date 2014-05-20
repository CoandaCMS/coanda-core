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
	| Available attributes, used by page types and layout blocks
	|--------------------------------------------------------------------------
	|
	*/
	'attribute_types' => [
		'CoandaCMS\Coanda\Core\Attributes\Types\Textline',
		'CoandaCMS\Coanda\Core\Attributes\Types\HTML',
		'CoandaCMS\Coanda\Core\Attributes\Types\Boolean',
		'CoandaCMS\Coanda\Core\Attributes\Types\Image',
	],

	/*
	|--------------------------------------------------------------------------
	| Modules
	|--------------------------------------------------------------------------
	|
	*/
	'enabled_modules' => [
	],


	/*
	|--------------------------------------------------------------------------
	| Page settings - available types, home page types and publish handlers
	|--------------------------------------------------------------------------
	|
	*/
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
	| Design & Layout settings
	|--------------------------------------------------------------------------
	|
	*/
	'theme_provider' => 'MySite\Theme\MySiteThemeProvider',

	'layouts' => [
		'MySite\Layouts\SingleColumn',
		'MySite\Layouts\TwoColumn',
	],

	'default_layout' => 'single-column',

	'layout_block_types' => [
		'MySite\LayoutBlocks\TextWithButton',
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


	/*
	|--------------------------------------------------------------------------
	| Search settings
	|--------------------------------------------------------------------------
	|
	*/
	'search_provider' => '',

);
