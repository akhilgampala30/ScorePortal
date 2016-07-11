<?php
$CurrentStudentObject = $_SESSION['StudentObj'];
$StudentName = $CurrentStudentObject->FirstName . ' ' . $CurrentStudentObject->LastName;
$StudentEmail = $CurrentStudentObject->Email;
$SchoolName = $CurrentStudentObject->Schools->SchoolName;
$StudentDistrictID = $CurrentStudentObject->SchoolDistrictID;
?>

<div id="wrapper">
    <div id="LightBox">
        <div id="ChangeBox">
            <form class="ChangeBoxContent" id="ChangeDistrictPasswordBox">
                <div class="ChangeOption">
                    <div class="ChangeOptionText">District ID:</div>
                    <div class="ChangeOptionText"><?php echo $StudentDistrictID; ?></div>
                </div>
                <div class="ChangeOption">
                    <div class="ChangeOptionText">New PowerSchool Password:</div>
                    <input type="password" name="NewDistrictPassword" placeholder="New Password">
                </div>
                <div class="ChangeOption">
                    <div class="ChangeOptionTextReturnMessage" id="ReturnMessageDistrict">Your password has been
                        successfully changed
                    </div>
                </div>
                <div class="CSSButtons" id="DistrictPasswordSubmitButton">
                    <div class="CSSButtonsText">
                        <span>Submit</span>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="SettingsBoard">
        <div class="BoardTitle" id="GeneralSettingsTitle">
            <span id="Name"><?php echo $StudentName; ?></span>
            <!--            <span id="MemberType">Normal Member</span>-->
            <div style='clear:both'></div>
        </div>
        <div id="LoginAccountColumn">
            <span class="SettingSubTitle">Login Account Connections</span>
            <div class="LoginAccount">
                <div id="GoogleLogo" class="LoginLogo">

                </div>
                <div class="LoginLink" id="GoogleLink">
                    <div class="LinkedLabel">linked to:</div>
                    <div class="LinkedAccountName"><?php echo $StudentEmail; ?></div>
                    <a href="/UnlinkUser" style="color: inherit;"><div class="ChangeAccount LightButton" id="GoogleChangeAccountButton">Unlink Google Account</div></a>
                </div>
                <div style="clear:both"></div>
            </div>
            <!--            <div class="LoginAccount">-->
            <!--                <div id="YahooLogo" class="LoginLogo">-->
            <!---->
            <!--                </div>-->
            <!--                <div class="LoginLink" id="YahooLink">-->
            <!--                    <div class="AddAccountButton LightButton" id="YahooConnect">-->
            <!--                        Use Yahoo Instead-->
            <!--                    </div>-->
            <!--                    <!---->
            <!--                    <div class="LinkedLabel">linked to:</div>-->
            <!--                    <div class="LinkedAccountName">awesome.sauce@scoreportal.org</div>-->
            <!--                    <div class="ChangeAccount LightButton" id="YahooChangeAccountButton">Change Google Account</div>-->
            <!--                    -->
            <!--                </div>-->
            <!--                <div style="clear:both"></div>-->
            <!--            </div>-->
        </div>
        <div id="PowerschoolLoginColumn">
            <span class="SettingSubTitle">Powerschool Login</span>
            <table id="PowerschoolInformationTable">
                <tr>
                    <td>School</td>
                    <td><?php echo $SchoolName; ?></td>
                </tr>
                <tr>
                    <td>School ID</td>
                    <td><?php echo $StudentDistrictID; ?></td>
                </tr>
            </table>
            <div class="ChangeAccount LightButton" id="ChangeDistrictPasswordButton">Change District Password</div>
        </div>
        <div style="clear:both">
        </div>
    </div>
    <!--    <div class="SettingsBoard">-->
    <!--        <div class="BoardTitle" id="SPProPromo">Pro Features</div>-->
    <!--        <img src="/images/ScorePortalProBanner.png"/>-->
    <!--    </div>-->
</div>