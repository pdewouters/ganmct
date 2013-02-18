(function ($) {
	"use strict";
	$(function () {
		$("body").on('click','.menu-item a', function(){
			var trackingCode = $(this).next(".ga-tracking");
			if(trackingCode.length > 0){
				var gaEventMethod = trackingCode.data("method"),
				    gaEventCat = trackingCode.data("category"),
				    gaEventAction = trackingCode.data("action"),
				    gaEventLabel = trackingCode.data("label"),
				    gaEventValue = trackingCode.data("value"),
				    gaEventNA = trackingCode.data("noninteraction"),
						isNonInteraction, labelParam, valueParam;

				(gaEventNA === 1) ?  isNonInteraction = true : isNonInteraction = false;
				(gaEventLabel.length !== 0) ? labelParam = gaEventLabel : labelParam = '' ;
				(gaEventValue.length !== 0) ? valueParam = gaEventLabel : valueParam = 0 ;

				if(gaEventMethod.length !== 0 && gaEventCat.length !== 0 && gaEventAction.length !== 0){
					_gaq.push([gaEventMethod,gaEventCat,gaEventAction,labelParam,valueParam,gaEventNA]);
				}

			}

		});
	});
}(jQuery));
