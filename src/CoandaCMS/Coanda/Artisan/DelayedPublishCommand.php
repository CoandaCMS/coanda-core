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
     * Run the command
     */
    public function fire()
    {        
        $pageRepository = \App::make('CoandaCMS\Coanda\Pages\Repositories\PageRepositoryInterface');
        $urlRepository = \App::make('CoandaCMS\Coanda\Urls\Repositories\UrlRepositoryInterface');

        DelayedPublishHandler::executeFromCommand($this, $pageRepository, $urlRepository);
    }
}