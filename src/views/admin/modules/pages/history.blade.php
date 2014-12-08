@extends('coanda::admin.layout.main')

@section('page_title', 'History: ' . $page->name)

@section('content')

<div class="row">

    <div class="breadcrumb-nav">
        <div class="pull-right">
            <a href="{{ Coanda::adminUrl('pages/trash') }}" class="trash-icon"><i class="fa fa-trash-o"></i> Trash</a>
        </div>

        <ul class="breadcrumb">
            <li><a href="{{ Coanda::adminUrl('pages') }}">Pages</a></li>
            <li><a href="{{ Coanda::adminUrl('pages/view/' . $page->id) }}">{{ $page->name }}</a></li>
            <li>History</li>
        </ul>
    </div>
</div>

<div class="row">
    <div class="page-name col-md-12">
        <h1 class="pull-left">History for "<a href="{{ Coanda::adminUrl('pages/view/' . $page->id) }}">{{ $page->name }}</a>" <small>{{ $page->type_name }}</small></h1>
        <div class="page-status pull-right">
            <span class="label label-default">Version {{ $page->current_version }}</span>

            @if ($page->is_trashed)
                <span class="label label-danger">{{ $page->status }}</span>
            @else
                <span class="label @if ($page->is_draft) label-warning @else label-success @endif">{{ $page->status }}</span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="page-options col-md-12"></div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="page-tabs">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#history" data-toggle="tab">History</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="history">

                    <p><i class="fa fa-level-up"></i> <a href="{{ Coanda::adminUrl('pages/view/' . $page->id) }}">Back to  {{ $page->name }}</a></p>

                    <table class="table table-striped table-history">
                        @foreach ($histories as $history)
                        <tr>
                            <td class="tight"><img src="{{ $history->present()->avatar }}" class="img-circle" width="45"></td>
                            <td>{{ $history->present()->username }}</td>
                            <td>{{ $history->present()->happening }}</td>
                            <td>{{ $history->present()->created_at }}</td>
                        </tr>
                        @endforeach
                    </table>

                    {{ $histories->links() }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="page-tabs">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#contributors" data-toggle="tab">Contributors</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="contributors">
                    <table class="table table-striped table-history">
                        @foreach ($contributors as $contributor)
                        <tr>
                            <td class="tight"><img src="{{ $contributor->avatar }}" class="img-circle" width="45"></td>
                            <td>{{ $contributor->present()->name }}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@stop
