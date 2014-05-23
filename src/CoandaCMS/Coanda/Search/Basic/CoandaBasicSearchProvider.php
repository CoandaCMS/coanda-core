<?php namespace CoandaCMS\Coanda\Search\Basic;

use CoandaCMS\Coanda\Search\CoandaSearchProvider;

/**
 * Class CoandaBasicSearchProvider
 * @package CoandaCMS\Coanda\Search\Basic
 */
class CoandaBasicSearchProvider implements CoandaSearchProvider {

    /**
     * @param $module
     * @param $module_id
     * @param $url
     * @param $search_data
     */
    public function register($module, $module_id, $url, $search_data)
	{
	}

    /**
     * @param $module
     * @param $module_id
     */
    public function unRegister($module, $module_id)
	{
	}

    /**
     *
     */
    public function handleSearch()
	{
	}

}