<?php
/**
 * User: Mike
 * Date: 12/22/13
 * Time: 4:11 PM
 */

if(!isset($LoadFromScratch) || $LoadFromScratch){ //Called from AJAX
    //Reload everything
    if(!defined('DatabaseIncludeLoaded') || !constant('DatabaseIncludeLoaded')){
        include $_SERVER['DOCUMENT_ROOT'].'/paths.php';
        require $path['Database.php'];
    }
    if(session_id() == '') {
        session_start();
    }
    if(isset($_SESSION['StudentObj']))
    {
        $GlobalStudentObject = $_SESSION['StudentObj'];
    }
    else{
        dieJSON(-1, 'Invalid Student Obj'); //TODO: Re enable redirect
        //header('Location: /'); //Redirect to home if StudentObj doesn't exist
    }
}
include $path['SessionState'];
$db = connect();

//include $path['Statistics.php']; //Already included in DB
include $path['CreateStudentGradeHistory.php']; //Get fx for creating the history chart
//Get ClassID of Current Page

$ClassID = $_GET['id'];
if(!isset($ClassID) || !is_numeric($ClassID))
    dieJSON(-1, 'Invalid ClassID/No Class ID'); //TODO: Re enable redirect
    //header('Location: /home'); //Redirect to home if invalid ClassID

/* @var $GlobalStudentObject Student
 * @var $CurrentClass _Class
 */
//Search for ClassID and set as current classObj
foreach($GlobalStudentObject->Classes as &$CurrentClass)
    if($CurrentClass->idClasses == $ClassID)
        $ClassObj =& $CurrentClass; //Pass byref
unset($CurrentClass);

if(!isset($ClassObj))
    dieJSON(-1, 'Invalid Class Obj'); //TODO: Re enable redirect
    //header('Location: /home'); //Redirect to home if ClassID not found
//TODO "headers already sent by index.php" so this redirect might not really work (though normally it shouldn't be needed anyway)

$GradeHistory = GetRecordedGradeHistory($ClassObj);
$CalculatedAssignmentHistory = ReturnAssignmentArray($ClassObj, time());
$ClassAverageHistory = GetClassAverageHistory($ClassObj, $CalculatedAssignmentHistory, $config['ClassAverageHistoryCutOff']);
$CalculatedGradeHistory = CalculatedGradeHistory($ClassObj, time(), 0, $ClassObj->isWeighted);
