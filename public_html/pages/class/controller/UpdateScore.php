<?php
/**
 * User: Mike
 * Date: 12/8/13
 * Time: 5:50 PM
 */

//RewriteRule     ^UpdateScore/(\d+)/(\d+)/(\d+)/?$    /pages/class/controller/UpdateScore.php?AssignmentEarnedPoints=$1&AssignmentPossiblePoints=$2&AssignmentID=$3    [NC,L]
//RewriteRule     ^UpdateScore/(\d+)/(\d+)/(\d+)/(\d+)/(.+)/?$    /pages/class/controller/UpdateScore.php?AssignmentEarnedPoints=$1&AssignmentPossiblePoints=$2&AssignmentID=$3&CategoryID=$4&AssignmentName=$5    [NC,L]

//Still Needs Testing
if(!defined('DatabaseIncludeLoaded') || !constant('DatabaseIncludeLoaded')){
    include $_SERVER['DOCUMENT_ROOT'].'/paths.php';
    require $path['Database.php'];
}
include $path['SessionState'];

session_start();

if(isset($_SESSION['StudentObj']) && $_SESSION['StudentID']) //TODO:Check if isset is enough
{
    $GlobalStudentObject = $_SESSION['StudentObj'];
    $idStudent = $_SESSION['StudentID'];
}
else{
    dieJSON(-1, 'Student not set');
}
//jQuery should set these values to a data-value param and any action reads from the data-value. This prevents from submitting half-finished typing in text box and then bookmarking.
//I'ma just abuse data-value for the jQuery part of this implementation

//Validate Assignment Properties
if((!isset($_GET['AssignmentEarnedPoints']) || !is_numeric($_GET['AssignmentEarnedPoints']))
    || (!isset($_GET['AssignmentPossiblePoints']) || !is_numeric($_GET['AssignmentPossiblePoints']))
    || (!isset($_GET['idAssignmentScore']) || !is_numeric($_GET['idAssignmentScore'])))
{
    dieJSON(-1, 'Invalid IDs'); //TODO: Actually redirect on failure
}

//TODO: I need to learn how to sanitize...

$idStudent = $_SESSION['StudentID'];

$UpdatedAssignmentObject = new Assignment();
$UpdatedAssignmentObject->ModifiedAssignmentEarnedPoints = $_GET['AssignmentEarnedPoints'];
$UpdatedAssignmentObject->ModifiedAssignmentPossiblePoints = $_GET['AssignmentPossiblePoints'];
$UpdatedAssignmentObject->idAssignment =  $_GET['idAssignmentScore']; //For added assignments
$UpdatedAssignmentObject->idAssignmentScore = $_GET['idAssignmentScore'];
$UpdatedAssignmentObject->idStudents = $idStudent;

$db = connect();

if(isset($_GET['CategoryID']) && isset($_GET['AssignmentName']) && is_numeric($_GET['CategoryID']) && GetUserAddedAssignment($UpdatedAssignmentObject->idAssignment, $idStudent, $db) !== false){ //If it's a user added assignment
    $UpdatedAssignmentObject->AssignmentName = $_GET['AssignmentName']; //TODO: possibly sanitize inputs more
    if(isset($_GET['modCategory']) && $_GET['modCategory'] == 1){
        $UpdatedAssignmentObject->isModifiedCategory = true;
    }
    $UpdatedAssignmentObject->idCategory = $_GET['CategoryID'];
    ModifyUserAddedAssignment($UpdatedAssignmentObject, $idStudent, $db);
}
else{ //Modified Assignment
    if(ModifyModifiedAssignmentScore($UpdatedAssignmentObject, $db)===false){
        dieJSON(-1, 'Error updating');
    }
}
UpdateStudentObject($db);
dieJSON(1, (isset($UpdatedAssignmentObject->AssignmentName)?'A'.$UpdatedAssignmentObject->idAssignment:'M'.$UpdatedAssignmentObject->idAssignmentScore));