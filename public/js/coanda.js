$(document).ready( function () {

	$('.breadcrumb-nav .breadcrumb li .expand').on('click', function (e) {

		if (!$($(this).attr('href')).is(':visible'))
		{
			$(this).addClass('selected');

			$('.sub-pages-expand').hide();

			$($(this).attr('href')).slideDown();
		}
		else
		{
			$(this).removeClass('selected');
			$($(this).attr('href')).hide()
		}

	});

	$('a.new-window').attr('target', '__blank');

	$('input.select-all').on('click', function () {

		$(this).get(0).setSelectionRange(0, this.value.length)

	});
	
});