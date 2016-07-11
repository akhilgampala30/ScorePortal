<?php
/**
 * User: Mike
 * Date: 11/1/13
 * Time: 9:15 PM
 * Contains All Init Functions for User
 * Runs on Login
 */
if (strlen(session_id()) < 1) {
    session_start();
}
if (!defined('DatabaseIncludeLoaded') || !constant('DatabaseIncludeLoaded')) {
    include $_SERVER['DOCUMENT_ROOT'] . '/paths.php';
    require $path['Database.php'];
}

//Init DB connection
$db = connect();

//RefreshStudentObject();

if (!isset($_SESSION['UserID']) || empty($_SESSION['UserID'])) {
    header('Location: /');
    exit;
}

$StudentID = GetStudentIDFromUserID($_SESSION['UserID'], $db);
if ($StudentID === false) { //There is no Student Associated with User, Probably unlinked
    $_SESSION['ValidUser'] = true;
    header('Location: /register');
    exit;
}
$_SESSION['StudentID'] = $StudentID;
// echo $_SESSION['StudentID'], $StudentID;
$_SESSION['StudentObj'] = GetStudent($StudentID, $db);

//include $path['LoginUpdate.php']; //Update student's grades

$IP = $_SERVER['REMOTE_ADDR'];
IncrementUserLoginNumber($_SESSION['UserID'], $db); //+1 User Login to Keep track of Number of Logins
ModifyLastLoginTime($_SESSION['UserID'], $db); //Update Last Login Time for User
if (isset($IP) && !empty($IP)) {
    AddLoginIP($IP, $_SESSION['UserID'], $db); //Add the user's IP
}
$_SESSION['PSUser'] = null;
$_SESSION['UserLoginNumberState'] = GetUserLoginNumber($_SESSION['UserID'], $db);

function RefreshStudentObject()
{
    GLOBAL $db;
    //Set Up Student Object to be used in the session.
    $StudentID = GetStudentIDFromUserID($_SESSION['UserID'], $db);
    $_SESSION['StudentID'] = $StudentID;
    $_SESSION['StudentObj'] = GetStudent($StudentID, $db);
}

//TODO: Add Update XML Grades

//TODO: Add Update Screenscrap Grades

header('Location: /home');
die;