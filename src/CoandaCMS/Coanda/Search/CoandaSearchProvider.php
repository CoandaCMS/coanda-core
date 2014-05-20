<?php namespace CoandaCMS\Coanda\Search;

interface CoandaSearchProvider {

	public function register($module, $module_id, $url, $search_data);

	public function unRegister($module, $module_id);

	public function handleSearch();

}