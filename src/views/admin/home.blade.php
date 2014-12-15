@extends('coanda::admin.layout.main')

@section('page_title', 'Home')

@section('content')

<div class="container">
	<h1>Welcome back, {{ Coanda::currentUser()->first_name }}</h1>

	@set('module_widget_templates', Coanda::dashboardWidgetTemplates())

	@if (count($module_widget_templates) > 0)
		<hr>

		<div class="row">
			@foreach ($module_widget_templates as $module_widget_template)
				<div class="col-md-6 col-xs-12">
					@include($module_widget_template)
				</div>
			@endforeach
		</div>
	@endif
</div>

@stop
