<?php
/**
 * User: Mike
 * Date: 12/31/13
 * Time: 9:34 AM
 */

/**
 * @param $AssignmentObject Assignment
 * @param $db
 * @param $notificationTime
 */
function CreateNewAssignmentNotification(Assignment $AssignmentObject, _Class $ClassObj, PDO $db, $notificationTime = null){
    $NewNotification = new Notification();
    $NewNotification->idStudents = $AssignmentObject->idStudents;

    if($AssignmentObject->AssignmentEarnedPoints > -1){
        $NewNotification->Notification = sprintf('You earned %d/%d (%d%%) on your %s %s "%s"', //You earned 50/50 (100%) on your Eng Lit/Cmp (AP) A homework "SIL Perm Slip"
            $AssignmentObject->AssignmentEarnedPoints, $AssignmentObject->AssignmentPossiblePoints, round($AssignmentObject->GetPercentage()), $ClassObj->Course->CourseName,
            strtolower($AssignmentObject->Category->CategoryName), $AssignmentObject->AssignmentName);
        $NewNotification->idNotificationType = 1;
    }else{
        $NewNotification->Notification = sprintf('%s inputted a new %s "%s" due by %s', //Barclay inputted a new homework "Ideologies Bloop" due 12/5/13
            preg_replace("/[^A-Za-z0-9 ]/", '', strtok($ClassObj->Teacher->TeacherName,' ')), strtolower($AssignmentObject->Category->CategoryName),
            $AssignmentObject->AssignmentName, date('m/d/Y',strtotime($AssignmentObject->AssignmentDate)));
        $NewNotification->idNotificationType = 2;
    }
    if(!isset($notificationTime))
        $notificationTime=date('Y-m-d H:i:s');
    $NewNotification->Time = $notificationTime;
    AddNotification($NewNotification, $db);
}