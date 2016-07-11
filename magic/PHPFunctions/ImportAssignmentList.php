<?php
/**
 * User: Mike
 * Date: 4/6/14
 * Time: 1:37 PM
 */

include $_SERVER['DOCUMENT_ROOT'].'/paths.php';
if(!defined('DatabaseIncludeLoaded')|| !constant('DatabaseIncludeLoaded')){
    require $path['Database.php'];
}
require $path['NotificationManager.php'];

/**
 * @param $AsmtList String Assignment List HTML from ppstudentasmlist.html
 * @param $idStudents Int
 * @param $idSchool Int
 * @param $CurrentNumericGrades array()
 * @param $CurrentLetterGrades array()
 * @param $CurrentGPA float GPA
 * @param $db PDO
 */
function import($AsmtList, $idStudents, $idSchool, $CurrentNumericGrades, $CurrentLetterGrades, $CurrentGPA, PDO $db){
    $doc = new DOMDocument();
    @$doc->loadHTML($AsmtList); //Suppress their malformed HTML errors

    $TermNumbers = GetAllTermNumbersOfSchool($idSchool, $db); //Gets Term Numbers for School
    $CurrentTermNumber = GetCurrentTermNumber($TermNumbers); //Get the current term number

    $StudentClasses = GetAllClasses($idStudents, $db, $CurrentTermNumber->idTermNumbers);

    $db->exec('START TRANSACTION;SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;SET foreign_key_checks = 0;');

    //Update GPA and last updated time
    StudentAsmntListModifyStudent($CurrentGPA, $idStudents, $db);

    $Courses = array();
    foreach($doc->getElementsByTagName('tr') as $AssignmentElem){
        //Check if it has oddrow/evenrow as aI class to show that it is an assignment row
        if($AssignmentElem->getAttribute('class') != 'oddrow' && $AssignmentElem->getAttribute('class') != 'evenRow' )
            continue;
        $Assignment = array();

        $AssignmentTDs = $AssignmentElem->getElementsByTagName('td');

        $Assignment['DueDate'] = $AssignmentTDs->item(1)->nodeValue;
        $Assignment['Category'] = $AssignmentTDs->item(2)->nodeValue;
        $Assignment['AssignmentName'] = $AssignmentTDs->item(3)->nodeValue;
        $Assignment['Score'] = $AssignmentTDs->item(4)->nodeValue;

        //Ignore assignments outside of the current term
        if(GetAssignmentTermNumber($Assignment['DueDate'],$TermNumbers)->idTermNumbers != $CurrentTermNumber->idTermNumbers){
            continue;
        }

        //Add to courses array. Course name as index
        $Courses[$AssignmentTDs->item(0)->nodeValue][] = $Assignment;
    }
    foreach($Courses as $CourseName => $Course ){

        $CurClass = GetClassFromCourseNameForStudent($CourseName, $StudentClasses);
        if(!$CurClass)
            continue; //Class couldn't be found.

        $Categories = array();
        $Assignments = array();

        foreach($Course as $Assignment){ //Get the assignments per course
            $Name = (string)$Assignment['AssignmentName']; //Assign the Name
            $DueDate = strtotime($Assignment['DueDate']); //Assign the DueDate
            $Grade = (string)$Assignment['Score']; //Assign Grade Format: Scored/Total

            //Get the category obj of the current assignment, pass Category array for caching
            $tempCategory = GetCategoryObject($Assignment['Category'], $CurClass->idClasses, $Categories,$db);
            $PointsArray = GetPoints($Assignment['Score']); //Explodes

            //Set up the Assignment object
            $tempAssignment = new Assignment();
            $tempAssignment->AssignmentBookmarked = false;
            $tempAssignment->AssignmentDate = date('Y-m-d H:i:s', $DueDate);
            $tempAssignment->AssignmentDisabled = false;
            $tempAssignment->UserAddedAssignment = false;
            $tempAssignment->idCategory = $tempCategory->idCategory;
            $tempAssignment->idClasses = $CurClass->idClasses;
            $tempAssignment->idStudents = $idStudents;
            $tempAssignment->AssignmentEarnedPoints = $PointsArray[0];
            $tempAssignment->AssignmentPossiblePoints = $PointsArray[1];
            if (contains("-", $Grade) || contains("Score Not Published", $Grade) || strlen($PointsArray[0])==0) { //If the score isn't put in, use -1 as a placeholder
                $tempAssignment->AssignmentEarnedPoints = -1;
            }
            $tempAssignment->AssignmentName = htmlspecialchars($Name, ENT_QUOTES);
            $tempAssignment->Category = $tempCategory;
            $tempAssignment->AssignmentNewAlert = false;

            $tempAssignment->idAssignment = GetAssignmentIDFromNameAndDate($tempAssignment->AssignmentName, $tempAssignment->AssignmentDate, $CurClass->idClasses, $db);

            //If the assignment is new
            if ($tempAssignment->idAssignment === false) {
                $tempAssignment->AssignmentNewAlert = true; //Since assignment is new, mark it as new
                $tempAssignmentID = AddAssignment($tempAssignment, $db); //Add the assignment and return the new id
                $tempAssignment->idAssignment = $tempAssignmentID;
                try{
                    AddAssignmentScore($tempAssignment, $idStudents, $db); //Add the score
                }catch(PDOException $e){
                    //Well, fuck.
                }
                UpdateAssignmentStatistics($tempAssignment->idAssignment, $db);
                CreateNewAssignmentNotification($tempAssignment, $CurClass, $db, $tempAssignment->AssignmentDate);
            }
            //update assignment if different
            else {
                if (!AssignmentScoreAgrees($tempAssignment, $db)) { //If the score has been updated
                    $tempAssignment->AssignmentNewAlert = true;
                    ModifyAssignmentScore($tempAssignment, $idStudents, $db);
                    UpdateAssignmentStatistics($tempAssignment->idAssignment, $db);
                }
                if(!AssignmentRecordExists($tempAssignment, $db)){ //If the user doesn't have this assignment added yet
                    $tempAssignment->AssignmentNewAlert = true; //Since assignment is new, mark it as new for the user
                    try{
                        AddAssignmentScore($tempAssignment, $idStudents, $db); //Add the score
                    }catch(PDOException $e){
                        //Fuck again
                    }
                    IncrementAssignmentPopulation($tempAssignment->idAssignment, $db);
                    UpdateAssignmentStatistics($tempAssignment->idAssignment, $db);
                    CreateNewAssignmentNotification($tempAssignment, $CurClass, $db, $tempAssignment->AssignmentDate);
                }
                if (!AssignmentPropertyAgrees($tempAssignment, $db)) { //If Category/Date has changed
                    $tempAssignment->AssignmentNewAlert = true;
                    ModifyAssignment($tempAssignment, $db);
                }
                $tempAssignment->AssignmentBookmarked = GetBookmarkStatus($tempAssignment->idAssignment, $idStudents, $db);
            }
            //Add the category to our category cache for later use
            if(!Category::CategoryArrayContains($Categories,$tempCategory)){
                array_push($Categories, $tempCategory);
            }
            array_push($Assignments, $tempAssignment);
        }

        //Set Arrays to the Course
        $CurClass->Assignments = $Assignments;
        $CurClass->Categories = $Categories;
        /* @var $cat Category*/
        foreach($CurClass->Categories as &$cat){ //Cause cats
            $cat->SumCategories($Assignments); //Cats should do f*d
        }
        $CurClass->CalculateGrade(); //Sum up the assignments


        //Add grade point to record
        $MySQLXMLTimestamp = date('Y-m-d H:i:s');
        //Only add current grade numeric point if the numeric grade is valid
        if(array_key_exists($CourseName, $CurrentNumericGrades) && is_numeric($CurrentNumericGrades[$CourseName])){
            $CurrentGradeNumeric = $CurrentNumericGrades[$CourseName];
            $CurrentLetterGrade = $CurrentLetterGrades[$CourseName];

            //Update student_classes_grade
            AddStudent_Classes_Grade($CurrentGradeNumeric, $CurClass->idClasses, $MySQLXMLTimestamp, $idStudents, $CurClass->ClassTotalPoints, $db);

            //Update student_classes
            ModifyStudent_Classes_ClassGrade($CurrentGradeNumeric, $CurrentLetterGrade, $CurClass->idClasses, $idStudents, $db);
        }
        AddClassAverage($CurClass->idClasses, $db, $MySQLXMLTimestamp);
    }

    $db->exec('COMMIT;SET foreign_key_checks = 1;'); //Commit changes

}

/**
 * Uses current time and searches DB and then creates the TermNumber object
 * @param TermNumber[] $TermNumbers
 * @return TermNumber
 */
function GetCurrentTermNumber($TermNumbers){
    date_default_timezone_set('America/Los_Angeles'); //Woo West Coast
    $date = date('Y-m-d');
    $curTermNumber = new TermNumber();
    foreach ($TermNumbers as $tempTermNumber) {
        if ($tempTermNumber->StartDate < $date && $tempTermNumber->EndDate > $date) { //TODO: Return on find
            $curTermNumber=$tempTermNumber;
        }
    }
    return $curTermNumber;
}

/**
 * Uses date of assignment to determine the term number
 * @param String $Date
 * @param TermNumber[] $TermNumbers
 * @return TermNumber
 */
function GetAssignmentTermNumber($Date, $TermNumbers){
    $TermNumber = new TermNumber();
    foreach ($TermNumbers as $tempTermNumber)
    {
        //If assignment falls between start/end date of a term, then we've got the right term
        if (strtotime($tempTermNumber->StartDate) <= strtotime($Date) && strtotime($tempTermNumber->EndDate) >= strtotime($Date))
        {
            $TermNumber = $tempTermNumber;
            break;
        }
    }
    return $TermNumber;
}

/**
 * @param $CourseName
 * @param $Classes
 * @return _Class
 */
function GetClassFromCourseNameForStudent($CourseName, $Classes){
    foreach($Classes as $Class){
        if($Class->Course->CourseName == $CourseName){
            return $Class;
        }
    }
    return false;
}

/**
 * Gets the category specified, created if does not exist
 * @param $CategoryName
 * @param $idClass
 * @param $db
 * @return Category
 */
function GetCategoryObject($CategoryName, $idClass, $CategoryArray, $db){
    $tc = new Category();
    $tc->CategoryName = $CategoryName;
    $tc->idClasses = $idClass;
    //If the category is already found in the array, don't bother looking it up again
    $obj = Category::CategoryArrayContains($CategoryArray, $tc);
    if($obj !== false){
        return $obj;
    }
    $CategoryID = GetCategoryID($CategoryName, $idClass, $db); //TODO: Increase efficiency by just calling GetCategory from a name so it's only 1 query
    $tempCategory = new Category();
    if ($CategoryID === false) {
        $tempCategory->CategoryName = $CategoryName;
        $tempCategory->CategoryNameAbrv = strlen($CategoryName) > 6 ? substr($CategoryName, 0, 4) . '.' : $CategoryName; //avoid abbreviating slightly-longer-than-4
        $tempCategory->idClasses = $idClass;
        $tempCategory->CategoryWeight = 0;
        $tempCategory->idCategory = AddCategory($tempCategory, $db);
    }
    else
        $tempCategory = GetCategory($CategoryID, $db);
    return $tempCategory;
}

//Fast Contains Function
function contains($needle, $hay){
    if (strpos($hay,$needle) !== false)
        return true;
    return false;
}

function GetPoints($Grade){
    return explode('/',$Grade);
}