@extends('coanda::admin.layout.main')

@section('page_title', 'Search')

@section('content')

<div class="row">
	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li>Search</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">Search</h1>
		<div class="page-status pull-right">
			<span class="label label-default">Results {{ $results->count() }}</span>
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
				<li class="active"><a href="#results" data-toggle="tab">Results</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="results">

					@if ($results->count() > 0)
						<table class="table table-striped search-results fifty-50">
							@foreach ($results as $page)
								<tr>
									<td>
										@if ($page->can_view)
											<a href="{{ Coanda::adminUrl('pages/view/' . $page->id) }}">{{ $page->name }}</a>
										@else
											{{ $page->name }}
										@endif
									</td>
									<td>
										@if ($page->can_view)
                                            @set('breadcrumb', $page->breadcrumb())

                                            @if ($breadcrumb)
                                                <ol class="breadcrumb">
                                                    @foreach ($breadcrumb as $breadcrumb_item)
                                                        @if ($breadcrumb_item['url'])
                                                            <li><a href="{{ url($breadcrumb_item['url']) }}">{{ $breadcrumb_item['name'] }}</a></li>
                                                        @endif
                                                    @endforeach
                                                </ol>
                                            @endif
										@endif
									</td>
								</tr>
							@endforeach
						</table>

						{{ $results->appends(['q' => $query])->links() }}
					@else
						<p>No results for "{{ $query }}", please try again.</p>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>

@stop
