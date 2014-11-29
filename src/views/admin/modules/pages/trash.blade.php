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

		<div class="page-status pull-right">
			<span class="label label-default">Total {{ $pages->count() }}</span>
		</div>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12">
	</div>
</div>

{{ Form::open(['url' => Coanda::adminUrl('pages/trash')]) }}

@set('show_delete_button', false)
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
							<tr>
								<th></th>
								<th>Name</th>
								<th></th>
								<th></th>
							</tr>
							@foreach ($pages as $page)
								<tr class="status-{{ $page->status }}">
									<td class="tight">
										@if ($page->can_edit)
											<input type="checkbox" name="permanent_remove_list[]" value="{{ $page->id }}">
											@set('show_delete_button', true)
										@endif
									</td>
									<td>
										@if ($page->is_draft)
											<i class="fa fa-circle-o"></i>
										@else
											<i class="fa {{ $page->pageType()->icon() }}"></i>
										@endif

										@if ($page->can_view)
											<a href="{{ Coanda::adminUrl('pages/view/' . $page->id) }}">{{ $page->name }}</a>
										@else
											{{ $page->name }}
										@endif
									</td>
									<td>
										@if ($page->can_view)
											<a href="{{ Coanda::adminUrl('pages') }}">Pages</a> /
											@foreach ($page->parents() as $parent)
												<a href="{{ Coanda::adminUrl('pages/view/' . $parent->id) }}">{{ $parent->name }}</a> /
											@endforeach
											{{ $page->name }}
										@endif
									</td>
									<td>
										@if ($page->can_view)
											<a class="pull-right btn btn-xs btn-primary" href="{{ Coanda::adminUrl('pages/restore/' . $page->id) }}">Restore</a>
										@endif
									</td>
								</tr>
							@endforeach
						</table>

                        @if ($show_delete_button)
						    {{ Form::button('Delete permanently', ['name' => 'permanent_remove', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-danger']) }}
						@endif
					@else
						<p>There are no trashed pages.</p>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
{{ Form::close() }}
@stop
