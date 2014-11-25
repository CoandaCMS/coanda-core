<?php namespace CoandaCMS\Coanda\Controllers;

use CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface;
use View, Redirect, App, Coanda, Input, Session;

use CoandaCMS\Coanda\Pages\Exceptions\PageVersionNotFound;
use CoandaCMS\Coanda\Exceptions\ValidationException;
/**
 * Class PagesController
 * @package CoandaCMS\Coanda\Controllers
 */
class PagesController extends BaseController {

    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @param PageRepositoryInterface $pageRepository
     */
    public function __construct(PageRepositoryInterface $pageRepository)
	{
		$this->pageRepository = $pageRepository;
	}

    /**
     * @param $preview_key
     * @return mixed
     */
    public function getPreview($preview_key)
	{
		try
		{
			$version = $this->pageRepository->getVersionByPreviewKey($preview_key);
			$invalid_fields = Session::has('invalid_fields') ? Session::get('invalid_fields') : [];

			return View::make('coanda::pages.preview', [ 'version' => $version, 'preview_key' => $preview_key, 'invalid_fields' => $invalid_fields ]);
		}
		catch (PageVersionNotFound $exception)
		{
			return App::abort('404');
		}
	}

	public function getRenderPreview($preview_key)
	{
		try
		{
			$version = $this->pageRepository->getVersionByPreviewKey($preview_key);

            $page = $version->page;

            $meta_title = $version->meta_page_title;

            $meta = [
                'title' => $meta_title !== '' ? $meta_title : $version->name,
                'description' => $version->meta_description
            ];

            $attributes = new \stdClass;

            foreach ($version->attributes as $attribute)
            {
                $attributes->{$attribute->identifier} = $attribute->render($page);
            }

            $breadcrumb = $page->breadcrumb();

            // We need to take the last item off and replace it with the version name...
            array_pop($breadcrumb);

            $breadcrumb[] = [
                'url' => false,
                'identifier' => 'pages:page-' . $page->id,
                'name' => $version->name
            ];

            $data = [
                'page' => $page,
                'page_id' => $page->id,
                'meta' => $meta,
                'attributes' => $attributes
            ];

            $data = $page->pageType()->preRender($data);

            // Make the view and pass all the render data to it...
            $rendered_version = View::make($page->pageType()->template($version, $data), $data);

            // Get the layout template...
            $layout = $this->getLayout($version);

            // Give the layout the rendered page and the data, and it can work some magic to give us back a complete page...
            $layout_data = [
                'layout' => $layout,
                'content' => $rendered_version,
                'meta' => $meta,
                'page_data' => $data,
                'breadcrumb' => $breadcrumb,
                'module' => 'pages',
                'module_identifier' => $page->id . ':' . $version->version
            ];

            return View::make($layout->template(), $layout_data)->render();
		}
		catch (PageVersionNotFound $exception)
		{
			return App::abort('404');
		}
	}

	public function postPreviewComment($preview_key)
	{
		try
		{
			$version = $this->pageRepository->getVersionByPreviewKey($preview_key);

			$this->pageRepository->addVersionComment($version, Input::all());

			return Redirect::to('pages/preview/' . $preview_key)->with('comment_saved', true);
		}
		catch (PageVersionNotFound $exception)
		{
			return App::abort('404');
		}
		catch (ValidationException $exception)
		{
			return Redirect::to('pages/preview/' . $preview_key)->with('invalid_fields', $exception->getInvalidFields())->withInput();
		}
	}

    private function getLayout($version)
    {
        if ($version->layout_identifier)
        {
            $layout = Coanda::layout()->layoutByIdentifier($version->layout_identifier);

            if ($layout)
            {
                return $layout;
            }
        }

        $page_type_layout = $version->page->pageType()->defaultLayout();

        if ($page_type_layout)
        {
            $layout = Coanda::layout()->layoutByIdentifier($page_type_layout);

            if ($layout)
            {
                return $layout;
            }
        }

        return Coanda::module('layout')->defaultLayout();
    }

}