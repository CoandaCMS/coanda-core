@extends('coanda::themes.simple.layout.main')

@section('page_title', $meta['title'])

@section('content')

	<h1>{{ $attributes['name']['content'] }}</h1>

	{{ $attributes['content']['content'] }}

@stop
