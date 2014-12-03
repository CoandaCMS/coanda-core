<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent\Queries;

use CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface;

class SubPageQuery {

    /**
     * @var PageRepositoryInterface
     */
    private $repository;
    /**
     * @var int
     */
    private $parent_page_id = 0;
    /**
     * @var int
     */
    private $per_page = 10;
    /**
     * @var int
     */
    private $current_page = 1;

    /**
     * @param PageRepositoryInterface $repository
     */
    public function __construct(PageRepositoryInterface $repository)
	{
		$this->repository = $repository;
	}

    /**
     * @param $query_parameters
     * @return mixed
     */
    public function execute($query_parameters)
	{
		$this->parent_page_id = $query_parameters['parent_page_id'];
		$this->per_page = isset($query_parameters['per_page']) ? $query_parameters['per_page'] : 10;
		$this->current_page = isset($query_parameters['current_page']) ? $query_parameters['current_page'] : 1;

		$query = $this->baseQuery();
		$query = $this->handleParameters($query, $query_parameters['parameters']);

		return $this->getResults($query, $query_parameters);
	}

    /**
     * @param $query
     * @param $parameters
     * @return mixed
     */
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

    /**
     * @param $query
     * @return mixed
     */
    private function getPaginatedResults($query)
	{
		$count = $query->count('pages.id');

		$query = $this->addOrdering($query);

        if ($this->current_page > 0)
        {
            $query->skip(($this->current_page - 1) * $this->per_page);
        }

		$items = [];

		foreach ($query->take($this->per_page)->get() as $result)
		{
			$items[] = $result;
		}

		return \Paginator::make($items, $count, $this->per_page);
	}

    /**
     * @return mixed
     */
    private function baseQuery()
	{
		$page_model = $this->repository->getPageModel();

		$query = $page_model
                    ->select('pages.*')
					->where('is_trashed', '=', '0')
					->where('is_home', '=', '0');

        if ($this->parent_page_id !== null)
        {
            $query->where('pages.parent_page_id', $this->parent_page_id);
        }

		$query->join('pageversions', 'pages.id', '=', 'pageversions.page_id');
		$query->where('pageversions.version', '=', \DB::raw('pages.current_version'));

		return $query;
	}

    /**
     * @param $query
     * @param $parameters
     * @return mixed
     */
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
            $query = $this->handleParameter($query, $parameter, $parameters[$parameter]);
		}

		return $query;
	}

    /**
     * @param $query
     * @param $parameter
     * @param $value
     * @return mixed
     */
    private function handleParameter($query, $parameter, $value)
    {
        $method = camel_case('parameter_' . $parameter);

        if (method_exists($this, $method))
        {
            $query = $this->$method($query, $value);
        }

        return $query;
    }

    /**
     * @param $query
     * @param $page_types
     * @return mixed
     */
    private function parameterIncludePageTypes($query, $page_types)
    {
        if (is_array($page_types) && count($page_types) > 0)
        {
            $query->whereIn('pages.type', $page_types);
        }

        return $query;
    }

    /**
     * @param $query
     * @param $page_types
     * @return mixed
     */
    private function parameterExcludePageTypes($query, $page_types)
    {
        if (is_array($page_types) && count($page_types) > 0)
        {
            $query->whereNotIn('pages.type', $page_types);
        }

        return $query;
    }

    /**
     * @param $query
     * @param $value
     * @return mixed
     */
    private function parameterIncludeInvisible($query, $value)
	{
		if (!$value)
		{
			$query->where( function ($query) {

				$query->where( function ($query) {

					$query->whereNull('pageversions.visible_from');
					$query->orWhere('pageversions.visible_from', '<', \DB::raw('NOW()'));

				});

				$query->where( function ($query) {

					$query->whereNull('pageversions.visible_to');
					$query->orWhere('pageversions.visible_to', '>', \DB::raw('NOW()'));

				});

			});
		}

		return $query;
	}

    /**
     * @param $query
     * @param $filters
     * @return mixed
     */
    private function parameterAttributeFilters($query, $filters)
	{
		if ($filters && count($filters) > 0)
		{
			$query->join('pageattributes', 'pageattributes.page_version_id', '=', 'pageversions.id');

			$query->where( function ($query) use ($filters) {

				foreach ($filters as $filter)
				{
					$query->where( function ($query) use ($filter) {

						$value = $filter['value'];

						if (!is_numeric($value))
						{
							$value = \DB::connection()->getPdo()->quote($value);
						}

						$nested_query = "select count(*)
							from pageattributes
							where page_version_id=pageversions.id
							and identifier='" . $filter['attribute'] . "'
							and attribute_data " . $filter['type'] . ' ' . $value;

						$nested_query = preg_replace('/\n/', '', $nested_query);

						$query->where(\DB::raw('(' . $nested_query . ')'), '>=', 1);

					});
				}
			});
		}

		return $query;
	}

    /**
     * @param $query
     * @param $value
     * @return mixed
     */
    private function parameterIncludeHidden($query, $value)
	{
		if (!$value)
		{
			$query->where( function ($query) {

				$query->where('pageversions.is_hidden', '=', 0);
				$query->where('pageversions.is_hidden_navigation', '=', 0);

			});
		}

		return $query;
	}

    /**
     * @param $query
     * @param $value
     * @return mixed
     */
    private function parameterIncludeDrafts($query, $value)
	{
		if (!$value)
		{
			$query->where('pageversions.status', '=', 'published');	
		}

		return $query;
	}

    /**
     * @param $query
     * @param $value
     * @return mixed
     */
    private function parameterOrderQuery($query, $value)
	{
		if ($value && isset($value['operator']) && isset($value['value']))
		{
			$query->where('order', $value['operator'], $value['value']);
		}

		return $query;
	}

    /**
     * @param $query
     * @return mixed
     */
    private function addOrdering($query)
	{
		$order = 'manual';

		if ($this->parent_page_id != 0)
		{
			try
			{
				$parent = $this->repository->findById($this->parent_page_id);
				$order = $parent->sub_page_order;
			}
			catch (\CoandaCMS\Coanda\Pages\Exceptions\PageNotFound $exception)
			{
				// Default to manual above...
			}
		}

		$query = $this->handleOrder($order, $query);

		return $query;
	}

    /**
     * @param $order
     * @param $query
     * @return mixed
     */
    private function handleOrder($order, $query)
	{
		$method = camel_case('order_' . str_replace(':', '_', $order));

		if (method_exists($this, $method))
		{
			return $this->$method($query);
		}

		return $query;
	}

    /**
     * @param $query
     * @return mixed
     */
    private function orderManual($query)
	{
		$query->orderBy('pages.order', 'asc');
		$query->orderBy('pages.id', 'asc');

		return $query;
	}

    /**
     * @param $query
     * @return mixed
     */
    private function orderAlphaAsc($query)
	{
		$query->orderBy('pages.name', 'asc');

		return $query;
	}

    /**
     * @param $query
     * @return mixed
     */
    private function orderAlphaDesc($query)
	{
		$query->orderBy('pages.name', 'desc');

		return $query;
	}

    /**
     * @param $query
     * @return mixed
     */
    private function orderCreatedAsc($query)
	{
		$query->orderBy('pages.created_at', 'asc');

		return $query;
	}

    /**
     * @param $query
     * @return mixed
     */
    private function orderCreatedDesc($query)
	{
		$query->orderBy('pages.created_at', 'desc');

		return $query;
	}
}