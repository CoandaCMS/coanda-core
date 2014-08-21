<table class="table table-striped">
	<tr>
		<th></th>
		<th>Name</th>
		{{--
		<th>Page Type</th>
		<th>Sub pages</th>
		<th>Status</th>
		<th>Created</th>
		--}}
		@if (!$location || $location->sub_location_order == 'manual')
			<th>Order</th>
		@endif
		<th></th>
	</tr>

	@foreach ($children as $child)
		<tr class="status-{{ $child->page->status }} @if (!$child->page->is_visible) info @endif @if ($child->page->is_hidden) danger @endif @if ($child->page->is_hidden_navigation) warning @endif @if ($child->page->is_pending) info @endif">

			@if (!$child->is_trashed)
				<td class="tight"><input type="checkbox" name="remove_page_list[]" value="{{ $child->page->id }}" @if (!Coanda::canView('pages', 'remove')) disabled="disabled" @endif></td>
			@endif

			<td>
				@if ($child->page->is_draft)
					<i class="fa fa-circle-o"></i>
				@else
					<i class="fa {{ $child->page->pageType()->icon() }}"></i>
				@endif
				<a href="{{ Coanda::adminUrl('pages/location/' . $child->id) }}">{{ $child->page->present()->name }}</a>

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
			{{--
			<td>{{ $child->page->present()->type }}</td>
			<td>
				@if ($child->page->pageType()->allowsSubPages())
					{{ $child->childCount() }}
				@else
					<em>n/a</em>
				@endif
			</td>
			<td>
				{{ $child->page->present()->status }}

				@if ($child->page->is_pending)
					<span class="label label-info show-tooltip" data-toggle="tooltip" data-placement="top" title="{{ $child->page->currentVersion()->present()->delayed_publish_date }}">
						Pending
						<i class="fa fa-calendar"></i>
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
			<td>{{ $child->page->present()->created_at }}</td>
			--}}

			@if (!$location || $location->sub_location_order == 'manual')
				<td class="order-column">{{ Form::text('ordering[' . $child->id . ']', $child->order, ['class' => 'form-control input-sm']) }}</td>
			@endif
			
			@if (!$child->is_trashed)
				<td class="tight">
					@if ($child->page->is_draft)
						<a href="{{ Coanda::adminUrl('pages/editversion/' . $child->page->id . '/1') }}"><i class="fa fa-pencil-square-o"></i></a>
					@else
						<a href="{{ Coanda::adminUrl('pages/edit/' . $child->page->id) }}"><i class="fa fa-pencil-square-o"></i></a>
					@endif
				</td>
			@else
				<td></td>
			@endif
		</tr>
	@endforeach
</table>
