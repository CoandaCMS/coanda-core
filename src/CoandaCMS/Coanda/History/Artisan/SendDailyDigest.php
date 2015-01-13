<?php namespace CoandaCMS\Coanda\History\Artisan;

use Illuminate\Console\Command;
use Coanda;

class SendDailyDigest extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'coanda:historydigest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the daily digest emails.';

    /**
     * @var
     */
    private $history_repository;

    /**
     * @param $app
     */
    public function __construct($app)
    {
        $this->history_repository = $app->make('CoandaCMS\Coanda\History\Repositories\HistoryRepositoryInterface');

        parent::__construct();
    }

    /**
     * Run the command
     */
    public function fire()
    {
        if (!\Config::get('coanda::coanda.daily_digest_enabled'))
        {
            $this->error('Daily digest emails are disabled.');
            return;
        }

        $offset = 0;
        $limit = 50;

        $from = \Carbon\Carbon::yesterday()->subDays(20);
        $to = \Carbon\Carbon::yesterday();

        $digest_data = [
            'summary_figures' => $this->history_repository->getDigestSummaryFigures(),
            'history_list' => $this->history_repository->getAllPaginatedByTimePeriod($from->format('y-m-d') . ' 00:00:00', $to->format('y-m-d') . ' 23:59:59', 20),
            'from' => $from,
            'to' => $to
        ];

        while (true)
        {
            $subscribers = $this->history_repository->getDigestSubscribers($limit, $offset);

            if ($subscribers->count() == 0)
            {
                $this->info('All done.');

                return;
            }

            foreach ($subscribers as $subscriber)
            {
                if ($subscriber->email)
                {
                    $email = $subscriber->email;

                    \Mail::send('coanda::admin.modules.history.emails.dailydigest', $digest_data, function($message) use ($email) {

                        $message->subject(\Config::get('coanda::coanda.daily_digest_subject'));
                        $message->from(\Config::get('coanda::coanda.site_admin_email'), \Config::get('coanda::coanda.site_name'));
                        $message->to($email);

                    });

                    $this->info('Sent digest email to: ' . $email);
                }
            }

            $offset += $limit;
        }
    }
}