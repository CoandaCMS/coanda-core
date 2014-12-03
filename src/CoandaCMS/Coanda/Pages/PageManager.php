<?php namespace CoandaCMS\Coanda\Pages;

use CoandaCMS\Coanda\History\Repositories\HistoryRepositoryInterface;
use CoandaCMS\Coanda\Pages\Exceptions\HomePageAlreadyExists;
use CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface;
use CoandaCMS\Coanda\Users\UserManager;
use Illuminate\Pagination\Factory;
use CoandaCMS\Coanda\Pages\Exceptions\SubPagesNotAllowed;

class PageManager {

    /**
     * @var Repositories\PageRepositoryInterface
     */
    private $repository;

    /**
     * @var Factory
     */
    private $paginator;

    /**
     * @var \CoandaCMS\Coanda\History\Repositories\HistoryRepositoryInterface
     */
    private $history;

    /**
     * @var UserManager
     */
    private $users;
    /**
     * @var
     */
    private $current_user_id;

    /**
     * @param PageRepositoryInterface $pageRepository
     * @param Factory $paginator
     * @param HistoryRepositoryInterface $historyRepository
     * @param UserManager $userManager
     * @internal param UserRepositoryInterface $userRepository
     */
    public function __construct(PageRepositoryInterface $pageRepository, Factory $paginator, HistoryRepositoryInterface $historyRepository, UserManager $userManager)
	{
		$this->repository = $pageRepository;
        $this->paginator = $paginator;
		$this->history = $historyRepository;
		$this->users = $userManager;
    }

    /**
     * @return mixed
     */
    private function getCurrentUserId()
    {
        if (!$this->current_user_id)
        {
            $this->current_user_id = $this->users->currentUser()->id;
        }

        return $this->current_user_id;
    }

    /**
     * @param $method
     * @param $parameters
     * @return mixed
     */
    private function callModuleMethod($method, $parameters)
	{
		$coanda = \App::make('coanda');

		return call_user_func_array([$coanda->pages(), $method], $parameters);
	}

    /**
     * @param $id
     * @param int $limit
     * @return mixed
     */
    private function getHistory($id, $limit = 10)
	{
		return $this->history->getPaginated('pages', $id, $limit);
	}

    /**
     * @param $what_happened
     * @param $identifier
     * @param $data
     */
    private function logHistory($what_happened, $identifier, $data)
	{
		$this->history->add('pages', $identifier, $this->getCurrentUserId(), $what_happened, $data);
	}

    /**
     * @return mixed
     */
    public function getHomePage()
	{
		return $this->repository->getHomePage();
	}

    /**
     * @param $current_page
     * @param $per_page
     * @return mixed
     */
    public function getTopLevelPages($current_page, $per_page)
	{
		return $this->getAdminSubPages(0, $current_page, $per_page);
	}

    /**
     * @param $parent_page_id
     * @param $current_page
     * @param $per_page
     * @return mixed
     */
    public function getAdminSubPages($parent_page_id, $current_page, $per_page)
	{
		return $this->repository->subPages($parent_page_id, $current_page, $per_page, [
			'include_drafts' => true,
			'include_invisible' => true,
			'include_hidden' => true
		]);
	}

    /**
     * @param $orders
     */
    public function updatePageOrders($orders)
	{
		foreach ($orders as $page_id => $new_order)
		{
			$this->repository->updatePageOrder($page_id, $new_order);
		}
	}

    /**
     * @param $page_id
     * @param $new_order
     */
    public function updateSubPageOrder($page_id, $new_order)
    {
        $this->repository->updateSubPageOrder($page_id, $new_order);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getPage($id)
	{
		return $this->repository->find($id);
	}

    /**
     * @param $ids
     * @return mixed
     */
    public function getPages($ids)
    {
        return $this->repository->findByIds($ids);
    }

    /**
     * @param $remote_id
     * @return mixed
     */
    public function getPageByRemoteId($remote_id)
    {
        return $this->repository->getByRemoteId($remote_id);
    }

    /**
     * @param $page_type
     * @return mixed
     * @throws HomePageAlreadyExists
     */
    public function createHomePage($page_type)
    {
        $home = $this->getHomePage();

        if ($home)
        {
            throw new HomePageAlreadyExists('Home Page has already been created.');
        }

        $type = $this->callModuleMethod('getHomePageType', [$page_type]);

        return $this->repository->createHome($type, $this->getCurrentUserId());
    }

    /**
     * @param $page_type
     * @param $parent_page_id
     * @return mixed
     * @throws SubPagesNotAllowed
     */
    public function createNewPage($page_type, $parent_page_id)
	{
		$type = $this->callModuleMethod('getPageType', [$page_type]);

        if ($parent_page_id)
        {
            $parent = $this->getPage($parent_page_id);

            if (!$parent->pageType()->allowsSubPages())
            {
                throw new SubPagesNotAllowed('This page type does not allow sub pages');
            }
        }

		return $this->repository->create($type, $this->getCurrentUserId(), $parent_page_id);
	}

    /**
     * @param $page_id
     * @param $base_version_number
     * @return mixed
     */
    public function createNewVersionForPage($page_id, $base_version_number)
	{
		return $this->repository->createNewVersion($page_id, $this->getCurrentUserId(), $base_version_number);
	}

    /**
     * @param $page_id
     * @param $version_number
     * @return mixed
     */
    public function getDraftVersionForPage($page_id, $version_number)
	{
		return $this->repository->getDraftVersion($page_id, $version_number);
	}

    /**
     * @param $page_id
     * @param $version_number
     */
    public function removeDraftVersion($page_id, $version_number)
	{
		$version = $this->getDraftVersionForPage($page_id, $version_number);

		$this->logHistory('discard_version', $version->page->id, ['version' => $version->version]);

		$this->repository->discardDraftVersion($version, $this->getCurrentUserId());
	}

    /**
     * @param $page_id
     * @param $version_number
     * @param $input
     */
    public function saveDraftVersion($page_id, $version_number, $input)
	{
		$version = $this->getDraftVersionForPage($page_id, $version_number);

		$this->repository->saveDraftVersion($version, $input);
	}

    /**
     * @return mixed
     */
    public function getTrashedPages()
    {
        return $this->repository->trashed();
    }

    /**
     * @return mixed
     */
    public function getTrashedCount()
    {
        return $this->repository->trashedCount();
    }

    /**
     * @param $page_id
     * @param bool $restore_sub_pages
     */
    public function restorePage($page_id, $restore_sub_pages = false)
    {
        $this->repository->restore($page_id, $restore_sub_pages);
    }

    /**
     * @param $id
     * @param bool $permanent
     * @return mixed
     */
    public function deletePage($id, $permanent = false)
    {
        return $this->repository->deletePage($id, $permanent);
    }

    /**
     * @param $ids
     * @param bool $permanent
     * @return mixed
     */
    public function deletePages($ids, $permanent = false)
	{
		return $this->repository->deletePages($ids, $permanent);
	}

    /**
     * @param $id
     * @param int $limit
     * @return mixed
     */
    public function pageHistory($id, $limit = 10)
	{
		return $this->getHistory($id, $limit);
	}

    /**
     * @param $id
     * @return mixed
     */
    public function pageContributors($id)
	{
		return $this->history->users('pages', $id);
	}

    /**
     * @param $page_id
     * @param bool $user_id
     * @return mixed
     */
    public function draftsForUser($page_id, $user_id = false)
	{
		if (!$user_id)
		{
			$user_id = $this->getCurrentUserId();
		}

		return $this->repository->draftsForUser($page_id, $user_id);
	}

    /**
     * @param $page_id
     * @param $per_page
     * @param $current_page
     * @param string $page_variable
     * @return \Illuminate\Pagination\Paginator
     */
    public function getVersionsForPagePaginated($page_id, $per_page, $current_page, $page_variable = 'versions_page')
    {
        $offset = ($current_page - 1) * $per_page;
        $versions = $this->repository->getVersionsForPage($page_id, $per_page, $offset);

        $version_array = [];

        foreach ($versions as $version)
        {
            $version_array[] = $version;
        }

        $version_count = $this->repository->getVersionCountForPage($page_id);

        $paginator = clone $this->paginator;

        $paginator->setPageName($page_variable);
        $results = $paginator->make($version_array, $version_count, $per_page);

        return $results;
    }

    /**
     * @param $page_id
     * @return mixed
     */
    public function getVersionCountForPage($page_id)
    {
        return $this->repository->getVersionCountForPage($page_id);
    }

    /**
     * @param $version
     * @param $publish_handler
     * @param $data
     * @return mixed
     */
    public function executePublishHandler($version, $publish_handler, $data)
    {
        $publish_handler = $this->callModuleMethod('getPublishHandler', [$publish_handler]);

        if ($publish_handler)
        {
            return $this->repository->executePublishHandler($version, $publish_handler, $data);
        }
    }
}

