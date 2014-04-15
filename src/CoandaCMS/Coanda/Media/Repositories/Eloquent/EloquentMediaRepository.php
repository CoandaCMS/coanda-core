<?php namespace CoandaCMS\Coanda\Media\Repositories\Eloquent;

use Coanda, Config;

use CoandaCMS\Coanda\Exceptions\ValidationException;
use CoandaCMS\Coanda\Media\Exceptions\MediaNotFound;
use CoandaCMS\Coanda\Media\Exceptions\MissingMedia;

use CoandaCMS\Coanda\Media\Repositories\Eloquent\Models\Media as MediaModel;

use CoandaCMS\Coanda\Media\Repositories\MediaRepositoryInterface;

use Carbon\Carbon;

class EloquentMediaRepository implements MediaRepositoryInterface {

    private $model;

    private $historyRepository;

    public function __construct(MediaModel $model, \CoandaCMS\Coanda\History\Repositories\HistoryRepositoryInterface $historyRepository)
	{
		$this->model = $model;
		$this->historyRepository = $historyRepository;
	}

    public function findById($id)
	{
		$media = $this->model->find($id);

		if (!$media)
		{
			throw new MediaNotFound('Media #' . $id . ' not found');
		}
		
		return $media;
	}

    public function findByIds($ids)
	{
		$media_list = new \Illuminate\Database\Eloquent\Collection;

		if (!is_array($ids))
		{
			return $media_list;
		}

		foreach ($ids as $id)
		{
			$media = $this->model->find($id);

			if ($media)
			{
				$media_list->add($media);
			}
		}

		return $media_list;
	}

	public function getList($per_page)
	{
		return $this->model->orderBy('created_at', 'desc')->paginate($per_page);
	}

	public function handleUpload($file)
	{
		$new_media = new $this->model;

		$new_media->original_filename = $file->getClientOriginalName();
		$new_media->mime = $file->getMimeType();
		$new_media->extension = $file->getClientOriginalExtension();
		$new_media->size = $file->getClientSize();

		$upload_filename = time() . '-' . md5($new_media->original_filename) . '.' . $file->getClientOriginalExtension();
		$upload_path = base_path() . '/' . Config::get('coanda::coanda.uploads_directory');

        $file->move($upload_path, $upload_filename);

        $new_media->filename = $upload_filename;

        if ($new_media->type == 'image')
        {
        	$dimensions = getimagesize($upload_path . '/' . $upload_filename);

        	$new_media->width = $dimensions[0];
        	$new_media->height = $dimensions[1];
        }

        $new_media->save();

        return $new_media;
	}

	public function downloadLink($media_id)
	{
		$media = $this->findById($media_id);

		return $media->originalFileLink();
	}
}