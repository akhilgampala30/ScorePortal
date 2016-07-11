<?php
/**
 * User: Mike
 * Date: 12/30/13
 * Time: 5:12 PM
 */

function UpdateClass($idClass, $db=null){
    if(!isset($db)){
        $db=connect();
    }
    /* @var $CurrentStudentObject Student*/
    $CurrentStudentObject =& $_SESSION['StudentObj']; //Pass by ref
    foreach($CurrentStudentObject->Classes as &$CurClass){ //Search with byref instance
        if($CurClass->idClasses == $idClass){
            $CurClass = GetClass($CurrentStudentObject->idStudents, $idClass, $db); //Change ref
        }
    }
    unset($CurClass); //Unset ref
}

function UpdateStudentObject($db=null){
    if(!isset($db)){
        $db=connect();
    }
    /* @var $CurrentStudentObject Student*/
    $CurrentStudentObject = $_SESSION['StudentObj']; //Pass by ref
    $StudentObj = GetStudent($CurrentStudentObject->idStudents, $db);
    $_SESSION['StudentObj'] = $StudentObj;
}