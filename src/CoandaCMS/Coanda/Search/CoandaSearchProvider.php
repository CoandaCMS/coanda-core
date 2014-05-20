<?php namespace CoandaCMS\Coanda\Search;

interface CoandaSearchProvider {

	public function register($index, $type, $id, $search_data);

	public function unRegister($index, $type, $id);

}