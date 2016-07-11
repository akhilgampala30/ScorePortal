<?php
/**
 * User: Mike
 * Date: 7/23/13
 * Time: 8:25 PM
 */
?>
    <link rel="stylesheet" type="text/css" href="/pages/home/style/home.css">
<?php
if ($_SESSION['UserLoginNumberState'] == 1) {
    ?>
    <script src="/pages/home/js/FirstLoginGuidersHome.js"></script>
    <script src="/libraries/Guiders/guiders.js"></script>
    <link rel="stylesheet" type="text/css" href="/libraries/Guiders/guiders.css">
<?php
}
?>