<?php namespace CoandaCMS\Coanda\Pages;

use App;

class PageQuery {

	private $pageRepository;

	private $location_id;
	private $attribute_filters;
	private $limit;
	private $order_query;

	private $include_hidden = false;
	private $include_invisible = false;
	private $paginate = false;

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

	public function includeInvisible()
	{
		$this->include_invisible = true;

		return $this;
	}

	public function order($operator, $value)
	{
		$this->order_query = [
			'operator' => $operator,
			'value' => $value
		];

		return $this;
	}

	public function filter($attribute_identifier, $filter, $query_type = '=')
	{
		$this->attribute_filters[] = [
			'attribute' => $attribute_identifier,
			'value' => $filter,
			'type' => $query_type
		];

		return $this;
	}

	public function paginate($limit)
	{
		$this->paginate = true;
		$this->limit = $limit;

		return $this;
	}

	public function get()
	{
		$parameters = [
			'include_invisible' => $this->include_invisible,
			'include_hidden' => $this->include_hidden,
			'paginate' => $this->paginate,
			'attribute_filters' => $this->attribute_filters,
			'order_query' => $this->order_query
		];

		return $this->pageRepository->subPages($this->location_id, $this->limit, $parameters);
	}

}