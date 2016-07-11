<?php
/**
 * User: Mike
 * Date: 8/7/13
 * Time: 2:26 PM
 */
session_start();

require $_SERVER['DOCUMENT_ROOT'].'/paths.php';
//require $path['PowerAPI.php'];
require $path['Database.php'];
require_once $path['PowerAPICore.php'];
require_once $path['PowerAPICourse.php'];
require_once $path['PowerAPIUser.php'];


$Return = array(); //Init return array, sends json encoded array back to the page that requested registration. Gives error messages/prtty stuff
//validate input information
if(!isset($_POST['s']) || $_POST['s'] != 'reg0' || !isset($_SESSION['LoginID']) || empty($_SESSION['LoginID']))  {
    $Return['status'] = -2;
    $Return['msg'] = 'http://www.youtube.com/watch?v=AfZKbjUPrSg'; //In case anyone actually pays attention.
    die(json_encode($Return));
}
if(!isset($_POST['Agreement']) || $_POST['Agreement'] != 'Agree'){
    $Return['status'] = -1;
    $Return['msg'] = 'Please check the agree to the Terms of Service and Privacy Policy before continuing.';
    die(json_encode($Return));
}
if(!isset($_POST['School']) || empty($_POST['School'])
    || !isset($_POST['DistrictID']) || empty($_POST['DistrictID'])
    || !isset($_POST['DistrictPassword']) || empty($_POST['DistrictPassword'])){

    $Return['status'] = -1;
    $Return['msg'] = 'Please complete the form before continuing.';
    die(json_encode($Return));
}

$dbConnection = connect();

//Post data does exist and is so far valid.
$SchoolID = intval($_POST['School']);
$DistrictID = $_POST['DistrictID'];
$DistrictPassword = $_POST['DistrictPassword'];
if(ValidateSchoolID($SchoolID, $dbConnection) != 1){
    $Return['status'] = -2;
    $Return['msg'] = 'Invalid school.';
    die(json_encode($Return));
}

$SelectedSchool = GetSchool($SchoolID, $dbConnection);
$PSConnectionURL = $SelectedSchool->PowerschoolRootURL;

try{
    $ps = new PowerAPI\Core($PSConnectionURL);
    $user = $ps->auth($DistrictID, $DistrictPassword);
} catch (Exception $e) {
    $Return['status'] = -1;
    $Return['msg'] = 'Invalid login information.';
    die(json_encode($Return));
}

$_SESSION['Validate'] = true;
$_SESSION['PSUser'] = serialize($user);
$_SESSION['SchoolID'] = $SchoolID;
$_SESSION['StudentID'] = $DistrictID;
$_SESSION['StudentPassword'] = $DistrictPassword;

$Return['status'] = 1;
$Return['msg'] = '/pages/register/status.php';
die(json_encode($Return));