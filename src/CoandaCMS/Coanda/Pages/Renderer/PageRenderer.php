<?php namespace CoandaCMS\Coanda\Pages\Renderer;

use Coanda;
use CoandaCMS\Coanda\Pages\PageStaticCacher;
use CoandaCMS\Coanda\Pages\PageManager;

class PageRenderer {

    /**
     * @var
     */
    private $page;
    /**
     * @var
     */
    private $location;
    /**
     * @var
     */
    private $meta;
    /**
     * @var
     */
    private $data;
    /**
     * @var
     */
    private $template;
    /**
     * @var
     */
    private $layout;
    /**
     * @var
     */
    private $cacher;
    /**
     * @var PageManager
     */
    private $manager;
    /**
     * @var \Illuminate\View\Factory
     */
    private $view;

    /**
     * @param PageStaticCacher $cacher
     * @param PageManager $manager
     */
    public function __construct(PageStaticCacher $cacher, PageManager $manager, \Illuminate\View\Factory $view)
    {
        $this->cacher = $cacher;
        $this->manager = $manager;
        $this->view = $view;
    }

    public function renderHomePage()
    {
        $home_page = $this->manager->getHomePage();

        if ($home_page)
        {
            $this->page = $home_page;

            return $this->render();
        }

        throw new \Exception('Home page not created yet!');

    }

    public function renderLocation($location_id)
    {
        // Does the cache have this location?
        if ($this->cacher->hasLocationCache($location_id))
        {
            return $this->cacher->getLocationCache($location_id);
        }

        $this->location = $this->manager->getLocation($location_id);
        $this->page = $this->location->page;

        $content = $this->render();

        if ($this->canStaticCache())
        {
            $this->cacher->putLocationCache($this->location->id, $content);
        }

        return $content;
    }

    /**
     * @return mixed
     */
    public function render()
    {
        $this->checkPageStatus();
        $this->buildMeta();
        $this->buildPageData();
        $this->preRender();

        if ($this->checkForRedirect())
        {
            return $this->data;
        }

        $this->getTemplate();

        return $this->mergeWithLayout($this->renderPage());
    }

    /**
     * @return mixed
     */
    private function renderPage()
    {
        return $this->view->make($this->template, $this->data);
    }

    private function canStaticCache()
    {
        return $this->page->pageType()->canStaticCache();
    }

    /**
     *
     */
    private function getTemplate()
    {
        $this->template = $this->page->pageType()->template($this->page->currentVersion(), $this->data);
    }

    /**
     *
     */
    private function preRender()
    {
        // Does the page type want to do anything before we carry on with the rendering?
        // e.g. Redirect, set some additional data variables
        $this->data = $this->page->pageType()->preRender($this->data);
    }

    /**
     * @return bool
     */
    private function checkForRedirect()
    {
        // Lets check if we got a redirect request back...
        if (is_object($this->data) && get_class($this->data) == 'Illuminate\Http\RedirectResponse')
        {
            return true;
        }

        return false;
    }

    /**
     * @param $rendered_content
     * @return mixed
     */
    private function mergeWithLayout($rendered_content)
    {
        // Get the layout template...
        $this->getLayout($this->page->currentVersion());

        // Give the layout the rendered page and the data, and it can work some magic to give us back a complete page...
        return $this->layout->render([
                'layout' => $this->layout,
                'content' => $rendered_content,
                'meta' => $this->meta,
                'breadcrumb' => ($this->location ? $this->location->breadcrumb() : []),
                'module' => 'pages',
                'module_identifier' => $this->page->id
            ]);
    }

    /**
     * @param $version
     */
    private function getLayout($version)
    {
        $possible_layouts = [
            $version->layout_identifier,
            $version->page->pageType()->defaultLayout(),
        ];

        foreach ($possible_layouts as $possible_layout)
        {
            $this->layout = Coanda::layout()->layoutByIdentifier($possible_layout);

            if ($this->layout)
            {
                break;
            }
        }

        if (!$this->layout)
        {
            $this->layout = Coanda::module('layout')->defaultLayout();
        }
    }

    /**
     *
     */
    private function checkPageStatus()
    {
        if ($this->page->is_trashed || !$this->page->is_visible || $this->page->is_hidden)
        {
            App::abort('404');
        }
    }

    /**
     * @return mixed
     */
    private function renderAttributes()
    {
        return $this->page->renderAttributes($this->location);
    }

    /**
     *
     */
    private function buildPageData()
    {
        $this->data = [
            'page_id' => $this->page->id,
            'version' => $this->page->current_version,
            'location_id' => ($this->location ? $this->location->id : false),
            'parent' => ($this->location ? $this->location->parent : false),
            'page' => $this->page,
            'attributes' => $this->renderAttributes(),
            'meta' => $this->meta,
            'slug' => $this->location ? $this->location->slug : '',
        ];
    }

    /**
     *
     */
    private function buildMeta()
    {
        $meta_title = $this->page->meta_page_title;

        $this->meta = [
            'title' => $meta_title !== '' ? $meta_title : $this->page->present()->name,
            'description' => $this->page->meta_description
        ];
    }
}