<?php
/**
 * Created by PhpStorm.
 * User: Jacky
 * Date: 12/31/13
 * Time: 12:37 AM
 */

require $_SERVER['DOCUMENT_ROOT'] . '/paths.php';
require $path['Database.php'];
require_once $path['PowerAPICore.php'];
require_once $path['PowerAPICourse.php'];
require_once $path['PowerAPIUser.php'];

session_start();

$Return = array(); //Init return array, sends json encoded array back to the page that requested registration. Gives error messages/prtty stuff

$dbConnection = connect();

$PSConnectionURL = $_SESSION['StudentObj']->Schools->PowerschoolRootURL;
$DistrictPassword = $_POST['NewDistrictPassword'];

try {
    $ps = new PowerAPI\Core($PSConnectionURL);
    $user = $ps->auth($_SESSION['StudentObj']->SchoolDistrictID, $DistrictPassword);
} catch (Exception $e) {
    $Return['status'] = -1;
    $Return['msg'] = 'Invalid login information.';
    die(json_encode($Return));
}

ModifyStudentDistrictPassword($DistrictPassword, $_SESSION['StudentObj']->idStudents, $dbConnection);

$_SESSION['Validate'] = true;
$_SESSION['StudentPassword'] = $DistrictPassword;

$Return['status'] = 1;
die(json_encode($Return));