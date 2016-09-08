/**
 * Created by Chamal on 3/7/16.
 */
require([
        'jquery'
    ],
    function (jQuery) {
        jQuery(window).load(function () {
            var top = jQuery('#admire_artwork_top').val();
            var left = jQuery('#admire_artwork_left').val();
            var right = jQuery('#admire_artwork_right').val();
            var multiply = jQuery('#admire_multiply').val();

            var product_sticker_image = jQuery('.product-sticker-image');


            if (top) {
                jQuery(product_sticker_image).css('top', top + '%');
            }
            if (left) {
                jQuery(product_sticker_image).css('left', left + '%');
            }
            if (right) {
                jQuery(product_sticker_image).css('right', right + '%');
            }
            if (multiply) {
                jQuery(product_sticker_image).css({
                    'filter': 'brightness(' + multiply + '%)',
                    '-webkit-filter': 'brightness(' + multiply + '%)'
                });
            }

            jQuery("#admire_artwork_top").keyup(function () {
                if (jQuery(this).val()) {
                    var top = jQuery(this).val();
                    jQuery(product_sticker_image).css('top', top + '%');
                }
            });

            jQuery("#admire_artwork_left").keyup(function () {
                if (jQuery(this).val()) {
                    var left = jQuery(this).val();
                    jQuery(product_sticker_image).css('left', left + '%');
                }
            });

            jQuery("#admire_artwork_right").keyup(function () {
                if (jQuery(this).val()) {
                    var right = jQuery(this).val();
                    jQuery(product_sticker_image).css('right', right + '%');
                }
            });
            jQuery("#admire_multiply").keyup(function () {
                if (jQuery(this).val()) {
                    var multiply = jQuery(this).val();
                    jQuery(product_sticker_image).css({
                        'filter': 'brightness(' + multiply + '%)',
                        '-webkit-filter': 'brightness(' + multiply + '%)'
                    });
                }
            });
        });
    });