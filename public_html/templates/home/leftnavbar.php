<?php
/**
 * User: Mike
 * Date: 7/24/13
 * Time: 2:15 PM
 */
?>
<div id="LeftNavBar">
    <?php


    /* @var $GlobalStudentObject Student
     * @var $CurrentClass _Class
     */
    foreach ($GlobalStudentObject->Classes as $CurrentClass) {
        ?>
        <div class="class <?php if ($PageName == 'class' && $CurrentClass->idClasses == $_GET['id']) {
            echo 'selectedclass';
        } ?>">
            <a class="ClassNavLink" href="/class/<?php echo $CurrentClass->idClasses; ?>"><span
                    style="position:absolute;width:100%;height:100%;top:0;left: 0;z-index: 1;background-image: url('/images/blank.gif');"></span></a>
            <?php echo $CurrentClass->Course->CourseName; ?>
        </div>
    <?php } ?>
    <div id="LeftNavBarFooter">
        <a href="/faq" target="_blank">FAQ</a> | <!-- SP FB page used to be here --> | <a href="/tos" target="_blank">TOS</a>
    </div>
</div>
