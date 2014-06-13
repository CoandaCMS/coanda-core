<?php namespace CoandaCMS\Coanda\Media\Repositories;

/**
 * Interface MediaRepositoryInterface
 * @package CoandaCMS\Coanda\Media\Repositories
 */
interface MediaRepositoryInterface {

    /**
     * @param $id
     * @return mixed
     */
    public function findById($id);

    /**
     * @param $ids
     * @return mixed
     */
    public function findByIds($ids);

    /**
     * @param $per_page
     * @return mixed
     */
    public function getList($per_page);

    /**
     * @param $type
     * @param $per_page
     * @return mixed
     */
    public function getListByType($type, $per_page);

    /**
     * @param $file
     * @return mixed
     */
    public function handleUpload($file, $module_identifier);

    /**
     * @param $url
     * @return mixed
     */
    public function fromURL($url);

    /**
     * @param $media_id
     * @return mixed
     */
    public function removeById($media_id);

    /**
     * @param $per_page
     * @return mixed
     */
    public function tags($per_page);

    /**
     * @param $media_id
     * @param $tag_name
     * @return mixed
     */
    public function tagMedia($media_id, $tag_name);

    /**
     * @param $media_id
     * @param $tag_id
     * @return mixed
     */
    public function removeTag($media_id, $tag_id);

    /**
     * @param $media_id
     * @return mixed
     */
    public function getTags($media_id);

    /**
     * @param $limit
     * @return mixed
     */
    public function recentTagList($limit);

    /**
     * @param $tag_id
     * @param $per_page
     * @return mixed
     */
    public function forTag($tag_id, $per_page);

    /**
     * @return mixed
     */
    public function maxFileSize();

}
