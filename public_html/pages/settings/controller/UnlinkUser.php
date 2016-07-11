<?php
/**
 * User: Mike
 * Date: 1/20/14
 * Time: 12:16 PM
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

if (!isset($_SESSION['UserID']) || empty($_SESSION['UserID'])) {
    header('Location: /');
    exit;
}

$UserID = $_SESSION['UserID'];
$StudentID = GetStudentIDFromUserID($_SESSION['UserID'], $db);
if ($StudentID === false) { //There is no Student Associated with User, Probably unlinked
    $_SESSION['ValidUser'] = true;
    header('Location: /register');
    exit;
}

UnlinkUserFromStudent($StudentID, $UserID, $db);
header('Location: /Logout');
exit;
