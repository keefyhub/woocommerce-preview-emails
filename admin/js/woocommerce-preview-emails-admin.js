(function ($) {
    var target = $('.woocommerce_page_wc-settings h2:contains("Email template")');
    var nextTarget = target.next('p');

    target.addClass('wcpe-u-hidden');
    nextTarget.addClass('wcpe-u-hidden');
})(jQuery);