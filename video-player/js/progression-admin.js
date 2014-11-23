(function ($) {
	"use strict";
	$(function () {

		$('.progression-skincolor').wpColorPicker();


		function toggleCustomSettings() {
			$( '.progression-skincolor' ).closest('tr')[$('#progression_custom_skin').is(':checked') ? 'show' : 'hide']();
		}

		$('#progression_custom_skin')
			
			.each(toggleCustomSettings)

			.change(toggleCustomSettings);

	});
}(jQuery));
