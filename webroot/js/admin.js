$(function() {
	$('input[data-content]').popover({});
	$('.topbar').dropdown();
	$('.datepicker').datepicker({
		dateFormat: 'yy-mm-dd'
	});
});