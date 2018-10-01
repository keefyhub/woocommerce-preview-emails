(function ($) {
    var target = $('.woocommerce_page_wc-settings h2:contains("Email template")');
    var nextTarget = target.next('p');

    target.addClass('wcep-u-hidden');
    nextTarget.addClass('wcep-u-hidden');
})(jQuery);