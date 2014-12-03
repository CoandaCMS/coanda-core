@extends('coanda::admin.layout.main')

@section('page_title', 'Browse for new parent page')

@section('content')

<div class="row">

	<div class="breadcrumb-nav">
		<ul class="breadcrumb">
			<li>Pages</li>
			<li>Browse for new parent page</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="page-name col-md-12">
		<h1 class="pull-left">Browse: {{ $parent_page ? $parent_page->name : 'Top Level' }}</h1>
	</div>
</div>

<div class="row">
	<div class="page-options col-md-12"></div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="page-tabs">
			<div class="tab-content">

                <div class="tab-pane active" id="subpages">

                    {{ Form::open(['url' => Coanda::adminUrl('pages/browse-parent/' . $version_id)]) }}

                        @if ($parent_page)
                            <table class="table table-striped">
                                <td class="tight">
                                    <input type="radio" name="new_parent_page_id" value="{{ $parent_page->id }}" checked="checked">
                                </td>
                                <td>{{ $parent_page->name }}</td>
                            </table>
                        @endif

                        @if ($parent_page && $parent_page->parent)
                            <p><i class="fa fa-level-up"></i> <a href="{{ Coanda::adminUrl('pages/browse-parent/' . $version_id . '/' . $parent_page->parent->id) }}">Up to {{ $parent_page->parent->name }}</a></p>
                        @else
                            @if (!$at_top)
                                <p><i class="fa fa-level-up"></i> <a href="{{ Coanda::adminUrl('pages/browse-parent/' . $version_id . '/0') }}">Up to Pages</a></p>
                            @endif
                        @endif

                        @if ($pages->count() > 0)
                            <table class="table table-striped">
                                @foreach ($pages as $page)
                                    <tr>
                                        @if ($page->pageType()->allowsSubPages())
                                            <td class="tight">
                                                <input type="radio" name="new_parent_page_id" value="{{ $page->id }}">
                                            </td>
                                            <td><a href="{{ Coanda::adminUrl('pages/browse-parent/' . $version_id . '/' . $page->id) }}">{{ $page->name }}</a></td>
                                        @else
                                            <td class="tight"><input type="radio" disabled="disabled"></td>
                                            <td>{{ $page->name }}</td>
                                        @endif
                                    </tr>
                                @endforeach
                            </table>

                            <div class="buttons">
                                {{ Form::button('OK', ['name' => 'choose_new_parent', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-primary']) }}
                                {{ Form::button('Move to Top Level', ['name' => 'move_to_top_level', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default']) }}
                                {{ Form::button('Cancel', ['name' => 'cancel', 'value' => 'true', 'type' => 'submit', 'class' => 'btn btn-default']) }}
                            </div>

                            {{ $pages->links() }}
                        @else
                            <p>This page doesn't have any sub pages</p>
                        @endif

                    {{ Form::close() }}
                </div>
			</div>
		</div>
	</div>
</div>

@stop
