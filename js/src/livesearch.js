    // ajax live search

    $('.search-form').submit(function() {

        var s = $(this).find('.search-field').val();

        if (s.length == 0) {
            return;
        }

        $.ajax({
            url: $(this).attr('action') + '?s=' + encodeURIComponent(s) + '&action=search_ajax',
            type: 'get',
            dataType: 'html',

            beforeSend: function() {
                var load = '<div align="center"><img src="' + myajax.loading + '"></div>';
                $('#content').empty().html(load);
            },

            success: function(data) {
                // remove infinite scrolling from live search results
		if ($.ias) {
			$.ias().unbind();
		}
                $('#content').empty().html(data);
            }

        });
        return false;
    });

    var timer, currentKey;
    var searched = false;

    $('.search-field').keyup(function() {

        clearTimeout(timer);

        timer = setTimeout(function() {

            var sInput = $('.search-field');
            var s = sInput.val();

            if (s.length == 0) {
                if (searched) {

                    $('#content').empty().html();
                    sInput.focus();
                    //$('.search-form span.processing').remove();
                    searched = false;
                }
                currentKey = s;
            } else {
                if (s != currentKey) {
                    if (!searched) {
                        searched = true;
                    }
                    currentKey = s;
                    if (s != ' ') {
                        $('.search-form').submit();
                        $('.search-field').val("");
                    }
                }
            }
        }, 800);
    });

