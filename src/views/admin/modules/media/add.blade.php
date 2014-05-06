@extends('coanda::admin.layout.main')

@section('page_title', 'Add new media')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('media') }}">Media</a></li>
			<li>Add new media</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">Add new media</h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12"></div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#add" data-toggle="tab">Add</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="add">

					@if (Session::has('missing_file'))
						<div class="alert alert-danger">
							Error
						</div>
					@endif

					{{ Form::open(['url' => Coanda::adminUrl('media/add'), 'files' => true]) }}

						<div class="form-group @if (Session::has('missing_file')) has-error @endif">
							<label for="file">File</label>
							<input type="file" name="file" class="form-control">

							@if (Session::has('missing_file'))
								<span class="help-block">Please specify a file</span>
							@endif
						</div>

						{{ Form::button('Upload', ['name' => 'upload', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-primary']) }}
						<a href="{{ Coanda::adminUrl('media') }}" class="btn btn-default">Cancel</a>

					{{ Form::close() }}

				</div>
			</div>
		</div>
	</div>
</div>

@stop
