<?php namespace CoandaCMS\Coanda\Pages\Commands;

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
        $limit = 5;

        while (true)
        {
            $locations = $this->pageRepository->locations($limit, $offset);

            if ($locations->count() == 0)
            {
                $this->info('All done.');

                return;
            }

            foreach ($locations as $location)
            {
                if ($location->page)
                {                
                    if ($location->page->is_trashed)
                    {
                        $this->info('Remove from index location: #' . $location->id);    
                        $this->pageRepository->unRegisterLocationWithSearchProvider($location);
                    }
                    else
                    {
                        $this->info('Indexing location: #' . $location->id);
                        $this->pageRepository->registerLocationWithSearchProvider($location);
                    }
                }
            }

            $offset += $limit;
        }

    }
}