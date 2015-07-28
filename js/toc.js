! function(a) {
    a.fn.inThisPost = function(b) {
        var c = a.extend({
                offset: 50,
                startingLevel: "h2",
                subItems: !0,
                pageHeader: ".post-header, .entry-header",
                title: "In this Post:",
                comments: "#comments",
                commentsLabel: "Comments",
                position: "top"
            }, b),
            d = this,
            e = [],
            f = parseInt(c.startingLevel.replace(/\D/g, "")),
            g = {
                init: function() {
                    return this.loadList(), this.generateBlock(), this.scrollTo("[data-content-target]"), this.onScroll(), d
                },
                slugfy: function(a) {
                    return a.toString().toLowerCase().replace(/\s+/g, "-").replace(/[^\w\-]+/g, "").replace(/\-\-+/g, "-").replace(/^-+/, "").replace(/-+$/, "")
                },
                onScroll: function() {
                    a(window).on("scroll", function() {
                        g.sticky(), g.scrollSpy()
                    }), this.sticky(), this.scrollSpy()
                },
                scrollSpy: function() {
                    a("[data-content-id]").each(function() {
                        var b = a(window).scrollTop(),
                            d = a(this).offset().top - c.offset - a(".content-index-block").outerHeight() - 10,
                            e = a('[data-content-target="' + a(this).attr("data-content-id") + '"]');
                        b > d && (a(".content-index-block a").removeClass("active"), e.toggleClass("active"))
                    })
                },
                sticky: function() {
                    var b = a(".content-index-block");
                    b.parent().css("position", "relative");
                    var d = a(c.pageHeader).offset().top,
                        e = a(window).scrollTop();
                    e > d ? b.slideDown("fast") : b.slideUp("fast")
                },
                getSubitems: function(b, c) {
                    b.nextUntil("h" + c).not("p, span, div, pre").each(function() {
                        var c = a(this);
                        c.attr("data-content-id", g.slugfy(c.text())), c.attr("id", g.slugfy(c.text())), c.attr("data-content-parent", g.slugfy(b.text())), e.push(c)
                    })
                },
                loadList: function() {
                    d.find("h1:first-child").each(function() {
                        var b = a(this),
                            c = b.text();
                        b.attr("data-content-id", g.slugfy(c)), b.attr("id", g.slugfy(c)), e.push(b)
                    }), d.find("h" + f).each(function() {
                        var b = a(this),
                            d = b.text();
                        b.attr("data-content-id", g.slugfy(d)), b.attr("id", g.slugfy(d)), e.push(b), c.subItems === !0 && g.getSubitems(b, f)
                    }), c.comments !== !1 && a(c.comments).each(function() {
                        var b = a(this),
                            d = c.commentsLabel;
                        b.attr("data-content-id", g.slugfy(d)), b.attr("id", g.slugfy(d)), e.push(b)
                    })
                },
                scrollTo: function(b) {
                    a(b).on("click", function(b) {
                        if (b.preventDefault(), location.pathname.replace(/^\//, "") === this.pathname.replace(/^\//, "") && location.hostname === this.hostname) {
                            var d = a(this.hash);
                            if (d = d.length ? d : a("[name=" + this.hash.slice(1) + "]"), d.length) return a("html, body").animate({
                                scrollTop: d.offset().top - c.offset - a(".content-index-block").outerHeight()
                            }, 1e3), !1
                        }
                    })
                },
                generateBlock: function() {
                    var b = a('<ul class="content-index-block"></ul>');
                    a.each(e, function(d, e) {
                        var f = e.attr("data-content-id"),
                            g = c.comments !== !1 && f === c.comments.replace("#", "") ? c.commentsLabel : e.text(),
                            h = a('<li data-content-list="' + f + '"></li>'),
                            i = a('<a href="#' + f + '" data-content-target="' + f + '">' + g + "</a>");
                        if (h.append(i), void 0 === e.attr("data-content-parent")) {
                            var j = a("<ul></ul>");
                            h.append(j), b.append(h)
                        } else {
                            var k = b.find('[data-content-list="' + e.attr("data-content-parent") + '"] > ul');
                            k.append(h)
                        }
                    }), b.addClass("content-index-display-inline"), b.css(c.position, 0), a("body").prepend(b)
                }
            };
        g.init()
    }, a(".post").inThisPost({
        startingLevel: "h2",
        position: "top",
        subItems: true
    })
}(jQuery);
