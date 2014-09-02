<table class="table table-striped">
	<tr>
		<th></th>
		<th>Name</th>
		@if (!$location || $location->sub_location_order == 'manual')
			<th></th>
		@endif
		<th></th>
	</tr>

	@foreach ($children as $child)
		<tr class="status-{{ $child->page->status }} @if (!$child->page->is_visible) info @endif @if ($child->page->is_hidden) danger @endif @if ($child->page->is_hidden_navigation) warning @endif @if ($child->page->is_pending) info @endif">

			@if (!$child->is_trashed)
				<td class="tight">
					<input type="checkbox" name="remove_page_list[]" value="{{ $child->page->id }}" @if (!$child->can_remove) disabled="disabled" @endif>
				</td>
			@endif

			<td>
				@if ($child->page->is_draft)
					<i class="fa fa-circle-o"></i>
				@else
					@if ($child->can_view)
						<i class="fa {{ $child->page->pageType()->icon() }}"></i>
					@else
						<i class="fa {{ $child->page->pageType()->icon() }} disabled"></i>
					@endif
				@endif

				@if ($child->can_view)
					<a href="{{ Coanda::adminUrl('pages/location/' . $child->id) }}">{{ $child->page->present()->name }}</a>
				@else
					<span class="disabled">{{ $child->page->present()->name }}</span>
				@endif

				@if ($child->page->is_pending)
					<span class="label label-info">
						Pending
						<i class="fa fa-calendar"></i>
						{{ $child->page->currentVersion()->present()->delayed_publish_date }}
					</span>
				@endif

				@if (!$child->page->is_visible)
					<span class="label label-info show-tooltip" title="{{ $child->page->present()->visible_dates }}">Invisible</span>
				@endif
				
				@if ($child->page->is_hidden)
					<span class="label label-danger">Hidden</span>
				@elseif ($child->page->is_hidden_navigation)
					<span class="label label-warning">Hidden from Navigation</span>
				@endif
			</td>

			@if (!$location || $location->sub_location_order == 'manual')
				<td class="order-column">
					@if ($child->can_edit)
						<input value="{{ $child->order }}" type="text" name="ordering[{{ $child->id }}]" class="form-control input-sm">
					@endif
				</td>
			@endif
			
			@if (!$child->is_trashed)
				<td class="tight">
					@if ($child->can_edit)
						@if ($child->page->is_draft)
							<a href="{{ Coanda::adminUrl('pages/editversion/' . $child->page->id . '/1') }}"><i class="fa fa-pencil-square-o"></i></a>
						@else
							<a href="{{ Coanda::adminUrl('pages/edit/' . $child->page->id) }}"><i class="fa fa-pencil-square-o"></i></a>
						@endif
					@endif
				</td>
			@else
				<td></td>
			@endif
		</tr>
	@endforeach
</table>
