<?php namespace CoandaCMS\Coanda\Controllers\Admin;

use View, App, Coanda, Redirect, Input, Session, Response;

use CoandaCMS\Coanda\Controllers\BaseController;

class HistoryAdminController extends BaseController {

    private $repository;

    public function __construct(\CoandaCMS\Coanda\History\Repositories\HistoryRepositoryInterface $repository)
    {
        $this->repository = $repository;

        $this->beforeFilter('csrf', ['on' => 'post']);
    }

    public function getIndex()
    {
        Coanda::checkAccess('history', 'view');

        $summary_figures = $this->repository->getActivitySummaryFigures();

        $from_date = Input::get('from', false);

        try
        {
            $from = \Carbon\Carbon::createFromFormat('d/m/Y', $from_date);
        }
        catch (\InvalidArgumentException $exception)
        {
            $from = \Carbon\Carbon::today();
        }

        $to_date = Input::get('to', false);

        try
        {
            $to = \Carbon\Carbon::createFromFormat('d/m/Y', $to_date);
        }
        catch (\InvalidArgumentException $exception)
        {
            $to = \Carbon\Carbon::today();
        }

        $history_list = $this->repository->getAllPaginatedByTimePeriod($from->format('y-m-d') . ' 00:00:00', $to->format('y-m-d') . ' 23:59:59');

        $view_data = [
            'summary_figures' => $summary_figures,
            'history_list' => $history_list,
            'from' => $from,
            'to' => $to
        ];

        return View::make('coanda::admin.modules.history.index', $view_data);
    }

    public function getAll()
    {
        Coanda::checkAccess('history', 'view');

        $history_list = $this->repository->getAllPaginated(50);

        return View::make('coanda::admin.modules.history.all', [ 'history_list' => $history_list ]);
    }

    public function getDigest()
    {
        if (!\Config::get('coanda::coanda.daily_digest_enabled'))
        {
            \App::abort('404');
        }

        Coanda::checkAccess('history', 'view');

        $subscribed = $this->repository->getDigestSubscriber(Coanda::currentUserId());;

        return View::make('coanda::admin.modules.history.digest', [ 'subscribed' => $subscribed ]);
    }

    public function postDigest()
    {
        if (!\Config::get('coanda::coanda.daily_digest_enabled'))
        {
            \App::abort('404');
        }

        Coanda::checkAccess('history', 'view');

        if (Input::get('subscribe', false) == 'true')
        {
            $this->repository->addDigestSubscriber(Coanda::currentUserId());
        }

        if (Input::get('unsubscribe', false) == 'true')
        {
            $this->repository->removeDigestSubscriber(Coanda::currentUserId());
        }

        return Redirect::to(Coanda::adminUrl('history/digest'));
    }
}