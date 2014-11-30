<table class="table table-striped @if (!$page || $page->sub_page_order == 'manual') sorted_table @endif">
	<thead>
		<tr>
			@if (!$page || $page->sub_page_order == 'manual')
			<th></th>
			@endif
			<th></th>
			<th>Name</th>
			<th></th>
		</tr>
	</thead>

	<tbody>
	@foreach ($children as $child)
		<tr class="status-{{ $child->status }} @if (!$child->is_visible) info @endif @if ($child->is_hidden) danger @endif @if ($child->is_hidden_navigation) warning @endif @if ($child->is_pending) info @endif">

			@if (!$page || $page->sub_page_order == 'manual')
			<td class="tight">
				<i class="fa fa-arrows"></i>
				<input value="{{ $child->order }}" type="hidden" name="ordering[{{ $child->id }}]" class="form-control input-sm">
			</td>
			@endif

			@if (!$child->is_trashed)
				<td class="tight">
					<input type="checkbox" name="remove_page_list[]" value="{{ $child->id }}" @if (!$child->can_remove) disabled="disabled" @endif>
				</td>
			@endif

			<td>
				@if ($child->is_draft)
					<i class="fa fa-circle-o"></i>
				@else
					@if ($child->can_view)
						<i class="fa {{ $child->type_icon }}"></i>
					@else
						<i class="fa {{ $child->type_icon }} disabled"></i>
					@endif
				@endif

				@if ($child->can_view)
					<a href="{{ Coanda::adminUrl('pages/view/' . $child->id) }}">{{ $child->name }}</a>
				@else
					<span class="disabled">{{ $child->name }}</span>
				@endif

				@if ($child->is_pending)
					<span class="label label-info">
						Pending
						<i class="fa fa-calendar"></i>
						{{ $child->currentVersion()->pending_display_text }}
					</span>
				@endif

				@if (!$child->is_visible)
					<span class="label label-info show-tooltip" title="{{ $child->visible_dates }}">Invisible</span>
				@endif
				
				@if ($child->is_hidden)
					<span class="label label-danger">Hidden</span>
				@elseif ($child->is_hidden_navigation)
					<span class="label label-warning">Hidden from Navigation</span>
				@endif
			</td>
			
			@if (!$child->is_trashed)
				<td class="tight">
					@if ($child->can_edit)
						@if ($child->is_draft)
							<a href="{{ Coanda::adminUrl('pages/editversion/' . $child->id . '/1') }}"><i class="fa fa-pencil-square-o"></i></a>
						@else
							<a href="{{ Coanda::adminUrl('pages/edit/' . $child->id) }}"><i class="fa fa-pencil-square-o"></i></a>
						@endif
					@endif
				</td>
			@else
				<td></td>
			@endif
		</tr>
	@endforeach
	</tbody>
</table>
