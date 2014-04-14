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
		$media_list = $this->mediaRepository->getList(12);
		
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
}