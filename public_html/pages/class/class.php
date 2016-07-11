<?php
/**
 * User: Mike
 * Date: 7/26/13
 * Time: 11:44 AM
 */
?>
<?php
include $_SERVER['DOCUMENT_ROOT'].'/paths.php';

$LoadFromScratch = false;
if(isset($_GET['NavLoad']) && $_GET['NavLoad']==1){ //AJAX Call from Nav Bar
    $LoadFromScratch = true;
}
include $path['InitClass.php']; //Initialize Class Objects and everything

if($LoadFromScratch){ //Load scripts if it's from ajax
    ?>
    <?php
}
?>
<script>
    document.title="<?php echo $ClassObj->Course->CourseName ?> - ScorePortal.org";
</script>
<div id="wrapper">
    <div class="ClassBoard">
        <span class="ClassName">
            <?php echo $ClassObj->Course->CourseName; ?>
        </span>
        <span class="ClassTeacher">
            <?php echo $ClassObj->Teacher->TeacherName; ?>
        </span>
        <span class="ClassScore">
            <?php echo $ClassObj->NumericGrade; ?>%
        </span>
        <div class="PercentageBar">
            <div class="CurrentGradeIndicator GradeIndicator" style="left: <?php echo $ClassObj->NumericGrade,'%';?>;">
            </div>
            <?php
            $LetterGrades = array(
                10=>array('A+',''),
                9=>array('A','A'),
                8=>array('B','A'),
                7=>array('C','B'),
                6=>array('D','C'),
                5=>array('F','D')
            );
            $NextPercentage = (int)($ClassObj->NumericGrade/10);
            if($NextPercentage < 5){$LetterGrade = 'F';$NextPercentage=5;}else{$LetterGrade = $LetterGrades[$NextPercentage][0];}
            ?>
            <div class="LetterGradeIndicator GradeIndicator" style="left: <?php echo $NextPercentage*10; ?>%;" data-letter-grade="<?php echo $LetterGrade; ?>">
            </div>
            <?php
            if($NextPercentage<9 && $NextPercentage!= 5){
                $LetterGrade = $LetterGrades[$NextPercentage][1];
                ?>
                <div class="LetterGradeIndicator GradeIndicator" style="left: <?php echo $NextPercentage*10+10; ?>%;" data-letter-grade="<?php echo $LetterGrade; ?>">
                </div>
            <?php
            }
            ?>
            <?php
            //If change in grade was positive
            if($ClassObj->GradeDiff > 0){
                $DisplayedGradeDiff = min(100,$ClassObj->NumericGrade)-min(100,$ClassObj->NumericGrade-$ClassObj->GradeDiff);
                $DisplayedGrade = min(100,$ClassObj->NumericGrade-$ClassObj->GradeDiff);
                ?>
                <div class="BlueBar" style="width: <?php echo $DisplayedGrade,'%';?>;">
                </div>
                <div class="GreenBar" style="left:<?php echo $DisplayedGrade; ?>%; width: <?php echo $DisplayedGradeDiff; ?>%;">
                </div>
            <?php } else {
                $DisplayedGradeDiff = min(100,$ClassObj->NumericGrade-$ClassObj->GradeDiff)-min(100,$ClassObj->NumericGrade);
                $DisplayedGrade = min(100,$ClassObj->NumericGrade);
                ?>
                <div class="BlueBar" style="width: <?php echo $DisplayedGrade,'%';?>;">
                </div>
                <div class="GreyBar" style="left:<?php echo $DisplayedGrade; ?>%; width: <?php echo $DisplayedGradeDiff; ?>%;">
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="Board" id="ClassNav">
        <div id="OriginalGradesNavButton" class="ClassNavButton SelectedClassNavButton">
            Original Grades
        </div>
        <div id="EditGradesNavButton" class="ClassNavButton">
            Edit Grades
        </div>
        <div id="ClassInformationNavButton" class="ClassNavButton">
            Class Information
        </div>
    </div>

    <div id="LightBox">
        <div id="LightBoxContent">
            <div id="ComingSoon">
                Class Information and other features are coming soon!
            </div>
        </div>
    </div>

    <div id="ClassContent">
        <?php require('OriginalGrades.php') ?>
    </div>
</div>