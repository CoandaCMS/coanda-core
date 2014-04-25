<?php namespace CoandaCMS\Coanda\Themes\Simple;

use View;

class SimpleThemeProvider implements \CoandaCMS\Coanda\CoandaThemeProvider {

	public function boot($coanda)
	{
		// Do anything which needs to be done, maybe you would like to set some internal variables to use when rendering.
	}

	public function renderHome()
	{
		$home_data = [
			'page_title' => 'Home page title'
		];
		
		return View::make('coanda::themes.simple.home', $home_data);
	}

	public function render($what, $with = [])
	{
		// Do what ever you like with the data provided!

		if ($what == 'page')
		{
			return $this->renderPage($with);
		}
	}

	private function renderPage($page_data)
	{
		return View::make('coanda::themes.simple.' . $page_data['type'], $page_data);
	}

}
