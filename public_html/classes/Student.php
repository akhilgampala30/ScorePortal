<?php

/**
 * User: Mike
 * Date: 7/5/13
 * Time: 2:55 PM

 */
class Student
{
    //IDs
    public $idStudents;
    public $idSchools;
    public $idMobileCarrier;
    public $LegacyID;


    //Attribs
    public $FirstName;
    public $LastName;
    public $Email;
    public $GPA;
    public $SchoolDistrictID;
    public $SchoolDistrictPassword;
    public $MobileNumber;
    public $GradeLastUpdated; //'Y-m-d H:i:s' MySQL Format
    public $Points;
    public $GradeLevel;

    //Objs
    public $Achievements; //Array
    /* @var $Notifications Notification[] */
    public $Notifications; //Array
    /* @var $Classes _Class[] */
    public $Classes; //Array
    public $MobileCarrier;
    /* @var $Schools School */
    public $Schools;

    /**
     * @param String $FirstName
     * @param String $LastName
     * @param Float $GPA
     * @param String $GradeLevel
     * @param Int $idSchools
     * @param Int $idStudents
     * @param School $Schools
     * @return Student
     */
    public static function ConstructXMLStudent($FirstName, $LastName, $GPA, $GradeLevel, $idSchools, $idStudents, $Schools)
    {
        $StudentObject = new Student();
        $StudentObject->FirstName = $FirstName;
        $StudentObject->LastName = $LastName;
        $StudentObject->GPA = $GPA;
        $StudentObject->GradeLevel = $GradeLevel;
        $StudentObject->idSchools = $idSchools;
        $StudentObject->idStudents = $idStudents;
        $StudentObject->Schools = $Schools;
        return $StudentObject;
    }


}