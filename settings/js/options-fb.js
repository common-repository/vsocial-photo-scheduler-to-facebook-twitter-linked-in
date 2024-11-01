/**
 * Prints out the inline javascript needed for the colorpicker and choosing
 * the tabs in the panel.
 */
jQuery(document).ready(function ($) {

    // Fade out the save message
    $('.fade').delay(1000).fadeOut(1000);

    // Color Picker
    $('.colorSelector').each(function () {
        var Othis = this; //cache a copy of the this variable for use inside nested function
        var initialColor = $(Othis).next('input').attr('value');
        $(this).ColorPicker({
            color: initialColor,
            onShow: function (colpkr) {
                $(colpkr).fadeIn(500);
                return false;
            },
            onHide: function (colpkr) {
                $(colpkr).fadeOut(500);
                return false;
            },
            onChange: function (hsb, hex, rgb) {
                $(Othis).children('div').css('backgroundColor', '#' + hex);
                $(Othis).next('input').attr('value', '#' + hex);
            }
        });
    }); //end color picker
    // Switches option sections
    $('.group').hide();
    var activetab = '';

    if (typeof (localStorage) != 'undefined') {
        activetab = localStorage.getItem("activetab");
    }
    if (activetab != '' && $(activetab).length) {
        $(activetab).fadeIn();
    } else {
        $('.group:first').fadeIn();
    }
    $('.group .collapsed').each(function () {
        $(this).find('input:checked').parent().parent().parent().nextAll().each(

        function () {
            if ($(this).hasClass('last')) {
                $(this).removeClass('hidden');
                return false;
            }
            $(this).filter('.hidden').removeClass('hidden');
        });
    });

    if (activetab != '' && $(activetab + '-tab').length) {
        $(activetab + '-tab').addClass('nav-tab-active');
    } else {
        $('.fb-pp_nav-tab-wrapper a:first').addClass('nav-tab-active');
    }
    $('.fb-pp_nav-tab-wrapper a').click(function (evt) {
        $('.fb-pp_nav-tab-wrapper a').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active').blur();
        var clicked_group = $(this).attr('href');
        if (typeof (localStorage) != 'undefined') {
            localStorage.setItem("activetab", $(this).attr('href'));
        }
        $('.group').hide();
        $(clicked_group).fadeIn();
        evt.preventDefault();
    });

    $('.group .collapsed input:checkbox').click(unhideHidden);

    function unhideHidden() {
        if ($(this).attr('checked')) {
            $(this).parent().parent().parent().nextAll().removeClass('hidden');
        } else {
            $(this).parent().parent().parent().nextAll().each(

            function () {
                if ($(this).filter('.last').length) {
                    $(this).addClass('hidden');
                    return false;
                }
                $(this).addClass('hidden');
            });

        }
    }

    // Image Options
    $('.wplannerfb-radio-img-img').click(function () {
        $(this).parent().parent().find('.wplannerfb-radio-img-img').removeClass('wplannerfb-radio-img-selected');
        $(this).addClass('wplannerfb-radio-img-selected');
    });

    $('.wplannerfb-radio-img-label').hide();
    $('.wplannerfb-radio-img-img').show();
    $('.wplannerfb-radio-img-radio').hide();
	
	jQuery("a.fbPPdeleteTask").live('click', function() {
		var $t = jQuery(this),
			theTr = $t.parent('td').parent('tr'),
			delete_id = theTr.find('td').first().text();
			
		var dataFB = {
			action: 'fbppdeletetask',
			rowid: delete_id
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, dataFB, function(response) {
			if(response == 'OK'){
				theTr.remove();
			}else{
				theTr.animate({
					'background-color' : '#FFB1B1'
				}, 'fast', function() {
					theTr.animate({
						'background-color' : '#FFFFFF'
					}, 'fast', function() {
						alert(response);
					})
				});
			}
			return false;
		});
	});
});