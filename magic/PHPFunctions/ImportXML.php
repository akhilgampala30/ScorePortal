<?php
/**
 * Takes XML String Input and adds/updates the database with the appropriate values.
 */

/*
 * TODO: Add teacher email to teachers
 * TODO: Category Name Abrv should actually be abrv in Category (Probably through Screen scrapping/matching)
 * TODO: Add room number in classes (probably through screen scrapping)
 * TODO: Error handling on failed queries
 * TODO: Remove assignments/class enrollment
*/




//Find term number first in case we're not looking for extra terms to save time
/*
$StartDate = $Course->UserDefinedExtensions->ao_CourseExtensions->ao_Assignments->ao_Assignment[0]->ao_DueDate;
$EndDate = $Course->UserDefinedExtensions->ao_CourseExtensions->ao_Assignments->ao_Assignment[count($Course->UserDefinedExtensions->ao_CourseExtensions->ao_Assignments) - 1]->ao_DueDate;
//TODO: Investigate how old courses affect the course term
$TermNumber = GetClassTermNumber($StartDate,$EndDate, $TermNumbers); //Gets appropriate term number for this class //TODO: Check if term number is null
//skip class if not in current term if only updating current term
if ($onlyCurTerm) {
    if($TermNumber!=$curTermNumber)
    {
        continue;
    }
}
*/



include $_SERVER['DOCUMENT_ROOT'].'/paths.php';
if(!defined('DatabaseIncludeLoaded')|| !constant('DatabaseIncludeLoaded')){
    require $path['Database.php'];
}
require $path['NotificationManager.php'];

//TODO: Break down into smaller functions
function parseXMLtoDB($xml_string, $idStudent, $idSchool, $transactionStarted, PDO $db, $onlyCurTerm, $email=null, $legacyid=null, $deadlock=0) {

    //Deadlock prevention
    if($deadlock>0){
        if($deadlock>3){
            return false; //Error out
        }
        else{
            $DeadlockWaitTime = 70000*$deadlock*2.3*(rand(4,25)/10);
            usleep($DeadlockWaitTime); //3000 microseconds (0.003s/3ms)
        }
    }else{ //Don't bother filtering if we've already filtered but hit deadlock
        $xml_string = returnStringXMLFromString($xml_string); //Filter string first
    }

    //Make sure the XML is Valid
    try{
        $xml = simplexml_load_string($xml_string); //Load as Xml
    }catch(Exception $e){
        file_put_contents('ErrorXML.'.rand(0,6000000).'.log', $xml_string);
        return false;
    }

    if (!$xml) {
        file_put_contents('ErrorXML.'.rand(0,6000000).'.log', $xml_string);
        throw new Exception('Invalid XML File.');
    }

    $XMLTimestamp = (string) $xml->TransmissionData[0]->CreatedDateTime; //Get Timestamp string
    $XMLDateTime = DateTime::createFromFormat("Y-m-d*H:i:se", $XMLTimestamp); //Convert from their terrible methods
    $MySQLXMLTimestamp = $XMLDateTime->format('Y-m-d H:i:s'); //Set to MySQL friendly timestamp

    $XMLLevelCode = (string) $xml->Student[0]->AcademicRecord[0]->StudentLevel[0]->StudentLevelCode;

    if(!$transactionStarted){
        $db->exec('START TRANSACTION;SET SESSION TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;SET foreign_key_checks = 0;');
    }

    $LevelCodes = array( //TODO: Check if this works for middle school
        'TwelfthGrade' => 12,
        'EleventhGrade'=> 11,
        'TenthGrade' => 10,
        'NinthGrade' => 9,
        'EighthGrade' => 8,
        'SeventhGrade' => 7,
        'SixthGrade' => 6
    );

    //create student obj
    $newStudent = new Student();

    if(isset($email) && isset($legacyid)){
        $newStudent->Email = $email;
        $newStudent->LegacyID = $legacyid;
    }
    $newStudent->FirstName = (string) $xml->Student[0]->Person[0]->Name[0]->FirstName;
    $newStudent->LastName = (string) $xml->Student[0]->Person[0]->Name[0]->LastName;
    $newStudent->GPA = (double) $xml->Student[0]->AcademicRecord[0]->GPA[0]->GradePointAverage;
    if(array_key_exists($XMLLevelCode, $LevelCodes)){
        $newStudent->GradeLevel = $LevelCodes[$XMLLevelCode];
    }
    $newStudent->idSchools = $idSchool;
    $newStudent->idStudents = $idStudent;

    $newStudent->Schools = GetSchool($idSchool, $db);
    $newStudent->GradeLastUpdated = $MySQLXMLTimestamp;

    if (!StudentExists($idStudent, $db)) //TODO: Check if this is neccessary
        $idStudent = CreateNewStudent($newStudent, $db);
    else
        ImportXMLModifyStudent($newStudent, $db);

    $newStudent->idStudents = $idStudent; //In case we added a new ID

    $TermNumbers = GetAllTermNumbersOfSchool($idSchool, $db); //Gets Term Number

    $Classes = array();

    $curTermNumber = GetCurrentTermNumber($TermNumbers); //Gets the current term number as of now() //TODO: Check if term number is null

    //produce array of course/class obj
    foreach ($xml->Student[0]->AcademicRecord[0]->Course as $Course) {
        //Get Basic Course Information First
        $CourseTeacher = (string) $Course->UserDefinedExtensions->ao_CourseExtensions->ao_CourseTeacher;
        $CurrentGradeNumeric = (double) $Course->UserDefinedExtensions->ao_CourseExtensions->ao_CourseGrade->ao_CurrentGradeNumeric;
        $CurrentGradeLetter = (string) $Course->UserDefinedExtensions->ao_CourseExtensions->ao_CourseGrade->ao_CurrentGradeLetter;
        $tempCourseFullName = (string) $Course->CourseTitle;

        $tempTeacher = GetTeacherObject($CourseTeacher, $idSchool, $db); //Gets the teacher, if not exist will create

        $tempCourse = GetCourseObject($tempCourseFullName, $idSchool, $db); //Gets class/course and create them if they don't exist //TODO: Possibly set these using objects


        $TermsAssignments = array(); //[idTermNumber][n] = AssignmentXMLNode
        $NewestTermNumber = new TermNumber();
        $PreviousAssignmentItem = null;
        //Loop through all Assignments and organize by idTermNumber
        foreach ($Course->UserDefinedExtensions->ao_CourseExtensions->ao_Assignments->ao_Assignment as $Assignment) {
            $AssignmentTerm = GetClassTermNumber((string)$Assignment->ao_DueDate, (string)$Assignment->ao_DueDate, $TermNumbers); //Get the class term this assignment should belong to
            if($AssignmentTerm->Name==''){ //Something wrong happened
                if(!isset($PreviousAssignmentItem)){ //Give up if no prev assignment set either
                    continue; //TODO: Change to pick a random working assignment
                }
                $AssignmentTerm = GetClassTermNumber((string)$PreviousAssignmentItem->ao_DueDate, (string)$PreviousAssignmentItem->ao_DueDate, $TermNumbers); //Guess and set the due date to the last successful assignment
            }else{
                $PreviousAssignmentItem = $Assignment; //Set last working assignment
            }
            if ($onlyCurTerm) { //Skip assignment if it's not the current term and we don't want anything not current term
                if($AssignmentTerm->idTermNumbers!=$curTermNumber->idTermNumbers)
                {
                    continue;
                }
            }
            if(strtotime($AssignmentTerm->EndDate) > strtotime($curTermNumber->EndDate)){ //It's not possible to have an assignment beyond this semester
                //$AssignmentTerm = $curTermNumber;
            }
            $TermsAssignments[$AssignmentTerm->idTermNumbers][] = $Assignment;
            if(strtotime($AssignmentTerm->StartDate) > strtotime($NewestTermNumber->StartDate)){
                if(strtotime($AssignmentTerm->EndDate) > strtotime($curTermNumber->EndDate)){ //If the assignment term is greater than current term, don't bother with setting it as a newer term
                    $NewestTermNumber = $curTermNumber;
                }else{
                    $NewestTermNumber = $AssignmentTerm;
                }
            }

        } //End Of Assignment Loop

        foreach($TermsAssignments as $Key=>$Term){
            //Per Class Term
            $TermNumberObject = GetTermNumberByID($Key, $TermNumbers);

            if($Key != $NewestTermNumber->idTermNumbers){//Older Class
                /* @var $tempClass _Class */
                $tempClass = GetClassObject($tempCourse, $tempTeacher, $TermNumberObject, $idStudent, $CurrentGradeNumeric, $CurrentGradeLetter, $db, $Classes);
                UpdatePreviousEnrollment($idStudent, $tempClass->idClasses, $db);
            }else{ //Most recent class
                /* @var $tempClass _Class */
                $tempClass = GetClassObject($tempCourse, $tempTeacher, $TermNumberObject, $idStudent, $CurrentGradeNumeric, $CurrentGradeLetter, $db, $Classes);
                UpdateStudentEnrollment($idStudent,$tempClass->idClasses, $CurrentGradeNumeric, $CurrentGradeLetter, $db); //if student not enrolled in class, enroll, else update, record grade
            }

            //Construct Assignment and Category Arrays For the Class
            $Assignments = array();
            $Categories = array();

            foreach($Term as $Assignment){
                //Per Assignment in Class
                $Name = (string) $Assignment->ao_Name; //Assign the Name
                $DueDate = strtotime((string)$Assignment->ao_DueDate); //Assign the DueDate
                $Grade = (string) $Assignment->ao_Grade; //Assign Grade Format: Scored/Total
                //UpdatePreviousEnrollment($idStudent, $)

                //if category does not exist, create
                $CategoryName = (string) $Assignment->ao_Category; //Assign the Category Name
                $tempCategory = GetCategoryObject($CategoryName, $tempClass->idClasses, $Categories,$db);
                $PointsArray = GetPoints($Grade); //Explodes

                //Create the TempAssignment //TODO: Make this a function somehow
                $tempAssignment = new Assignment();
                $tempAssignment->AssignmentBookmarked = false;
                $tempAssignment->AssignmentDate = date('Y-m-d H:i:s', $DueDate);
                $tempAssignment->AssignmentDisabled = false;
                $tempAssignment->UserAddedAssignment = false;
                $tempAssignment->idCategory = $tempCategory->idCategory;
                $tempAssignment->idClasses = $tempClass->idClasses;
                $tempAssignment->idStudents = $idStudent;
                $tempAssignment->AssignmentEarnedPoints = $PointsArray[0];
                $tempAssignment->AssignmentPossiblePoints = $PointsArray[1];
                if (contains("-", $Grade) || contains("Score Not Published", $Grade) || strlen($PointsArray[0])==0) { //If the score isn't put in, use -1 as a placeholder
                    $tempAssignment->AssignmentEarnedPoints = -1;
                }
                $tempAssignment->AssignmentName = htmlspecialchars($Name, ENT_QUOTES);
                $tempAssignment->Category = $tempCategory;
                $tempAssignment->AssignmentNewAlert = false;

                $tempAssignment->idAssignment = GetAssignmentIDFromNameAndDate($tempAssignment->AssignmentName, $tempAssignment->AssignmentDate, $tempClass->idClasses, $db);

                //Add assignment if new
                if ($tempAssignment->idAssignment === false) {
                    $tempAssignment->AssignmentNewAlert = true; //Since assignment is new, mark it as new
                    $tempAssignmentID = AddAssignment($tempAssignment, $db); //Add the assignment and return the new id
                    $tempAssignment->idAssignment = $tempAssignmentID;
                    try{
                        AddAssignmentScore($tempAssignment, $idStudent, $db); //Add the score
                    }catch(PDOException $e){
                        //var_dump((int)$e->getCode());
                        if((int)$e->getCode() == 40001){ //We've deadlocked
                            //print_r('Deadlock Detected!!');
                            return parseXMLtoDB($xml_string, $idStudent, $idStudent, $transactionStarted, $db, $onlyCurTerm, $email, $legacyid, $deadlock+1);
                        }
                    }
                    UpdateAssignmentStatistics($tempAssignment->idAssignment, $db);
                    CreateNewAssignmentNotification($tempAssignment, $tempClass, $db, $tempAssignment->AssignmentDate);
                }
                //update assignment if different
                else {
                    if (!AssignmentScoreAgrees($tempAssignment, $db)) { //If the score has been updated
                        $tempAssignment->AssignmentNewAlert = true;
                        ModifyAssignmentScore($tempAssignment, $idStudent, $db);
                        UpdateAssignmentStatistics($tempAssignment->idAssignment, $db);
                    }
                    if(!AssignmentRecordExists($tempAssignment, $db)){ //If the user doesn't have this assignment added yet
                        $tempAssignment->AssignmentNewAlert = true; //Since assignment is new, mark it as new for the user
                        try{
                            AddAssignmentScore($tempAssignment, $idStudent, $db); //Add the score
                        }catch(PDOException $e){
                            if((int)$e->getCode() == 40001){ //We've deadlocked
                                //print_r('Deadlock Detected!!');
                                return parseXMLtoDB($xml_string, $idStudent, $idSchool, $transactionStarted, $db, $onlyCurTerm, $email, $legacyid, $deadlock+1);
                            }
                        }
                        IncrementAssignmentPopulation($tempAssignment->idAssignment, $db);
                        UpdateAssignmentStatistics($tempAssignment->idAssignment, $db);
                        CreateNewAssignmentNotification($tempAssignment, $tempClass, $db, $tempAssignment->AssignmentDate);
                    }
                    if (!AssignmentPropertyAgrees($tempAssignment, $db)) { //If Category/Date has changed
                        $tempAssignment->AssignmentNewAlert = true;
                        ModifyAssignment($tempAssignment, $db);
                    }
                    $tempAssignment->AssignmentBookmarked = GetBookmarkStatus($tempAssignment->idAssignment, $idStudent, $db);
                }
                if(!Category::CategoryArrayContains($Categories,$tempCategory)){
                    array_push($Categories, $tempCategory);
                }
                array_push($Assignments, $tempAssignment);
            }
            //Set Arrays to the Course
            $tempClass->Assignments = $Assignments;
            $tempClass->Categories = $Categories;
            if($Key == $NewestTermNumber->idTermNumbers){//If this class is in the current term
                /* @var $cat Category*/
                foreach($tempClass->Categories as &$cat){ //Cause cats
                    $cat->SumCategories($Assignments); //Cats should do f*d
                }
                $tempClass->CalculateGrade(); //Sum up the assignments
                //Add grade point to record
                AddStudent_Classes_Grade($CurrentGradeNumeric, $tempClass->idClasses, $MySQLXMLTimestamp, $idStudent, $tempClass->ClassTotalPoints, $db);
                AddClassAverage($tempClass->idClasses, $db, $MySQLXMLTimestamp);
            }
            //Add Class to Array
            array_push($Classes,$tempClass);
        }
    }
    $newStudent->Classes = $Classes;

    AddXML($idStudent, $xml_string, $db); //Store XML File for archiving purposes

    if(!$transactionStarted){
        $db->exec('COMMIT;SET foreign_key_checks = 1;'); //Commit changes
    }

    return $newStudent;
}

/**
 * Filters XML for invalid characters
 * @param String $string_
 * @return String
 */
function returnStringXMLFromString($string_)
{
	$stringxml_ = html_entity_decode($string_, ENT_COMPAT, 'UTF-8');
    $stringxml_ = preg_replace('~(</?)([a-z0-9_]+):~is', '$1$2_', $stringxml_);
    $stringxml_ = preg_replace("~&(?![A-Za-z]{1,8};)~", "&amp;", $stringxml_); //only replace & not followed by 1-8 letters and a semicolon (we assume otherwise it represents a legal xml entity)
    $stringxml_ = preg_replace("/<([^>]*?)</", "<", $stringxml_); //Remove any non parsed tags
    return $stringxml_;
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

function GetTermNumberByID($idTermNumber, $TermNumbers){
    foreach ($TermNumbers as $tempTermNumber) {
        if ($tempTermNumber->idTermNumbers == $idTermNumber) {
            return $tempTermNumber;
        }
    }
}

/**
 * Uses start/end date of course to determine the term for a specific class number
 * @param String $StartDate
 * @param String $EndDate
 * @param TermNumber[] $TermNumbers
 * @return TermNumber
 */
function GetClassTermNumber($StartDate, $EndDate, $TermNumbers){
    $TermNumber = new TermNumber();
    foreach ($TermNumbers as $tempTermNumber)
    {
        //if this term starts on/before the first assignment and ends on/after the last assignment, we've found the right term for this course
        if (strtotime($tempTermNumber->StartDate) <= strtotime($StartDate) && strtotime($tempTermNumber->EndDate) >= strtotime($EndDate))
        {
            $TermNumber = $tempTermNumber;
            break;
        }
    }
    return $TermNumber;
}

/**
 * Gets the teacher object by name and school id, creates the teacher if they don't exist
 * @param String $TeacherName
 * @param Int $idSchool
 * @param PDO $db
 * @return Teacher
 */
function GetTeacherObject($TeacherName, $idSchool, $db){
    $TeacherObj = GetTeacherByName($TeacherName, $idSchool, $db);
    if ($TeacherObj === false) {
        $tempTeacher = new Teacher();
        $tempTeacher->idSchools = $idSchool;
        $tempTeacher->TeacherName = $TeacherName;
        $tempTeacher->idTeachers = AddTeacher($tempTeacher, $db);
        return $tempTeacher;
    }
    return $TeacherObj;
}
/**
 * Gets the Course object by name and school id, creates the course if it doesn't exist
 * @param String $CourseFullName
 * @param Int $idSchool
 * @param PDO $db
 * @return Course
 */
function GetCourseObject($CourseFullName, $idSchool, $db){
    $tempCourseID = GetCourseIDFromName($CourseFullName, $db); //TODO: Further optimization by getting obj by name
    if ($tempCourseID === false) { // No rows will return false
        //Add New Course
        $tempCourse = new Course();
        $tempCourse->CourseName = $CourseFullName;
        $tempCourse->idSchools = $idSchool;
        $tempCourse->Population = 1;
        $tempCourse->idCourses = AddCourse($tempCourse, $db);
    }
    else{
        //Get Course from Course Name
        $tempCourse = GetCourse($tempCourseID,$db);
    }
    return $tempCourse;
}

/**
 * Gets the class object and creates it if it doesn't exist.
 * @param Course $objCourse
 * @param Teacher $objTeacher
 * @param TermNumber $objTermNumber
 * @param $idStudent
 * @param $CurrentGradeNumeric
 * @param $CurrentGradeLetter
 * @param $db
 * @param $ClassArray _Class[]
 * @return _Class|bool
 */
function GetClassObject(Course $objCourse, Teacher $objTeacher, TermNumber $objTermNumber, $idStudent, $CurrentGradeNumeric, $CurrentGradeLetter, $db, $ClassArray = null){
    if(isset($ClassArray)){ //Search class array first
        $tempClass = new _Class();
        $tempClass->idCourses = $objCourse->idCourses;
        $tempClass->idTeachers = $objTeacher->idTeachers;
        $tempClass->idTermNumber = $objTermNumber->idTermNumbers;

        $tempClass = _Class::ClassArrayContains($ClassArray, $tempClass);
        if($tempClass !== false){
            return $tempClass;
        }
    }

    $ClassID = GetClassID($objCourse->idCourses, $objTeacher->idTeachers, $objTermNumber->idTermNumbers, $db); //TODO: Further optimization by getting obj by name
    if($ClassID === false){
        $idClass = AddClass($objCourse->idCourses, $objTeacher->idTeachers, $objTermNumber->idTermNumbers, $db);
        $tempClass = new _Class();
        $tempClass->idClasses = $idClass;
        $tempClass->idCourses = $objCourse->idCourses;
        $tempClass->idTeachers = $objTeacher->idTeachers;
        $tempClass->idStudent = $idStudent;
        $tempClass->TermNumber = $objTermNumber;
        $tempClass->Teacher = $objTeacher;
        $tempClass->Course = $objCourse;
        $tempClass->NumericGrade = $CurrentGradeNumeric;
        $tempClass->LetterGrade = $CurrentGradeLetter;
    }
    else {
        $tempClass = GetClass($idStudent, $ClassID, $db);
    }
    return $tempClass;
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

/**
 * Enrolls the student in the class if not enrolled, updates grades otherwise
 * @param $idStudent
 * @param $idClass
 * @param $CurrentGradeNumeric
 * @param $CurrentGradeLetter
 * @param $db
 */
function UpdateStudentEnrollment($idStudent, $idClass, $CurrentGradeNumeric, $CurrentGradeLetter, $db){
    if (!StudentIsEnrolledInClass($idStudent, $idClass, $db)) { //TODO: Optimize through INSERT ON DUPLICATE UPDATE
        IncrementClassPopulation($idClass, $db); //User just joined class
        AddStudent_Classes(-1, $CurrentGradeNumeric, $CurrentGradeLetter, $idClass, $idStudent, $db);
    } else {
        ModifyStudent_Classes(-1, $CurrentGradeNumeric, $CurrentGradeLetter, $idClass, $idStudent, $db);
    }
}

/**
 * Enrolls the student in a previous class if not enrolled
 * @param $idStudent
 * @param $idClass
 * @param $db
 */
function UpdatePreviousEnrollment($idStudent, $idClass, $db){
    if (!StudentIsEnrolledInClass($idStudent, $idClass, $db)) { //TODO: Optimize through INSERT ON DUPLICATE UPDATE
        IncrementClassPopulation($idClass, $db); //User just joined class
        AddStudent_Classes(-1, -1, 'Z', $idClass, $idStudent, $db);
    }
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
?>
