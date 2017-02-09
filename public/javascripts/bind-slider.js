jQuery(function() {
    jQuery( "#popular-products .scroll-wrapper" ).itemsSlider({
	        nextSelector : "#popular-products #indexOfferNext",
	        backSelector : "#popular-products #indexOfferPrev",
                minShown : 9,
	        speed : "slow",
			slideBy : 3
    });

    jQuery( "#new-products .scroll-wrapper" ).itemsSlider({
	        nextSelector : "#new-products #indexOfferNext",
	        backSelector : "#new-products #indexOfferPrev",
                minShown : 9,
	        speed : "slow",
			slideBy : 3
    });

    jQuery( "#similar-products .scroll-wrapper" ).itemsSlider({
	        nextSelector : "#similar-products #indexOfferNext",
	        backSelector : "#similar-products #indexOfferPrev",
                minShown : Math.floor(jQuery(document).width()/182),
	        speed : "slow",
			slideBy : 3
    });
});

