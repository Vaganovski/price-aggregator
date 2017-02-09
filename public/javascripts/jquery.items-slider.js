/*
Слайдер для дивов
*/

jQuery.fn.itemsSlider = function( settings ) {
	settings = jQuery.extend({
        nextSelector : ".next",
        backSelector : ".back",
        minShown : 3,
        speed : "normal",
		slideBy : 2
    }, settings);
    return jQuery(this).each(function() {
		jQuery.fn.itemsSlider.run( jQuery( this ), settings );
    });
};
jQuery.fn.itemsSlider.run = function( $this, settings ) {
	jQuery( ".javascript_css", $this ).css( "display", "none" );
        containerWidth = $this.parent().width();
	var div = jQuery( ">li", $this );
	// var li = div.children();
	if ( div.length > settings.slideBy ) {
		var $next = jQuery(settings.nextSelector);
		var $back = jQuery(settings.backSelector);
                var divWidth = containerWidth / settings.minShown;
                div.each(function () {
                    jQuery(this).width(Math.ceil(divWidth - parseInt(jQuery(div[1]).css( "margin-right" )) - parseInt(jQuery(div[1]).css("margin-left"))))
                })
		divWidth = jQuery(div[1]).width() + parseInt(jQuery(div[1]).css( "margin-right" )) + parseInt(jQuery(div[1]).css("margin-left")) ;
		var animating = false;
		$this.css( "width", ( div.length * divWidth ) );
                if (div.length <= settings.minShown) {
                    $next.addClass('indexOfferArrDisabled')
                    $back.addClass('indexOfferArrDisabled')
                } else {
                    $next.live("click", function() {
                            if ( !animating ) {
                                    animating = true;
                                    var elementLeft = $this.css("left");
                                    if (elementLeft == 'auto') {
                                        elementLeft = 0;
                                    }
                                    countSlided = (div.length - (-1 * parseInt(elementLeft)) / divWidth) - settings.minShown
                                    if (countSlided < settings.slideBy) {
                                        slideBy = countSlided;
                                    } else {
                                        slideBy = settings.slideBy;
                                    }
                                    offsetLeft = parseInt(elementLeft) - (divWidth * slideBy);
                                    if ( offsetLeft + $this.width() > ((settings.minShown - 1) * divWidth) ) {
                                            //$back.css( "display", "block" );
                                            $back.removeClass('indexOfferArrDisabled')
                                            $this.animate({
                                                    left: offsetLeft
                                            }, settings.speed, function() {
                                                    if ( parseInt( $this.css( "left" ) ) + $this.width() <= divWidth * settings.minShown ) {
                                                            //$next.css( "display", "none" );
                                                            $next.addClass('indexOfferArrDisabled')
                                                    }
                                                    animating = false;
                                            });
                                    } else {
                                            animating = false;
                                    }
                            }
                           // $(this).attr("class", "next");
                            return false;
                    });
                    $back.click(function() {
                            if ( !animating ) {
                                    animating = true;
                                    var elementLeft = $this.css("left");
                                    if (elementLeft == 'auto') {
                                        elementLeft = 0;
                                    }
                                    countSlided = (-1 * parseInt(elementLeft)) / divWidth
                                    if (countSlided < settings.slideBy) {
                                        slideBy = countSlided;
                                    } else {
                                        slideBy = settings.slideBy;
                                    }
                                    offsetRight = parseInt(elementLeft) + (divWidth * slideBy);
                                    if ( offsetRight + $this.width() <= $this.width() ) {
                                            //$next.css( "display", "block" );
                                            $next.removeClass('indexOfferArrDisabled')
                                            $this.animate({
                                                    left: offsetRight
                                            }, settings.speed, function() {
                                                    if ( parseInt( $this.css( "left" ) ) == 0 ) {
                                                            //$back.css( "display", "none" );
                                                            $back.addClass('indexOfferArrDisabled')
                                                    }
                                                    animating = false;
                                            });
                                    } else {
                                            animating = false;
                                    }
                            }
                            return false;
                    });
                }
	}
};