@extends('coanda::admin.layout.main')

@section('page_title', 'Urls')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li><a href="{{ Coanda::adminUrl('urls') }}">Urls</a></li>
			<li>All Urls</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">All Urls</h1>
		<div class="page-status pull-right">
			<span class="label label-default">Total {{ $urls->getTotal() }}</span>
		</div>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12"></div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="page-tabs">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#urls" data-toggle="tab">All Urls</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="urls">
					<table class="table table-striped">
						@foreach ($urls as $url)
							<tr>
								<td>
									{{ $url->slug }}

									<a href="{{ url($url->slug) }}"><i class="fa fa-external-link"></i></a>
								</td>
								<td class="tight">{{ $url->type }}:{{ $url->type_id }}</td>
							</tr>
						@endforeach
					</table>

					{{ $urls->links() }}
				</div>
			</div>
		</div>
	</div>
</div>

@stop
