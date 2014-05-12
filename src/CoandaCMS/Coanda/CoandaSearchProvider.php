<?php namespace CoandaCMS\Coanda;

interface CoandaSearchProvider {

	public function register($type, $id, $search_data);

	public function unRegister($type, $id);

}