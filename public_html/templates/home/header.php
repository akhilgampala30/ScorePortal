<?php
/**
 * User: Mike
 * Date: 7/23/13
 * Time: 8:25 PM
 */
?>
<div id="header">
    <a href="/home">
        <div id="logo"></div>
    </a>

    <div class="ui-widget" id="SearchBar">
        <div id="SearchBarSearch"></div>
        <input type="text" name="SearchIn" id="SearchBarInput" placeholder="Search Classes and Assignments">
    </div>
    <div id="RightUserPanel">
        <span id="RightPanelUserName">
            <?php
            /* @var $GlobalStudentObject Student */
            echo $GlobalStudentObject->FirstName, ' ', $GlobalStudentObject->LastName;
            ?>
        </span>
        <a href="/home" title="Home">
            <div id="RightPanelHomeButton" class="RightUserPanelIcons">
            </div>
        </a>
        <a href="/Logout" title="Log Out">
            <div id="RightPanelLogOutButton" class="RightUserPanelIcons">
            </div>
        </a>
        <a href="/settings" title="Settings">
            <div id="RightPanelSettingsButton" class="RightUserPanelIcons">
            </div>
        </a>
    </div>
</div>
<div id="HeaderFiller">

</div>
<!-- more fb-root fb js sdk stuff -->
