<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent\Queries;

class SubPageQuery {

	private $repository;
	private $parent_location_id = 0;
	private $per_page = 10;
	private $current_page = 1;

	public function __construct(\CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface $repository)
	{
		$this->repository = $repository;
	}

	public function execute($query_parameters)
	{
		$this->parent_location_id = $query_parameters['parent_location_id'];
		$this->per_page = isset($query_parameters['per_page']) ? $query_parameters['per_page'] : 10;
		$this->current_page = isset($query_parameters['current_page']) ? $query_parameters['current_page'] : 1;

		$query = $this->baseQuery();
		$query = $this->handleParameters($query, $query_parameters['parameters']);

		return $this->getResults($query, $query_parameters);
	}

	private function getResults($query, $parameters)
	{
		$paginate = isset($parameters['parameters']['paginate']) ? $parameters['parameters']['paginate'] : true;

		if ($paginate)
		{
			return $this->getPaginatedResults($query);
		}

		$query = $this->addOrdering($query);

		return $query->take($this->per_page)->get();
	}

	private function getPaginatedResults($query)
	{
		$count = $query->count('pagelocations.id');

		$query = $this->addOrdering($query);
		$query->skip(($this->current_page > 0 ? (($this->current_page - 1) * $this->per_page) : 0));

		$items = [];

		foreach ($query->take($this->per_page)->get() as $result)
		{
			$items[] = $result;
		}

		return \Paginator::make($items, $count, $this->per_page);
	}

	private function baseQuery()
	{
		$page_location_model = $this->repository->getPageLocationModel();

		$query = $page_location_model
					->with('page')
					->where('parent_page_id', $this->parent_location_id)
					->whereHas('page', function ($query) {

						$query->where('is_trashed', '=', '0'); 

					});

		$query->select('pagelocations.*')->distinct();
		$query->join('pages', 'pagelocations.page_id', '=', 'pages.id');
		$query->join('pageversions', 'pagelocations.page_id', '=', 'pageversions.page_id');
		$query->where('pageversions.version', '=', \DB::raw('pages.current_version'));

		return $query;
	}

	private function handleParameters($query, $parameters)
	{
		$default_parameters = [
			'include_invisible' => false,
			'include_hidden' => false,
			'include_drafts' => false,
			'order_query' => false,
			'attribute_filters' => [],
			'paginate' => true,
		];

		$parameters = array_merge($default_parameters, $parameters);

		foreach (array_keys($parameters) as $parameter)
		{
			$method = camel_case('parameter_' . $parameter);

			if (method_exists($this, $method))
			{
				$query = $this->$method($query, $parameters[$parameter]);
			}
		}

		return $query;
	}

	private function parameterIncludeInvisible($query, $value)
	{
		if (!$value)
		{
			$query->visible();
		}

		return $query;
	}

	private function parameterAttributeFilters($query, $filters)
	{
		if ($filters && count($filters) > 0)
		{
			$query->join('pageattributes', 'pageattributes.page_version_id', '=', 'pageversions.id');

			$query->attributeFilter($filters);
		}

		return $query;
	}

	private function parameterIncludeHidden($query, $value)
	{
		if (!$value)
		{
			$query->notHidden();
		}

		return $query;
	}

	private function parameterIncludeDrafts($query, $value)
	{
		if (!$value)
		{
			$query->where('pageversions.status', '=', 'published');	
		}

		return $query;
	}

	private function parameterOrderQuery($query, $value)
	{
		if ($value && isset($value['operator']) && isset($value['value']))
		{
			$query->where('order', $value['operator'], $value['value']);
		}

		return $query;
	}

	private function addOrdering($query)
	{
		$order = 'manual';

		if ($this->parent_location_id != 0)
		{
			$parent = $this->repository->locationById($this->parent_location_id);

			if ($parent)
			{
				$order = $parent->sub_location_order;
			}
		}

		$query = $this->handleOrder($order, $query);

		return $query;
	}

	private function handleOrder($order, $query)
	{
		$method = camel_case('order_' . str_replace(':', '_', $order));

		if (method_exists($this, $method))
		{
			return $this->$method($query);
		}

		return $query;
	}

	private function orderManual($query)
	{
		$query->orderBy('pagelocations.order', 'asc');
		$query->orderBy('pagelocations.id', 'asc');

		return $query;
	}

	private function orderAlphaAsc($query)
	{
		$query->orderByPageName('asc');

		return $query;
	}

	private function orderAlphaDesc($query)
	{
		$query->orderByPageName('desc');

		return $query;
	}

	private function orderCreatedAsc($query)
	{
		$query->orderByPageCreated('asc');

		return $query;
	}

	private function orderCreatedDesc($query)
	{
		$query->orderByPageCreated('desc');

		return $query;
	}
}