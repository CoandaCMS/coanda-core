<?php namespace CoandaCMS\Coanda\Core\Attributes\Types;

use CoandaCMS\Coanda\Core\Attributes\AttributeType;
use CoandaCMS\Coanda\Exceptions\AttributeValidationException;

use Carbon\Carbon;
use Config;

class Date extends AttributeType {

    /**
     * @return string
     */
    public function identifier()
    {
        return 'date';
    }

    /**
     * @return string
     */
    public function edit_template()
    {
    	return 'coanda::admin.core.attributes.edit.date';  
    }

    /**
     * @return string
     */
    public function view_template()
    {
    	return 'coanda::admin.core.attributes.view.date';
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
        $format = Config::get('coanda::coanda.date_format');
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
            // return Carbon::createFromTimeStamp($data)->format(Config::get('coanda::coanda.date_format'));
            return Carbon::createFromTimeStamp($data);
        }

        return '';
	}
}