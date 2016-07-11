/**
 * Created by Jacky on 1/19/14.
 */

function removeLightBox() {
    $('#LightBox').fadeOut();
    $('#ChangeDistrictPasswordBox').hide();
    ('#ReturnMessageDistrict').slideUp();
    $('#DistrictPasswordSubmitButton').html("Submit");
}


$(document).ready(function () {
    $('#ChangeDistrictPasswordButton').click(function () {
        $('#LightBox').fadeIn();
        $('#ChangeDistrictPasswordBox').show();
    });

    $('#LightBox').click(function () {
        removeLightBox();
    });

    $('#ChangeBox').click(function (event) {
        event.stopPropagation();
    });

    var ButtonObj = $('#DistrictPasswordSubmitButton');
    var ReturnMsg = $('#ReturnMessageDistrict');

    ButtonObj.click(function (event) {
        if (ButtonObj.html() == 'Done')
            removeLightBox();
        else {
            if (!ValidateForm())
                return false; //TODO: Error msg here
            ButtonObj.css('background-image', 'url(/images/icons/gifs/SmallSquareLoader.gif)');
            ButtonObj.css('background-repeat', 'no-repeat');
            ButtonObj.css('background-position', 'center');
            ButtonObj.children().css('font-size', '0');
            $.post('/include/UserSessionState/ValidateNewDistrictPassword.php', {NewDistrictPassword: $("input[name='NewDistrictPassword']").val().replace(/ /g, '')}, function (data) {
                var returnData = jQuery.parseJSON(data);
                switch (returnData.status) {
                    case 1:
                        ReturnMsg.html("Your password has been successfully changed!");
                        ReturnMsg.css({color: "green"});
                        ReturnMsg.slideDown();
                        ButtonObj.css('background-image', 'none');
                        ButtonObj.children().css('font-size', '22px');
                        ButtonObj.html('Done');
                        break;
                    default:
                        ReturnMsg.css({color: "red"});
                        ReturnMsg.html(returnData.msg);
                        ReturnMsg.slideDown();
                        ButtonObj.css('background-image', 'none');
                        ButtonObj.children().css('font-size', '22px');
                        break;
                }
                console.log(data);
            });
        }
    });
});

function ValidateForm() { //TODO: Enhance validation
    var DistrictPassword = $("input[name='NewDistrictPassword']").val().replace(/ /g, '');
    if (DistrictPassword != '')
        return true;
    return false;
}
