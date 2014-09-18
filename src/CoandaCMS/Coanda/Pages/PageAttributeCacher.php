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

    public function __construct(\Illuminate\Cache\Repository $cache, \Illuminate\Config\Repository $config)
    {
        $this->cache = $cache;
        $this->config = $config;

        $this->checkIfEnabled();
    }

    public function get($page_id, $version, $location_id = false)
    {
        $key = $this->generateCacheKey($page_id, $version, $location_id);

        if ($this->cache->has($key))
        {
            return $this->cache->get($key);
        }

        return false;
    }

    public function put($attributes, $page_id, $version, $location_id = false)
    {
        $key = $this->generateCacheKey($page_id, $version, $location_id);
        $this->cache->put($key, $attributes, 5);
    }

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

