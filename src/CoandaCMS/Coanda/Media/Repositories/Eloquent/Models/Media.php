<?php namespace CoandaCMS\Coanda\Media\Repositories\Eloquent\Models;

use Eloquent, Coanda, App, Config, File;
use Carbon\Carbon;

use CoandaCMS\Coanda\Media\ImageHandlers\DefaultImageHandler as ImageHandler;

use CoandaCMS\Coanda\Media\Exceptions\ImageGenerationException;
use CoandaCMS\Coanda\Media\Exceptions\OriginalFileCacheException;

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

	private $image_handler;

	public function __construct()
	{
		$this->image_handler = new ImageHandler;
	}

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

	public function generateImage($filename)
	{
		if ($this->admin_only)
		{
			throw new ImageGenerationException;
		}
		
		if ($this->type == 'image')
		{
			if (preg_match('/^([c|r])(.*)\.(.*)$/', $filename, $matches))
			{
				$original = $this->originalFilePath();
				$output = $this->cacheDirectory() . '/' . $filename;
				$size = $matches[2];

				if ($matches[1] == 'c')
				{
					return $this->image_handler->crop($original, $output, $size);
				}

				if ($matches[1] == 'r')
				{
					return $this->image_handler->resize($original, $output, $size);
				}
			}
		}

		throw new ImageGenerationException;
	}

	public function cropUrl($size)
	{
		return $this->generateImageCacheUrl('c', $size);
	}

	public function resizeUrl($size)
	{
		return $this->generateImageCacheUrl('r', $size);
	}

	private function generateImageCacheUrl($type, $size)
	{
		if ($this->admin_only)
		{
			throw new ImageGenerationException;
		}

		return Config::get('coanda::coanda.image_cache_directory') . '/' . $this->id . '/' . $type . $size . '.' . $this->extension;		
	}

	public function downloadUrl()
	{
		if ($this->admin_only)
		{
			return false;
		}

		return Config::get('coanda::coanda.file_cache_directory') . '/' . $this->id . '/' . $this->id . '.' . $this->extension;
	}

	public function generateOriginalFileCache($filename)
	{
		if ($this->admin_only)
		{
			throw new OriginalFileCacheException;
		}

		if ($filename !== ($this->id . '.' . $this->extension))
		{
			throw new OriginalFileCacheException;
		}

		$original = $this->originalFilePath();
		$cache_directory = public_path() . '/' .Config::get('coanda::coanda.file_cache_directory') . '/' . $this->id;
		$destination = $cache_directory . '/' . $this->id . '.' . $this->extension;

		if (!is_dir($cache_directory))
		{
			mkdir($cache_directory, 0777, true);
		}

		copy($original, $destination);
	}

    /**
     * @return string
     */
    public function originalFilePath()
	{
		return base_path() . '/' . Config::get('coanda::coanda.uploads_directory') . '/' . $this->filename;
	}

    private function cacheDirectory()
    {
    	if ($this->type == 'image')
    	{
    		return public_path() . '/' .Config::get('coanda::coanda.image_cache_directory') . '/' . $this->id;
    	}
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
		$download_url = $this->downloadUrl();

		return [
			'id' => $this->id,
			'original_filename' => $this->original_filename,
			'original_file_url' => $download_url ? url($download_url) : false,
			'thumbnail_url' => $this->type == 'image' ? url($this->cropUrl(200)) : false,
			'mime' => $this->mime
		];
	}
}