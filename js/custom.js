/*!
 * Custom Functions for the TwentyFifteen Child Theme
 */

jQuery(document).ready(function($) {

    // ajax infinite scrolling

    var ias = $.ias({
        container: '#main',
        item: '.post',
        pagination: '.navigation',
        next: '.next.page-numbers'
    });

    ias.extension(new IASSpinnerExtension());
    ias.extension(new IASHistoryExtension());
    ias.extension(new IASPagingExtension());

    // alphabet ajax post loading

    $('.azindex').click(function(e) {
        e.preventDefault();

        if ($(this).parent().hasClass("active")) {
            return false;
        }
        var letter = $(this).attr("rel");
        $.ajax({
            url: myajax.ajaxurl,
            data: {
                action: 'myajax_alphabet',
                letter: letter
            },
            type: 'get',
            beforeSend: function() {
                $(this).parent().addClass("active").siblings().removeClass();
                $('#main').empty().html('<center><img src="' + myajax.loading + '"></center>');
            },
            error: function(request) {
                alert(request.responseText);
            },
            success: function(data) {
                $('#main').empty().html(data);
            }
        });
        return false;
    });

    // ajax comments

    $('#commentform').on('submit', function(e) {

        e.preventDefault();

        if ($("#comment_error").length == 0) var $message = $('<span class="comments_error" id="comment_error"></span>').appendTo("#commentform");

        $.ajax({

            beforeSend: function(e) {
                $("#comment_error").html('Processing...');
            },

            type: 'post',
            url: $(this).attr('action'),
            data: $(this).serialize(),
            dataType: 'html',
            error: function(e) {
                if (e.status == 500) {
                    $("#comment_error").html(e.responseText.split('<p>')[1].split('</p>')[0]);
                } else if (e.status == 'timeout') {
                    $("#comment_error").html('Error:Server time out,try again!');
                } else {
                    $("#comment_error").html('too fast error'); //too fast error
                }
            },

            success: function(data) {
                $('#comments').html('<center><img src="' + myajax.loading + '"></center>');
                var link = document.location.href;

                $.ajax({
                    type: "GET",
                    cache: false,
                    url: link,
                    success: function(data) {
                        var oneval = $(data).find('#comments');
                        $('#comments').html(oneval.html());
                    }
                }).done(function() {});

                $("#comment_error").html('thank you');
            }
        });

        return false;

    });

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
                $.ias().unbind();

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

    // ajax dropdown archives

    $("#archive-browser select").change(function() {

        $(".message").hide();
        $("#archive-content").empty().html('<div style="text-align: center; padding: 30px;"><img src="' + myajax.loading + '"></div>');

        var date = $('#month-choice option:selected').val();
        var dateChunk = date.split("/");
        var year = dateChunk[3];
        var month = dateChunk[4];
        var cat = $('#cat').val();

        $.ajax({
            url: myajax.ajaxurl,
            type: 'GET',
            data: {
                action: 'load_posts',
                _wpnonce: myajax.custom_nonce,
                cat: cat,
                month: month,
                year: year,
            },
            success: function(data) {
                if (date == 'no-choice' && cat == "-1") {
                    $("#archive-content").empty().html('<div class="message" align="center">Please choose from above.</div>');
                } else {
                    $("#archive-content").empty().html(data);
                }
            }
        });
        return false;
    });

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
