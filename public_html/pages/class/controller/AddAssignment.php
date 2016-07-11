<?php
/**
 * User: Mike
 * Date: 12/23/13
 * Time: 2:16 PM
 */

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
    dieJSON(-1, 'Not Logged In');  //TODO: Actually redirect on failure
}
//Validate Assignment Properties
if((!isset($_GET['AssignmentEarnedPoints']) || !is_numeric($_GET['AssignmentEarnedPoints']))
    || (!isset($_GET['AssignmentPossiblePoints']) || !is_numeric($_GET['AssignmentPossiblePoints']))
    || (!isset($_GET['modCategory']) || !is_numeric($_GET['modCategory']))
    || (!isset($_GET['idCategory']) || !is_numeric($_GET['idCategory']))
    || (!isset($_GET['idClass']) || !is_numeric($_GET['idClass']))
    || (!isset($_GET['AssignmentName'])))
{
    dieJSON(-1, 'Message Invalid'); //TODO: Actually redirect on failure
}

$idStudent = $_SESSION['StudentID'];

$AddedAssignmentObject = new Assignment();
$AddedAssignmentObject->ModifiedAssignmentEarnedPoints = $_GET['AssignmentEarnedPoints'];
$AddedAssignmentObject->ModifiedAssignmentPossiblePoints = $_GET['AssignmentPossiblePoints'];
$AddedAssignmentObject->idClasses = $_GET['idClass'];
$AddedAssignmentObject->AssignmentName = $_GET['AssignmentName'];
$AddedAssignmentObject->idStudents = $idStudent;
$modifiedCategory = ($_GET['modCategory']==0 ? false : true);

if($modifiedCategory){ //Set appropriate ID
    $AddedAssignmentObject->idModifiedCategory = $_GET['idCategory'];
}
else{
    $AddedAssignmentObject->idCategory = $_GET['idCategory'];
}

if(preg_match("/^[a-zA-Z0-9 ]+$/", $_GET['AssignmentName']) != 1) {
    dieJSON(-1, 'Invalid Assignment Name');
}

$db = connect();
if(!VerifyUniqueModifiedAssignmentName($AddedAssignmentObject, $db)){
    dieJSON(-1, 'Non-Unique Assignment Name');
}
if(!StudentIsEnrolledInClass($idStudent, $AddedAssignmentObject->idClasses, $db)){ //Checks both if idClass and idStudent is valid for the class
    dieJSON(-1, 'Not Enrolled in Class');
}
if($modifiedCategory && !idModifiedCategoryIsValid($AddedAssignmentObject->idCategory, $AddedAssignmentObject->idClasses, $idStudent, $db)){
    dieJSON(-1, 'Invalid Category');
}
if(!$modifiedCategory && !idCategoryIsValid($AddedAssignmentObject->idCategory, $AddedAssignmentObject->idClasses, $db)){
    dieJSON(-1, 'Invalid Category');
}

/*
 * Checked StudentObj/idStudent is set
 * Checked if idClass is valid and Student has permission
 * Checked if idCategory/idModifiedCategory exists
 * Checked if Assignment Name has only a-Z and 0-9
 * */

if(($result = AddUserAddedAssignment($AddedAssignmentObject, $db))===false){
    dieJSON(-1, 'Error fulfilling request');
}
else{
    UpdateClass($AddedAssignmentObject->idClasses, $db);
    dieJSON($result, 'Success');
}