<?php namespace CoandaCMS\Coanda\Core\Attributes\Types;

use CoandaCMS\Coanda\Core\Attributes\AttributeType;
use CoandaCMS\Coanda\Exceptions\AttributeValidationException;

use Carbon\Carbon;
use Config;

class DateTime extends AttributeType {

    /**
     * @return string
     */
    public function identifier()
    {
        return 'datetime';
    }

    /**
     * @return string
     */
    public function edit_template()
    {
    	return 'coanda::admin.core.attributes.edit.datetime';  
    }

    /**
     * @return string
     */
    public function view_template()
    {
    	return 'coanda::admin.core.attributes.view.datetime';
    }

    /**
     * @param $data
     * @param $is_required
     * @param $name
     * @return mixed
     * @throws \CoandaCMS\Coanda\Exceptions\AttributeValidationException
     */
    public function store($data, $is_required, $name, $parameters = [])
	{
        $format = Config::get('coanda::coanda.datetime_format');
        $date_instance = false;

        try
        {
            $date_instance = Carbon::createFromFormat($format, $data, date_default_timezone_get());
        }
        catch (\InvalidArgumentException $exception)
        {
            if ($is_required)
            {
                throw new AttributeValidationException($name . ' is required');    
            }
        }
        catch (\ErrorException $exception)
        {
            throw new AttributeValidationException($name . ' is invalid');
        }

        return $date_instance ? $date_instance->timestamp : '';
	}

    /**
     * @param $data
     * @return mixed
     */
    public function data($data, $parameters = [])
	{
        if (is_numeric($data))
        {
            return Carbon::createFromTimeStamp($data)->format(Config::get('coanda::coanda.datetime_format'));
        }

        return '';
	}
}