<?php namespace CoandaCMS\Coanda\Pages\Renderer;

use Cache;
use Config;
use Input;

class PageCacher {

    private $page;
    private $location;
    private $cache_key = false;
    private $enabled = false;
    private $lifetime = 10;

    public function __construct($page, $location)
    {
        $this->page = $page;
        $this->location = $location;

        $this->checkIfEnabled();
        $this->getCacheLifetime();

        $this->generateCacheKey();
    }

    public function get()
    {
        if ($this->enabled && $this->cache_key && Cache::has($this->cache_key))
        {
            return Cache::get($this->cache_key);
        }
    }

    public function put($content)
    {
        if ($this->enabled && $this->cache_key)
        {
            return Cache::put($this->cache_key, $content, $this->lifetime);
        }
    }

    private function checkIfEnabled()
    {
        $this->enabled = Config::get('coanda::coanda.page_cache_enabled') && $this->page->pageType()->canStaticCache();
    }

    private function getCacheLifetime()
    {
        $this->lifetime = Config::get('coanda::coanda.page_cache_lifetime');
    }

    private function generateCacheKey()
    {
        if ($this->location)
        {
            $this->cache_key = 'location-' . $this->location->id . '-' . md5(var_export($this->getInput(), true));
        }
    }

    private function getInput()
    {
        $all_input = \Input::all();

        // If we are viewing ?page=1 - then this is cached the same as without it...
        if (isset($all_input['page']) && $all_input['page'] == 1)
        {
            unset($all_input['page']);
        }

        return $all_input;
    }
}
