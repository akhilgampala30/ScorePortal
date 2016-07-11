<?php
/**
 * User: Mike
 * Date: 7/25/13
 * Time: 12:40 PM
 */
if (!isset($_SESSION['StudentID'])) {
    header('Location: /');
    die;
}
if (!isset($_SESSION['StudentObj'])) {
    $GlobalStudentObject = GetStudent($_SESSION['StudentID'], connect());
    if ($GlobalStudentObject === false) {
        header('Location: /');
        die;
    }
    $_SESSION['StudentObj'] = $GlobalStudentObject;
}
require('header.php');
require($_SERVER['DOCUMENT_ROOT'] . '/templates/home/leftnavbar.php');
require($_SERVER['DOCUMENT_ROOT'] . '/templates/home/rightstatusbar.php');

?>
<?php
switch ($PageName) {
    case 'home':
        require($_SERVER['DOCUMENT_ROOT'] . '/pages/home/home.php');
        break;
    case 'class':
        require($_SERVER['DOCUMENT_ROOT'] . '/pages/class/class.php');
        break;
    case 'settings':
        require($_SERVER['DOCUMENT_ROOT'] . '/pages/settings/settings.php');
        break;

}
//require($_SERVER['DOCUMENT_ROOT'].'/pages/home/home.php');
//require($_SERVER['DOCUMENT_ROOT'].'/pages/class/class.php');
?>
