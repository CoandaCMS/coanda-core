<?php namespace CoandaCMS\Coanda\Artisan;

use Illuminate\Console\Command;
use Coanda;

use CoandaCMS\Coanda\Pages\PublishHandlers\Delayed as DelayedPublishHandler;

/**
 * Class DelayedPublishCommand
 * @package CoandaCMS\Coanda\Artisan
 */
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

    /**
     * @var
     */
    private $pageRepository;
    /**
     * @var
     */
    private $urlRepository;
    /**
     * @var
     */
    private $historyRepository;

    /**
     * @param $app
     */
    public function __construct($app)
    {
        $this->pageRepository = $app->make('CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface');
        $this->urlRepository = $app->make('CoandaCMS\Coanda\Urls\Repositories\UrlRepositoryInterface');
        $this->historyRepository = $app->make('CoandaCMS\Coanda\History\Repositories\HistoryRepositoryInterface');

        parent::__construct();
    }

    /**
     * Run the command
     */
    public function fire()
    {        
        $this->info('Running delayed publish handler');

        DelayedPublishHandler::executeFromCommand($this, $this->pageRepository, $this->urlRepository, $this->historyRepository);
    }
}