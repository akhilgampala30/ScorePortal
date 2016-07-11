/**
 * Created by Mike on 12/28/13.
 */
$(function(){
    $('body').on('click','#UpdateGrades', function(event){
        var CurrentObject = $(this);
        if(CurrentObject.children('img').first().hasClass('fa-spin')){
            return;
        }
        CurrentObject.children('img').addClass('fa-spin');
        CurrentObject.children('span').text('Updating Grades...');
        ga('send', 'event', 'Update', 'Click', 'Quick Stats Update Button');
        $.get("/PullGrades", function( data ) {
            CurrentObject.children('img').removeClass('fa-spin');
            try{
                var msg = $.parseJSON(data);
                if(msg.code == 1){
                    CurrentObject.children('span').text('Update Successful!');
                    ga('send', 'event', 'Update', 'Updated', 'Successful Quick Stats Update');
                }
                else{
                    CurrentObject.children('span').text('Failed to Update.');
                }
            }catch(e){
                CurrentObject.children('img').removeClass('fa-spin');
                CurrentObject.children('span').text('Failed to Update.');
            }
        }).fail(function() {
            CurrentObject.children('img').removeClass('fa-spin');
            CurrentObject.children('span').text('Failed to Update.');
        });
    })
});







console.log('       _       _          ____               _____                 _                                  _     _______                   _ ');
console.log('      | |     (_)        / __ \\             |  __ \\               | |                                | |   |__   __|                 | |');
console.log('      | | ___  _ _ __   | |  | |_   _ _ __  | |  | | _____   _____| | ___  _ __  _ __ ___   ___ _ __ | |_     | | ___  __ _ _ __ ___ | |');
console.log("  _   | |/ _ \\| | '_ \\  | |  | | | | | '__| | |  | |/ _ \\ \\ / / _ \\ |/ _ \\| '_ \\| '_ ` _ \\ / _ \\ '_ \\| __|    | |/ _ \\/ _` | '_ ` _ \\| |");
console.log(' | |__| | (_) | | | | | | |__| | |_| | |    | |__| |  __/\\ V /  __/ | (_) | |_) | | | | | |  __/ | | | |_     | |  __/ (_| | | | | | |_|');
console.log('  \\____/ \\___/|_|_| |_|  \\____/ \\__,_|_|    |_____/ \\___| \\_/ \\___|_|\\___/| .__/|_| |_| |_|\\___|_| |_|\\__|    |_|\\___|\\__,_|_| |_| |_(_)');
console.log('                                                                          | |                                                           ');
console.log('                                                                          |_|                                                           ');
console.log('Contact us at support@ScorePortal.org or hit us up on www.Facebook.com/ScorePortal');