(function ($) {
    // TODO - removed nextTarget in future update. Left for backwards compatibility
    var target = $('.woocommerce_page_wc-settings h2:contains("Email template")');
    var nextTarget = target.next('p');

    nextTarget.addClass('wcpe-u-hidden');
})(jQuery);