<?php
/**
 * User: Mike
 * Date: 12/25/13
 * Time: 10:22 PM
 */

//Updates through ImportXML for grades, no ScreenScrapping

require $_SERVER['DOCUMENT_ROOT'].'/paths.php';
require $path['Database.php'];
require_once $path['PowerAPICore.php'];
require_once $path['PowerAPICourse.php'];
require_once $path['PowerAPIUser.php'];
require $path['ImportXML.php'];
require $path['ScreenScrap.php'];

session_start();
if(!isset($_SESSION['StudentObj'])){
    dieJSON(-1, 'Invalid Student');
}
if(isset($_SESSION['UpdatingGrades']) && $_SESSION['UpdatingGrades']){
    dieJSON(-1, 'Update in Progress');
}
/* @var $StudentObj Student*/
$StudentObj = $_SESSION['StudentObj'];

//Make sure the student can't make multiple requests
$_SESSION['UpdatingGrades'] = true;
session_write_close(); //Release lock

$db = connect();

set_time_limit(120); //To prevent blocking our servers with abnormally long requests

try{
    $ps = new PowerAPI\Core($StudentObj->Schools->PowerschoolRootURL);
    $user = $ps->auth($StudentObj->SchoolDistrictID, $StudentObj->SchoolDistrictPassword);
} catch (Exception $e) {
    dieJSON(-1, 'Invalid login information.');
}
$Transcript = $user->fetchTranscript();
/*
$tarp = $_SERVER['DOCUMENT_ROOT'].'/tempXML/'.'72';
$Transcript = file_get_contents($tarp);
*/
if($Transcript === false){
    dieJSON(-1, 'Invalid Student');
}

parseXMLtoDB($Transcript, $StudentObj->idStudents, $StudentObj->Schools->idSchools, false, $db, true);
ScrapPS($StudentObj->idStudents, $db, $user);

session_start(); //Get lock back

//Set updating grades flag to false so the user can update again and update the student object
$_SESSION['UpdatingGrades'] = false;
$_SESSION['StudentObj'] = GetStudent($StudentObj->idStudents, $db);
dieJSON(1, 'Updated');