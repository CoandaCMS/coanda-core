<?php namespace CoandaCMS\Coanda\Pages;

class PageManager {

	private $repository;

    public function __construct(\CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface $pageRepository)
	{
		$this->repository = $pageRepository;
	}

	public function getHomePage()
	{
		return $this->repository->getHomePage();
	}

	public function getLocation($id)
	{
		return $this->repository->locationById($id);
	}

	public function getTopLevelPages($current_page, $per_page)
	{
		return $this->getAdminSubLocations(0, $current_page, $per_page);
	}

	public function getAdminSubLocations($parent_location_id, $current_page, $per_page)
	{
		// Include the parameters to see drafts, invisible and hidden pages
		$parameters = [
			'include_drafts' => true,
			'include_invisible' => true,
			'include_hidden' => true
		];

		return $this->repository->subPages($parent_location_id, $current_page, $per_page, $parameters);
	}

}
