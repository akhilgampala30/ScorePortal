/**
 * Created by Jacky on 1/18/14.
 */

function setCookie(cname, cvalue) {
    document.cookie = cname + "=" + cvalue + "; ";
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i].trim();
        if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
    }
    return "";
}


function disableGuiders() {
    guiders.hideAll();
    setCookie("UserLoginNumberState", "3");
}

if (getCookie("UserLoginNumberState") == '')
    setCookie("UserLoginNumberState", $('#UserLoginNumberState').html());

$(document).ready(function () {

    if (getCookie("UserLoginNumberState") == "1") {

        var firstClassLink = $('.ClassNavLink:first').attr('href');

        guiders.createGuider({
            buttons: [
                {name: "Exit Guide", onclick: function () {
                    disableGuiders();
                }},
                {name: "Next"}
            ],
            description: "This introductory guide will walk you through our features and help you get started. Click Next to begin!",
            id: "0",
            next: "1",
            overlay: true,
            onShow: function () {
                $('#5').hide()
            },
            title: "Welcome to ScorePortal!"
        }).show();

        guiders.createGuider({
            attachTo: "#QuickStats",
            buttons: [
                {name: "Exit Guide", onclick: function () {
                    disableGuiders();
                }},
                {name: "Next"}
            ],
            description: "Simple GPA and Last Updated Time are displayed here at a glance along with the Update Grades button.", onclick: function () {
                disableGuiders();
            },
            id: "1",
            next: "2",
            position: 9,
            title: "Quick Stats",
            overlay: true
        });

        guiders.createGuider({
            attachTo: "#NotificationDock",
            buttons: [
                {name: "Exit Guide", onclick: function () {
                    disableGuiders();
                }},
                {name: "Next"}
            ],
            description: "No more searching for which assignment was changed/updated last. This Notification Dock alerts all newly added/modified assignments!",
            id: "2",
            next: "3",
            position: 10,
            title: "Notification Dock",
            overlay: true
        });

        guiders.createGuider({
            attachTo: ".ClassBoard",
            buttons: [
                {name: "Exit Guide", onclick: function () {
                    disableGuiders();
                }},
                {name: "Next", onclick: function () {
                    window.location.href = firstClassLink;
                }}
            ],
            description: "View quick grade changes of individual classes on these class boards. The grade progress bar will display green/gray segments when your grade changes after updates to indicate gain/loss. Clicking on this board will lead you to the class page.",
            id: "3",
            position: 6,
            title: "Class Boards",
            overlay: true
        });
    }
});