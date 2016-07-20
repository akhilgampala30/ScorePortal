<?php
/**
 * User: Mike
 * Date: 7/18/13
 * Time: 4:40 PM
 */
?>
<div class="ContentTitle">
    Connecting to Server
</div>
<div class="ContentDescription">
    Please wait while ScorePortal connects to your district servers.
</div>
<div class="Data">
    <table>
        <tr>
            <td>
                <img src="/images/icons/bullet_green.png">
            </td>
            <td>
                <span class="Text">Validating Log In Credentials</span>
            </td>
        </tr>
        <tr>
            <td>
                <img src="/images/icons/bullet_blue.png">
            </td>
            <td>
                <span class="Text CurrentlyLoading">Obtaining Grade Data</span>
            </td>
        </tr>
        <tr>
            <td>
                <img src="/images/icons/bullet_black.png">
            </td>
            <td>
                <span class="Text">Setting Up User</span>
            </td>
        </tr>
        <tr>
            <td>
                <img src="/images/icons/bullet_black.png">
            </td>
            <td>
                <span class="Text">Obtaining Auxiliary Data</span>
            </td>
        </tr>
        <tr>
            <td>
                <img src="/images/icons/bullet_black.png">
            </td>
            <td>
                <span class="Text">Encrypting Data</span>
            </td>
        </tr>
    </table>

    <div id="ErrorMessage" >There was an error obtaining your grade data, please <a href="/">try again</a> later.</div>

    <a style="text-decoration:none;" href="/Login">
        <div class="CSSButtons" id="DoneButton" style="display:none;">
            <div class="CSSButtonsText">
                <span>Done</span>
            </div>
        </div>
    </a>
</div>
<iframe src="/include/UserSessionState/RegisterUser.php" style="display:block">
</iframe>