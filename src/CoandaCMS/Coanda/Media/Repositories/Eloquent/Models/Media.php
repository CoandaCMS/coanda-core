<?php namespace CoandaCMS\Coanda\Media\Repositories\Eloquent\Models;

use Eloquent, Coanda, App, Config, File;
use Carbon\Carbon;

/**
 * Class Media
 * @package CoandaCMS\Coanda\Media\Repositories\Eloquent\Models
 */
class Media extends Eloquent {

	use \CoandaCMS\Coanda\Core\Presenters\PresentableTrait;

    /**
     * @var string
     */
    protected $presenter = 'CoandaCMS\Coanda\Media\Presenters\Media';

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'media';

    /**
     *
     */
    public function delete()
	{
		$original_file = base_path() . '/' . Config::get('coanda::coanda.uploads_directory') . '/' . $this->filename;

		if (file_exists($original_file))
		{
			unlink($original_file);
		}

		foreach ($this->tags()->lists('media_tag_id') as $tag_id)
		{
			$this->tags()->detach($tag_id);
		}

		parent::delete();
	}

    /**
     * @return string
     */
    public function type()
	{
		$mime_type_parts = explode('/', $this->mime);

		if (count($mime_type_parts) == 2)
		{
			if ($mime_type_parts[0] == 'image')
			{
				return 'image';
			}

			if ($mime_type_parts[0] == 'video')
			{
				return 'video';
			}
		}

		return 'file';
	}

    /**
     * @return string
     */
    public function getTypeAttribute()
	{
		return $this->type();
	}

    /**
     * @return string
     */
    public function originalFilePath()
	{
		return base_path() . '/' . Config::get('coanda::coanda.uploads_directory') . '/' . $this->filename;
	}

    /**
     * @return string
     */
    public function originalFileLink()
	{
        $original_file = base_path() . '/' . Config::get('coanda::coanda.uploads_directory') . '/' . $this->filename;
        $cache_base = Config::get('coanda::coanda.file_cache_directory');

        $cache_directory = $cache_base . '/' . $this->id;
        $cache_path = $cache_directory . '/' . $this->id . '.' . $this->extension;

        if(!file_exists($cache_path))
        {
            if( !is_dir($cache_directory))
            {
				mkdir($cache_directory);
            }

            copy($original_file, $cache_path);
        }

        return $cache_path;
	}

    /**
     * @return mixed
     */
    public function tags()
	{
		return $this->belongsToMany('CoandaCMS\Coanda\Media\Repositories\Eloquent\Models\MediaTag');
	}

    /**
     * @return array
     */
    public function toArray()
	{
		return [
			'id' => $this->id,
			'original_filename' => $this->original_filename,
			'original_file_url' => $this->present()->original_file_url,
			'thumbnail_url' => $this->type == 'image' ? $this->present()->thumbnail_url : false,
			'mime' => $this->mime
		];
	}
}