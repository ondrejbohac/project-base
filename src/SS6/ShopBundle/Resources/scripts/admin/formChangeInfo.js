(function ($) {

	SS6 = window.SS6 || {};
	SS6.formChangeInfo = SS6.formChangeInfo || {};

	SS6.formChangeInfo.init = function () {
		$('.web__content form')
			.change(function(){
				SS6.formChangeInfo.showInfo();
			})
			.each(function() {
				if ($(this).find('.form-input-error:first, .js-validation-errors-list li:first').size() > 0) {
					SS6.formChangeInfo.showInfo();
				}
			});

		if (typeof CKEDITOR !== 'undefined') {
			for (var i in CKEDITOR.instances) {
				CKEDITOR.instances[i].on('change', function () {
					SS6.formChangeInfo.showInfo();
				});
			}
		}
	};

	SS6.formChangeInfo.showInfo = function () {
		var textToShow = SS6.translator.trans('Provedli jste změny, nezapomeňte je uložit!');
		var $fixedBarIn = $('.web__content .window-fixed-bar .window-fixed-bar__in');
		var $infoDiv = $fixedBarIn.find('#js-form-change-info');
		if ($infoDiv.length === 0) {
			$fixedBarIn.prepend(
				'<div class="window-fixed-bar__item">\
					<div id="js-form-change-info" class="window-fixed-bar__item__cell text-center">\
						<strong>' + textToShow + '</strong>\
					</div>\
				</div>');
		} else {
			$infoDiv.text = textToShow;
		}
	};

	SS6.formChangeInfo.removeInfo = function () {
		$('#js-form-change-info').remove();
	};

	$(document).ready(function () {
		SS6.formChangeInfo.init();
	});

})(jQuery);