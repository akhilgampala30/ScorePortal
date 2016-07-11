<?php
/**
 * User: Mike
 * Date: 12/24/13
 * Time: 5:02 PM
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
if((!isset($_GET['ClassID']) || !is_numeric($_GET['ClassID'])))
{
    die('Error: Invalid Assignment Parameters'); //TODO: Actually redirect on failure
}

$idClass = $_GET['ClassID'];
$db = connect();

if(RemoveAllAddedAssignmentsForClass($idClass, $idStudent, $db) === false || RemoveAllModifiedAssignmentsForClass($idClass, $idStudent, $db) === false){
    die('Error: Invalid Class!'); //TODO: Error handling here
}
$_SESSION['StudentObj'] = GetStudent($idStudent, $db);
//TODO: Refresh single assignment instead of complete Student to save time.
echo 'true';