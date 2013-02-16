(function ($) {
	"use strict";
	$(function () {
		$("body").on('click','.menu-item a', function(){
			var trackingCode = $(this).next(".ga-tracking");
			if(trackingCode.length > 0){
				var gaEventMethod = trackingCode.data("method"),
				    gaEventCat = trackingCode.data("category"),
				    gaEventAction = trackingCode.data("action"),
				    gaEventLabel = trackingCode.data("label");
				    //gaEventValue = trackingCode.data("value");
				    //gaEventNA = trackingCode.data("noninteraction");
				_gaq.push([gaEventMethod,gaEventCat,gaEventAction,gaEventLabel]);
			}

		});
	});
}(jQuery));
