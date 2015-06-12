    // Scroll to Top

   $(window).scroll(function() {
        if ($(this).scrollTop() > 400) $(".totop").fadeIn();
        else $(".totop").fadeOut();
    });

    $(".totop").click(function() {
        $("body,html").animate({
            scrollTop: 0
        }, 800);
        return false;
    });

