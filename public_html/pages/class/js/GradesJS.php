<?php
/*
$AssignmentHistory = array();
$GradeHistoryData = array();
for($i=0;$i<25;$i++){
    //Random Grades
    $GradeHistoryData[] = array(strtotime("-{$i} day")*1000, rand(70,99)); //Date and Grade at that Point
    //Array key is date, assignments in the date as a string is stored in the array to display
    $AssignmentGrade = rand(70,99);
    $AssignmentHistory[date('m/d',strtotime("-{$i} day"))] = "Assignment {$i}: {$AssignmentGrade}%"; //HTML Output in Tooltip
}
$ClassAvgData = array();
for($i=0;$i<25;$i++){
    $ClassAvgData[] = array(strtotime("-{$i} day")*1000, rand(60,80));
}*/
?>

<script>
var ApproxGradeHistory = <?php
    global $CalculatedGradeHistory;
    echo json_encode($CalculatedGradeHistory);
    ?>;
var GradeHistoryData = <?php
    global $GradeHistory;
    echo json_encode($GradeHistory);
    ?>;
var ClassAvgData = <?php
    global $ClassAverageHistory;
    echo json_encode($ClassAverageHistory);
    ?>;
var AssignmentHistory = <?php
    global $CalculatedAssignmentHistory;
    echo json_encode($CalculatedAssignmentHistory);
    ?>;

var DonutData = <?php global $IsEditedGradesPage; echo json_encode(PieChartJSON($ClassObj,$TotalPoints, $IsEditedGradesPage)); ?>;

var GradeHistorySeries = {"label":"Your Grade","data":GradeHistoryData, color:0};
var CalculatedGradeSeries = {"label":"Calculated Grade","data":ApproxGradeHistory, color:1};
var ClassAverageSeries = {"label":"Class Average","data":ClassAvgData, color:2};

var ClassID = <?php echo $ClassObj->idClasses; ?>

$(function () {
    /* ~~~~~~~~~ Donut Chart of Grade Make-Up ~~*/
    var data = [{"label":"Your Grade","data":GradeHistoryData },
        {"label":"Calculated Grade","data":ApproxGradeHistory},
        {"label":"Class Average","data":ClassAvgData} ];
    CreateGradeCompositionChart();
    console.log(data);
    CreateGradeProgressChart(data);
    labelPosition();
    //Start by sorting assignment date
    $('#AssignmentListContainer>.AssignmentItem').tsort('.AssignmentUpdatedDate',{order: 'desc', data: 'sort'});
});

</script>