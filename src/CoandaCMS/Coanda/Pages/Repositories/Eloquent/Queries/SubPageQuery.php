<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent\Queries;

class SubPageQuery {

	private $repository;

	public function __construct(\CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface $repository)
	{
		$this->repository = $repository;
	}

	public function execute($query_parameters)
	{
		$parent_location_id = $query_parameters['parent_location_id'];
		$current_page = $query_parameters['current_page'];
		$per_page = $query_parameters['per_page'];
		$parameters = $query_parameters['parameters'];

		$default_parameters = [
			'include_invisible' => false,
			'include_hidden' => false,
			'include_drafts' => false,
			'paginate' => true,
			'attribute_filters' => [],
			'order_query' => false
		];

		$parameters = array_merge($default_parameters, $parameters);

		$page_location_model = $this->repository->getPageLocationModel();

		$query = $page_location_model
					->with('page')
					->where('parent_page_id', $parent_location_id)
					->whereHas('page', function ($query) {

						$query->where('is_trashed', '=', '0'); 

					});

		$query->select('pagelocations.*')->distinct();
		$query->join('pages', 'pagelocations.page_id', '=', 'pages.id');
		$query->join('pageversions', 'pagelocations.page_id', '=', 'pageversions.page_id');
		$query->where('pageversions.version', '=', \DB::raw('pages.current_version'));

		if ($parameters['attribute_filters'] && count($parameters['attribute_filters']) > 0)
		{
			$query->join('pageattributes', 'pageattributes.page_version_id', '=', 'pageversions.id');

			$query->attributeFilter($parameters['attribute_filters']);
		}

		if (!$parameters['include_drafts'])
		{
			$query->where('pageversions.status', '=', 'published');	
		}

		if (!$parameters['include_invisible'])
		{
			$query->visible();
		}

		if (!$parameters['include_hidden'])
		{
			$query->notHidden();
		}

		if ($parameters['order_query'] && isset($parameters['order_query']['operator']) && isset($parameters['order_query']['value']))
		{
			$query->where('order', $parameters['order_query']['operator'], $parameters['order_query']['value']);
		}

		if ($parameters['paginate'])
		{
			$count = $query->count('pagelocations.id');

			// Add the ordering...
			$query = $this->addOrdering($query, $parent_location_id);

			if ($current_page > 0)
			{
				$query->skip(($current_page - 1) * $per_page);
			}

			$results = $query->take($per_page)->get($per_page);

			$items = [];

			foreach ($results as $result)
			{
				$items[] = $result;
			}

			return \Paginator::make($items, $count, $per_page);
		}

		$query = $this->addOrdering($query, $parent_location_id);

		return $query->take($per_page)->get($per_page);
		
	}

	private function addOrdering($query, $parent_location_id)
	{
		$order = 'manual';

		if ($parent_location_id != 0)
		{
			$parent = $this->repository->locationById($parent_location_id);

			if ($parent)
			{
				$order = $parent->sub_location_order;
			}
		}

		if ($order == 'manual')
		{
			$query->orderBy('pagelocations.order', 'asc');
			$query->orderBy('pagelocations.id', 'asc');
		}

		if ($order == 'alpha:asc')
		{
			$query->orderByPageName('asc');
		}

		if ($order == 'alpha:desc')
		{
			$query->orderByPageName('desc');
		}

		if ($order == 'created:asc')
		{
			$query->orderByPageCreated('asc');
		}

		if ($order == 'created:desc')
		{
			$query->orderByPageCreated('desc');
		}

		return $query;
	}

}