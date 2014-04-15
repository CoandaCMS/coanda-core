<?php namespace CoandaCMS\Coanda\Controllers\Admin;

use View, App, Coanda, Redirect, Input, Session, Response;

use CoandaCMS\Coanda\Media\Exceptions\MediaNotFound;
use CoandaCMS\Coanda\Media\Exceptions\MissingMedia;

use CoandaCMS\Coanda\Controllers\BaseController;

class MediaAdminController extends BaseController {

    private $mediaRepository;

    public function __construct(\CoandaCMS\Coanda\Media\Repositories\MediaRepositoryInterface $mediaRepository)
	{
		$this->mediaRepository = $mediaRepository;

		$this->beforeFilter('csrf', ['on' => 'post', 'except' => ['postHandleUpload']]);
	}

    public function getIndex()
	{
		$media_list = $this->mediaRepository->getList(18);

		return View::make('coanda::admin.media.index', [ 'media_list' => $media_list ]);
	}

	public function postHandleUpload()
	{
		if (!Input::hasFile('file'))
		{
			return Response::json(['error' => 'File not found, please try again.'], 500);
		}
        
		$file = Input::file('file');

		try
		{
			return $this->mediaRepository->handleUpload($file);
		}
		catch (MissingMedia $exception)
		{
			return Response::json(['error' => 'File not found, please try again.'], 500);
		}
	}

	public function getAdd()
	{
		return View::make('coanda::admin.media.add');
	}

	public function postAdd()
	{
		if (Input::hasFile('file'))
		{
			$file = Input::file('file');

			try
			{
				$new_media = $this->mediaRepository->handleUpload($file);

				return Redirect::to(Coanda::adminUrl('media'))->with('media_uploaded', true)->with('media_uploaded_message', $new_media->present()->name);
			}
			catch (MissingMedia $exception)
			{
				return Redirect::to(Coanda::adminUrl('media/add'))->with('missing_file', true);
			}
		}
		else
		{
			return Redirect::to(Coanda::adminUrl('media/add'))->with('missing_file', true);
		}
	}

	public function getView($media_id)
	{
		try
		{
			$media = $this->mediaRepository->findById($media_id);

			return View::make('coanda::admin.media.view', ['media' => $media]);
		}
		catch (MediaNotFound $exception)
		{
			App::abort('404');
		}
	}

	public function getDownload($media_id)
	{
		try
		{
			$link = $this->mediaRepository->downloadLink($media_id);

			return Redirect::to(url($link));
		}
		catch (MediaNotFound $exception)
		{
			App::abort('404');
		}
	}
}