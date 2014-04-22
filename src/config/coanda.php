<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Admin path (e.g. yoursite.com/admin)
	|--------------------------------------------------------------------------
	|
	*/
	'admin_path' => 'admin',

	'enabled_modules' => [

		],

	'page_types' => [
		'MySite\Coanda\PageTypes\Page',
		// 'MySite\Coanda\PageTypes\NewsArticle'
	],
	
	'page_attribute_types' => [
		'CoandaCMS\Coanda\Pages\PageAttributeTypes\Textline',
		'CoandaCMS\Coanda\Pages\PageAttributeTypes\HTML',
	],

	'publish_handlers' => [
		'CoandaCMS\Coanda\Pages\PublishHandlers\Delayed',	
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
