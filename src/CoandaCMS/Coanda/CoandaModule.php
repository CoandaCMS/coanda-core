<?php namespace CoandaCMS\Coanda;

interface CoandaModule {

	/**
	 * Boot method is called from the service provider, allows the module to do anything it may need to e.g. Add view namespace
	 */
	public function boot();

	/**
	 * Allows the module to add any admin routes
	 */
	public function adminRoutes();

	/**
	 * Allows the module to add an front end 'user' routes
	 */
	public function userRoutes();

}
