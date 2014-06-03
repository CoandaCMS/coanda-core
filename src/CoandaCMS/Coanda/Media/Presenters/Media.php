<?php namespace CoandaCMS\Coanda\Media\Presenters;

use Config;

// import the Intervention Image Class
use Intervention\Image\Image as ImageFactory;

/**
 * Class Media
 * @package CoandaCMS\Coanda\Media\Presenters
 */
class Media extends \CoandaCMS\Coanda\Core\Presenters\Presenter {

    /**
     * @return mixed
     */
    public function name()
	{
		return $this->model->original_filename;
	}

    /**
     * @return string
     */
    public function size()
	{
		$bytes = $this->model->size;

		$size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
		$factor = floor((strlen($bytes) - 1) / 3);
		$decimals = 2;

		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
	}

    /**
     * @return bool
     */
    public function has_preview()
	{
		if ($this->model->type == 'image')
		{
			return true;
		}

		return false;
	}

    /**
     * @return bool|string
     */
    public function thumbnail_url()
	{
		if ($this->model->type == 'image')
		{
			return $this->thumbnail();
		}

		return false;
	}

    /**
     * @return bool|string
     */
    public function large_url()
	{
		if ($this->model->type == 'image')
		{
			return $this->large();
		}

		return false;
	}

    /**
     * @return string
     */
    public function original_file_url()
	{
		return url($this->model->originalFileLink());
	}

    /**
     * @return bool|string
     */
    public function thumbnail()
    {
        return $this->generateCrop(200, 200);
    }

    /**
     * @return bool|string
     */
    public function large()
    {
        return $this->generateResized(800, 800);
    }

    private function uploadsDirectory()
    {
    	return base_path() . '/' . Config::get('coanda::coanda.uploads_directory');
    }

    private function cacheDirectory()
    {
    	return public_path() . '/' .Config::get('coanda::coanda.image_cache_directory');
    }

    /**
     * @param $width
     * @param $height
     * @param bool $maintain_ratio
     * @return bool|string
     */
    private function generateResized($width, $height, $maintain_ratio = true)
    {
    	if ($this->type == 'image')
    	{
	        ini_set('memory_limit', '64M');

	        $file_name = 'r' . $width . '.' . $this->extension;

	        $cache_path = $this->cacheDirectory() . '/' . $this->model->id;

	        if (!file_exists($cache_path . '/' . $file_name))
	        {
	            if (!is_dir($cache_path))
	            {
					mkdir($cache_path);
	            }

	            $imageFactory = ImageFactory::make($this->uploadsDirectory() . '/' . $this->model->filename);
	            $imageFactory->resize($width, $height, $maintain_ratio, false)->save($cache_path . '/' . $file_name);
	        }

	        return '/' . Config::get('coanda::coanda.image_cache_directory') . '/' . $this->model->id . '/' . $file_name;
    	}

    	return false;
    }

    /**
     * @param $width
     * @param $height
     * @return bool|string
     */
    private function generateCrop($width, $height)
    {
    	if ($this->type == 'image')
    	{
	        ini_set('memory_limit', '64M');
	        
	        $uploads_directory = base_path() . '/' . Config::get('coanda::coanda.uploads_directory');
	        $cache_base = Config::get('coanda::coanda.image_cache_directory');

	        $cache_directory = $cache_base . '/' . $this->model->id;

	        $cache_path = $cache_directory . '/c' . $width . '.' . $this->extension;

	        if(!file_exists($cache_path))
	        {
	            if( !is_dir($cache_directory))
	            {
					mkdir($cache_directory);
	            }

	            $file_path = $uploads_directory . '/' . $this->model->filename;

	            $imageFactory = ImageFactory::make($file_path);
	            $imageFactory->grab($width, $height)->save($cache_path);
	        }

	        return url($cache_path);
    	}

    	return false;
    }    
}