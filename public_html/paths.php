<?php
/**
 * User: Mike
 * Date: 11/28/13
 * Time: 1:35 PM
 *
 * Store paths of all major required files. Place folder in DOCUMENT_ROOT.
 */

$path['config.php'] = dirname($_SERVER['DOCUMENT_ROOT']).'/magic/config.php';

//Classes
$path['LoadClasses.php'] = $_SERVER['DOCUMENT_ROOT'].'/include/LoadClasses.php';
$path['ClassesDirectory'] = $_SERVER['DOCUMENT_ROOT'].'/classes/';

//Database
$path['Database.php'] = dirname($_SERVER['DOCUMENT_ROOT']).'/magic/Database/Database.php';
$path['DatabaseAddFunctions.php'] = dirname($_SERVER['DOCUMENT_ROOT']).'/magic/Database/DatabaseAddFunctions.php';
$path['DatabaseGetFunctions.php'] = dirname($_SERVER['DOCUMENT_ROOT']).'/magic/Database/DatabaseGetFunctions.php';
$path['DatabaseModifyFunctions.php'] = dirname($_SERVER['DOCUMENT_ROOT']).'/magic/Database/DatabaseModifyFunctions.php';
$path['DatabaseValidateFunctions.php'] = dirname($_SERVER['DOCUMENT_ROOT']).'/magic/Database/DatabaseValidateFunctions.php';

//UserSessionState
$path['CheckID.php'] = $_SERVER['DOCUMENT_ROOT'].'/include/UserSessionState/CheckID.php';
$path['RegisterUser.php'] = $_SERVER['DOCUMENT_ROOT'].'/include/UserSessionState/RegisterUser.php';
$path['UserLoginInit.php'] = $_SERVER['DOCUMENT_ROOT'].'/include/UserSessionState/UserLoginInit.php';
$path['ValidateUserRegisterInformation.php'] = $_SERVER['DOCUMENT_ROOT'].'/include/UserSessionState/ValidateUserRegisterInformation.php';
$path['SessionState'] = $_SERVER['DOCUMENT_ROOT'].'/include/UserSessionState/SessionState.php';

/*Libraries*/
//PowerAPI
//$path['PowerAPI.php'] = $_SERVER['DOCUMENT_ROOT'].'/libraries/PowerAPI/PowerAPI.php';
//OpenID
$path['OpenID.php'] = $_SERVER['DOCUMENT_ROOT'].'/libraries/OpenID/OpenID.php';
//Statistics
$path['Statistics.php'] = $_SERVER['DOCUMENT_ROOT'].'/libraries/Statistics/Statistics.php';
//PowerAPI2.3
$path['PowerAPICore.php']= $_SERVER['DOCUMENT_ROOT'].'/libraries/PowerAPIv2.3/Core.php';
$path['PowerAPICourse.php']= $_SERVER['DOCUMENT_ROOT'].'/libraries/PowerAPIv2.3/Course.php';
$path['PowerAPIUser.php']= $_SERVER['DOCUMENT_ROOT'].'/libraries/PowerAPIv2.3/User.php';

//Misc
$path['UpdateStatus.php'] = $_SERVER['DOCUMENT_ROOT'].'/pages/register/UpdateStatus.php';
$path['LoginUpdate.php'] = $_SERVER['DOCUMENT_ROOT'].'/include/UserSessionState/LoginUpdate.php';


//PHP Functions
$path['ImportXML.php'] = dirname($_SERVER['DOCUMENT_ROOT']).'/magic/PHPFunctions/ImportXML.php';
$path['ImportAssignmentList.php'] = dirname($_SERVER['DOCUMENT_ROOT']).'/magic/PHPFunctions/ImportAssignmentList.php';
$path['ScreenScrap.php'] = dirname($_SERVER['DOCUMENT_ROOT']).'/magic/PHPFunctions/ScreenScrap.php';
$path['CreateStudentGradeHistory.php'] = dirname($_SERVER['DOCUMENT_ROOT']).'/magic/PHPFunctions/CreateStudentGradeHistory.php';
$path['NotificationManager.php'] = dirname($_SERVER['DOCUMENT_ROOT']).'/magic/PHPFunctions/NotificationManager.php';

//Initialize Objects for Class Page
$path['InitClass.php'] = $_SERVER['DOCUMENT_ROOT'].'/pages/class/controller/InitClass.php';

//Sum Categories
$path['SumCategories.php'] = $_SERVER['DOCUMENT_ROOT'].'/pages/class/controller/SumCategories.php';
//Mark Assignments as Not New
$path['MarkAssignmentsRead.php'] =  $_SERVER['DOCUMENT_ROOT'].'/pages/class/controller/MarkAssignmentsRead.php';

//Misc Heads To Include
$path['class/head.php'] =   $_SERVER['DOCUMENT_ROOT'].'/pages/class/head.php';