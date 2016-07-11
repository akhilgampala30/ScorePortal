$(function(){
    //Preload images file
    preload([
        '/images/icons/gifs/SmallSquareLoader.gif',
        '/images/icons/bullet_blue.png',
        '/images/icons/bullet_black.png',
        '/images/icons/bullet_green.png'
    ]);

    $("#DistrictID, #DistrictPassword").keyup(function(event){
        if(event.keyCode == 13){
            $("#SubmitButton").click();
        }
    });

    $('body').on('click', '#SubmitButton', function(event){
        var ButtonObj = $(this);
        if(!ValidateForm()){
            $('#ErrorMessage').slideDown().html('Error Validating Form');
            ButtonObj.css('background-image','none');
            ButtonObj.children().css('font-size','22px');
            return false;
        }
        ButtonObj.css('background-image','url(/images/icons/gifs/SmallSquareLoader.gif)');
        ButtonObj.css('background-repeat','no-repeat');
        ButtonObj.css('background-position','center');
        ButtonObj.children().css('font-size','0');
        var FormData = $('#RegisterForm').serialize();
        $.post('/include/UserSessionState/ValidateUserRegisterInformation.php', FormData, function(data){
            var returnData = jQuery.parseJSON(data);
            switch(returnData.status){
                case 1:
                    $('#Content').load(returnData.msg);
                    break;
                default:
                    $('#ErrorMessage').slideDown().html(returnData.msg);
                    ButtonObj.css('background-image','none');
                    ButtonObj.children().css('font-size','22px');
                    break;
            }
            //console.log(data);
        });
    });
});
function ValidateForm(){ //TODO: Enhance validation
    var SelectedSchoolID = $("select[name='School']").val();
    var DistrictID = $("input[name='DistrictID']").val().replace(/ /g,'');
    var DistrictPassword = $("input[name='DistrictPassword']").val().replace(/ /g,'');
    var Agree = $("input[name='Agreement']").is(':checked');
    if(SelectedSchoolID == 1
        && DistrictID!=''
        && DistrictPassword!=''
        && Agree)
    return true;
    return false;
}
function preload(arrayOfImages) {
    $(arrayOfImages).each(function(){
        $('<img/>')[0].src = this;
    });
}