<?php namespace CoandaCMS\Coanda;

interface CoandaModuleProvider {

	/**
	 * Boot method is called from the service provider, allows the module to do anything it may need to e.g. Add view namespace
	 */
	public function boot($coanda);

	/**
	 * Allows the module to add any admin routes
	 */
	public function adminRoutes();

	/**
	 * Allows the module to add an front end 'user' routes
	 */
	public function userRoutes();

	/**
	 * Allow the module to set its own bindings in the IoC
	 * @param  Illuminate\Foundation\Application $app
	 */
	public function bindings($app);

}