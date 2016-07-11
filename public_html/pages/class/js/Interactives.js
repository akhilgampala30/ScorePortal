//TODO: Drop down the assignment details on first run for new users

$(document).ready(function () {
    //To display class information coming soon, will be removed later
    $('#ClassInformationNavButton').click(function () {
        $('#LightBox').fadeIn();
    });
    $('#LightBox').click(function () {
        $('#LightBox').fadeOut();
    });
    $("#LightBoxContent").click(function (event) {
        event.stopPropagation();
    });
});

$(function () {
    //ResetAllAssignments
    $('body').on('click', '#ResetAllAssignments', function(event){
        var CurrentObject = $(this);
        CurrentObject.children('.fa-refresh').addClass('fa-spin');
        $.get("/RemoveAllModifiedAssignments/"+ClassID, function( data ) {
            if(data.indexOf("true") != -1){
                ga('send', 'event', 'Edit', 'Click', 'Reset Assignments');
                reloadPage('e');
            }
            else{
                CurrentObject.children('.fa-refresh').css('textShadow','#ff0000');
                CurrentObject.children('.fa-refresh').removeClass('fa-spin');
            }
        }).fail(function() {
                CurrentObject.children('.fa-refresh').css('color','#ff0000');
                CurrentObject.children('.fa-refresh').removeClass('fa-spin');
        });
    });


    /* Handle Minimize/Maximize of Assignment Descriptions */
    $('body').on('click','.AssignmentOverview', function(event){
        if($(event.target).hasClass('clickable')) //Ensure it isn't a clickable object (ex. Textbox, Bookmark)
            return;
        $(this).siblings('.AssignmentDetailContainer').slideToggle(); //.siblings, changed so only the overview div will slide toggle
        $(this).siblings('.AssignmentBarColor').fadeOut();
        ga('send', 'event', 'Assignment', 'Click', 'Toggle Assignment Description');
    });

    /* ~~~~~ Handle Sorting Clicks ~~*/
    //Category Name
    $('body').on('click', '#CategorySortItem', function(event){
        var SortObj = $('.AssignmentCategory'); var SortOrder = 'asc';
        if(SortObj.data('o') == 'asc'){SortObj.data('o','desc'); SortOrder='desc';}
        else{SortObj.data('o','asc'); SortOrder='asc';}
        $('#AssignmentListContainer>.AssignmentItem').tsort('.AssignmentCategory',{order:SortOrder, attr:'title'});
        ga('send', 'event', 'Sort', 'Click', 'Sort By Category Name');
    });
    //Assignment Name
    $('body').on('click', '#AssignmentNameSortItem', function(event){
        var SortObj = $('.AssignmentName'); var SortOrder = 'asc';
        if(SortObj.data('o') == 'asc'){SortObj.data('o','desc'); SortOrder='desc';}
        else{SortObj.data('o','asc'); SortOrder='asc';}
        $('#AssignmentListContainer>.AssignmentItem').tsort('.AssignmentName',{order:SortOrder, data: 'sort'});
        ga('send', 'event', 'Sort', 'Click', 'Sort By Assignment Name');
    });
    //Point Score
    $('body').on('click', '#PointScoreSortItem', function(event){
        var SortObj = $('.AssignmentPointScore'); var SortOrder = 'asc';
        if(SortObj.data('o') == 'asc'){SortObj.data('o','desc'); SortOrder='desc';}
        else{SortObj.data('o','asc'); SortOrder='asc';}
        $('#AssignmentListContainer>.AssignmentItem').tsort('.AssignmentPointScore',{order:SortOrder, data: 'sort'});
        ga('send', 'event', 'Sort', 'Click', 'Sort By Assignment Points');
    });
    //Percentage Score
    $('body').on('click', '#PercentageSortItem', function(event){
        var SortObj = $('.AssignmentPercentageScore'); var SortOrder = 'asc';
        if(SortObj.data('o') == 'asc'){SortObj.data('o','desc'); SortOrder='desc';}
        else{SortObj.data('o','asc'); SortOrder='asc';}
        $('#AssignmentListContainer>.AssignmentItem').tsort('.AssignmentPercentageScore',{order:SortOrder, data: 'sort'});
        ga('send', 'event', 'Sort', 'Click', 'Sort By Assignment Percentage');
    });
    //Updated Date
    $('body').on('click', '#UpdatedDateSortItem', function(event){
        var SortObj = $('.AssignmentUpdatedDate'); var SortOrder = 'asc';
        if(SortObj.data('o') == 'asc'){SortObj.data('o','desc'); SortOrder='desc';}
        else{SortObj.data('o','asc'); SortOrder='asc';}
        $('#AssignmentListContainer>.AssignmentItem').tsort('.AssignmentUpdatedDate',{order: SortOrder, data: 'sort'});
        ga('send', 'event', 'Sort', 'Click', 'Sort By Assignment Date');
    });

    /* ~~~~~~ Handling Page Navigating within Class w/ AJAX ~~~~ */
    //TODO: Make jQuery selectors more efficient
    $('body').on('click', '#OriginalGradesNavButton', function(event){
        $('#ClassNav>div').each(function(){
            $(this).removeClass('SelectedClassNavButton');
        });
        $('#OriginalGradesNavButton').addClass('SelectedClassNavButton');
        ga('send', 'event', 'Navigation', 'Click', 'Original Grades Button');
        reloadPage('o');
    });
    $('body').on('click', '#EditGradesNavButton', function(event){
        $('#ClassNav>div').each(function(){
            $(this).removeClass('SelectedClassNavButton');
        });
        $('#EditGradesNavButton').addClass('SelectedClassNavButton');
        ga('send', 'event', 'Navigation', 'Click', 'Edit Grades Button');
        reloadPage('e');
    });

    /*~~~~~~~~~~~~~~~~ Handle Add Assignment Button Action ~~~~~~~~~~~~~~~~~~~~~~*/
    $('body').on('click', '.AddButton', function(event){
        var CurrentObj = $(this);
        var AssignmentName = CurrentObj.siblings('.AddAssignmentName').val();
        var EarnedPoints = CurrentObj.siblings('#AddAssignmentEarnedPointsInput').val();
        var PossiblePoints = CurrentObj.siblings('#AddAssignmentPossiblePointsInput').val();
        var CategoryID = CurrentObj.siblings('.AddAssignmentCategorySelect').val();
        var modCategory = (CategoryID.indexOf('m.')!=-1)+0; //hackish int conversion
        CategoryID = CategoryID.replace("m.",""); //Remove Mod Identifier
        if(ValidateAssignmentName(AssignmentName) && ValidateAssignmentScore(EarnedPoints) && ValidateAssignmentScore(PossiblePoints)) //Ensure that the inputs are valid
        {
            $.get("/AddAssignment/"+EarnedPoints+"/"+PossiblePoints+"/"+modCategory+"/"+CategoryID+"/"+ClassID+"/"+AssignmentName, function( data ) {
                var result = $.parseJSON(data);
                if(result.code>0){
                    ga('send', 'event', 'Edit', 'Click', 'Add Assignment');
                    reloadPage('e','#A'+result.code);
                }
                else{
                    $('#AddAssignmentError').text(result.msg).fadeIn();
                    redShadow(CurrentObj);
                }
            }).fail(function() {
                redShadow(CurrentObj);
            });
        }
        else{
            redShadow(CurrentObj);
        }
    });


    /*~~~~~~~~~~~~~~~~ Handle Bookmark Button Actions ~~~~~~~~~~~~~~~~~~~~~~*/
    $('body').on('click', '.BookmarkButton', function(event){
        var CurrentObj = $(this);
        var Added = (CurrentObj.data('added')!=null && CurrentObj.data('added')!=-1);
        var AssignmentID=0;
        if(Added)
            AssignmentID = CurrentObj.data('added'); //Get the assignment ID
        else
            AssignmentID = CurrentObj.data('assignmentid'); //Get the assignment ID
        var Bookmarked = CurrentObj.hasClass('Bookmarked'); //Check if it's bookmarked already
        CurrentObj.toggleClass('Bookmarked'); //Immediately change visible bookmark before sending request
        $.get("/UpdateBookmark/"+AssignmentID+"/"+Number(!Bookmarked)+"/"+Number(Added), function( data ) {
            var msg = $.parseJSON(data);
            if(msg.code==1){
                ga('send', 'event', 'Assignment', 'Click', 'Bookmark Assignment');
            }
            else{
                CurrentObj.toggleClass('Bookmarked'); //Toggle back if an error occured
            }
        }).fail(function() {
            CurrentObj.toggleClass('Bookmarked');
        });
    });

    /*~~~~~~~~~~~~~~~~ Handle Delete Button Actions ~~~~~~~~~~~~~~~~~~~~~~*/
    $('body').on('click', '.DeleteAssignment', function(event){
        var CurrentObj = $(this);
        var AssignmentID = CurrentObj.data('added'); //Get the assignment ID
        if(AssignmentID==-1) //You can't delete original/modified assignments
            return; //TODO: Figure out why -1 in DeleteAssignment leads to a register redirect
        var OriginalBackgroundImage = CurrentObj.css('background-image');
        CurrentObj.css('background-image','url(/images/icons/gifs/SmallAjaxLoader.gif)'); //Insert a small loader to show users that it's requesting
        $.get("/DeleteAssignment/"+AssignmentID, function( data ) {
            if(data.indexOf("true") != -1){
                ga('send', 'event', 'Edit', 'Click', 'Delete Assignment');
                reloadPage('e');
            }
            else{
                CurrentObj.css('background-image',OriginalBackgroundImage); //Reset Gif Animation
                redShadow(CurrentObj);
            }
        }).fail(function() {
            CurrentObj.css('background-image',OriginalBackgroundImage); //Reset Gif Animation
            redShadow(CurrentObj);
        });
    });

    /*~~~~~~~~~~~~~~~~ Handle Disable Button Actions ~~~~~~~~~~~~~~~~~~~~~~*/
    $('body').on('click', '.DisableAssignment', function(event){
        var CurrentObj = $(this);
        var Added = (CurrentObj.data('added')!=-1);
        var AssignmentID =0;
        if(Added){
            AssignmentID = CurrentObj.data('added'); //Get the assignment ID
        }
        else{
            AssignmentID = CurrentObj.data('assignmentid'); //Get the assignment ID
        }
        var Disabled = (CurrentObj.data('disabled')==0); //Check if it's disabled already
        var OriginalBackgroundImage = CurrentObj.css('background-image');
        CurrentObj.css('background-image','url(/images/icons/gifs/SmallAjaxLoader.gif)'); //Insert a small loader to show users that it's requesting
        $.get("/DisableAssignment/"+AssignmentID+"/"+Number(Disabled)+"/"+Number(Added), function( data ) {
            var result = $.parseJSON(data);
            if(result.code == 1){
                ga('send', 'event', 'Edit', 'Click', 'Disable Assignment');
                reloadPage('e','#'+result.msg);
            }
            else{
                CurrentObj.css('background-image',OriginalBackgroundImage); //Reset Gif Animation
                redShadow(CurrentObj);
            }
        }).fail(function() {
            CurrentObj.css('background-image',OriginalBackgroundImage); //Reset Gif Animation
            redShadow(CurrentObj);
        });
    });

    /*~~~~~~~~~~~~~~~~ Handle Reset Button Actions ~~~~~~~~~~~~~~~~~~~~~~*/
    $('body').on('click', '.ResetAssignment', function(event){
        var CurrentObj = $(this);
        if(CurrentObj.data('added')!=-1) //You can't reset added assignments
            return;
        var AssignmentID = CurrentObj.data('assignmentid'); //Get the assignment ID
        var OriginalBackgroundImage = CurrentObj.css('background-image');
        CurrentObj.css('background-image','url(/images/icons/gifs/SmallAjaxLoader.gif)'); //Insert a small loader to show users that it's requesting
        $.get("/ResetAssignment/"+AssignmentID, function( data ) { //TODO: Figure out why without ID, it just redirects to home
            var result = $.parseJSON(data);
            if(result.code == 1){
                ga('send', 'event', 'Edit', 'Click', 'Reset Assignment');
                reloadPage('e', '#'+result.msg);
            }
            else{
                CurrentObj.css('background-image',OriginalBackgroundImage); //Reset Gif Animation
                redShadow(CurrentObj);
            }
        }).fail(function() {
            CurrentObj.css('background-image',OriginalBackgroundImage); //Reset Gif Animation
            redShadow(CurrentObj);
        });
    });

    /*~~~~~~~~~~~~~~~~ Handle Save Button Actions ~~~~~~~~~~~~~~~~~~~~~~*/
    $('body').on('click', '.SaveAssignment', function(event){
        var CurrentObj = $(this);
        var EarnedPoints = CurrentObj.siblings('.AssignmentEarnedPointsInput').val();
        var PossiblePoints  = CurrentObj.siblings('.AssignmentPossiblePointsInput').val();
        var Added = (CurrentObj.data('added')!=-1);
        var AssignmentID =0;
        if(Added){
            AssignmentID = CurrentObj.data('added'); //Get the assignment ID
        }
        else{
            AssignmentID = CurrentObj.data('assignmentid'); //Get the assignment ID
        }
        var CategoryID = CurrentObj.closest('.EditBox').find('.EditBoxCategorySelect').eq(0).val();
        var modCategory = (CategoryID.indexOf('m.')!=-1)+0; //hackish int conversion
        CategoryID = CategoryID.replace("m.",""); //Remove Mod Identifier
        var Title = CurrentObj.closest('.EditBox').find('.AssignmentTitleInput').eq(0).val();
        if((Added && !ValidateAssignmentName(Title)) || !ValidateAssignmentScore(EarnedPoints) || !ValidateAssignmentScore(PossiblePoints)){ //Does not allow for added assignments to have symbols
            redShadow(CurrentObj);
            return;
        }
        var GetURLString='';
        if(Added){
            GetURLString = '/UpdateScore/'+EarnedPoints+'/'+PossiblePoints+'/'+AssignmentID+'/'+CategoryID+'/'+Title+'/'+Number(modCategory);
        }
        else{
            GetURLString = '/UpdateScore/'+EarnedPoints+'/'+PossiblePoints+'/'+AssignmentID;
        }
        var OriginalBackgroundImage = CurrentObj.css('background-image');
        CurrentObj.css('background-image','url(/images/icons/gifs/SmallAjaxLoader.gif)'); //Insert a small loader to show users that it's requesting
        $.get(GetURLString, function( data ) {
            var result = $.parseJSON(data);
            if(result.code == 1){
                ga('send', 'event', 'Edit', 'Click', 'Save Assignment');
                reloadPage('e','#'+result.msg);
            }
            else{
                CurrentObj.css('background-image',OriginalBackgroundImage); //Reset Gif Animation
                redShadow(CurrentObj);
            }
        }).fail(function() {
                CurrentObj.css('background-image',OriginalBackgroundImage); //Reset Gif Animation
                redShadow(CurrentObj);
        });
    });
});

function reloadPage(Page, Selector){
    $('#ClassContent').animate({'opacity':'0.3'}, 747); //Fade out to show that we're loading the new page
    $('#ClassContent').load('/Grades/'+Page+'/1/'+ClassID, function(){
        ga('send', 'pageview', '/Grades/'+Page+'/1/'+ClassID);
        $('#ClassContent').animate({'opacity':'1'}, 747); //Fade out to show that we're loading the new page
        labelPosition();
        $('#AssignmentListContainer>.AssignmentItem').tsort('.AssignmentUpdatedDate',{order: 'desc', data: 'sort'});
        $(Selector).children('.AssignmentDetailContainer').slideDown(1);
    });
}

function redShadow(HTMLObject){
    HTMLObject.css('box-shadow','0px 0px 7px 1px rgba(208, 20, 0, .5)');
    HTMLObject.css('-webkit-box-shadow','0px 0px 7px 1px rgba(208, 20, 0, .5)');
}
