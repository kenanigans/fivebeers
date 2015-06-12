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
		// remove infinite on alphabet load
		if ($.ias) {
			$.ias().unbind();
		}
                $('#main').empty().html(data);
            }
        });
        return false;
    });

