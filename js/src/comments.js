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

