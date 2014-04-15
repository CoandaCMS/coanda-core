<?php namespace CoandaCMS\Coanda\Media\Repositories\Eloquent;

use Coanda, Config;

use CoandaCMS\Coanda\Exceptions\ValidationException;
use CoandaCMS\Coanda\Media\Exceptions\MediaNotFound;
use CoandaCMS\Coanda\Media\Exceptions\MissingMedia;

use CoandaCMS\Coanda\Media\Repositories\Eloquent\Models\Media as MediaModel;
use CoandaCMS\Coanda\Media\Repositories\Eloquent\Models\MediaTag as MediaTagModel;

use CoandaCMS\Coanda\Media\Repositories\MediaRepositoryInterface;

use Carbon\Carbon;

class EloquentMediaRepository implements MediaRepositoryInterface {

    private $model;
    private $tag_model;

    private $historyRepository;

    public function __construct(MediaModel $model, MediaTagModel $tag_model, \CoandaCMS\Coanda\History\Repositories\HistoryRepositoryInterface $historyRepository)
	{
		$this->model = $model;
		$this->tag_model = $tag_model;
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

	public function removeById($media_id)
	{
		$media = $this->findById($media_id);

		$media->delete();
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

	public function tagMedia($media_id, $tag_name)
	{
		$media = $this->findById($media_id);

		if ($tag_name && $tag_name !== '')
		{
			$tag_name = mb_strtolower($tag_name);

			$tag = $this->tag_model->whereTag($tag_name)->first();

			if (!$tag)
			{
				$tag = new $this->tag_model;
				$tag->tag = $tag_name;
				$tag->save();
			}

			$current_tags = $media->tags()->lists('media_tag_id');

			if (!is_array($current_tags))
			{
				$current_tags = [];
			}

			$current_tags[] = $tag->id;

			$tags = array_values(array_unique($current_tags));

			$media->tags()->sync($tags, true);
		}
	}

	public function removeTag($media_id, $tag_id)
	{
		$media = $this->findById($media_id);

		$media->tags()->detach($tag_id);
	}

	public function getTags($media_id)
	{
		$media = $this->findById($media_id);

		return $media->tags;
	}

	public function recentTagList($limit)
	{
		return $this->tag_model->with('media')->orderBy('created_at', 'desc')->take($limit)->get();
	}

	public function maxFileSize()
	{
		return ini_get('upload_max_filesize');
	}
}