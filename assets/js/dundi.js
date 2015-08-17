$(document).ready(
    function(){
        $(".info").each(function(){
            $(this).after('<span class="help">?<span>' + $(this).find('span').html() + '</span></span>');
            $(this).find('span').remove();
            $(this).html($(this).html());
        });
		$(".help").on('mouseenter', function(){
			side = fpbx.conf.text_dir == 'lrt' ? 'left' : 'right';
			var pos = $(this).offset();
			var offset = (200 - pos.side) + "px";
			$(this).find("span").css(side, offset).stop(true, true).delay(500).animate({opacity:"show"}, 750);
		}).on('mouseleave', function(){
			$(this).find("span").stop(true, true).animate({opacity:"hide"}, "fast");
		});
        $(".dundi_ctxentry_type").change(function(){
            if (this.value === "") {
                $(this).next().hide();
            } else {
                var repl = $("#dundi_ctx_" + this.value).
                    clone().
                    removeAttr("id").
                    attr("name", "context_entries[]");
                $(this).next().replaceWith(repl);
                $(this).next().show().focus();
            }
        });
        $("#dundi_ctxentry_more").click(function(){
            var oldlast = $("td.dundi_ctxentry").last().parent();
            var newlast = oldlast.clone(true, true).insertAfter(oldlast);
            // get the delete button on the old last row
            oldlast.children("td").last().replaceWith(oldlast.prev("tr").children("td").last().clone(true,true));
            $(".dundi_ctxentry_type").last().next().hide();
            // remove the add button from the old last row
            $(this).remove();
        });
        $(".dundi_ctxentry_del").click(function(){
            $(this).closest("tr").remove();
        });
    }
);