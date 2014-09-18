@extends('coanda::admin.layout.main')

@section('page_title', 'Home')

@section('content')

<div class="container">
	<h1>Welcome back, {{ Coanda::currentUser()->first_name }}</h1>
</div>

@stop
