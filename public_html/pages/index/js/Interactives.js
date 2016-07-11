/**
 * Created by Mike on 12/25/13.
 */
$(function(){
    $('#JavascriptWarning').hide();
    $('#ConnectButtonsContainer').show();
    //TODO: Fix glitch when going back and forth really fast messes this up
    $('body').on('mouseenter','#TopWelcomeStripWatchVideo, #TopWelcomeStripDemo', function(event){
        if($(this).attr('id') == 'TopWelcomeStripDemo'){
            $('#TopWelcomeStripDemo').filter(':not(:animated)').animate({width:'780'}, 800); $('#TopWelcomeStripWatchVideo').filter(':not(:animated)').fadeOut(800);
        }else{
            $('#TopWelcomeStripWatchVideo').filter(':not(:animated)').animate({width:'780'}, 800); $('#TopWelcomeStripDemo').filter(':not(:animated)').fadeOut(800);
        }
    }).on('mouseleave','#TopWelcomeStripWatchVideo, #TopWelcomeStripDemo', function(event){
        if($(this).attr('id') == 'TopWelcomeStripDemo'){
            if($('#TopWelcomeStripDemo').css('width') != '480px'){
                $('#TopWelcomeStripDemo').animate({width:'480'}, 800); $('#TopWelcomeStripWatchVideo').fadeIn(800);
            }
        }else{
            if($('#TopWelcomeStripWatchVideo').css('width') != '480px'){
                $('#TopWelcomeStripWatchVideo').animate({width:'480'}, 800); $('#TopWelcomeStripDemo').fadeIn(800);
            }
        }
    });
});

function LoginClick(){
    try{
        ga('send', 'event', 'Login', 'Click', 'Google');
    } catch(err){}
    setTimeout(function() {
        document.location.href = '/include/UserSessionState/CheckID.php?ServiceID=0';
    }, 100);
}