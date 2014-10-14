@extends('coanda::admin.layout.main')

@section('page_title', 'Error')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li>Error</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">Error</h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12"></div>
</div>

<div class="row">
	<div class="col-md-12">
	    <div class="edit-container">
	        <p>You do not have the correct permissions to access this page.</p>
	    </div>
	</div>
</div>

@stop
