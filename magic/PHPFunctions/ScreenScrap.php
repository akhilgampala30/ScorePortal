<?php
/**
 * User: Mike
 * Date: 12/24/13
 * Time: 10:24 AM
 */

//TODO: Update Assignments from recent assignment list through screen scrapping

if(!defined('DatabaseIncludeLoaded')|| !constant('DatabaseIncludeLoaded')){
    include $_SERVER['DOCUMENT_ROOT'].'/paths.php';
    require $path['Database.php'];
}

require_once $path['PowerAPICore.php'];
require_once $path['PowerAPICourse.php'];
require_once $path['PowerAPIUser.php'];
/**
 * Sets periods and sets weights for the student
 * @param $idStudent
 * @param PDO $db
 * @param null $PSUser
 * @param null $PowerSchoolLogin
 * @param null $CurrentStudent
 */
function ScrapPS($idStudent, PDO $db, $PSUser=null, $PowerSchoolLogin=null, $CurrentStudent=null){
    if(!isset($PSUser)){
        if(!isset($CurrentStudent)){ //If we didn't get an obj, let's get an obj
            $CurrentStudent = GetStudent($idStudent, $db);
        }
        if(!isset($PowerSchoolLogin)){ //Grab login info from database if not given
            $PowerSchoolURL = $CurrentStudent->Schools->PowerschoolRootURL;
            $PowerSchoolStudentID = $CurrentStudent->SchoolDistrictID;
            $PowerSchoolPassword = $CurrentStudent->SchoolDistrictPassword;
        }else{
            $PowerSchoolURL = $PowerSchoolLogin['PSHomeUrl']; //"https://powerschool.ausd.net/"
            $PowerSchoolStudentID = $PowerSchoolLogin['StudentID'];
            $PowerSchoolPassword = $PowerSchoolLogin['StudentPassword'];
        }
        $ps = new PowerAPI\Core($PowerSchoolURL);

        try {
            $user = $ps->auth($PowerSchoolStudentID, $PowerSchoolPassword);
        } catch (Exception $e) {
            die('Something went wrong! Press the Back button on your browser and try again.<br />PA said: '.$e->getMessage());
        }
    }else{
        $user = $PSUser;
        if(!isset($CurrentStudent)){ //If we didn't get an obj, let's get an obj
            $CurrentStudent = GetStudent($idStudent, $db);
        }
    }

    $courses = $user->getCourses();
    SetUserPeriods($idStudent, $courses, $CurrentStudent, $db);

    $TermNumbers = GetAllTermNumbersOfSchool($CurrentStudent->Schools->idSchools, $db); //TODO: Term Number should be stored in the student obj

    $CurrentTermNumber = GetCurrentTermNumber($TermNumbers);
    foreach($courses as $course){
        $course->getAssignments($CurrentTermNumber->Name);
    }
    SetClassWeights($idStudent, $courses, $CurrentStudent, $db);
}

//Use screenscrapped courses to get periods
function SetUserPeriods($idStudent, $courses, Student $studentObj, PDO $db){
    foreach($courses as $course){
        preg_match('/^([0-9]+)(.*?)$/', $course->period, $matches);
        $PeriodNumber = $matches[0]; //Only take the number when it's 1(A) format
        $CourseName = $course->name;
        foreach($studentObj->Classes as $Class){
            $output = $Class->Course->CourseName;
            $output = preg_replace('/[^(\x20-\x7F)]*/','', $output); //Sanitize the output from MySQL
            if($CourseName == $output) //TODO: This depends on course names having A/B to differ between semesters
            {
                ModifyStudent_Classes_Period($PeriodNumber, $Class->idClasses, $idStudent, $db);
            }
        }
    }
}

/**
 * @param $idStudent
 * @param $courses
 * @param Student $studentObj
 * @param PDO $db
 */
function SetClassWeights($idStudent, $courses, Student $studentObj, PDO $db){
    foreach($courses as $course){ //Loop through each course
        if(count($course->weights)>0){ //Detected Weights
            $CourseName = $course->name;
            foreach($studentObj->Classes as $Class){ //Find corresponding Course Obj
                if($CourseName == $Class->Course->CourseName) //TODO: This depends on course names having A/B to differ between semesters
                {
                    $isWeightedSet = false;
                    $idClass = $Class->idClasses;
                    foreach($course->weights as $rawcategoryname=>$weight){
                    	$categoryname = html_entity_decode($rawcategoryname);
                        $foundCategory = false;
                        foreach($Class->Categories as $Category){ //Find Category Match
                            if($Category->CategoryName == $categoryname){ //If the names match
                                ModifyCategoryWeight($Category->idCategory, $idClass, ($weight/100.00), $idStudent, $db);
                                $foundCategory = true;
                                if(!$isWeightedSet){
                                    ModifyClassIsWeightedFlag(true, $idClass, $idStudent, $db);
                                    $isWeightedSet = true;
                                }
                            }
                        }
                        if(!$foundCategory){ //If the category doesn't exist, add it up
                            $newCategory = new Category();
                            $newCategory->CategoryName = $categoryname;
                            $newCategory->CategoryWeight = ($weight/100.00);
                            $newCategory->idClasses = $idClass;
                            $newCategory->CategoryNameAbrv = ''; //TODO: Set Proper Abbrv
                            AddCategory($newCategory, $db);
                        }
                    }
                }
            }
        }
    }
}

/**
 * Uses current time and searches DB and then creates the TermNumber object
 * @param TermNumber[] $TermNumbers
 * @return TermNumber
 * //Already imported from ImportXML
function GetCurrentTermNumber($TermNumbers){
    date_default_timezone_set('America/Los_Angeles'); //Whoo West Coast
    $date = date('Y-m-d');
    $curTermNumber = new TermNumber();
    foreach ($TermNumbers as $tempTermNumber) {
        if ($tempTermNumber->StartDate < $date && $tempTermNumber->EndDate > $date) {
            $curTermNumber=$tempTermNumber;
        }
    }
    return $curTermNumber;
}
 */