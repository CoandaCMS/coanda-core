@extends('coanda::admin.layout.main')

@section('page_title', 'Media tags')

@section('content')

<div class="row">

    <div class="breadcrumb-nav">
        <ul class="breadcrumb">
            <li><a href="{{ Coanda::adminUrl('media') }}">Media</a></li>
            <li>Tags</li>
        </ul>
    </div>
</div>

<div class="row">
    <div class="page-name col-md-12">
        <h1 class="pull-left">Tags <small>Media</small></h1>
        <div class="page-status pull-right">
            <span class="label label-default">Total {{ $tags->getTotal() }}</span>
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
                <li class="active"><a href="#tags" data-toggle="tab">Tags</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tags">

                    <p><i class="fa fa-level-up"></i> <a href="{{ Coanda::adminUrl('media') }}">Up to Media</a></p>

                    <table class="table table-striped table-history">
                        @foreach ($tags as $tag)
                        <tr>
                            <td class="tight"><i class="fa fa-tag"></i></td>
                            <td><a href="{{ Coanda::adminUrl('media/tag/' . $tag->id) }}">{{ $tag->tag }}</a></td>
                            <td>{{ $tag->media->count() }} tagged media</td>
                            <td>Created {{ $tag->present()->created_at }}</td>
                        </tr>
                        @endforeach
                    </table>

                    {{ $tags->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@stop
