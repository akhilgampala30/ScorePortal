/**
 * Created by Jacky on 1/15/14.
 */
$(document).ready(function () {
    var lastClickedName = "Intro",
        avatar = $('.Avatar');

    avatar.hover(function () {
        if (parseFloat($(this).css("opacity").substring(0, 3)) < 0.8)
            $(this).stop().animate({opacity: 0.8}, "fast");
    }, function () {
        if ($(this).attr("title") != lastClickedName)
            $(this).stop().animate({opacity: 0.6}, "fast");
    });

    avatar.click(function (event) {
        var clickedName = $(this).attr("title");
        var reducedLastClickedName = lastClickedName.replace(/\s+/g, '');
        if (clickedName == "Intro") {
            $("#" + reducedLastClickedName).css("z-index", "-1");
            $("#" + clickedName).css("z-index", "0");
            $(this).stop().animate({opacity: 1});
            if (lastClickedName != "Intro")
                $("[title='" + lastClickedName + "']").stop().animate({opacity: .6});
        }
        else {
            var reducedClickedName = clickedName.replace(/\s+/g, '');
            if (lastClickedName == clickedName) {
                $("#" + reducedClickedName).css("z-index", "-1");
                $("#Intro").css("z-index", "0");
                $("[title='Intro']").stop().animate({opacity: 1});
                $(this).stop().animate({opacity: .6});
            }
            else {
                $("#" + reducedClickedName).css("z-index", "0");
                $("#" + reducedLastClickedName).css("z-index", "-1");
                $("[title='" + lastClickedName + "']").stop().animate({opacity: .6});
                $(this).stop().animate({opacity: 1});
            }
        }
        if (lastClickedName == clickedName)
            lastClickedName = "Intro";
        else
            lastClickedName = clickedName;
    });
});