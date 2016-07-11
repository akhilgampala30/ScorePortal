/**
 * Created by Jacky on 1/18/14.
 */
function setCookie(cname, cvalue) {
    var d = new Date();
    document.cookie = cname + "=" + cvalue + "; " + "path=/";
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

$(document).ready(function () {
    if (getCookie("UserLoginNumberState") == "1") {
        guiders.createGuider({
            attachTo: "#GradeProgress",
            buttons: [
                {name: "Exit Guide", onclick: function () {
                    disableGuiders();
                }},
                {name: "Next"}
            ],
            description: "This line graph is automatically generated with your current data. You can zoom, pan, and hover over critical points to view the specific assignment.",
            id: "5",
            overlay: true,
            next: "6",
            position: 11,
            title: "Grade Progress Graph",
            autoFocus: true
        }).show();

        guiders.createGuider({
            attachTo: "#CategoryBreakdown",
            buttons: [
                {name: "Exit Guide", onclick: function () {
                    disableGuiders();
                }},
                {name: "Next"}
            ],
            description: "Different categories, different weights. ScorePortal helps you to visualize your grade composition of individual categories and how they contribute to the overall grade.",
            id: "6",
            next: "7",
            position: 11,
            title: "Category Breakdown",
            overlay: true,
            autoFocus: true
        });

        guiders.createGuider({
            attachTo: "#Assignments",
            buttons: [
                {name: "Exit Guide", onclick: function () {
                    disableGuiders();
                }},
                {name: "Next"}
            ],
            description: "Here lies the heart of ScorePortal - your Assignments list.",
            id: "7",
            next: "8",
            position: 11,
            title: "Assignments",
            overlay: true,
            autoFocus: true
        });

        guiders.createGuider({
            attachTo: "#AssignmentsSortBar",
            buttons: [
                {name: "Exit Guide", onclick: function () {
                    disableGuiders();
                }},
                {name: "Next"}
            ],
            description: "You can sort your assignments alphabetically by Category or Assignment Name, and numerically by Score and Last Updated Date.",
            id: "8",
            next: "9",
            position: 12,
            title: "Sort Assignments",
            overlay: true,
            autoFocus: true
        });

        guiders.createGuider({
            attachTo: ".AssignmentItem",
            buttons: [
                {name: "Exit Guide", onclick: function () {
                    disableGuiders();
                }},
                {name: "Next"}
            ],
            description: "An assignment item can be expanded by a single click to reveal its effects on your class grade and your score's percentile in the class.",
            id: "9",
            next: "10",
            position: 1,
            title: "Assignment Details",
            overlay: true,
            onShow: function () {
                $('.AssignmentOverview:first').click()
            },
            autoFocus: true
        });

        guiders.createGuider({
            attachTo: "#SearchBarInput",
            buttons: [
                {name: "Exit Guide", onclick: function () {
                    disableGuiders();
                }},
                {name: "Next"}
            ],
            description: "With the new ScorePortal you can also search for classes and assignments via the above Search Bar.",
            id: "10",
            next: "11",
            position: 6,
            overlay: true,
            title: "Search Bar",
            autoFocus: true
        });

        guiders.createGuider({
            attachTo: "#EditGradesNavButton",
            buttons: [
                {name: "Exit Guide", onclick: function () {
                    disableGuiders();
                }},
                {name: "Next"}
            ],
            description: "Now let's explore how edit grades work. You can enter the Edit Grades mode by clicking this tab.",
            id: "11",
            next: "12",
            position: 12,
            title: "Edit Grades Tab",
            overlay: true,
            onHide: function () {
                $('#EditGradesNavButton').click()
            },
            autoFocus: true
        });

        guiders.createGuider({
            buttons: [
                {name: "Exit Guide", onclick: function () {
                    disableGuiders();
                }},
                {name: "Next"}
            ],
            description: "In Edit Grades you can modify your assignment data and see how your grade will change. Modifications made here do not affect your original grades.",
            id: "12",
            next: "13",
            title: "What is Edit Grades?",
            overlay: true,
            autoFocus: true
        });

        guiders.createGuider({
            attachTo: "#AddAssignmentHeader",
            buttons: [
                {name: "Exit Guide", onclick: function () {
                    disableGuiders();
                }},
                {name: "Next"}
            ],
            description: "Here you can add new assignments with customized category and points.",
            id: "13",
            next: "14",
            position: 1,
            title: "Add Assignments",
            onHide: function () {
                $('.AssignmentOverview:first').click()
            },
            overlay: true,
            autoFocus: true
        });

        guiders.createGuider({
            attachTo: ".EditBox",
            buttons: [
                {name: "Exit Guide", onclick: function () {
                    disableGuiders();
                }},
                {name: "Next"}
            ],
            description: "Expanding assignment items in Edit Grades will allow you to edit your assignment scores, change its category, delete added assignments, and hide original assignments from being included in grade calculations.",
            id: "14",
            next: "15",
            position: 6,
            title: "Edit Assignments",
            overlay: true,
            autoFocus: true
        });

        guiders.createGuider({
            attachTo: "#ResetAllAssignments",
            buttons: [
                {name: "Exit Guide", onclick: function () {
                    disableGuiders();
                }},
                {name: "Next"}
            ],
            description: "You may also reset all your modifications for this class with this Reset Assignments button",
            id: "15",
            next: "16",
            position: 12,
            title: "Reset Assignments",
            overlay: true,
            autoFocus: true
        });

        guiders.createGuider({
            buttons: [
                {name: "Exit Guide", onclick: function () {
                    disableGuiders();
                }}
            ],
            description: "You have reached the end of the features guide. We hope you find ScorePortal useful and helpful to your academic career. Good luck!",
            id: "16",
            next: "17",
            title: "Now It's Your Turn",
            overlay: true,
            autoFocus: true
        });
    }
});