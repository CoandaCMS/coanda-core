<?php namespace CoandaCMS\Coanda\Controllers\Admin;

use View, App, Coanda, Redirect, Input, Session, Response;

use CoandaCMS\Coanda\Urls\Exceptions\UrlAlreadyExists;
use CoandaCMS\Coanda\Urls\Exceptions\InvalidSlug;
use CoandaCMS\Coanda\Exceptions\ValidationException;

use CoandaCMS\Coanda\Controllers\BaseController;

class UrlAdminController extends BaseController {

    private $urlRepository;

    public function __construct(\CoandaCMS\Coanda\Urls\Repositories\UrlRepositoryInterface $urlRepository)
	{
		$this->urlRepository = $urlRepository;

		$this->beforeFilter('csrf', ['on' => 'post']);
	}

    /**
     * @return mixed
     */
    public function getIndex()
	{
		Coanda::checkAccess('urls', 'view');

		$urls = $this->urlRepository->getRedirectUrls(10);

		return View::make('coanda::admin.modules.urls.index', [ 'urls' => $urls ]);
	}

    /**
     * @return mixed
     */
    public function getAll()
	{
		Coanda::checkAccess('urls', 'view');

		$urls = $this->urlRepository->getList(10);

		return View::make('coanda::admin.modules.urls.all', [ 'urls' => $urls ]);
	}

	public function getAddRedirect()
	{
		$invalid_fields = Session::has('invalid_fields') ? Session::get('invalid_fields') : [];

		if (Session::has('invalid_from'))
		{
			$invalid_fields['from'] = 'The URL is invalid';
		}

		if (Session::has('from_in_use'))
		{
			$invalid_fields['from'] = 'The URL is already in use';
		}

		return View::make('coanda::admin.modules.urls.addredirect', ['invalid_fields' => $invalid_fields]);
	}

	public function postAddRedirect()
	{
		try
		{
			$this->urlRepository->addRedirect(Input::get('from_url'), Input::get('to_url'));

			return Redirect::to(Coanda::adminUrl('urls'))->with('promo_add', true);
		}
		catch (InvalidSlug $exception)
		{
			return Redirect::to(Coanda::adminUrl('urls/add-redirect'))->withInput()->with('invalid_from', true);
		}
		catch (UrlAlreadyExists $exception)
		{
			return Redirect::to(Coanda::adminUrl('urls/add-redirect'))->withInput()->with('from_in_use', true);
		}
		catch (ValidationException $exception)
		{
			return Redirect::to(Coanda::adminUrl('urls/add-redirect'))->withInput()->with('invalid_fields', $exception->getInvalidFields());
		}
	}

	public function getRemoveRedirect($url_id)
	{
		$url = $this->urlRepository->getRedirectUrl($url_id);

		if (!$url)
		{
			App::abort('404');
		}

		return View::make('coanda::admin.modules.urls.removeredirect', ['url' => $url]);
	}

	public function postRemoveRedirect($url_id)
	{
		$this->urlRepository->removeRedirectUrl($url_id);

		return Redirect::to(Coanda::adminUrl('urls'))->with('removed', true);
	}
}