(function ($) {
    $j(document).ready(function(){
        if (window.PIE) {
            $j('.pie, button.button.black span, button.button span, .header .form-search .search-autocomplete ul, .dashboard .data-table .link-reorder, .addresses-additional .link-remove, .my-account .link-reorder, .my-account .link-print').each(function() {
                PIE.attach(this);
            });
        }
    });
})(jQuery);