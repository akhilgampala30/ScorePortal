/**
 * Created by Mike on 12/25/13.
 */
$(function(){
    $('body').on('click', '.ClassNavLink', function(event){
        var CurrentObject = $(this);
        var DestinationLink = CurrentObject.attr('href');
        var LinkBits = /\/(class)\/(\d+)/.exec(DestinationLink);
        if(LinkBits!=null && history.pushState){
            history.pushState([],'',DestinationLink);
            navClass(LinkBits[2]);
            $(this).closest('.class').addClass('selectedclass').siblings().removeClass('selectedclass');
            return false;
        }
    });
});

function navClass(idClass){
    $('#wrapper').animate({'opacity':'0.3'}, 747); //Fade out to show that we're loading the new page
    //loadCSS('/pages/class/style/class.css');
    //loadCSS('/pages/class/style/OriginalGrades.css');
    $('#wrapper').load('/Class/a/1/'+idClass, function(response, status, xhr){
        ga('send', 'pageview', '/Class/a/1/'+idClass);
        $('#wrapper').animate({'opacity':'1'}, 747); //Fade in new page
    });
}
loadCSS = function(href) {
    $("head").append("<link>");
    var css = $("head").children(":last");
    css.attr({
        rel:  "stylesheet",
        type: "text/css",
        href: href
    });
};
loadJS = function(src) {
    var jsLink = $("<script type='text/javascript' src='"+src+"'>");
    $("head").append(jsLink);
};
