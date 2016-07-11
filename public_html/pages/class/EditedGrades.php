<?php
/**
 * User: Mike
 * Date: 8/5/13
 * Time: 3:18 PM
 */
?>
<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.

include $_SERVER['DOCUMENT_ROOT'] . '/paths.php';
if (isset($_GET['load'])) { //Called from AJAX
    $LoadFromScratch = true;
    include $path['InitClass.php'];
    /* @var $ClassObj _Class */
}
$IsEditedGradesPage = true;

$TotalPoints = 0;
include $path['SumCategories.php'];
$TotalPoints = SumModifiedCategories($ClassObj);
include('js/GradesJS.php');
$ClassObj->CalculateEditedGrade();
?>

<div id="GradeProgress" class="Board">
    <div class="BoardTitle" id="GradeProgressHeader">Grade Progress
        <div type="button" id="ResetGradeProgressButton" style="float:right;margin-top:3px;" class="tinybutton">Reset
            Graph
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
        <table id="CategoryGradeChartTable"><!-- TR Per Category. Set Color to Corresponding Category and Width to % -->
            <tr>
                <td>
                    <div class="CategoryGradeProgressBarName" title="Total">Total:</div>
                </td>
                <td class='CategoryGradeProgressBarHolder'>
                    <div class="CategoryGradeProgressBar"
                         style="width: <?php echo 80 * max(0.32, min(1, $ClassObj->ClassEditedCalculatedPercentage)); ?>%;
                             background: <?php echo Category::$Colors[0][0]; ?>;"><?php echo round($ClassObj->ClassEditedEarnedPoints, 2); ?>
                        /<?php echo round($ClassObj->ClassEditedTotalPoints, 2); ?></div>
                    <span
                        class="CategoryGradeProgressBarLabel"><?php echo round($ClassObj->ClassEditedCalculatedPercentage * 100, 2); ?>
                        %</span>
                </td>
            </tr>
            <?php /* @var $Category Category */
            $CategoryCounter = 1;
            foreach ($ClassObj->Categories as $Category) {
                ?>
                <tr>
                    <td>
                        <div class="CategoryGradeProgressBarName"
                             title="<?php echo $Category->CategoryName; ?>"><?php echo $Category->CategoryName; ?>:
                        </div>
                    </td>
                    <td class='CategoryGradeProgressBarHolder'>
                        <div class="CategoryGradeProgressBar"
                             style="width: <?php echo 80 * max(0.32, min(1, $Category->ModifiedPercentage)); ?>%; background: <?php echo Category::$Colors[$CategoryCounter % 8][0]; ?>;"><?php echo round($Category->ModifiedTotalPointsEarned, 2); ?>
                            /<?php echo round($Category->ModifiedTotalPointsPossible, 2); ?></div>
                        <span class="CategoryGradeProgressBarLabel"><?php echo $Category->ModifiedPercentage * 100; ?>
                            %</span>
                    </td>
                </tr>
                <?php $CategoryCounter++;
            } ?>
        </table>
    </div>
    <div style="clear:both;"></div>
</div>

<div id="AddAssignment" class="Board">
    <div class="BoardTitle" id="AddAssignmentHeader">Add Assignment</div>
    <div id="AddAssignmentBar">
        <div id="AddAssignmentCategoryHeader">Category</div>
        <div id="AddAssignmentNameHeader">Assignment Name</div>
        <div id="AddAssignmentScoreHeader">Assignment Score</div>
    </div>
    <span id="AddAssignmentError"
          style="display:none;position: absolute;top: 49px;left: 312px;color: red;font-size: 12px;text-align: right;width: 170px;">Invalid Assignment Name!</span>

    <div id="AddAssignmentForm">
        <select name="SelectCategory" class="AddAssignmentCategorySelect">
            <?php foreach ($ClassObj->Categories as $CurrentCategory) { //Echo m.idCategory if it's modified?>
                <option value="<?php if (isset($CurrentCategory->isUserCreated) && $CurrentCategory->isUserCreated) {
                    echo 'm.' . $CurrentCategory->idCategory;
                } else {
                    echo $CurrentCategory->idCategory;
                } ?>">
                    <?php echo $CurrentCategory->CategoryName ?>
                </option>
            <?php } ?>
        </select>
        <input type="text" name="AssignmentName" class="AddAssignmentName" placeholder="Assignment Name">
        <input type="text" name="AssignmentEarnedPoints" class="AddAssignmentPoints" id="AddAssignmentEarnedPointsInput"
               placeholder="100">
        &nbsp;/&nbsp;
        <input type="text" name="AssignmentPossiblePoints" class="AddAssignmentPoints"
               id="AddAssignmentPossiblePointsInput" placeholder="100">
        <i class="AddButton fa fa-plus-square"></i>
    </div>

</div>

<div id="Assignments" class="Board">
    <div class="BoardTitle" id="AssignmentsHeader">
        <span>
            Assignments
        </span>

        <div id="ResetAllAssignments">
            <i class="fa fa-refresh"></i> Reset Assignments
        </div>
    </div>

    <div id="AssignmentsSortBar">
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
        /* @var $ClassObj _Class */
        foreach ($ClassObj->Assignments as $CurrentAssignment) {
            $CategoryFluctuation = 100 * CalculateModifiedAssignmentCategoryImpact($CurrentAssignment);
            $GradeFluctuation = 100 * CalculateModifiedAssignmentGradeImpact($CurrentAssignment, $ClassObj);
            $idAssignment = $CurrentAssignment->idAssignment;
            $idAssignmentScore = $CurrentAssignment->idAssignmentScore;
            $isAssignmentModified = isset($CurrentAssignment->idModifiedAssignmentScore);
            $isAssignmentAdded = $CurrentAssignment->UserAddedAssignment;
            $EarnedPoints = (isset($CurrentAssignment->ModifiedAssignmentEarnedPoints) ? $CurrentAssignment->ModifiedAssignmentEarnedPoints : $CurrentAssignment->AssignmentEarnedPoints);
            $PossiblePoints = (isset($CurrentAssignment->ModifiedAssignmentPossiblePoints) ? $CurrentAssignment->ModifiedAssignmentPossiblePoints : $CurrentAssignment->AssignmentPossiblePoints);
            ?>
            <div class="AssignmentItem"
                 id="<?php echo($isAssignmentAdded ? 'A' . $idAssignment : ''), ($isAssignmentModified ? 'M' . $idAssignmentScore : '' . $idAssignmentScore); ?>">
                <div
                    class="AssignmentOverview <?php echo($CurrentAssignment->AssignmentDisabled ? 'DisabledAssignment' : ''); ?>">
                    <div
                        class="BookmarkButton <?php echo($CurrentAssignment->AssignmentBookmarked ? 'Bookmarked' : ''); ?> clickable"
                        data-AssignmentID="<?php echo $idAssignmentScore; ?>"
                        data-Added="<?php echo($isAssignmentAdded ? $idAssignment : '-1'); ?>">&nbsp;</div>
                    <div class="AssignmentCategory"
                         title="<?php echo $CurrentAssignment->Category->CategoryName; ?>"><?php echo $CurrentAssignment->Category->CategoryName; ?></div>
                    <div class="AssignmentName"
                         title="<?php echo $CurrentAssignment->AssignmentName; ?>"><?php echo $CurrentAssignment->AssignmentName; ?></div>
                    <div class="AssignmentPointScore" data-sort="<?php echo $EarnedPoints; ?>"
                         title="<?php echo($EarnedPoints == -1 ? '--' : round($EarnedPoints, 2)), '/', ($PossiblePoints == -1 ? '--' : round($PossiblePoints, 2)); ?>">
                        <?php echo($EarnedPoints == -1 ? '--' : round($EarnedPoints, 2)), '/', ($PossiblePoints == -1 ? '--' : round($PossiblePoints, 2)); ?>
                    </div>
                    <div class="AssignmentScoreDivider">|</div>
                    <?php $percentage = ($PossiblePoints == 0 || $EarnedPoints == -1) ? 0 : round(($EarnedPoints / $PossiblePoints) * 100, 2); ?>
                    <div class="AssignmentPercentageScore"
                         data-sort="<?php echo $percentage; ?>"><?php echo $percentage; ?>%
                    </div>
                    <div class="AssignmentUpdatedDate"
                         data-sort="<?php echo $CurrentAssignment->AssignmentDate; ?>"><?php echo date('n/d/y', $CurrentAssignment->AssignmentDate); ?></div>
                    <!--                    <div class="AssignmentShareButton clickable">&nbsp;</div>-->
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
                    <!-- AssignmentDetailLeft -->
                    <div class="AssignmentDetailRight">
                        <div class="EditBox">
                            <div class="EditBoxUpper clickable">
                                <span class="EditBoxLabel">Edit</span>
                                <select name="SelectCategory"
                                        class="EditBoxCategorySelect clickable" <?php if (!$CurrentAssignment->UserAddedAssignment) {
                                    echo "disabled";
                                } ?>>
                                    <?php foreach ($ClassObj->Categories as $CurrentCategory) { ?>
                                        <option
                                            value="<?php echo $CurrentCategory->idCategory ?>" <?php if ($CurrentAssignment->idCategory == $CurrentCategory->idCategory) {
                                            echo "selected";
                                        } ?>><?php echo $CurrentCategory->CategoryName ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div style="clear:both;"></div>
                            <div class="EditBoxLower clickable">
                                <input type="text" name="AssignmentEarnedPoints"
                                       class="AssignmentEarnedPointsInput EditPointsInput tinyInput clickable"
                                       value="<?php echo($EarnedPoints == -1 ? '--' : round($EarnedPoints, 2)) ?>">
                                &nbsp;/&nbsp;
                                <input type="text" name="AssignmentPossiblePoints"
                                       class="AssignmentPossiblePointsInput EditPointsInput tinyInput clickable"
                                       value="<?php echo($PossiblePoints == -1 ? '--' : round($PossiblePoints, 2)); ?>">

                                <div class="SaveAssignment EditBoxButtons clickable" name="SaveAssignment"
                                     title="Save Changes"
                                     data-Added="<?php echo($isAssignmentAdded ? $idAssignment : '-1'); ?>"
                                     data-AssignmentID="<?php echo $idAssignmentScore; ?>"></div>
                                <div
                                    class="ResetAssignment EditBoxButtons clickable <?php echo($CurrentAssignment->UserAddedAssignment ? 'InputDisabled' : ''); ?>"
                                    title="Reset Changes" name="ResetAssignment"
                                    data-Added="<?php echo($isAssignmentAdded ? $idAssignment : '-1'); ?>"
                                    data-AssignmentID="<?php echo $idAssignmentScore; ?>"
                                    style="<?php echo($CurrentAssignment->UserAddedAssignment ? 'cursor:auto;opacity:.5;' : ''); ?>"></div>
                                <div class="DisableAssignment EditBoxButtons clickable"
                                     title="<?php echo($CurrentAssignment->AssignmentDisabled ? 'Enable' : 'Disable'); ?> Assignment"
                                     name="DisableAssignment"
                                     data-Added="<?php echo($isAssignmentAdded ? $idAssignment : '-1'); ?>"
                                     data-AssignmentID="<?php echo $idAssignmentScore ?>"
                                     data-Disabled="<?php echo $CurrentAssignment->AssignmentDisabled; ?>"></div>
                                <div class="DeleteAssignment EditBoxButtons clickable" name="DeleteAssignment"
                                     title="Delete Assignment"
                                     data-Added="<?php echo($isAssignmentAdded ? $idAssignment : '-1'); ?>"
                                     style="<?php echo($CurrentAssignment->UserAddedAssignment ? '' : 'cursor:auto;opacity:.5;'); ?>"></div>
                            </div>
                            <!-- EditBoxLower -->
                            <div class="EditTitle">
                                <input
                                    type="text" <?php echo($CurrentAssignment->UserAddedAssignment ? '' : 'disabled'); ?>
                                    class="AssignmentTitleInput clickable"
                                    value="<?php echo $CurrentAssignment->AssignmentName; ?>">
                            </div>
                        </div>
                        <!-- EditBox -->
                    </div>
                    <!-- AssignmentDetailRight -->
                </div>
                <!-- AssignmentDetailContainer -->
            </div> <!-- AssignmentItem -->
        <?php
        }
        ?>
    </div>
    <!-- AssignmentListContainer -->
    <div id='AssignmentListContainerClear' style='clear:both'></div>
