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
	]
	
);
