@extends('coanda::admin.layout.main')

@section('page_title', 'History')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('history') }}">History</a></li>
			<li>Overview</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">History</h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12">
		<a href="{{ Coanda::adminUrl('history/all') }}" class="btn btn-primary">View all history</a>

		@if (Config::get('coanda::coanda.daily_digest_enabled'))
			<a href="{{ Coanda::adminUrl('history/digest') }}" class="btn btn-default">Digest</a>
		@endif
	</div>
</div>

<div class="row">
	<div class="col-md-12">

	    <div class="row summary-figures">
	        <div class="col-xs-6 col-sm-3 figure">
	            <div class="well well-sm">
                    <div class="big-figure">{{ $summary_figures['today'] }}</div>

                    @set('today_from', Carbon\Carbon::today()->format('d/m/Y'))
                    @set('today_to', Carbon\Carbon::today()->format('d/m/Y'))
                    <a href="{{ Coanda::adminUrl('history?from=' . $today_from . '&to=' . $today_to) }}"><span class="text">Today</span></a>
	            </div>
	        </div>
	        <div class="col-xs-6 col-sm-3 figure">
    	        <div class="well well-sm">
                    <div class="big-figure">{{ $summary_figures['week'] }}</div>

                    @set('week_from', Carbon\Carbon::today()->startOfWeek()->format('d/m/Y'))
                    @set('week_to', Carbon\Carbon::today()->format('d/m/Y'))
                    <a href="{{ Coanda::adminUrl('history?from=' . $week_from . '&to=' . $week_to) }}"><span class="text">This week</span></a>
                </div>
	        </div>
	        <div class="col-xs-6 col-sm-3 figure">
                <div class="well well-sm">
                    <div class="big-figure">{{ $summary_figures['month'] }}</div>

                    @set('month_from', Carbon\Carbon::today()->startOfMonth()->format('d/m/Y'))
                    @set('month_to', Carbon\Carbon::today()->format('d/m/Y'))
                    <a href="{{ Coanda::adminUrl('history?from=' . $month_from . '&to=' . $month_to) }}"><span class="text">This month</span></a>
                </div>
	        </div>
	        <div class="col-xs-6 col-sm-3 figure">
	            <div class="well well-sm">
                    <div class="big-figure">{{ $summary_figures['year'] }}</div>

                    @set('year_from', Carbon\Carbon::today()->startOfYear()->format('d/m/Y'))
                    @set('year_to', Carbon\Carbon::today()->format('d/m/Y'))
                    <a href="{{ Coanda::adminUrl('history?from=' . $year_from . '&to=' . $year_to) }}"><span class="text">This year</span></a>
                </div>
	        </div>
	    </div>

		{{ Form::open(['url' => Coanda::adminUrl('history'), 'method' => 'get']) }}
			<div class="row" style="margin-bottom: 10px;">
				<div class="col-sm-5">
					<div class="form-group">
						<label class="sr-only" for="from">From</label>
						<div class="input-group datetimepicker" data-date-format="DD/MM/YYYY" data-hide-time="true">
							<span class="input-group-addon">From</span>
							<input type="text" class="date-field form-control" id="from" name="from" value="{{ $from->format('d/m/Y') }}">
							<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
						</div>
					</div>
				</div>
				<div class="col-sm-5">
					<div class="form-group">
						<label class="sr-only" for="to">To</label>
						<div class="input-group datetimepicker" data-date-format="DD/MM/YYYY" data-hide-time="true">
							<span class="input-group-addon">To</span>
							<input type="text" class="date-field form-control" id="to" name="to" value="{{ $to->format('d/m/Y') }}">
							<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
						</div>
					</div>
				</div>
				<div class="col-sm-2">
					<button class="btn btn-block btn-primary">Update</button>
				</div>
			</div>
		{{ Form::close() }}

		@if ($history_list->count() > 0)
			<table class="table table-striped">
				@foreach ($history_list as $history)
					<tr data-history-id="{{ $history->id }}">
						<td class="tight">{{ $history->created_at->format(\Config::get('coanda::coanda.datetime_format')) }}</td>
						<td class="tight">{{ $history->user->full_name }}</td>
						<td>{{ $history->display_text }}</td>
					</tr>
				@endforeach
			</table>

			<p>Total <span class="label label-warning">{{ number_format($history_list->getTotal()) }}</span></p>

			{{ $history_list->appends(['from' => $from->format('d/m/Y'), 'to' => $to->format('d/m/Y')])->links() }}
		@else
			<p>No activity in selected period.</p>
		@endif

	</div>
</div>

@stop