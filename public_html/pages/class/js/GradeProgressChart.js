/**
 * Created with JetBrains PhpStorm.
 * User: Mike
 * Date: 11/3/13
 * Time: 9:27 AM
 */
/* ~~~~~~ Line Graph of Grade History ~~~~~~~~~~*/
$(function(){
    var SeriesDisplay = [true,true,true];

    $('body').on('click','#ResetGradeProgressButton',function(event){
        var data = [GradeHistorySeries,CalculatedGradeSeries,ClassAverageSeries];
        $('#legendholder>table').find('.legendColorBox').fadeTo(300,1);
        SeriesDisplay = [true,true,true];
        console.log();
        CreateGradeProgressChart(data);
    });

    $('body').on('click','.LegendItem',function(event){
        var ElementID = $(this).attr('id');
        var data = [];//[GradeHistorySeries,CalculatedGradeSeries,ClassAverageSeries];
        if($(this).children('.legendColorBox').css('opacity') == 0){
            $(this).children('.legendColorBox').stop().fadeTo(300,1);
        }else{
            $(this).children('.legendColorBox').stop().fadeTo(300,0);
        }
        switch(ElementID){
            case 'YourGradeProgressLegendItem':
                SeriesDisplay[0] = !SeriesDisplay[0];
                if(SeriesDisplay[0])
                    $(this).children('.legendColorBox').stop().fadeTo(300,1);
                else
                    $(this).children('.legendColorBox').stop().fadeTo(300,0);
                break;
            case 'CalculatedGradeProgressLegendItem':
                SeriesDisplay[1] = !SeriesDisplay[1];
                if(SeriesDisplay[1])
                    $(this).children('.legendColorBox').stop().fadeTo(300,1);
                else
                    $(this).children('.legendColorBox').stop().fadeTo(300,0);
                break;
            case 'ClassAverageGradeProgressLegendItem':
                SeriesDisplay[2] = !SeriesDisplay[2];
                if(SeriesDisplay[2])
                    $(this).children('.legendColorBox').stop().fadeTo(300,1);
                else
                    $(this).children('.legendColorBox').stop().fadeTo(300,0);
                break;
        }
        if(SeriesDisplay[0])
            data.push(GradeHistorySeries);
        if(SeriesDisplay[1])
            data.push(CalculatedGradeSeries);
        if(SeriesDisplay[2])
            data.push(ClassAverageSeries);
        CreateGradeProgressChart(data)
    });
});

function CreateGradeProgressChart(data){

    console.log(data);
    var HistoryGraph = $.plot($("#GradeProgressChart"),data,
        {
            grid:{
                borderWidth:{'top':0,'left':1, 'right':0, 'bottom': 1},
                clickable: true,
                hoverable: true
            },
            series: {
                lines: { show: true, fill: false},
                points: { show: true, fill: true }
            },
            xaxis: {
                mode: "time",
                timeformat: "%m/%d",
                zoomRange: [0,100000000000]
            },
            yaxis:{
                max: 100,
                minTickSize: 1,
                tickFormatter: yFormatter,
                zoomRange: [1,100],
                panRange: [0,120]
            },
            legend: {
                position: "bottom",
                container: '#legendholder'
            },
            colors: ['#709be2','#afafaf'],
            zoom: {
                interactive:true
            },
            pan: {
                interactive:true
            },
            legend: { show: false, container: '#legendholder' }
        }
    );

    var prevItem = new Array();
    $("#GradeProgressChart").bind("plothover", function (event, pos, item) {
        if(item == null){ //TODO: Insert better fade out logic here
            $("#tooltip").fadeOut(380);
            prevItem[0] = '';
            prevItem[1] = '';
        }
        if(item && (item.datapoint[0] != prevItem[0] || item.datapoint[1] != prevItem[1])){
            //Prevent tooltip flashing
            prevItem[0] = item.datapoint[0];
            prevItem[1] = item.datapoint[1];
            console.log(item);
            var NearestDate = GetNearestDataPoint(item.datapoint[0], item.series.data);
            console.log(NearestDate);
            var Assignments = GetAllAssignmentsForDateInterval(NearestDate, item.datapoint[0], AssignmentHistory);
            console.log(Assignments);
            $("#tooltip").remove();
            showTooltip(item.pageX, item.pageY,
                formatDate(new Date(item.datapoint[0]),'%M/%d') + ' '+ item.datapoint[1] +'%<br/>' + ConstructTooltipAssignmentList(Assignments));
        }
    });
}

function ConstructTooltipAssignmentList(AssignmentArray){
    var TooltipText='';
    for(var i=0; i<Math.min(AssignmentArray.length,10); i++){ //Max 7 assignments
        TooltipText += AssignmentArray[i]['AssignmentName'] + ': ' + AssignmentArray[i]['AssignmentPercent'] + '%<br>';
    }
    if(AssignmentArray.length>10)
        TooltipText+= "&#43;"+ (AssignmentArray.length-10) +' More...';
    return TooltipText;
}

//Uses the data fed to flot and finds the nearest date before the current date, used to construct the date interval
function GetNearestDataPoint(curDate, Grades){
    //Assume it's arranged in sequential order by date
    var nearestDate = Grades[0][0];
    for(var i=0; i<Grades.length; i++){
        if(Grades[i][0]<curDate){
            nearestDate = Grades[i][0];
        }
        else{
            return nearestDate;
        }
    }
}

//Gets all assignment objects between dateA and dateB
function GetAllAssignmentsForDateInterval(dateA, dateB, AssignmentHistory){
    var Assignments = [];
    for(var i = 0; i<AssignmentHistory.length; i++){
        if(DateInterval(AssignmentHistory[i].AssignmentDate,dateA,dateB)){
            Assignments.push(AssignmentHistory[i]);
        }
    }
    return Assignments;
}

//test if date is between two dates
function DateInterval(date, a, b){
    return ((a == b && date <= a) //a == b if this is the leftmost point, so we have to include everything before it as well (mainly applies to "Your Grade")
    		|| (date > a && date <= b)); //exclude assignments from the previous point
}

//http://people.iola.dk/olau/flot/examples/interacting.html
function showTooltip(x, y, contents) {
    $('<div id="tooltip">' + contents + '</div>').css( {
        position: 'absolute',
        display: 'none',
        top: y + 10,
        left: x + 10
    }).appendTo("body").fadeIn(380);
}
//http://stackoverflow.com/questions/2315408/how-do-i-format-a-timestamp-in-javascript-to-display-it-in-graphs-utc-is-fine
function formatDate(date, fmt) {
    function pad(value) {
        return (value.toString().length < 2) ? '0' + value : value;
    }
    return fmt.replace(/%([a-zA-Z])/g, function (_, fmtCode) {
        switch (fmtCode) {
            case 'Y':
                return date.getUTCFullYear();
            case 'M':
                return pad(date.getUTCMonth() + 1);
            case 'd':
                return pad(date.getUTCDate());
            case 'H':
                return pad(date.getUTCHours());
            case 'm':
                return pad(date.getUTCMinutes());
            case 's':
                return pad(date.getUTCSeconds());
            default:
                throw new Error('Unsupported format code: ' + fmtCode);
        }
    });
}

function yFormatter(val, axis) {
    return val + '%';
}