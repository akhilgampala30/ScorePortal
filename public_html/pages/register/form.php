<?php
/**
 * User: Mike
 * Date: 7/16/13
 * Time: 10:45 PM
 */
?>
<div class="ContentTitle">
    Basic Information
</div>
<div class="ContentDescription">
    Please enter your log in information to connect your district server with ScorePortal.
</div>
<div class="Data">
    <form id="RegisterForm">
        <table>
            <tr>
                <td>Email: </td>
                <td><?php echo (isset($_SESSION['AuthUserEmail'])?$_SESSION['AuthUserEmail']:'none'); ?></td>
            </tr>
            <tr>
                <td>
                    School:
                </td>
                <td>
                    <select name="School" class="SchoolSelector">
                        <option value="1">Arcadia High School</option>
                        <option value="2">Arcadia High School (Summer)</option>
                        <!--<option value="2">San Marino High School</option>
                        <option value="3">La Salle High School</option>-->
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    Student ID:
                </td>
                <td>
                    <input id="DistrictID" type="text" name="DistrictID" placeholder="District ID">
                </td>
            </tr>
            <tr>
                <td>
                    Student Password:
                </td>
                <td>
                    <input id="DistrictPassword" type="password" name="DistrictPassword" placeholder="Password">
                </td>
            </tr>
        </table>
        <div id="AgreementCheckbox">
            <input type="checkbox" name="Agreement" value="Agree"><span>I have read and agreed to the <a href="/tos" target="_blank">Terms of Service</a>.</span><br>
        </div>
        <div id="ErrorMessage" style="margin-top: 15px;margin-bottom:-15px;">Invalid Username/Password</div>
        <div class="CSSButtons" id="SubmitButton">
            <div class="CSSButtonsText">
                <span>Submit</span>
            </div>
        </div>
        <input type="hidden" name="s" value="reg0">
    </form>
</div>