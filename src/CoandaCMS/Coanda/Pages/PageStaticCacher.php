<?php namespace CoandaCMS\Coanda\Pages;

use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Http\Request;

class PageStaticCacher {

    /**
     * @var bool
     */
    private $enabled = false;
    /**
     * @var CacheRepository
     */
    private $cache;
    /**
     * @var Request
     */
    private $request;
    /**
     * @var ConfigRepository
     */
    private $config;

    public function __construct(CacheRepository $cache, Request $request, ConfigRepository $config)
    {
        $this->cache = $cache;
        $this->request = $request;
        $this->config = $config;

        $this->checkIfEnabled();
    }

    /**
     * @param $page_id
     * @return bool
     */
    public function hasPageCache($page_id)
    {
        return $this->has($this->generatePageCacheKey($page_id));
    }

    /**
     * @param $page_id
     * @return mixed
     */
    public function getPageCache($page_id)
    {
        return $this->get($this->generatePageCacheKey($page_id));
    }

    /**
     * @param $page_id
     * @param $content
     */
    public function putPageCache($page_id, $content)
    {
        $this->put($this->generatePageCacheKey($page_id), $content);
    }

    /**
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        return $this->enabled ? $this->cache->has($key) : false;
    }

    /**
     * @return mixed
     */
    public function get($key)
    {
        return $this->enabled ? $this->cache->get($key) : false;
    }

    /**
     * @param $content
     * @return mixed
     */
    public function put($key, $content)
    {
        if ($this->enabled)
        {
            return $this->cache->put($key, $content, $this->getCacheLifetime());
        }
    }

    /**
     *
     */
    private function checkIfEnabled()
    {
        $this->enabled = $this->config->get('coanda::coanda.page_cache_enabled');
    }

    /**
     *
     */
    private function getCacheLifetime()
    {
        return $this->config->get('coanda::coanda.page_cache_lifetime');
    }

    /**
     * @param $page_id
     * @return string
     */
    private function generatePageCacheKey($page_id)
    {
        return 'page-' . $page_id . '-' . md5(var_export($this->getInput(), true));
    }

    /**
     * @return mixed
     */
    private function getInput()
    {
        $all_input = $this->request->all();

        // If we are viewing ?page=1 - then this is cached the same as without it...
        if (isset($all_input['page']) && $all_input['page'] == 1)
        {
            unset($all_input['page']);
        }

        return $all_input;
    }
}
