<?php namespace CoandaCMS\Coanda\Search\ElasticSearch;

use CoandaCMS\Coanda\Search\CoandaSearchProvider;

class CoandaElasticSearchProvider implements CoandaSearchProvider {

	public function register($type, $id, $search_data)
	{
		\Log::info('Register with ES: ' . $type . '->' . $id);
		\Log::info($search_data);
	}

	public function unRegister($type, $id)
	{
		// dd('un register with ES');
	}

}