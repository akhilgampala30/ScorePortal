<?php
/**
 * User: Mike
 * Date: 12/22/13
 * Time: 11:43 AM
 */

//RewriteRule     ^ResetAssignment/(\d+)/?$      /pages/class/controller/ResetAssignment.php?AssignmentID=$1    [NC,L]

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
    dieJSON(-1, 'Student not set');
}
/*Validation Stuffs*/
if((!isset($_GET['AssignmentID']) || !is_numeric($_GET['AssignmentID'])))
{
    dieJSON(-1, 'Invalid assignment');
}

$idAssignmentScore = $_GET['AssignmentID'];
$db = connect();

if(!DeleteModifiedAssignmentScore($idAssignmentScore, $idStudent, $db)){ //Delete modified assignment, if it doesn't exist then throw error
    dieJSON(-1, 'Unable to complete request');
}
UpdateStudentObject($db);
dieJSON(1, $idAssignmentScore);