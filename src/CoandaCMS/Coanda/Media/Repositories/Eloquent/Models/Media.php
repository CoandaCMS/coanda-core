<?php namespace CoandaCMS\Coanda\Media\Repositories\Eloquent\Models;

use Eloquent, Coanda, App, Config, File;
use Carbon\Carbon;

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

	public function getTypeAttribute()
	{
		return $this->type();
	}

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

}