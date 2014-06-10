<?php namespace CoandaCMS\Coanda\Pages;

use App;

class PageQuery {

	private $pageRepository;

	private $location_id;
	private $limit;

	private $include_hidden = false;

	public function __construct($pageRepository)
	{
		$this->pageRepository = $pageRepository;
	}

	public function in($location_id)
	{
		$this->location_id = $location_id;

		return $this;
	}

	public function take($limit)
	{
		$this->limit = $limit;

		return $this;
	}

	public function includeHidden()
	{
		$this->include_hidden = true;

		return $this;
	}

	public function get()
	{
		return $this->pageRepository->subPages($this->location_id, $this->limit, $this->include_hidden);
	}

}