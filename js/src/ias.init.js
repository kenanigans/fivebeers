   // Infinite Scrolling

   if ($.ias) {
        var ias = $.ias({
            container: '#main',
            item: '.post',
            pagination: '.navigation',
            next: '.next.page-numbers'
        });

        ias.extension(new IASSpinnerExtension());
        ias.extension(new IASHistoryExtension());
        ias.extension(new IASPagingExtension());
    }

