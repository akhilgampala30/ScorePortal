<?php

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache'); // recommended to prevent caching of event data.

/**
 * User: Mike
 * Date: 9/28/13
 * Time: 9:48 PM
 */

session_start();
require $_SERVER['DOCUMENT_ROOT'].'/paths.php';
require $path['Database.php'];
require $path['UpdateStatus.php'];
require $path['ImportXML.php'];
require $path['ScreenScrap.php'];

//TODO: Remove This
if($_SESSION['AuthUserEmail']=="scoreportalta@gmail.com"){
    $dbConnection = connect(); //Set Connection for this session
    $getUserID = GetUserIDFromLoginID($_SESSION['LoginID'], $dbConnection, $_SESSION['ServiceID']);
    die($getUserID);
    $dbUserID = AddUserToDatabase("ScorePortal","DevTeam","scoreportalta@gmail.com");
    AddLoginUserService(0, $_SESSION['LoginID'], $dbUserID);
    die("Done!");
}
if($_SESSION['AuthUserEmail']=="t7.mike@gmail.com"){
    $dbConnection = connect(); //Set Connection for this session
    $ggg = CheckIfStudentExists("15623", "by84rzu5", 1, $dbConnection);
    var_dump($ggg);
    die;
}

if(!isset($_SESSION['Validate']) || !$_SESSION['Validate'] || !isset($_SESSION['PSUser']) || !isset($_SESSION['SchoolID'])
    || !isset($_SESSION['StudentID']) || !isset($_SESSION['StudentPassword']) || !isset($_SESSION['AuthUserEmail']) || !isset($_SESSION['LoginID']) || !isset($_SESSION['ServiceID'])){
    die("Invalid Registration Token");
}

$SchoolID = $_SESSION['SchoolID'];
$StudentID = $_SESSION['StudentID'];
$StudentPassword = $_SESSION['StudentPassword'];
$Email = $_SESSION['AuthUserEmail'];
$FirstName = $_SESSION['AuthUserFirstName'];
$LastName = $_SESSION['AuthUserLastName'];

$dbConnection = connect(); //Set Connection for this session

$getUserID = GetUserIDFromLoginID($_SESSION['LoginID'], $dbConnection, $_SESSION['ServiceID']);
$getStudentID = GetStudentIDFromUserID($_SESSION['UserID'], $dbConnection);

if($getUserID !== false && $getStudentID !== false){
    die("Expired Registration Token");
}

$PSUser = unserialize($_SESSION['PSUser']);

$dbConnection->exec('START TRANSACTION;');

//VALIDATING LOG IN CREDENTIALS...
SetBulletColor(0, 'blue');

//OBTAINING GRADE DATA...
SetBulletColor(0, 'green');
SetBulletColor(1, 'blue');
set_time_limit(120); //To prevent blocking our servers with abnormally long requests

//session_write_close(); //Prevents other scripts from the user being locked during the long request
$Transcript = $PSUser->fetchTranscript();
//session_start(); //Gets the session back
//echo ($Transcript);
if($Transcript === false){
    SetBulletColor(1, 'red');
    ErrorMessageVisible(); //Show error message if transcript times out
    die('Connection Timeout');
}

//SETTING UP ACCOUNT
SetBulletColor(1, 'green');
SetBulletColor(2, 'blue');
$XMLFirstName='';$XMLLastName='';
if(preg_match_all('/<FirstName>(.*?)<\/FirstName>/',$Transcript, $fMatches) == 1){
    $XMLFirstName = $fMatches[1][0];
}
if(preg_match_all('/<LastName>(.*?)<\/LastName>/',$Transcript, $lMatches) == 1){
    $XMLLastName = $lMatches[1][0];
}
//See if the credentials provided already match an existing student, if so, match the user with that student
$StudentExists = CheckIfStudentExists($StudentID, $StudentPassword, $SchoolID, $dbConnection);
if($StudentExists !== false){
    $dbStudentID = $StudentExists['idStudents'];
}
else{ //If they're new
    $legacyStudentID = CheckLegacyStatus($_SESSION['LoginID'], $Email, $XMLFirstName, $XMLLastName, $dbConnection); //Check if the student has been imported over
    if($legacyStudentID !== false){
        //This student was already registered under the old database
        $dbStudentID = $legacyStudentID['idStudents'];
        UpdateLegacyStudent($legacyStudentID['idStudents'], $FirstName, $LastName, $SchoolID, $StudentID, $StudentPassword, $Email);
    }else{ //If completely new, register the student as usual
        $dbStudentID = AddStudentToDatabase($FirstName, $LastName, $SchoolID, $StudentID, $StudentPassword, $Email);
    }
}
if(isset($_SESSION['ValidUser']) && $_SESSION['ValidUser'] && isset($_SESSION['UserID'])){
    $dbUserID = $_SESSION['UserID']; //This user is already valid but was unlinked
}else{
    $dbUserID = AddUserToDatabase($FirstName,$LastName,$Email);
    AddLoginUserService($_SESSION['ServiceID'], $_SESSION['LoginID'], $dbUserID);
}
AddStudent_User($dbStudentID, $dbUserID, $dbConnection);

parseXMLtoDB($Transcript, $dbStudentID, $SchoolID, true, $dbConnection, false);

//OBTAINING AUXILIARY DATA
SetBulletColor(2, 'green');
SetBulletColor(3, 'blue');

$_SESSION['StudentID'] = $dbStudentID;
$_SESSION['UserID'] = $dbUserID;

ScrapPS($dbStudentID, $dbConnection, $PSUser);

//ENCRYPTING DATA...
SetBulletColor(3, 'green');
SetBulletColor(4, 'blue');

//COMPLETE
SetBulletColor(4, 'green');
$dbConnection->exec('COMMIT;'); //Commit changes
DoneButtonVisible();
function AddStudentToDatabase($FirstName, $LastName, $SchoolID, $StudentID, $StudentPassword, $Email){
    global $dbConnection;
    $NewStudent = new Student();
    $NewStudent->idSchools = $SchoolID;
    $NewStudent->SchoolDistrictID = $StudentID;
    $NewStudent->SchoolDistrictPassword = $StudentPassword;
    $NewStudent->Email = $Email;
    $NewStudent->FirstName = $FirstName;
    $NewStudent->LastName = $LastName;
    return CreateNewStudent($NewStudent, $dbConnection);
}

function UpdateLegacyStudent($idStudents, $FirstName, $LastName, $SchoolID, $StudentID, $StudentPassword, $Email){
    global $dbConnection;
    $NewStudent = new Student();
    $NewStudent->idStudents = $idStudents;
    $NewStudent->idSchools = $SchoolID;
    $NewStudent->SchoolDistrictID = $StudentID;
    $NewStudent->SchoolDistrictPassword = $StudentPassword;
    $NewStudent->Email = $Email;
    $NewStudent->FirstName = $FirstName;
    $NewStudent->LastName = $LastName;
    return ModifyStudent($NewStudent, $dbConnection);
}

function AddUserToDatabase($FirstName, $LastName, $Email){
    global $dbConnection;
    $NewUser = new User();
    $NewUser->FirstName = $FirstName;
    $NewUser->LastName = $LastName;
    $NewUser->Email = $Email;
    return AddNewUser($NewUser,$dbConnection);
}

function AddLoginUserService($idLoginServiceType, $LoginServiceID, $idUser){
    global $dbConnection;
    $NewLoginService = new LoginService();
    $NewLoginService->idLoginServiceType = $idLoginServiceType;
    $NewLoginService->LoginServiceID = $LoginServiceID;
    $NewLoginService->idUser = $idUser;
    AddLoginService($NewLoginService,$dbConnection);
}

function Output($status){
    echo "<script>console.log('".$status."');</script>";
    flush();
    ob_flush();
}