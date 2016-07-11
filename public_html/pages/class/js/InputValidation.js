/**
 * Created by Mike on 12/23/13.
 */
$(function () {
    /* ~~~~~~~~~ Handle Add Assignment Input Validation ~~~~~~~~~~~~~~~ */

    //Assignment Name Alphanumeric
    $('body').on('keyup', '.AddAssignmentName', function(event){
        if(!ValidateAssignmentName($(this).val())){
            $(this).addClass('inputInvalid');
        }
        else{
            $(this).removeClass('inputInvalid');
        }
    });
    //Assignment Score Numeric
    $('body').on('keyup', '.AddAssignmentPoints', function(event){
        if(!ValidateAssignmentScore($(this).val())){
            $(this).addClass('inputInvalid');
        }
        else{
            $(this).removeClass('inputInvalid');
        }
    });

    //Assignment Name Alphanumeric
    $('body').on('keyup', '.AssignmentTitleInput ', function(event){
        if(!ValidateAssignmentName($(this).val())){
            $(this).addClass('inputInvalid');
        }
        else{
            $(this).removeClass('inputInvalid');
        }
    });
    //Assignment Score Numeric
    $('body').on('keyup', '.EditPointsInput', function(event){
        if(!ValidateAssignmentScore($(this).val())){
            $(this).addClass('inputInvalid');
        }
        else{
            $(this).removeClass('inputInvalid');
        }
    });
});

function ValidateAssignmentName(testString){
    return /^([A-Za-z0-9 ])+$/.test(testString) && !(/^\s*$/.test(testString)); //a-Z0-9 but not all white space
}
function ValidateAssignmentScore(testString){
    return /^\d*\.?\d+$/.test(testString);
}