    // beta live search

    function betasearch() {
        var query_value = $('input#live-search').val();
        $('span#live-search-string').html(query_value);
        if (query_value !== '') {
            $.ajax({
                type: "POST",
                url: myajax.ajaxurl,
                data: {
                    live_search_query: query_value,
                    action: 'live_search',
                    security: myajax.ajaxnonce
                },
                cache: false,
                success: function(data) {
                    var html = $.parseJSON(data);
                    $("#live-search-results").html(html);
                }
            });
        }
        return false;
    }

    $("input#live-search").live("keyup", function(e) {

        if (e.keyCode != 40 && e.keyCode != 38 && e.keyCode != 13) {

            clearTimeout($.data(this, 'timer'));
            var search_string = $(this).val();

            if (search_string == '') {
                $("#live-search-results").fadeOut();
                $('#live-search-text').fadeOut();
            } else {
                $("#live-search-results").fadeIn();
                $('#live-search-text').fadeIn();
                $(this).data('timer', setTimeout(betasearch, 100));
            };

        }

    });

    var chosenResult = "";

    $("input#live-search").focus().keydown(function(e) {

        if (e.keyCode == 13) {
            var search_string = $(this).val();
            document.location.href = '/search/' + search_string;
        }

        if (e.keyCode == 40) {
            if (chosenResult === "") {
                chosenResult = 0;
            } else if ((chosenResult + 1) < $('#live-search-results li').length) {
                chosenResult++;
            }
            $('#live-search-results li').removeClass('selected');
            $('#live-search-results li:eq(' + chosenResult + ')').addClass('selected');
            return false;
        }

        if (e.keyCode == 38) {
            if (chosenResult === "") {
                chosenResult = 0;
            } else if (chosenResult > 0) {
                chosenResult--;
            }
            $('#live-search-results li').removeClass('selected');
            $('#live-search-results li:eq(' + chosenResult + ')').addClass('selected');
            return false;
        }
    });

});
