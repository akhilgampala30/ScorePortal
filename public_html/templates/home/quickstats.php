<?php
/**
 * User: Mike
 * Date: 7/25/13
 * Time: 7:08 PM
 */
?>
<div id="QuickStats">
    <div id="LastUpdated">
        <!--Accept.png-->
        <img src="/images/icons/accept.png">
        Last Updated: <span><?php /* @var $GlobalStudentObject Student */ echo date("M jS g:i a", strtotime($GlobalStudentObject->GradeLastUpdated)); ?></span>
    </div>
    <div id="CurrentGPA">
        <!--chart_bar.png-->
        <img src="/images/icons/chart_bar.png">
        Current Simple GPA: <span><?php /* @var $GlobalStudentObject Student */ echo number_format($GlobalStudentObject->GPA, 2, '.', ''); ?></span>
    </div>
    <div id="AverageClassPercentile" style="display:none;opacity:0;">
        <!--chart_line.png-->
        <img src="/images/icons/chart_line.png">
        Average Class Percentile: <span>88%</span>
    </div>
    <div id="UpdateGrades">
        <!--BlueShareThis.png-->
        <?php
            $UpdatingGrades = false;
            if(isset($_SESSION['UpdatingGrades']) && $_SESSION['UpdatingGrades']){
                $UpdatingGrades = true;
            }
        ?>
        <img src="/images/icons/arrow_refresh.png" class="<?php if($UpdatingGrades){ echo 'fa-spin';} ?>" >
        <span style="font-weight:400;font-size: 15px;" data-value=""><?php echo ($UpdatingGrades?'Updating Grades...':'Update Grades') ?></span>
    </div>
</div>