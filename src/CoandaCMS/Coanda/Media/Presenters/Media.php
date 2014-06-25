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
}