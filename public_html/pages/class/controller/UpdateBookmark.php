<?php
/**
 * User: Mike
 * Date: 11/9/13
 * Time: 11:52 AM
 */

//RewriteRule     ^UpdateBookmark/(\d+)/(\d)/?$      /pages/class/controller/UpdateBookmark.php?AssignmentID=$1&AssignmentBookmarked=$2    [NC,L]

//Load Database Functions
if(!defined('DatabaseIncludeLoaded') || !constant('DatabaseIncludeLoaded')){
    include $_SERVER['DOCUMENT_ROOT'].'/paths.php';
    require $path['Database.php'];
}
$Return = array();
session_start();

//Check if the Student Object exists
if(isset($_SESSION['StudentObj']) && $_SESSION['StudentID']) //TODO:Check if isset is enough
{
    $GlobalStudentObject = $_SESSION['StudentObj'];
    $idStudent = $_SESSION['StudentID'];
}
else{
    dieJSON(-2, 'Invalid Student');
}
/*Validation Stuffs*/
if((!isset($_GET['AssignmentID']) || !is_numeric($_GET['AssignmentID']))
    || (!isset($_GET['AssignmentBookmarked']) || !is_numeric($_GET['AssignmentBookmarked'])))
{
    dieJSON(-1, 'Invalid Input');
}

$idAssignment = $_GET['AssignmentID'];
$AssignmentBookmarked = $_GET['AssignmentBookmarked'];
$db = connect();

if(isset($_GET['Added']) && $_GET['Added'] == 1){ //If this is a user added assignment we're talking about here
    if(GetUserAddedAssignment($idAssignment, $idStudent, $db) !== false){ //Make sure that this assignment actually exists
        if($AssignmentBookmarked == 0 || $AssignmentBookmarked == 1){
            BookmarkUserAddedAssignment($idAssignment, $idStudent, $AssignmentBookmarked, $db);
        }
        else{
            dieJSON(-1, 'Invalid Bookmark Input');
        }
    }
    else{
        dieJSON(-1, 'Invalid Assignment');
    }
}
else{
    if(GetAssignment($idAssignment, $idStudent, $db) !== false){ //Make sure that this assignment actually exists
        if($AssignmentBookmarked == 0 || $AssignmentBookmarked == 1){
            BookmarkAssignment($idAssignment, $idStudent, $AssignmentBookmarked, $db);
        }
        else{
            dieJSON(-1, 'Invalid Bookmark Input');
        }
    }
    else{
        dieJSON(-1, 'Invalid Assignment');
    }
}
$_SESSION['StudentObj'] = GetStudent($idStudent, $db);
//TODO: Refresh single assignment instead of complete Student to save time.
dieJSON(1, 'true');