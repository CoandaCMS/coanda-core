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

    /**
     * @var ImageHandler
     */
    private $image_handler;

    /**
     *
     */
    public function __construct()
	{
		$this->image_handler = new ImageHandler;
	}

    /**
     *
     */
    public function delete()
	{
		$original_file = Config::get('coanda::coanda.uploads_directory') . '/' . $this->filename;

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
     * @param $filename
     * @return string
     * @throws ImageGenerationException
     */
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
				$size = $this->getNearestAllowedSize($matches[2]);
                $output = $this->cacheDirectory() . '/' . $matches[1] . $size . '.' . $matches[3];

				if ($matches[1] == 'c')
				{
					$this->image_handler->crop($original, $output, $size);
				}

				if ($matches[1] == 'r')
				{
					$this->image_handler->resize($original, $output, $size);
				}

                return $this->generateImageCacheUrl($matches[1], $size);

            }
		}

		throw new ImageGenerationException;
	}

    /**
     * @param $size
     * @return string
     * @throws ImageGenerationException
     */
    public function cropUrl($size)
	{
		return $this->generateImageCacheUrl('c', $size);
	}

    /**
     * @param $size
     * @return string
     * @throws ImageGenerationException
     */
    public function resizeUrl($size)
	{
		return $this->generateImageCacheUrl('r', $size);
	}

    /**
     * @param $type
     * @param $size
     * @return string
     * @throws ImageGenerationException
     */
    private function generateImageCacheUrl($type, $size)
	{
		if ($this->admin_only)
		{
			throw new ImageGenerationException;
		}

		return Config::get('coanda::coanda.image_cache_directory') . '/' . $this->id . '/' . $type . $size . '.' . $this->extension;		
	}

    /**
     * @return bool|string
     */
    public function downloadUrl()
	{
		if ($this->admin_only)
		{
			return false;
		}

		return Config::get('coanda::coanda.file_cache_directory') . '/' . $this->id . '/' . $this->id . '.' . $this->extension;
	}

    /**
     * @param $filename
     * @throws OriginalFileCacheException
     */
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
		return Config::get('coanda::coanda.uploads_directory') . '/' . $this->filename;
	}

    /**
     * @return string
     */
    private function cacheDirectory()
    {
    	if ($this->type == 'image')
    	{
    		return public_path() . '/' . Config::get('coanda::coanda.image_cache_directory') . '/' . $this->id;
    	}
    }

    /**
     * @param $size
     * @return mixed
     */
    private function getNearestAllowedSize($size)
    {
        return $this->getClosestSize(Config::get('coanda::coanda.available_image_sizes'), $size);
    }

    /**
     * @param $sizes
     * @param $required_size
     * @return mixed
     */
    private function getClosestSize($sizes, $required_size)
    {
        $closest = array_shift($sizes);

        foreach ($sizes as $size)
        {
            $closest = ($required_size - $closest > $size - $required_size) ? $size : $closest;
        }

        return $closest;
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