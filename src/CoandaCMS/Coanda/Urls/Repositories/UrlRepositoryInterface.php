<?php namespace CoandaCMS\Coanda\Urls\Repositories;

interface UrlRepositoryInterface {

	public function findFor($for, $for_id);

	public function findById($id);

	public function findBySlug($slug);

	public function register($slug, $for, $for_id);

	public function canUse($slug, $for, $for_id);
}
