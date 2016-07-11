/**
 * User: Mike
 * Date: 11/3/13
 * Time: 9:30 AM
 */
function CreateGradeCompositionChart(){

    $.plot('#GradeCompositionChart', DonutData, {
        series: {
            pie: {
                innerRadius: 0.64,
                show: true,
                radius:0.88,
                //offset:{top: -7},
                label: {
                    show: true,
                    radius: 1,
                    formatter: labelFormatter,
                    threshold: 0.02
                },
                stroke: { //Removed outlines
                    width: 2,
                    mod2:true //TODO: Sometimes when the slices are really small, the missing category slice still has a border
                },
                highlight: {
                    opacity: 0.2
                }
            }
        },
        legend: {
            show: false
        },
        grid: {
            hoverable: true,
            clickable: true
        }
    });

    var PrevSelectedItem;
    $("#GradeCompositionChart").bind("plothover", function (event, pos, item) {
        console.log(item);
        if(item ==null || item.series.label == ''){
            $('.PieChartLabel').children('div').stop().fadeTo(200, 0);
            PrevSelectedItem = null;
        }
        if(item !=null && item.series.label!=PrevSelectedItem){
            PrevSelectedItem = item.series.label;
            var labelName = item.series.label.replace(/\W/g, '');
            $('.PieChartLabel').children('div').stop().fadeTo(200, 0);
            $('.PieChartLabel.'+labelName).children('div').stop().fadeTo(1000, 1);
            console.log($('.PieChartLabel.'+labelName).html());
        }
    });
}

function labelFormatter(label, series, x, y, canvasWidth, canvasHeight) {
    if(label == "Category Missed"){
        return "";
    }
    var TextAlign, displabel = label; //Smarter labeling of pie chart through x,y,canvas args
    if(x>canvasWidth/2){TextAlign = 'text-align:center;margin-left:15px;'; }
    else{TextAlign = 'text-align:center;';}
    if(y>canvasHeight/2){
        return "<div class='PieChartLabel "+label.replace(/\W/g, '')+"' style='"+TextAlign+" position:relative;top:7px;'>" + Math.round(series.percent) +"%<div class='PieChartCategoryLabel''>"  + displabel + '</div></div>';
    }
    else{
        return "<div class='PieChartLabel "+label.replace(/\W/g, '')+"' style='"+TextAlign+" position:relative;top:-7px;'><div class='PieChartCategoryLabel'>" + displabel + "</div>" + Math.round(series.percent) + '% </div>';
    }
}

//Adjust Label Positions to provide more space between chart and the labels
function labelPosition(){
    var centerX = $('#GradeCompositionChart').offset().left + ($('#GradeCompositionChart').width()/2);
    $('.pieLabel').each(function(){
        var LabelXPos = $(this).offset().left;
        var LeftOffset = parseInt($(this).css('left'));
        if(LabelXPos > centerX)
            $(this).css({
                'text-align':'right',
                left: (LeftOffset+2)+'px'
            });
        else
            $(this).css({
                'text-align':'left',
                left: (LeftOffset-2)+'px'
            });
    });
}