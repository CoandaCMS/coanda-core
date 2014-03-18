<?php namespace CoandaCMS\Coanda;

use App, Route, Config;

class Coanda {

	/**
	 * Our user implementation
	 * @var [type]
	 */
	private $user;

	/**
	 * @param CoandaCMSCoandaAuthenticationUser $user [description]
	 */
	public function __construct(\CoandaCMS\Coanda\Authentication\User $user)
	{
		$this->user = $user;
	}

	/**
	 * Takes the path and prepends the current admin_path from the config
	 * @param  string $path
	 * @return string
	 */
	public static function adminUrl($path)
	{
		return url(Config::get('coanda::coanda.admin_path') . '/' . $path);
	}

	/**
	 * Checks to see if we have a user
	 * @return boolean
	 */
	public function isLoggedIn()
	{
		return $this->user->isLoggedIn();
	}

	/**
	 * Returns the current user
	 * @return boolean
	 */
	public function currentUser()
	{
		return $this->user->currentUser();
	}

	/**
	 * Creates all the required filters
	 * @return
	 */
	public function filters()
	{
		Route::filter('admin_auth', function()
		{
		    if (!App::make('coanda')->isLoggedIn())
		    {
		    	return Redirect::to('/' . Config::get('coanda::coanda.admin_path') . '/login');
		    }

		});
	}

	/**
	 * Outputs all the routes, including those added by modules
	 * @return 
	 */
	public function routes()
	{
		Route::group(array('prefix' => Config::get('coanda::coanda.admin_path')), function()
		{
			Route::get('pages', function () {

				dd('hello!');

			});

			Route::controller('/', 'CoandaCMS\Coanda\Controllers\Admin');

		});

		Route::get('/', function () {
			
			echo 'hello!';

		});
	}

	public function bindings($app)
	{
		$app->bind('CoandaCMS\Coanda\Authentication\User', 'CoandaCMS\Coanda\Authentication\Eloquent\User');
	}

}