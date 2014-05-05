@extends('coanda::admin.layout.main')

@section('page_title', 'Confirm remove layout block')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('layout') }}">Layouts</a></li>
			<li><a href="{{ Coanda::adminUrl('layout/block-view/' . $block->id) }}">{{ $block->name }}</a></li>
			<li>Confirm delete</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">Confirm layout block removal <small><i class="fa fa-th-large"></i></small></h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12"></div>
</div>

{{ Form::open(['url' => Coanda::adminUrl('layout/block-delete/' . $block->id)]) }}
<div class="row">
	<div class="col-md-12">

		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#delete" data-toggle="tab">Block</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="delete">

					<div class="alert alert-danger">
						<i class="fa fa-exclamation-triangle"></i> Are you sure you want to delete this layout block?
					</div>

					{{ Form::button('I understand, please delete it', ['name' => 'permanent_remove', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-danger']) }}

				</div>
			</div>
		</div>

	</div>
</div>
{{ Form::close() }}

@stop