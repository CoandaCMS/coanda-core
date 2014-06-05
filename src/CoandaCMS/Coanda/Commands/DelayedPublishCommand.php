<?php namespace CoandaCMS\Coanda\Commands;

use Illuminate\Console\Command;
use Coanda;

use CoandaCMS\Coanda\Pages\PublishHandlers\Delayed as DelayedPublishHandler;

/**
 * Class DelayedPublishCommand
 * @package CoandaCMS\Coanda\Commands
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
    private $pageFactory;
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
        $this->pageFactory = $app->make('CoandaCMS\Coanda\Pages\Factory\PageFactoryInterface');
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

        DelayedPublishHandler::executeFromCommand($this, $this->pageFactory, $this->urlRepository, $this->historyRepository);
    }
}