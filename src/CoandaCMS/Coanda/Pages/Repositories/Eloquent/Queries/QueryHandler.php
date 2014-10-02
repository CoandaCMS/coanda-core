<?php namespace CoandaCMS\Coanda\Pages\Repositories\Eloquent\Queries;

class QueryHandler {

    /**
     * @var \CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface
     */
    private $repository;

    /**
     * @param \CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface $repository
     */
    public function __construct(\CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface $repository)
	{
		$this->repository = $repository;
	}

    /**
     * @param $parameters
     * @return mixed
     */
    public function subLocations($parameters)
	{
		$query = new \CoandaCMS\Coanda\Pages\Repositories\Eloquent\Queries\SubPageQuery($this->repository);

		return $query->execute($parameters);
	}

}