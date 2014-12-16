<!DOCTYPE html>
<html>
	<head>
		<title>Preview #{{ $version->version }} of page #{{ $version->page_id }} | Coanda CMS</title>

		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="{{ asset('packages/coandacms/coanda-core/css/preview.css') }}">
	</head>
<body>

<div id="overlay">
	<div id="overlay-inner">

		<div class="pull-left">
			<div class="preview-text">You are previewing version #{{ $version->version }} of "{{ $version->page->name }}"</div>
		</div>

		@if ($version->status == 'draft')
			<div class="pull-right">
				@if (Session::has('comment_saved'))
					<span class="label label-success">Comment added!</span>
				@endif
				<button class="btn btn-default" id="add-comment-button">Add comment</button>
			</div>
		@endif

		<div class="clearfix"></div>

		<div id="overlay-comment-form" @if (count($invalid_fields) > 0) class="visible" @endif>

			{{ Form::open(['url' => 'pages/preview-comment/' . $preview_key])}}

				<div class="form-group @if (isset($invalid_fields['name'])) has-error @endif">
					<label class="control-label" for="name">Name</label>
					<input class="form-control" type="text" name="name" id="name" value="{{ Input::old('name') }}">

					@if (isset($invalid_fields['name']))
						<span class="help-block">{{ $invalid_fields['name'] }}</span>
					@endif
				</div>

				<div class="form-group @if (isset($invalid_fields['comment'])) has-error @endif">
					<label class="control-label" for="comment">Comment</label>
					<textarea rows="5" class="form-control" name="comment" id="comment">{{ Input::old('comment') }}</textarea>

					@if (isset($invalid_fields['comment']))
						<span class="help-block">{{ $invalid_fields['comment'] }}</span>
					@endif
				</div>

				<button type="submit" class="pull-right btn btn-default">Send</button>

			{{ Form::close() }}

		</div>
	</div>
	
</div>

<iframe id="preview" src="{{ url('pages/render-preview/' . $preview_key) }}"></iframe>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

<script type="text/javascript">

function resize_preview()
{
	$('#preview').css('margin-top', 0);
	$('#preview').css('height', '100%');

	var overlay_height = $('#overlay').height();
	var preview_height = $('#preview').height() - overlay_height;

	$('#preview').css('margin-top', overlay_height);
	$('#preview').css('height', preview_height);
}

$(document).ready ( function () {

	resize_preview();

	$(window).resize( function () {

		resize_preview();

	});

	$('#add-comment-button').on('click', function () {

		$('#overlay-comment-form').toggleClass('visible');

	});

});
</script>

</body>
</html>