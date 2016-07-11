<?php
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


if(!isset($_SESSION['Validate']) || !$_SESSION['Validate'] || !isset($_SESSION['PSUser']) || !isset($_SESSION['SchoolID']) || !isset($_SESSION['StudentID']) || !isset($_SESSION['StudentPassword']) || !isset($_SESSION['AuthUserEmail'])){
    //TODO: Add Error Message Here
    print_r($_SESSION);
    die("Invalid Registration Token");
}
$PSUser = unserialize($_SESSION['PSUser']);

$SchoolID = $_SESSION['SchoolID'];
$StudentID = $_SESSION['StudentID'];
$StudentPassword = $_SESSION['StudentPassword'];
$Email = $_SESSION['AuthUserEmail'];
$FirstName = $_SESSION['AuthUserFirstName'];
$LastName = $_SESSION['AuthUserLastName'];

$dbConnection = connect(); //Set Connection for this session

$dbConnection->exec('START TRANSACTION;');

//VALIDATING LOG IN CREDENTIALS...
SetBulletColor(0, 'blue');

//OBTAINING GRADE DATA...
SetBulletColor(0, 'green');
SetBulletColor(1, 'blue');

set_time_limit(600); //To prevent blocking our servers with abnormally long requests

//SETTING UP ACCOUNT
SetBulletColor(1, 'green');
SetBulletColor(2, 'blue');

//See if the credentials provided already match an existing student, if so, match the user with that student
$StudentExists = CheckIfStudentExists($StudentID, $StudentPassword, $SchoolID, $dbConnection);
if($StudentExists !== false){
    $dbStudentID = $StudentExists['idStudents'];
}
else{ //If they're new
    $legacyStudentID = CheckLegacyStatus($_SESSION['LoginID'], $Email, $dbConnection); //Check if the student has been imported over
    if($legacyStudentID !== false){
        //This student was already registered under the old database
        $dbStudentID = UpdateLegacyStudent($legacyStudentID['idStudents'], $FirstName, $LastName, $SchoolID, $StudentID, $StudentPassword, $Email);
    }else{ //If completely new, register the student as usual
        $dbStudentID = AddStudentToDatabase($FirstName, $LastName, $SchoolID, $StudentID, $StudentPassword, $Email);
    }
}

$dbUserID = AddUserToDatabase($FirstName,$LastName,$Email);
AddStudent_User($dbStudentID, $dbUserID, $dbConnection);
AddLoginUserService($_SESSION['ServiceID'], $_SESSION['LoginID'], $dbUserID);


for($i=69; $i<70; $i++){
    $temppath = $_SERVER['DOCUMENT_ROOT'].'/tempXML/'.$i;
    $fileContents = file_get_contents($temppath);
    parseXMLtoDB($fileContents, $dbStudentID, $SchoolID, true, $dbConnection, false); //TODO: Strip 0 grade histories
}

//OBTAINING AUXILLARY DATA
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