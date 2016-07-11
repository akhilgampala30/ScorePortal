<?php
/**
 * User: Mike
 * Date: 9/29/13
 * Time: 3:58 PM
 */

header( 'Content-type: text/html; charset=utf-8' ); //Set header for flushing
echo("<html><body>");
echo "<script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'></script>"; //include jQuery

// ~~ DEBUG Reset All Bullets
for($ii=0;$ii<4;$ii++){
    SetBulletColor($ii,'black');
}

//Set Notification Images
function SetBulletColor($stage,$status){
    ob_implicit_flush(1);
    echo "<script>$(parent.document).find('table img:eq({$stage})').attr('src','/images/icons/bullet_{$status}.png');</script>";
    switch($status){
        case 'blue':
            echo "<script>$(parent.document).find('table span:eq({$stage})').addClass('CurrentlyLoading');</script>";
            break;
        default:
            echo "<script>$(parent.document).find('table span:eq({$stage})').removeClass('CurrentlyLoading');</script>";
            break;
    }
    echo str_repeat('&nbsp;',20); //Ensure browser will display data
    flush();
    ob_flush();
    ob_start();
}

function ErrorMessageVisible(){
    echo "<script> $(parent.document).find('#ErrorMessage').show(); </script>";
    echo str_repeat('&nbsp;', 20); //Ensure browser will display data
    flush();
    ob_flush();
}

function DoneButtonVisible(){
    echo "<script> $(parent.document).find('#DoneButton').show(); window.top.location.href = '/Login'; </script>";
    echo str_repeat('&nbsp;', 20); //Ensure browser will display data
    flush();
    ob_flush();
}