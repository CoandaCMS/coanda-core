@extends('coanda::admin.layout.main')

@section('page_title', 'History')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('history') }}">History</a></li>
			<li>All</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">History</h1>
		<div class="page-status pull-right">
			<span class="label label-default">Total {{ number_format($history_list->getTotal()) }}</span>
		</div>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12"></div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#history" data-toggle="tab">All</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="history">
					<table class="table table-striped">
						@foreach ($history_list as $history)
							<tr data-history-id="{{ $history->id }}">
								<td class="tight">{{ $history->created_at->format(\Config::get('coanda::coanda.datetime_format')) }}</td>
								<td class="tight">{{ $history->user->full_name }}</td>
								<td>{{ $history->display_text }}</td>
							</tr>
						@endforeach
					</table>

					{{ $history_list->links() }}
				</div>
			</div>
		</div>
	</div>
</div>

@stop
