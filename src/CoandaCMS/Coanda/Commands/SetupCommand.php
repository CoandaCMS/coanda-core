<?php namespace CoandaCMS\Coanda\Commands;

use Illuminate\Console\Command;
use Coanda;

class SetupCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'coanda:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets up the coanda CMS system';

    private $pageRepository;

    public function __construct($app)
    {
        $this->pageRepository = $app->make('CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface');

        parent::__construct();
    }

    /**
     * Compile and generate the file
     */
    public function fire()
    {
        $this->info('--------------------------');
        $this->info('Welcome to CoandaCMS setup');
        $this->info('--------------------------');

        // $admin_group_name = $this->ask('Please specify the name for the main administrators group? (default: Administrators)');

        // create the main admin group....

        // $admin_email = $this->ask('Please specify email address for the first main admin account?');
        // $admin_password = $this->ask('Please specify password for the first main admin account?');

        // create the admin user

        $seed = $this->ask('Would you like to seed the system with demo pages (Y/N)? (default: N)');

        if ($seed == 'Y')
        {
            $this->seed();
        }
    }

    private function seed()
    {
        $this->createSubTree(0);
    }

    private function createSubTree($parent_page_id)
    {
        $page_type = 'page';

        try
        {
            $type = Coanda::module('pages')->getPageType($page_type);

            foreach (range(0, rand(2, 20)) as $index)
            {
                $page = $this->pageRepository->create($type, 1, $parent_page_id);

                $version = $this->pageRepository->getDraftVersion($page->id, 1);

                $dummy_data = [];

                foreach ($version->attributes as $attribute)
                {
                    $dummy_data['attribute_' . $attribute->id] = 'Page name: ' . $index;
                }

                echo '.';

                $dummy_data['slug'] = 'page-' . $index;
                $dummy_data['meta_page_title'] = 'Page #' . $index;
                $dummy_data['meta_description'] = 'Page #' . $index;

                $this->pageRepository->saveDraftVersion($version, $dummy_data);
                $this->pageRepository->publishVersion($version);

                if ($page->depth < 4)
                {
                    $this->createSubTree($page->id);                    
                }
            }
        }
        catch (PageTypeNotFound $exception)
        {
            $this->error('Could not find page type: ' . $page_type);
        }
    }
}