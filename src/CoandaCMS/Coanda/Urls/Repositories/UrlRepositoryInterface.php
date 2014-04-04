<?php namespace CoandaCMS\Coanda\Urls\Repositories;

interface UrlRepositoryInterface {

	public function findById($id);

	public function findBySlug($slug);

	public function register($slug, $for, $for_id);

	// public function unRegister($slug);

	public function canUse($slug, $for, $for_id);

	public function getForPage($id);

	public function updateSubTree($slug, $new_for);

}
