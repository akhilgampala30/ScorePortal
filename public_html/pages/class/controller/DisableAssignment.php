<?php
/**
 * User: Mike
 * Date: 12/21/13
 * Time: 7:19 PM
 */

//RewriteRule     ^DisableAssignment/(\d+)/(\d)/?$      /pages/class/controller/DisableAssignment.php?AssignmentID=$1&AssignmentDisabled=$2    [NC,L]

//Load Database Functions
if(!defined('DatabaseIncludeLoaded') || !constant('DatabaseIncludeLoaded')){
    include $_SERVER['DOCUMENT_ROOT'].'/paths.php';
    require $path['Database.php'];
}
include $path['SessionState'];

session_start();

//Check if the Student Object exists
if(isset($_SESSION['StudentObj']) && $_SESSION['StudentID']) //TODO:Check if isset is enough
{
    $GlobalStudentObject = $_SESSION['StudentObj'];
    $idStudent = $_SESSION['StudentID'];
}
else{
    dieJSON(-1, 'Student Not Set');
}
/*Validation Stuffs*/
if((!isset($_GET['AssignmentID']) || !is_numeric($_GET['AssignmentID']))
    || (!isset($_GET['AssignmentDisabled']) || !is_numeric($_GET['AssignmentDisabled'])))
{
    dieJSON(-1, 'Invalid Parameters');
}

$idAssignment = $_GET['AssignmentID'];
$AssignmentDisabled = $_GET['AssignmentDisabled'];
$db = connect();
$Added = false;
if(isset($_GET['Added']) && $_GET['Added']==1){ //If this is a user added assignment we're talking about here
    if(GetUserAddedAssignment($idAssignment, $idStudent, $db) !== false){ //Make sure that this assignment actually exists
        if($AssignmentDisabled == 0 || $AssignmentDisabled == 1){
            DisableUserAddedAssignment($idAssignment, $idStudent, $AssignmentDisabled, $db);
            $Added = true;
        }
        else{
            dieJSON(-1, 'Invalid Assignment');
        }
    }
    else{
        dieJSON(-1, 'Invalid Assignment');
    }
}
else{
    if($AssignmentDisabled == 0 || $AssignmentDisabled == 1){
        if(!DisableModifiedAssignment($idAssignment, $idStudent, $AssignmentDisabled, $db)){
            dieJSON(-1, 'Unable to Complete Request');
        }
    }
    else{
        dieJSON(-1, 'Invalid ID');
    }
}
UpdateStudentObject($db);
dieJSON(1, ($Added?'A':'M').$idAssignment);