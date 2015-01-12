@extends('coanda::admin.layout.main')

@section('page_title', 'History')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('history') }}">History</a></li>
			<li>Digest</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">History digest</h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12"></div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="page-tabs">

			<p>The daily digest contains an overview of activity on the site.</p>

			{{ Form::open(['url' => Coanda::adminUrl('history/digest')]) }}
				@if ($subscribed)
					<p>You are signed up to receive a daily digest.</p>
					<button class="btn btn-danger" name="unsubscribe" value="true">Stop receiving daily updates</button>
				@else
					<button class="btn btn-success" name="subscribe" value="true">Send me daily updates</button>
				@endif
			{{ Form::close() }}
		</div>
	</div>
</div>

@stop