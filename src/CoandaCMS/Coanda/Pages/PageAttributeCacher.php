<?php namespace CoandaCMS\Coanda\Pages;

class PageAttributeCacher
{
    /**
     * @var bool
     */
    private $enabled = false;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var \Illuminate\Config\Repository
     */
    private $config;

    /**
     * @param \Illuminate\Cache\Repository $cache
     * @param \Illuminate\Config\Repository $config
     */
    public function __construct(\Illuminate\Cache\Repository $cache, \Illuminate\Config\Repository $config)
    {
        $this->cache = $cache;
        $this->config = $config;

        $this->checkIfEnabled();
    }

    /**
     * @param $page_id
     * @param $version
     * @param bool $location_id
     * @return bool|mixed
     */
    public function get($page_id, $version, $location_id = false)
    {
        return false;

        $key = $this->generateCacheKey($page_id, $version, $location_id);

        if ($this->cache->has($key))
        {
            return $this->cache->get($key);
        }

        return false;
    }

    /**
     * @param $attributes
     * @param $page_id
     * @param $version
     * @param bool $location_id
     */
    public function put($attributes, $page_id, $version, $location_id = false)
    {
        $key = $this->generateCacheKey($page_id, $version, $location_id);
        $this->cache->put($key, $attributes, 5);
    }

    /**
     * @param $page_id
     * @param $version
     * @param $location_id
     * @return string
     */
    private function generateCacheKey($page_id, $version, $location_id)
    {
        return 'attributes_' . $page_id . '_' . $version . ($location_id ? '_' . $location_id : '');
    }

    /**
     *
     */
    private function checkIfEnabled()
    {
        $this->enabled = $this->config->get('coanda::coanda.attribute_cache_enabled');
    }

}

