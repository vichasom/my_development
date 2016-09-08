/**
 * Created by Admin on 12/1/2015.
 */

jQuery.fn.extend({
    equalHeight: function () {
        jQuery(this).css('height', 'auto');
        var maxHeight = 0;
        jQuery(this).each(function () {
            if (jQuery(this).height() > maxHeight) {
                maxHeight = jQuery(this).height();
            }
        }).height(maxHeight);
    }
});

jQuery('.header-cart').hover(function () {
    jQuery(this).find('ul').slideToggle(200);
});

jQuery('.slick-arrow').click(function (e) {
    e.preventDefault();
});

jQuery(document).ready(function () {
    var menuLeft = document.getElementById('cbp-spmenu-s1'),
        showLeft = document.getElementById('showLeft'),
        closeLeft = document.getElementById('mobile-menu-close'),
        body = document.body;

    showLeft.onclick = function () {
        classie.toggle(this, 'active');
        classie.toggle(menuLeft, 'cbp-spmenu-open');
        disableOther('showLeft');
    };

    closeLeft.onclick = function () {
        classie.toggle(this, 'active');
        classie.toggle(menuLeft, 'cbp-spmenu-open');
        disableOther('showLeft');
    };

    function disableOther(button) {
        if (button !== 'showLeft') {
            classie.toggle(showLeft, 'disabled');
        }
    }
});