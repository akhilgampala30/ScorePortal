<?php
/**
 * User: Mike
 * Date: 7/26/13
 * Time: 2:10 PM
 */
?>
<?php
include $_SERVER['DOCUMENT_ROOT'] . '/paths.php';
if (isset($_GET['load'])) { //Called from AJAX
    $LoadFromScratch = true;
    include $path['InitClass.php'];
    /* @var $ClassObj _Class */
}
$TotalPoints = 0;
include $path['SumCategories.php'];
$TotalPoints = SumCategories($ClassObj);

//Mark assignments as read after load
include $path['MarkAssignmentsRead.php'];
$db = connect();
MarkAssignmentsRead($ClassObj, $db);

$ClassObj->CalculateGrade();
?>
    <div id="GradeProgress" class="Board">
        <div class="BoardTitle" id="GradeProgressHeader">Grade Progress
            <div type="button" id="ResetGradeProgressButton" style="float:right;margin-top:3px;" class="tinybutton">
                Reset Graph
            </div>
        </div>
        <div id="GradeProgressChart"></div>
        <div id="legendholder">
            <table style="font-size:smaller;color:#545454">
                <tr id="YourGradeProgressLegendItem" class="LegendItem" data-visible="true">
                    <td class="legendColorBox">
                        <div style="border:1px solid #ccc;padding:1px">
                            <div style="width:4px;height:0;border:5px solid rgb(112,155,226);overflow:hidden"></div>
                        </div>
                    </td>
                    <td class="legendLabel">Your Grade</td>
                </tr>
                <tr id="CalculatedGradeProgressLegendItem" class="LegendItem" data-visible="true">
                    <td class="legendColorBox">
                        <div style="border:1px solid #ccc;padding:1px">
                            <div style="width:4px;height:0;border:5px solid rgb(175,175,175);overflow:hidden"></div>
                        </div>
                    </td>
                    <td class="legendLabel">Calculated Grade</td>
                </tr>
                <tr id="ClassAverageGradeProgressLegendItem" class="LegendItem" data-visible="true">
                    <td class="legendColorBox">
                        <div style="border:1px solid #ccc;padding:1px">
                            <div style="width:4px;height:0;border:5px solid rgb(89,124,180);overflow:hidden"></div>
                        </div>
                    </td>
                    <td class="legendLabel">Class Average</td>
                </tr>
            </table>
        </div>
    </div>
    <div id="CategoryBreakdown" class="Board">
        <div class="BoardTitle" id="CategoryBreakdownHeader">Category Breakdown</div>
        <div id='GradeCompositionChartTitle'>Category Composition of Current Grade</div>
        <div id="GradeCompositionChart"></div>
        <div id="CategoryGradeChart">
            <div id="CategoryGradeChartTitle">Grades of Individual Categories</div>
            <table id="CategoryGradeChartTable">
                <!-- TR Per Category. Set Color to Corresponding Category and Width to % -->
                <tr>
                    <td>
                        <div class="CategoryGradeProgressBarName" title="Total">Total:</div>
                    </td>
                    <td class='CategoryGradeProgressBarHolder'>
                        <div class="CategoryGradeProgressBar"
                             style="width: <?php echo 80 * max(0.32, min(1, $ClassObj->ClassCalculatedPercentage)); ?>%;
                                 background: <?php echo Category::$Colors[0][0]; ?>;"><?php echo round($ClassObj->ClassEarnedPoints, 2); ?>
                            /<?php echo round($ClassObj->ClassTotalPoints, 2); ?></div>
                        <span
                            class="CategoryGradeProgressBarLabel"><?php echo round($ClassObj->ClassCalculatedPercentage * 100, 2); ?>
                            %</span>
                    </td>
                </tr>
                <?php $CategoryCounter = 1;
                foreach ($ClassObj->Categories as $Category) { //I stole color 0 for total?>
                    <tr>
                        <td>
                            <div class="CategoryGradeProgressBarName"
                                 title="<?php echo $Category->CategoryName; ?>"><?php echo $Category->CategoryName; ?>:
                            </div>
                        </td>
                        <td class='CategoryGradeProgressBarHolder'>
                            <div class="CategoryGradeProgressBar"
                                 style="width: <?php echo 80 * max(0.32, min(1, $Category->Percentage)); ?>%; background: <?php echo Category::$Colors[$CategoryCounter % 8][0]; ?>;"><?php echo round($Category->TotalPointsEarned, 2); ?>
                                /<?php echo round($Category->TotalPointsPossible, 2); ?></div>
                            <span class="CategoryGradeProgressBarLabel"><?php echo $Category->Percentage * 100; ?>
                                %</span>
                        </td>
                    </tr>
                    <?php $CategoryCounter++;
                } ?>
            </table>
        </div>
        <div style="clear:both;"></div>
    </div>

    <div id="Assignments" class="Board">
        <div class="BoardTitle" id="AssignmentsHeader">
        <span>
            Assignments
        </span>

            <div id="AssignmentLegend">
                <div class="AssignmentLegendColor" id="NewlyUpdatedColor">
                </div>
                <div class="AssignmentLegendLabel">
                    Newly Updated
                </div>
            </div>
        </div>

        <div id="AssignmentsSortBar" title="Click to Sort">
            <div class="SortItem" id="CategorySortItem">Category</div>
            <div class="SortItem" id="AssignmentNameSortItem">Assignment Name</div>
            <div id="AssignmentScoreSortContainer">
                <div id="AssignmentScoreSortTitle">Assignment Score</div>
                <div id="AssignmentScoreSortLowerRow">
                    <div id="PointScoreSortItem" class="SortItem">Point Score</div>
                    <div id="AssignmentScoreSortDivider"> |</div>
                    <div class="SortItem" id="PercentageSortItem">Percentage</div>
                </div>
            </div>
            <div class="SortItem" id="UpdatedDateSortItem">Updated Date</div>
        </div>

        <div id="AssignmentListContainer">
            <?php
            foreach ($ClassObj->Assignments as $CurrentAssignment) {
                if ($CurrentAssignment->UserAddedAssignment) //Filter out modified assignments
                    continue;
                $CategoryFluctuation = 100 * CalculateAssignmentCategoryImpact($CurrentAssignment);
                $GradeFluctuation = 100 * CalculateAssignmentGradeImpact($CurrentAssignment, $ClassObj);
                ?>
                <div class="AssignmentItem">
                    <div class="AssignmentBarColor"
                         style="<?php echo($CurrentAssignment->AssignmentNewAlert ? 'background-color:#e4ffd8;' : ''); ?>"></div>
                    <div class="AssignmentOverview">
                        <div
                            class="BookmarkButton <?php echo($CurrentAssignment->AssignmentBookmarked ? 'Bookmarked' : ''); ?> clickable"
                            data-AssignmentID="<?php echo $CurrentAssignment->idAssignment; ?>">&nbsp;</div>
                        <div class="AssignmentCategory"
                             title="<?php echo $CurrentAssignment->Category->CategoryName; ?>"><?php echo $CurrentAssignment->Category->CategoryName; ?></div>
                        <div class="AssignmentName"
                             data-sort="<?php echo preg_replace('/[^\p{L}0-9]/', '', strtoupper($CurrentAssignment->AssignmentName)); ?>"
                             title="<?php echo $CurrentAssignment->AssignmentName; ?>"><?php echo $CurrentAssignment->AssignmentName; ?></div>
                        <div class="AssignmentPointScore"
                             data-sort="<?php echo $CurrentAssignment->AssignmentEarnedPoints; ?>"
                             title="<?php echo($CurrentAssignment->AssignmentEarnedPoints == -1 ? '--' : round($CurrentAssignment->AssignmentEarnedPoints, 2)), '/', ($CurrentAssignment->AssignmentPossiblePoints == -1 ? '--' : round($CurrentAssignment->AssignmentPossiblePoints, 2)); ?>">
                            <?php echo($CurrentAssignment->AssignmentEarnedPoints == -1 ? '--' : round($CurrentAssignment->AssignmentEarnedPoints, 2)), '/', ($CurrentAssignment->AssignmentPossiblePoints == -1 ? '--' : round($CurrentAssignment->AssignmentPossiblePoints, 2)); ?>
                        </div>
                        <div class="AssignmentScoreDivider">|</div>
                        <?php $percentage = $CurrentAssignment->GetPercentage(); ?>
                        <div class="AssignmentPercentageScore"
                             data-sort="<?php echo $percentage; ?>"><?php if ($percentage != -1) {
                                echo $percentage; ?>%<?php } ?></div>
                        <div class="AssignmentUpdatedDate"
                             data-sort="<?php echo $CurrentAssignment->AssignmentDate; ?>"><?php echo date('n/d/y', $CurrentAssignment->AssignmentDate); ?></div>
                        <!--                    <div class="AssignmentShareButton clickable" title="Share">&nbsp;</div>-->
                    </div>
                    <div class="AssignmentDetailContainer">
                        <div class="AssignmentDetailLeft">
                            <div class="OverallGradeImprovement">
                                <span class="OverallGradeImprovementPercent"
                                      style="<?php echo($GradeFluctuation < 0 ? 'color:#ff0000' : ''); ?>"><?php echo($GradeFluctuation >= 0 ? '+' . $GradeFluctuation : $GradeFluctuation); ?>
                                    %</span><span>&nbsp;&nbsp;&nbsp;&nbsp;On Overall Grade</span>
                            </div>
                            <div class="CategoryGradeImprovement">
                                <span class="CategoryGradeImprovementPercent"
                                      style="<?php echo($CategoryFluctuation < 0 ? 'color:#a6181c' : ''); ?>"><?php echo($CategoryFluctuation >= 0 ? '+' . $CategoryFluctuation : $CategoryFluctuation); ?>
                                    %</span><span>&nbsp;&nbsp;&nbsp;&nbsp;On Category Grade</span>
                            </div>
                        </div>
                        <div class="AssignmentDetailRight">
                            <?php
                            if ($CurrentAssignment->AssignmentPossiblePoints == 0 || $CurrentAssignment->AssignmentEarnedPoints == -1) {
                                ?>
                                No statistics for this assignment.
                            <?php
                            } elseif ($CurrentAssignment->Population < $config['AssignmentCutOff']) {
                                ?>
                                There weren't enough students with this assignment to give you accurate data. Ask your friends to update their scores or join ScorePortal!
                            <?php
                            } else {
                                $Percentile = roundToNearest(CalculatePercentile($percentage / 100, $CurrentAssignment->AveragePercent, $CurrentAssignment->StandardDeviation, $CurrentAssignment->Population) * 100, 20);
                                if ($Percentile >= 100) {
                                    $Percentile = 99;
                                };
                                if ($Percentile <= 0) {
                                    $Percentile = 1;
                                }
                                ?>
                                <div class="NormalDistributionChart">
                                    <div class="PercentileInClassLabel">
                                        <span class="PercentileInClassLabelTitle">Percentile in Class</span>
                                        <span class="PercentileInClassLabelData"><span
                                                style="font-size: 16px;position:relative;top:1px;">~</span>&nbsp;<?php echo round($Percentile, 0) ?>
                                            %</span>

                                        <div class="PercentileInClassIndicator"></div>
                                    </div>
                                    <div class="ClassAveragePercentileLabel">
                                        <div class="ClassAverageIndicator"></div>
                                        <div class="ClassAveragePercentileLabelTitle">Class Average</div>
                                        <div
                                            class="ClassAveragePercentileLabelData"><?php echo round($CurrentAssignment->AveragePercent * $CurrentAssignment->AssignmentPossiblePoints, 1) . '/' . round($CurrentAssignment->AssignmentPossiblePoints, 1); ?>
                                            | <?php echo round($CurrentAssignment->AveragePercent * 100, 2); ?>%
                                        </div>
                                    </div>
                                    <div class="NormalDistributionGraphBackground">
                                        <div class="PopulationData">
                                            Students: <?php echo OutputRange($CurrentAssignment->Population); ?>
                                        </div>
                                        <div class="NormalDistributionAverageIndicator">
                                        </div>
                                        <div class="NormalDistributionGraphOverlay"
                                             style="width: <?php echo (inverse_ncdf($Percentile / 100) + 2.4) / 4.8 * 205; ?>px;">
                                        </div>
                                    </div>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
        <div id='AssignmentListContainerClear' style='clear:both'></div>
    </div>
<?php
//Execute all jScript after page load
include('js/GradesJS.php');
//Refresh Student Object by Class To Ensure New Assignments are marked as read
UpdateClass($ClassObj->idClasses, $db);
?>