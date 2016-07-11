<?php

//Add Functions
//Student Obj from Session
/**
 * Creates a new student in the database and returns the new student ID
 *
 * @param Student $StudentObj
 * @param PDO $db
 * @return int $lastInsertID
 */
function CreateNewStudent($StudentObj, PDO $db) {
    try {
        $i=1;
        $StartingPoints = 0; //TODO: Actually set this to an actual starting number
        $query = $db->prepare("INSERT INTO students (FirstName, LastName, GPA, GradeLevel, Points, Schools_idSchools, idStudents, StudentDistrictID, StudentDistrictPassword, Email, GradesLastUpdated, LegacyID)
            VALUES (?, /*fName*/
            ?, /*lName*/
            ?,?,?,?,?, /*GPA, GradeLevel, Points, idSchools, idStudents*/
            ?,  /*DistrictID*/
            ?, /*DistrictPassword*/
            ?, /*Email*/
            ?,?)");
        $query->bindValue($i++, php_aes_encrypt($StudentObj->FirstName, S1));
        $query->bindValue($i++, php_aes_encrypt($StudentObj->LastName, S1));
        $query->bindParam($i++, $StudentObj->GPA);
        $query->bindParam($i++, $StudentObj->GradeLevel);
        $query->bindParam($i++, $StartingPoints); //Should be set to 0 but maybe some starting points
        $query->bindParam($i++, $StudentObj->idSchools);
        $query->bindParam($i++, $StudentObj->idStudents);
        $query->bindValue($i++, php_aes_encrypt($StudentObj->SchoolDistrictID, S2));
        $query->bindValue($i++, php_aes_encrypt($StudentObj->SchoolDistrictPassword, S2));
        $query->bindValue($i++, php_aes_encrypt($StudentObj->Email, S1));
        $query->bindParam($i++, $StudentObj->GradeLastUpdated);
        $query->bindParam($i++, $StudentObj->LegacyID);
        $query->execute();

        return $db->lastInsertId();

    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

//Add new User to DB, IDK why this was removed
function AddNewUser($UserObj, PDO $db) {
    try
    {
        //$db = connect();
        $query = $db->prepare("INSERT INTO users (FirstName, LastName, Email, DateJoined, LastLogin, UserLevel_idUserLevel, LoginNumber) VALUES (?,?,?,NOW(),NOW(),1,0)"); //Login number set to 0 so InitLogin can set to 1
        //if(ValidateSchoolID($StudentObj->Schools_idSchools, $db) == 0)
        //    return -2;
        $query->bindValue(1, php_aes_encrypt($UserObj->FirstName, S4));
        $query->bindValue(2, php_aes_encrypt($UserObj->LastName, S4));
        $query->bindValue(3, php_aes_encrypt($UserObj->Email, S4));
        $query->execute();

        return $db->lastInsertId();

        //$db = null;
    } catch(PDOException $e)
    {
        print "Error on ".__FUNCTION__."!: " . $e->getMessage() . "<br/>";
        //$db = null;
        //die();
        return -1;
    }
}

function AddStudent_User($StudentID, $UserID, PDO $db){
    try{
        $query = $db->prepare("INSERT INTO students_users (Student_idStudent, Users_idUsers) VALUES (:idStudent, :idUsers)");
        $query->bindParam(':idStudent', $StudentID);
        $query->bindParam(':idUsers', $UserID);
        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function AddAssignment($AssignmentObj, PDO $db) {
    try {
        $query = $db->prepare("INSERT INTO assignments (AssignmentName, AssignmentDate, Category_idCategory, Classes_idClasses, Population, NewAlert) VALUES (?,?,?,?,1,1)"); //If adding new assignment, mark "new alert". Dunno when to use/turn off though :(
        $query->bindValue(1, php_aes_encrypt($AssignmentObj->AssignmentName, S3));
        $query->bindParam(2, $AssignmentObj->AssignmentDate);
        $query->bindParam(3, $AssignmentObj->idCategory);
        $query->bindParam(4, $AssignmentObj->idClasses);

        $query->execute();

        return $db->lastInsertId();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function AddAssignmentScore(Assignment $AssignmentObj, $idStudent, PDO $db) {
    try {
        //$db = connect();
        $query = $db->prepare("INSERT INTO assignmentscore (Assignments_idAssignments, Student_idStudent, AssignmentEarnedPoints, AssignmentPossiblePoints, AssignmentBookmarked, AssignmentNewAlert) VALUES (?,?,?,?,?,?)");
        $query->bindParam(1, $AssignmentObj->idAssignment);
        $query->bindParam(2, $idStudent);
        $query->bindParam(3, $AssignmentObj->AssignmentEarnedPoints);
        $query->bindParam(4, $AssignmentObj->AssignmentPossiblePoints);
        $query->bindParam(5, $AssignmentObj->AssignmentBookmarked);
        $query->bindParam(6, $AssignmentObj->AssignmentNewAlert);
        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        throw $e;
        return -1;
    }
}

function AddModifiedAssignmentScore(Assignment $AssignmentObj, PDO $db) {
    try {
        $i=1;
        $query = $db->prepare("INSERT INTO modifiedassignmentscore (AssignmentEarnedPoints, AssignmentPossiblePoints, AssignmentDisabled, AssignmentScore_idAssignmentScore, Students_idStudents) VALUES (?,?,?,?,?)");
        $query->bindParam($i++, $AssignmentObj->AssignmentEarnedPoints);
        $query->bindParam($i++, $AssignmentObj->AssignmentPossiblePoints);
        $query->bindParam($i++, $AssignmentObj->AssignmentDisabled);
        $query->bindParam($i++, $AssignmentObj->idAssignmentScore);
        $query->bindParam($i++, $AssignmentObj->idStudents);
        $query->execute();
        //$db = null;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        //$db = null;
        //die();
        return -1;
    }
}

function AddUserAddedAssignment(Assignment $AssignmentObj, PDO $db) {
    try {
        if(isset($AssignmentObj->isModifiedCategory) && $AssignmentObj->isModifiedCategory) //Construct query depending on the AssignmentObject
            $query = $db->prepare("INSERT INTO useraddedassignments (Students_idStudents, UserAddedAssignmentName, UserAddedAssignmentEarnedPoints, UserAddedAssignmentPossiblePoints, ModifiedCategory_idModifiedCategory, Classes_idClasses) VALUES (?,?,?,?,?,?)");
        else
            $query = $db->prepare("INSERT INTO useraddedassignments (Students_idStudents, UserAddedAssignmentName, UserAddedAssignmentEarnedPoints, UserAddedAssignmentPossiblePoints, Category_idCategory, Classes_idClasses) VALUES (?,?,?,?,?,?)");
        $query->bindParam(1, $AssignmentObj->idStudents);
        $query->bindParam(2, $AssignmentObj->AssignmentName);
        $query->bindParam(3, $AssignmentObj->ModifiedAssignmentEarnedPoints);
        $query->bindParam(4, $AssignmentObj->ModifiedAssignmentPossiblePoints);
        $query->bindParam(5, $AssignmentObj->idCategory);
        $query->bindParam(6, $AssignmentObj->idClasses);
        if($query->execute())
            return $db->lastInsertId();
        else
            return false;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function AddClassAveragePoint($AverageObj, PDO $db) {
    try {
        //$db = connect();
        $query = $db->prepare("INSERT INTO classaverage (NumericGrade, RecordedTime, Classes_idClasses) VALUES (?,?,?)");
        $query->bindParam(1, $AverageObj->NumericGrade);
        $query->bindParam(2, $AverageObj->RecordedTime);
        $query->bindParam(3, $AverageObj->idOwner);
        $query->execute();
        //$db = null;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        //$db = null;
        //die();
        return -1;
    }
}

function AddLoginService(LoginService $LoginServiceObj, PDO $db) {
    try {

        $query = $db->prepare("SELECT * FROM loginservicetype WHERE idLoginServiceType = :idLoginServiceType");
        $query->bindParam(':idLoginServiceType', $LoginServiceObj->idLoginServiceType);
        $query->execute();
        $row = $query->fetch();

        if (!isset($row['idLoginServiceType'])) {
            print "Error on " . __FUNCTION__ . "!: Service Not Found, Check DB for current config!<br/>";
            die();
        }

        $query = $db->prepare("INSERT INTO loginservice (LoginServiceType_idLoginServiceType, LoginServiceID, Users_idUsers) VALUES(:ServiceTypeID, :ID, :UserID) ");
        $query->bindParam(':ServiceTypeID', $row['idLoginServiceType']);
        $query->bindParam(':ID', $LoginServiceObj->LoginServiceID);
        $query->bindParam(':UserID', $LoginServiceObj->idUser);
        $query->execute();
        //$row = $query->fetch();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function AddClass($idCourse, $idTeacher, $TermNumber, PDO $db) {
    try {
        $query = $db->prepare("INSERT INTO classes (Courses_idCourses, Teachers_idTeachers, TermNumbers_idTermNumbers,Population) VALUES (?,?,?, 0)");
        $query->bindParam(1, $idCourse);
        $query->bindParam(2, $idTeacher);
        $query->bindParam(3, $TermNumber);
        $query->execute();
        return $db->lastInsertId();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        //var_dump($e->getTrace());
        return -1;
    }
}

function AddCourse($CourseObj, PDO $db) {
    try {
        $query = $db->prepare("INSERT INTO courses (Schools_idSchools, CourseName, CourseDescription, Population) VALUES (?,?,?,0)");
        $query->bindParam(1, $CourseObj->idSchools);
        $query->bindValue(2, php_aes_encrypt($CourseObj->CourseName, S3));
        $query->bindParam(3, $CourseObj->CourseDescription);
        //$query->bindParam(4, $CourseObj->Population); //TODO: Actually have population, but for now it's not necessary
        $query->execute();

        return $db->lastInsertId();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function AddTeacher($TeacherObj, PDO $db) {
    try {
        $query = $db->prepare("INSERT INTO teachers (TeacherName, TeacherEmail, Schools_idSchools) VALUES (?,?,?)");
        $query->bindValue(1, php_aes_encrypt($TeacherObj->TeacherName, S3));
        $query->bindValue(2, php_aes_encrypt($TeacherObj->TeacherEmail, S3));
        $query->bindParam(3, $TeacherObj->idSchools);
        $query->execute();
        return $db->lastInsertId();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function AddStudent_Classes($Period, $NumericGrade, $LetterGrade, $idClasses, $idStudent, PDO $db) {
    try {
        $query = $db->prepare("INSERT INTO student_classes (Student_Classes_Period, Student_Classes_NumericGrade, Student_Classes_LetterGrade, Student_Classes_GradeDiff, Classes_idClasses, Student_idStudent) VALUES (?,?,?,0,?,?)");
        $i=1;
        $query->bindParam($i++, $Period);
        $query->bindParam($i++, $NumericGrade);
        $query->bindParam($i++, $LetterGrade);
        //$query->bindParam($i++, 0);
        $query->bindParam($i++, $idClasses);
        $query->bindParam($i++, $idStudent);
        return $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

//TODO: Add Letter Grade!!
function AddStudent_Classes_Grade($NumericGrade, $idClasses, $RecordedTime, $idStudent, $TotalPoints, PDO $db){
    try {
        $query = $db->prepare("INSERT INTO student_classes_grade (NumericGrade, RecordedTime, Student_Classes_Classes_idClasses,Student_Classes_Student_idStudent, TotalClassPoints) VALUES (?,?,?,?,?)");
        $i=1;
        $query->bindParam($i++, $NumericGrade);
        $query->bindParam($i++, $RecordedTime);
        $query->bindParam($i++, $idClasses);
        $query->bindParam($i++, $idStudent);
        $query->bindParam($i++, $TotalPoints);
        return $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function AddCategory($CategoryObj, PDO $db) {
    try {
        $query = $db->prepare("INSERT INTO category (CategoryName, CategoryWeight, Classes_idClasses, CategoryNameAbrv) VALUES (?,?,?,?)");
        $query->bindValue(1, php_aes_encrypt($CategoryObj->CategoryName, S3));
        $query->bindParam(2, $CategoryObj->CategoryWeight);
        $query->bindParam(3, $CategoryObj->idClasses);
        $query->bindValue(4, php_aes_encrypt($CategoryObj->CategoryNameAbrv, S3));

        $query->execute();
        return $db->lastInsertId();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function AddLoginIP($IP, $idUser, PDO $db){
    try {
        $query = $db->prepare("INSERT INTO loginip (Users_idUsers, IP, Time) VALUES (?,?,NOW())");
        $query->bindParam(1, $idUser);
        $query->bindValue(2, php_aes_encrypt($IP, S2));
        $query->execute();
        return $db->lastInsertId();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function AddXML($idStudent, $XML, PDO $db){
    try {
        $query = $db->prepare("INSERT INTO xmlarchives (Students_idStudents, XML, RecordedTime) VALUES (?,COMPRESS(?),NOW())");
        $query->bindParam(1, $idStudent);
        $query->bindValue(2, php_aes_encrypt($XML, S1));
        $query->execute();
        return $db->lastInsertId();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function AddClassAverage($idClass,PDO $db, $xmlTime=null){
    try
        {
        $stats = GetLatestStudent_Classes_Grades($idClass, $xmlTime, $db);
        if($stats===false){
            return false;
        }
        $values = array();
        foreach($stats as $stat){ //Create into a statistics friendly array
            $values[] = array($stat['TotalClassPoints'], $stat['NumericGrade']);
        }

        $ClassStats = WeightedMeanAndSD($values);

        $i=1;
        $NumericGrade = $ClassStats[0];
        $sd = $ClassStats[1];
        $n = $ClassStats[2];

        $xmlTime = (isset($xmlTime) ? $xmlTime : date('Y-m-d H:i:s'));

        $query = $db->prepare("INSERT INTO classaverage (NumericGrade, StandardDeviation, Population, RecordedTime, Classes_idClasses) VALUES (?, ?, ?, ?, ?)");
        $query->bindParam($i++, $NumericGrade);
        $query->bindParam($i++, $sd);
        $query->bindParam($i++, $n);
        $query->bindParam($i++, $xmlTime);
        $query->bindParam($i++, $idClass);

        return $query->execute();

    } catch(PDOException $e)
    {
        print "Error on ".__FUNCTION__."!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function AddNotification(Notification $NotificationObject, PDO $db){
    try {
        $i=1;
        $query = $db->prepare("INSERT INTO notifications (Students_idStudents, NotificationURL, Notification, NotificationTime, NotificationType_idNotificationType) VALUES (?,?,?,?,?)");
        $query->bindParam($i++, $NotificationObject->idStudents);
        $query->bindParam($i++, $NotificationObject->NotificationURL);
        $query->bindValue($i++, php_aes_encrypt($NotificationObject->Notification, S1));
        $query->bindParam($i++, $NotificationObject->Time);
        $query->bindParam($i++, $NotificationObject->idNotificationType);
        $query->execute();
        return $db->lastInsertId();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

?>
