<?php namespace CoandaCMS\Coanda\Pages\Artisan;

use Illuminate\Console\Command;
use Coanda;

class Reindex extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'coanda:reindex';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reindex all the current pages';

    /**
     * @var
     */
    private $pageRepository;

    /**
     * @param $app
     */
    public function __construct($app)
    {
        $this->pageRepository = $app->make('CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface');

        parent::__construct();
    }

    /**
     * Run the command
     */
    public function fire()
    {
        $go = $this->ask('Are you sure you want to reindex all pages? Y/N (default: N)');

        if ($go !== 'Y')
        {
            return;
        }

        $offset = 0;
        $limit = 50;

        while (true)
        {
            $pages = $this->pageRepository->get($limit, $offset);

            if ($pages->count() == 0)
            {
                $this->info('All done.');

                return;
            }

            foreach ($pages as $page)
            {
                if ($page->currentVersion()->is_hidden || $page->currentVersion()->is_hidden_navigation || $page->is_trashed || $page->status !== 'published')
                {
                    $this->info('Remove from index page: #' . $page->id);
                    $this->pageRepository->unRegisterPageWithSearchProvider($page);
                }
                else
                {
                    $this->info('Indexing page: #' . $page->id);
                    $this->pageRepository->registerPageWithSearchProvider($page);
                }
            }

            $offset += $limit;
        }

    }
}