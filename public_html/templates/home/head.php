<?php
/**
 * User: Mike
 * Date: 7/23/13
 * Time: 8:25 PM
 */
?>
<link rel="stylesheet" type="text/css" href="/templates/home/style/rightstatusbar.css">
<link rel="stylesheet" type="text/css" href="/templates/home/style/quickstats.css">
<link rel="stylesheet" type="text/css" href="/templates/home/style/notificationdock.css">
<link rel="stylesheet" type="text/css" href="/templates/home/style/home.css">
<link rel="stylesheet" type="text/css" href="/templates/home/style/header.css">
<link rel="stylesheet" type="text/css" href="/templates/home/style/leftnavbar.css">
<link rel="stylesheet" type="text/css" href="/templates/home/style/rightadbar.css">

<script src="/templates/home/js/notificationdock.js"></script>
<script src="/templates/home/js/LeftBarNav.js"></script>
<script src="/templates/home/js/QuickStats.js"></script>
<script src="/templates/home/js/Search.js"></script>

<?php
?>
<div id="UserLoginNumberState" style="display:none;"><?php echo($_SESSION['UserLoginNumberState']); ?></div><?php
?>

<!-- Class CSS -->
<?php
include $_SERVER['DOCUMENT_ROOT'] . '/paths.php';
require($path['class/head.php']);
?>
