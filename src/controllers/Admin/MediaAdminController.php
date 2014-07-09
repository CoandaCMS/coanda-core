<?php namespace CoandaCMS\Coanda\Controllers\Admin;

use View, App, Coanda, Redirect, Input, Session, Response;

use CoandaCMS\Coanda\Media\Exceptions\MediaNotFound;
use CoandaCMS\Coanda\Media\Exceptions\TagNotFound;
use CoandaCMS\Coanda\Media\Exceptions\MissingMedia;

use CoandaCMS\Coanda\Controllers\BaseController;

/**
 * Class MediaAdminController
 * @package CoandaCMS\Coanda\Controllers\Admin
 */
class MediaAdminController extends BaseController {

    /**
     * @var \CoandaCMS\Coanda\Media\Repositories\MediaRepositoryInterface
     */
    private $mediaRepository;

    /**
     * @param CoandaCMS\Coanda\Media\Repositories\MediaRepositoryInterface $mediaRepository
     */
    public function __construct(\CoandaCMS\Coanda\Media\Repositories\MediaRepositoryInterface $mediaRepository)
	{
		$this->mediaRepository = $mediaRepository;

		$this->beforeFilter('csrf', ['on' => 'post', 'except' => ['postHandleUpload']]);
	}

    /**
     * @return mixed
     */
    public function getIndex()
	{
		Coanda::checkAccess('media', 'view');

		$media_list = $this->mediaRepository->getList(18);
		$tags = $this->mediaRepository->recentTagList(10);
		$max_upload = $this->mediaRepository->maxFileSize();

		return View::make('coanda::admin.modules.media.index', [ 'media_list' => $media_list, 'max_upload' => $max_upload, 'tags' => $tags ]);
	}

    /**
     * @return mixed
     */
    public function postHandleUpload()
	{
		Coanda::checkAccess('media', 'create');

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

    /**
     * @return mixed
     */
    public function getAdd()
	{
		Coanda::checkAccess('media', 'create');

		return View::make('coanda::admin.modules.media.add');
	}

    /**
     * @return mixed
     */
    public function postAdd()
	{
		Coanda::checkAccess('media', 'create');

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

    /**
     * @param $media_id
     * @return mixed
     */
    public function getView($media_id)
	{
		Coanda::checkAccess('media', 'view');

		try
		{
			$media = $this->mediaRepository->findById($media_id);
			$tags = $this->mediaRepository->getTags($media_id);

			return View::make('coanda::admin.modules.media.view', ['media' => $media, 'tags' => $tags]);
		}
		catch (MediaNotFound $exception)
		{
			App::abort('404');
		}
	}

    /**
     * @param $media_id
     * @return mixed
     */
    public function getDownload($media_id)
	{
		Coanda::checkAccess('media', 'view');

		try
		{
			$media = $this->mediaRepository->findById($media_id);
			$original_file = $media->originalFilePath();

			$fp = fopen($original_file, 'rb');

			header('Content-Type: ' . $media->mime);
			header('Content-Length: ' . filesize($original_file));

			fpassthru($fp);

			exit();
		}
		catch (MediaNotFound $exception)
		{
			App::abort('404');
		}
	}

    /**
     * @param $media_id
     * @return mixed
     */
    public function postAddTag($media_id)
	{
		Coanda::checkAccess('media', 'tag');

		try
		{
			$this->mediaRepository->tagMedia($media_id, Input::get('tag'));

			return Redirect::to(Coanda::adminUrl('media/view/' . $media_id));
		}
		catch (MediaNotFound $exception)
		{
			App::abort('404');
		}
	}

    /**
     * @param $media_id
     * @param $tag_id
     * @return mixed
     */
    public function getRemoveTag($media_id, $tag_id)
	{
		Coanda::checkAccess('media', 'tag');

		try
		{
			$this->mediaRepository->removeTag($media_id, $tag_id);

			return Redirect::to(Coanda::adminUrl('media/view/' . $media_id));
		}
		catch (MediaNotFound $exception)
		{
			App::abort('404');
		}
	}

    /**
     * @param $media_id
     * @return mixed
     */
    public function getRemove($media_id)
	{
		Coanda::checkAccess('media', 'remove');

		try
		{
			$media = $this->mediaRepository->findById($media_id);

			return View::make('coanda::admin.modules.media.remove', ['media' => $media]);
		}
		catch (MediaNotFound $exception)
		{
			App::abort('404');
		}
	}

    /**
     * @param $media_id
     * @return mixed
     */
    public function postRemove($media_id)
	{
		Coanda::checkAccess('media', 'remove');

		try
		{
			$this->mediaRepository->removeById($media_id);

			return Redirect::to(Coanda::adminUrl('media'));
		}
		catch (MediaNotFound $exception)
		{
			App::abort('404');
		}
	}

    /**
     * @return mixed
     */
    public function getTags()
	{
		Coanda::checkAccess('media', 'view');

		$tags = $this->mediaRepository->tags(10);

		return View::make('coanda::admin.modules.media.tags', ['tags' => $tags]);
	}

    /**
     * @param $tag_id
     * @return mixed
     */
    public function getTag($tag_id)
	{
		Coanda::checkAccess('media', 'view');

		try
		{
			$tag = $this->mediaRepository->tagById($tag_id);
			$media_list = $this->mediaRepository->forTag($tag->id, 18);

			return View::make('coanda::admin.modules.media.tag', ['tag' => $tag, 'media_list' => $media_list]);
		}
		catch (TagNotFound $exception)
		{
			App::abort('404');
		}
	}

    /**
     * @return mixed
     */
    public function getBrowse()
	{
		$per_page = 12;
		$type = 'all';

		if (Input::has('type') && Input::get('type') == 'image')
		{
			$type = 'image';
		}

		if (Input::has('type') && Input::get('type') == 'file')
		{
			$type = 'file';
		}

		$media_list = $this->mediaRepository->getListByType($type, $per_page);

		return $media_list;
	}
}