<?php namespace CoandaCMS\Coanda\Media\Presenters;

use Config;

// import the Intervention Image Class
use Intervention\Image\Image as ImageFactory;

class Media extends \CoandaCMS\Coanda\Core\Presenters\Presenter {

    public function name()
	{
		return $this->model->original_filename;
	}

	public function has_preview()
	{
		if ($this->model->type == 'image')
		{
			return true;
		}

		return false;
	}

	public function thumbnail_url()
	{
		if ($this->model->type == 'image')
		{
			return $this->thumbnail();
		}

		return false;
	}

    public function thumbnail()
    {
        return $this->generateResized(200, 200);
    }

    public function large()
    {
        return $this->generateResized(800, 800);
    }

    private function generateResized($width, $height, $maintainRatio = false)
    {
    	if ($this->type == 'image')
    	{
	        ini_set('memory_limit', '64M');
	        
	        $uploads_directory = Config::get('coanda::coanda.uploads_directory');
	        $cache_base = Config::get('coanda::coanda.image_cache_directory');

	        $cache_directory = $cache_base . '/' . $this->model->id;

	        $cache_path = $cache_directory . '/' . $width . '.' . $this->extension;

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