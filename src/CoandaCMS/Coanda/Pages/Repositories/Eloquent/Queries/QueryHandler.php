<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent\Queries;

class QueryHandler {

	private $repository;

	public function __construct(\CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface $repository)
	{
		$this->repository = $repository;
	}

	public function subLocations($parameters)
	{
		$query = new \CoandaCMS\Coanda\Pages\Repositories\Eloquent\Queries\SubPageQuery($this->repository);

		return $query->execute($parameters);
	}

}