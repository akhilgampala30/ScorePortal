<?php
/**
 * User: Mike
 * Date: 7/23/13
 * Time: 8:25 PM
 */
?>
<div id="wrapper" data-Page="{Current->Student.Home.Page}">

    <?php
    /* @var $GlobalStudentObject Student
     * @var $CurrentClass _Class
     */
    foreach($GlobalStudentObject->Classes as $CurrentClass){
    ?>
        <div class="ClassBoard" style="">
            <div style="<?php if($CurrentClass->NewAssignmentAlertInClass()){echo "display: block;";}else{echo "display: none;";} ?>"
                 class="<?php if($CurrentClass->NewAssignmentAlertInClass()){echo "notification";} ?>"></div>
            <a href="/class/<?php echo $CurrentClass->idClasses; ?>"><span style="position:absolute;width:100%;height:100%;top:0;left: 0;z-index: 1;background-image: url('/images/blank.gif');"></span></a>
            <span class="ClassName">
                <!--Statistics AP (B)-->
                <?php
                echo $CurrentClass->Course->CourseName;
                ?>
            </span>
            <span class="ClassTeacher">
                <!--Walker, Brian E-->
                <?php
                echo $CurrentClass->Teacher->TeacherName;
                ?>
            </span>
            <span class="ClassScore">
                <!--90%-->
                <?php echo $CurrentClass->NumericGrade,'%';?>
            </span>
            <div class="PercentageBar">
                <div class="CurrentGradeIndicator GradeIndicator" style="left: <?php echo $CurrentClass->NumericGrade,'%';?>;">
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
                    $NextPercentage = (int)($CurrentClass->NumericGrade/10);
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
                if($CurrentClass->GradeDiff > 0){
                    $DisplayedGradeDiff = min(100,$CurrentClass->NumericGrade)-min(100,$CurrentClass->NumericGrade-$CurrentClass->GradeDiff);
                    $DisplayedGrade = min(100,$CurrentClass->NumericGrade-$CurrentClass->GradeDiff);
                    ?>
                    <div class="BlueBar" style="width: <?php echo $DisplayedGrade,'%';?>;">
                    </div>
                    <div class="GreenBar" style="left:<?php echo $DisplayedGrade; ?>%; width: <?php echo $DisplayedGradeDiff; ?>%;">
                    </div>
                <?php } else {
                    $DisplayedGradeDiff = min(100,$CurrentClass->NumericGrade-$CurrentClass->GradeDiff)-min(100,$CurrentClass->NumericGrade);
                    $DisplayedGrade = min(100,$CurrentClass->NumericGrade);
                    ?>
                    <div class="BlueBar" style="width: <?php echo $DisplayedGrade,'%';?>;">
                    </div>
                    <div class="GreyBar" style="left:<?php echo $DisplayedGrade; ?>%; width: <?php echo $DisplayedGradeDiff; ?>%;">
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
</div>
