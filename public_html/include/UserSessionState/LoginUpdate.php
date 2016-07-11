<?php
/**
 * User: Mike
 * Date: 4/6/14
 * Time: 5:01 PM
 *
 * Ran every time the user login in fresh. Just pulls the grades and updates the overall grades, assumes no weights were changed. Uses ppstudentasmntlist.html to construct.
 */

if (!defined('DatabaseIncludeLoaded') || !constant('DatabaseIncludeLoaded')) {
    include $_SERVER['DOCUMENT_ROOT'] . '/paths.php';
    require $path['Database.php'];
}
require_once $path['PowerAPICore.php'];
require_once $path['PowerAPICourse.php'];
require_once $path['PowerAPIUser.php'];
require $path['ImportAssignmentList.php'];

if(!isset($_SESSION['StudentObj'])){
    dieJSON(-1, 'Invalid Student');
}
if(isset($_SESSION['LoginUpdate']) && $_SESSION['LoginUpdate']){
    dieJSON(-1, 'Update in Progress');
}
/* @var $StudentObj Student*/
$StudentObj = $_SESSION['StudentObj'];

//Make sure the student can't make multiple requests
$_SESSION['LoginUpdate'] = true;
session_write_close(); //Release lock

$db = connect();

set_time_limit(120); //To prevent blocking our servers with abnormally long requests

try{
    $ps = new PowerAPI\Core($StudentObj->Schools->PowerschoolRootURL);
    $user = $ps->auth($StudentObj->SchoolDistrictID, $StudentObj->SchoolDistrictPassword);
} catch (Exception $e) {
    dieJSON(-1, 'Invalid login information.');
}
$Transcript = $user->fetchAssignmentList();
//$Transcript = file_get_contents('http://spu.localhost/test/aa.html');
$courses = $user->getCourses();
$CourseNumericGrades = array();
$CourseLetterGrades = array();
foreach($courses as $course){
    $CourseNumericGrades[$course->getName()] = end($course->getScores()); //Set the key as the course name and get last (latest) score
    $CourseLetterGrades[$course->getName()] = end($course->getLetters());
}
if($Transcript === false){
    dieJSON(-1, 'Invalid Student');
}

import($Transcript, $StudentObj->idStudents, $StudentObj->Schools->idSchools, $CourseNumericGrades, $CourseLetterGrades, $user->getGPA(), $db);

session_start(); //Get lock back

//Set updating grades flag to false so the user can update again and update the student object
$_SESSION['LoginUpdate'] = false;
$_SESSION['StudentObj'] = GetStudent($StudentObj->idStudents, $db);
//dieJSON(1, 'Updated');