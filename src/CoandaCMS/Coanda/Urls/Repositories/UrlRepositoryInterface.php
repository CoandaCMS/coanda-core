<?php namespace CoandaCMS\Coanda\Urls\Repositories;

interface URLRepositoryInterface {

	public function findById($id);

	// public function findBySlug($slug);

	public function register($slug, $for, $for_id);

	// public function unRegister($slug);

}
