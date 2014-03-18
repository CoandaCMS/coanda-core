<?php namespace CoandaCMS\Coanda\Modules\Pages;

use Route, View;
use CoandaCMS\Coanda\CoandaModule;

class Pages implements CoandaModule {

	private $coanda;

	/**
	 * Main constructor
	 * @param CoandaCMSCoandaCoanda $coanda
	 */
	public function __construct(\CoandaCMS\Coanda\Coanda $coanda)
	{
		$this->coanda = $coanda;
	}

	/**
	 * Boot the module and add the view namespace
	 */
	public function boot()
	{
		View::addNamespace('coandapages', __DIR__ . '/views');
	}

	/**
	 * Add the admin routes for the module
	 */
	public function adminRoutes()
	{
		Route::controller('pages', 'CoandaCMS\Coanda\Modules\Pages\Controllers\Admin');
	}

	/**
	 * Add the front end routes for the module
	 */
	public function userRoutes()
	{
		Route::get('{slug}', function ($slug) {

			dd('Page: ' . $slug);

		});

	}
}