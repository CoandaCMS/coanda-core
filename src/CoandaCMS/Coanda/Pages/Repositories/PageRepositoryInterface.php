<?php namespace CoandaCMS\Coanda\Pages\Repositories;

/**
 * Interface PageRepositoryInterface
 * @package CoandaCMS\Coanda\Pages\Repositories
 */
interface PageRepositoryInterface {

    /**
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * @param $id
     * @return mixed
     */
    public function findById($id);

    public function locationById($id);

    /**
     * @param $ids
     * @return mixed
     */
    public function findByIds($ids);

    /**
     * @param $per_page
     * @return mixed
     */
    public function topLevel($per_page);

    /**
     * @param $page_id
     * @param $per_page
     * @return mixed
     */
    public function subPages($page_id, $per_page);

    /**
     * @param $type
     * @param $user_id
     * @param $parent_page_id
     * @return mixed
     */
    public function create($type, $user_id, $parent_page_id);

    public function createHome($type, $user_id);
    
    /**
     * @param $page_id
     * @param $version
     * @return mixed
     */
    public function getDraftVersion($page_id, $version);

    /**
     * @param $id
     * @return mixed
     */
    public function getVersionById($id);
    /**
     * @param $preview_key
     * @return mixed
     */
    public function getVersionByPreviewKey($preview_key);

    /**
     * @param $version
     * @param $data
     * @return mixed
     */
    public function saveDraftVersion($version, $data);

    public function addNewVersionSlug($version_id, $page_location_id);

    /**
     * @param $version
     * @return mixed
     */
    public function discardDraftVersion($version);

    /**
     * @param $page_id
     * @param $user_id
     * @return mixed
     */
    public function draftsForUser($page_id, $user_id);

    /**
     * @param $version
     * @param $user_id
     * @param $urlRepository
     * @param $historyRepository
     * @return mixed
     */
    public function publishVersion($version, $user_id, $urlRepository, $historyRepository);

    /**
     * @param $version
     * @return mixed
     */
    public function executePublishHandler($version, $publish_handler, $data);

    /**
     * @param $page_id
     * @param $user_id
     * @return mixed
     */
    public function createNewVersion($page_id, $user_id);

    /**
     * @param $page_id
     * @return mixed
     */
    public function recentHistory($page_id);

    /**
     * @param $page_id
     * @return mixed
     */
    public function history($page_id);

    /**
     * @param $page_id
     * @return mixed
     */
    public function contributors($page_id);

    /**
     * @param $page_id
     * @param bool $permanent
     * @return mixed
     */
    public function deletePage($page_id, $permanent = false);

    /**
     * @param $pages_ids
     * @param bool $permanent
     * @return mixed
     */
    public function deletePages($pages_ids, $permanent = false);

    /**
     * @return mixed
     */
    public function trashed();

    /**
     * @param $page_id
     * @param $restore_sub_pages
     * @return mixed
     */
    public function restore($page_id, $restore_sub_pages);

    /**
     * @param $new_orders
     * @return mixed
     */
    public function updateOrdering($new_orders);

    /**
     * @param $offset
     * @param $limit
     * @return mixed
     */
    public function getPendingVersions($offset, $limit);

    public function getHomePage();
}
