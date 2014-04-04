@extends('coanda::admin.layout.main')

@section('page_title', 'Confirm deletion of ' . $page->name)

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('pages') }}">Pages</a></li>

			@foreach ($page->parents() as $parent)
				<li>
					<a href="{{ Coanda::adminUrl('pages/view/' . $parent->id) }}">{{ $parent->present()->name }}</a>
					&nbsp;&nbsp;
					<a href="#sub-pages-{{ $parent->id }}" class="expand"><i class="fa fa-caret-square-o-down"></i></a>
				</li>	
			@endforeach
			<li>{{ $page->present()->name }}</li>
		</ul>

		@foreach ($page->parents() as $parent)
			<div class="sub-pages-expand" id="sub-pages-{{ $parent->id }}">
				
				<p>Loading <span class="one">.</span><span class="two">.</span><span class="three">.</span></p>

			</div>
		@endforeach
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">{{ $page->present()->name }} <small>{{ $page->present()->type }}</small></h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12"></div>
</div>

<div class="edit-container">

	<div class="alert alert-danger">
		<i class="fa fa-exclamation-triangle"></i> Are you sure you want to remove this page?
	</div>

	@if ($page->children->count() > 0)
		<p><i class="fa fa-info-circle"></i> Deleting this page will also delete {{ $page->children->count() }} sub page{{ $page->children->count() != 1 ? 's' : '' }}</p>
	@endif

	<div class="row">
		<div class="col-md-12">

			{{ Form::open(['url' => Coanda::adminUrl('pages/delete/' . $page->id)]) }}
				{{ Form::button('Yes, I understand', ['name' => 'confirm_delete', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-primary']) }}
				<a class="btn btn-default" href="{{ Coanda::adminUrl('pages/view/' . $page->id) }}">Cancel</a>
			{{ Form::close() }}

		</div>
	</div>

</div>
@stop
