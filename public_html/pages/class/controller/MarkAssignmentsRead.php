<?php
/**
 * User: Mike
 * Date: 12/24/13
 * Time: 3:58 PM
 */


/**
 * Simply goes through all assignments in class and removes the AssignmentNewAlert Flag
 *
 * @param _Class $ClassObj
 * @param PDO $db
 * @return int Removed Flags Count
 */

function MarkAssignmentsRead(_Class $ClassObj, PDO $db){
    $RemovedCount = 0;
    foreach($ClassObj->Assignments as $Assignment){
        if($Assignment->AssignmentNewAlert){
            RemoveNewAlertFlag($Assignment->idAssignment, $Assignment->idStudents, $db);
            $RemovedCount++;
        }
    }
    return $RemovedCount;
}