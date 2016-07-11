<?php
/**
 * User: Mike
 * Date: 7/13/13
 * Time: 4:48 PM
 *
 * Main Page With Includes Logic For All Pages
 */

//All webpages should have database functions/student classes loaded along with the session started
if (!defined('DatabaseIncludeLoaded') || !constant('DatabaseIncludeLoaded')) {
    include $_SERVER['DOCUMENT_ROOT'] . '/paths.php';
    require $path['Database.php'];
}
//require $path['config.php']; //All files should have a config loaded
session_start();
if (isset($_SESSION['StudentObj'])) {
    $GlobalStudentObject = $_SESSION['StudentObj'];
}
//TODO: Login Check
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="icon"
          type="image/png"
          href="/images/icons/favico.png">

    <link rel="image_src" href="/images/ScorePortalLargeSilverFB.png" / >
    <link rel="image_src" href="/images/ScorePortalMedSilverFB.png" / >
    <link rel="image_src" href="/images/Logo.png" / >
    <meta name="description" content="Gain insight into your education and grades never thought possible before.">
    <meta name="keywords" content="Education,Grades,ScorePortal">
    <meta name="author" content="ScorePortal Development Team">
    <meta property="og:title" content="ScorePortal.org" />
    <meta property="og:url" content="https://ScorePortal.org" />
    <meta property="og:description" content="Gain insight into your education and grades never thought possible before.">
    <meta property="og:image" content="https://scoreportal.org/images/ScorePortalLargeSilverFB.png" />
    <meta property="fb:admins" content="KayThanksBye" />

    <?php
    require('templates/global/head.php');
    if (isset($_GET['page']))
        $PageName = strtolower($_GET['page']);
    else
        $PageName = '';
    switch ($PageName) {
        case 'index':
            require('templates/index/head.php');
            require('pages/index/head.php');
            echo "<title>ScorePortal.org</title>";
            break;
        case 'home':
            require('templates/home/head.php');
            require('pages/home/head.php');
            echo "<title>Home - ScorePortal.org</title>";
            break;
        case 'settings':
            require('pages/settings/head.php');
            require('templates/home/head.php');
            echo "<title>Settings - ScorePortal.org</title>";
            break;
        case 'class':
            require('templates/home/head.php');
            echo "<title>Class - ScorePortal.org</title>";
            break;
        case 'register':
            require('templates/index/head.php');
            require('pages/register/head.php');
            echo "<title>Register - ScorePortal.org</title>";
            break;
        case 'about':
            require('templates/index/head.php');
            require('pages/about/head.php');
            echo "<title>About Us - ScorePortal.org</title>";
            break;
        case 'tos':
            require('templates/index/head.php');
            require('pages/tos/head.php');
            echo "<title>Terms of Service - ScorePortal.org</title>";
            break;
        case 'faq':
            require('templates/index/head.php');
            require('pages/faq/head.php');
            echo "<title>FAQ - ScorePortal.org</title>";
            break;
        case 'error':
            require('templates/index/head.php');
            require('pages/error/head.php');
            break;
        default:
            require('templates/index/head.php');
            require('pages/index/head.php');
            echo "<title>ScorePortal.org</title>";
            break;
    }
    if (isset($_GET['id']))
        $PageID = strtolower($_GET['id']);
    else
        $PageID = '';
    ?>
</head>
<body>
<!-- TODO: Put some Google Analytics Tracking Code Here -->
<?php
switch ($PageName) {
    case 'index':
    case 'register':
        require('templates/index/index.php');
        break;
    case 'settings':
    case 'class':
    case 'home':
        require('templates/home/home.php');
        break;
    default:
        require('templates/index/index.php');
        break;
}
?>
</body>
</html>
