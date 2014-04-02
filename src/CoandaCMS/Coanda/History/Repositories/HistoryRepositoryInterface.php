<?php namespace CoandaCMS\Coanda\History\Repositories;

interface HistoryRepositoryInterface {

	public function add($for, $for_id, $user_id, $data);

	public function get($for, $for_id);

}
