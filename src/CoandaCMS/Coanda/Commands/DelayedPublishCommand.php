<?php namespace CoandaCMS\Coanda\Commands;

use Illuminate\Console\Command;
use Coanda;

class DelayedPublishCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'coanda:delayed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks to see if any delayed/pending versions need to be published';

    private $pageRepository;
    private $urlRepository;
    private $historyRepository;

    public function __construct($app)
    {
        $this->pageRepository = $app->make('CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface');
        $this->urlRepository = $app->make('CoandaCMS\Coanda\Urls\Repositories\UrlRepositoryInterface');
        $this->historyRepository = $app->make('CoandaCMS\Coanda\History\Repositories\HistoryRepositoryInterface');

        parent::__construct();
    }

    /**
     * Compile and generate the file
     */
    public function fire()
    {        
        $this->info('Running delayed publish handler');

        \CoandaCMS\Coanda\Pages\PublishHandlers\Delayed::executeFromCommand($this, $this->pageRepository, $this->urlRepository, $this->historyRepository);
    }
}