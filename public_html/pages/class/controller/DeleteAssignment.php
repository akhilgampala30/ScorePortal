<?php
/**
 * User: Mike
 * Date: 12/24/13
 * Time: 1:13 AM
 */

//Load Database Functions
if(!defined('DatabaseIncludeLoaded') || !constant('DatabaseIncludeLoaded')){
    include $_SERVER['DOCUMENT_ROOT'].'/paths.php';
    require $path['Database.php'];
}

session_start();

//Check if the Student Object exists
if(isset($_SESSION['StudentObj']) && $_SESSION['StudentID']) //TODO:Check if isset is enough
{
    $GlobalStudentObject = $_SESSION['StudentObj'];
    $idStudent = $_SESSION['StudentID'];
}
else{
    die('Student Object Not Set'); //TODO: Actually redirect on failure
}
/*Validation Stuffs*/
if((!isset($_GET['AssignmentID']) || !is_numeric($_GET['AssignmentID'])))
{
    die('Error: Invalid Assignment Parameters'); //TODO: Actually redirect on failure
}

$idAssignment = $_GET['AssignmentID'];
$db = connect();

if(GetUserAddedAssignment($idAssignment, $idStudent, $db) !== false){ //Make sure that this assignment actually exists
    if(DeleteUserAddedAssignment($idAssignment, $idStudent, $db) === false){
        die('Error: Could not complete request');
    }
}
else{
    die('Error: Invalid Assignment'); //TODO: Error handling here
}
$_SESSION['StudentObj'] = GetStudent($idStudent, $db);
//TODO: Refresh single assignment instead of complete Student to save time.
echo 'true';