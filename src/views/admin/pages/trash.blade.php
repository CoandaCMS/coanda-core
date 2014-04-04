@extends('coanda::admin.layout.main')

@section('page_title', 'Trash')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('pages') }}">Pages</a></li>
			<li>Trash</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">Trash <small>Pages</small></h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12">
		<a href="{{ Coanda::adminUrl('pages/empty-trash') }}" class="btn btn-primary">Empty trash</a>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#trashedpages" data-toggle="tab">Trashed pages</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="trashedpages">

					@if ($pages->count() > 0)
						<table class="table table-striped">
						@foreach ($pages as $page)
							<tr class="status-{{ $page->status }}">
								<td>
									@if ($page->is_draft)
										<i class="fa fa-circle-o"></i>
									@else
										<i class="fa fa-circle"></i>
									@endif
									<a href="{{ Coanda::adminUrl('pages/view/' . $page->id) }}">{{ $page->present()->name }}</a>
								</td>
								<td>{{ $page->present()->type }}</td>
								<td>
									@foreach ($page->parents() as $parent)
										<a href="{{ Coanda::adminUrl('pages/view/' . $parent->id) }}">{{ $parent->present()->name }}</a> /
									@endforeach									
								</td>
								<td><a href="{{ Coanda::adminUrl('pages/restore/' . $page->id) }}">Restore</a></td>
							</tr>
						@endforeach
						</table>
					@else
						<p>There are no trashed pages.</p>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>

@stop
