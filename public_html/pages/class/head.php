<?php
/**
 * User: Mike
 * Date: 7/26/13
 * Time: 11:43 AM
 */
?>

<link rel="stylesheet" type="text/css" href="/pages/class/style/class.css">
<link rel="stylesheet" type="text/css" href="/pages/class/style/OriginalGrades.css">

<script src="/libraries/flot/jquery.flot.js"></script>
<script src="/libraries/flot/jquery.flot.time.min.js"></script>
<script src="/libraries/flot/jquery.flot.navigate.min.js"></script>
<script src="/libraries/flot/jquery.flot.pie.js"></script>
<script src="/libraries/flot/jquery.flot.fillbetween.min.js"></script>

<script src="/pages/class/js/GradeProgressChart.js"></script>
<script src="/pages/class/js/GradeCompositionChart.js"></script>
<script src="/pages/class/js/InputValidation.js"></script>
<script src="/pages/class/js/Interactives.js"></script>

<?php
if ($_SESSION['UserLoginNumberState'] == 1) {
    ?>
    <script src="/pages/class/js/FirstLoginGuidersClass.js"></script>
    <script src="/libraries/Guiders/guiders.js"></script>
    <link rel="stylesheet" type="text/css" href="/libraries/Guiders/guiders.css">
<?php
}
?>
