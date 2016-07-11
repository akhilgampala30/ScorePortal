<?php

//Get Functions
function GetSchoolIDFromStudentID($idStudents, PDO $db) {
    try {
        $query = $db->prepare("SELECT Schools_idSchools FROM students WHERE idStudents = :idStudents");
        $query->bindParam(':idStudents', $idStudents);
        $query->execute();
        return $query->fetchColumn();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetCategoryID($CategoryName, $idClasses, PDO $db) {
    try {
        $query = $db->prepare("SELECT idCategory FROM category WHERE CategoryName = :categoryName AND Classes_idClasses = :idClasses");
        $query->bindValue(':categoryName', php_aes_encrypt($CategoryName, S3));
        $query->bindParam(':idClasses', $idClasses);
        $query->execute();
        return $query->fetchColumn();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetClassID($idCourses, $idTeachers, $idTermNumber, PDO $db) {
    try {
        $query = $db->prepare("SELECT idClasses FROM classes WHERE Courses_idCourses = :idCourses AND Teachers_idTeachers = :idTeachers AND TermNumbers_idTermNumbers = :termNumber");
        $query->bindParam(':idCourses', $idCourses);
        $query->bindParam(':idTeachers', $idTeachers);
        $query->bindParam(':termNumber', $idTermNumber);
        $query->execute();
        return $query->fetchColumn();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

//TODO: Don't we need to use AES Encrypt CourseName for matching?
function GetCourseID($CourseName, $idSchools, PDO $db) {
    try {
        $query = $db->prepare("SELECT idCourses FROM courses WHERE CourseName = :courseName AND Schools_idSchools = :idSchools");
        $query->bindParam(':courseName', $CourseName);
        $query->bindParam(':idSchools', $idSchools);
        $query->execute();
        return $query->fetchColumn();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

/*
function GetXMLArchiveFromStudentID($idStudents, PDO $db) {
    try { //TODO Decompress and Decrypt
        $query = $db->prepare("SELECT XML FROM xmlarchives WHERE Students_idStudents = :idStudents");
        $query->bindParam(':idStudents', $idStudents);
        $query->execute();
        return $query->fetchColumn();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}*/

function GetStudent($idStudents, PDO $db) { //308ms Execution
    try {
        //TODO: Cache Category Objects/Strict Assignments (For modified assignments)
        $Student = new Student();

        $query = $db->prepare("SELECT idStudents,
        FirstName,
        LastName,
        Email,
        GPA,
        StudentDistrictID,
        StudentDistrictPassword,
        MobileNumber,
        MobileCarrier_idMobileCarrier,
        Schools_idSchools,
        GradesLastUpdated,
        Points,
        GradeLevel,
        LegacyID
        FROM students WHERE idStudents = :idStudents");
        $query->bindParam(':idStudents', $idStudents);

        $query->execute();
        $row = $query->fetch();

        if($row === false){
            return false;
        }


        $Student->idStudents = $row['idStudents'];

        //IDs
        $Student->idSchools = GetSchoolIDFromStudentID($idStudents, $db);
        $Student->idMobileCarrier = $row['MobileCarrier_idMobileCarrier'];
        $Student->Schools = GetSchool($Student->idSchools, $db); //Set School Obj

        $TermNumbers = GetAllTermNumbersOfSchool($Student->idSchools, $db);
        $curTermNumber = GetCurrentMySQLTermNumber($TermNumbers);

        $Student->FirstName = php_aes_decrypt($row['FirstName'], S1);
        $Student->LastName = php_aes_decrypt($row['LastName'], S1);
        $Student->Email = php_aes_decrypt($row['Email'], S1);
        $Student->GPA = $row['GPA'];
        $Student->SchoolDistrictID = php_aes_decrypt($row['StudentDistrictID'], S2);
        $Student->SchoolDistrictPassword = php_aes_decrypt($row['StudentDistrictPassword'], S2);
        $Student->MobileNumber = $row['MobileNumber'];
        $Student->GradeLastUpdated = $row['GradesLastUpdated'];
        $Student->Points = $row['Points'];
        $Student->GradeLevel = $row['GradeLevel'];
        //Objs
        $Student->Achievements = GetAllEarnedAchievements($idStudents, $db);
        $Student->MobileCarrier = GetMobileCarrier($row['MobileCarrier_idMobileCarrier'], $db);
        $Student->Notifications = GetAllNotifications($idStudents, $db);
        $Student->Classes = GetAllClasses($idStudents, $db, $curTermNumber->idTermNumbers);
        usort($Student->Classes, array('_Class','ClassSort'));
        return $Student;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

/**
 * @param $TermNumbers
 * @return TermNumber, bool
 */
function GetCurrentMySQLTermNumber($TermNumbers){
    date_default_timezone_set('America/Los_Angeles'); //Woo West Coast
    $date = date('Y-m-d', time() - 5184000); //Offset for 2 months
    foreach ($TermNumbers as $tempTermNumber) {
        if ($tempTermNumber->StartDate < $date && $tempTermNumber->EndDate > $date) {
            return $tempTermNumber;
        }
    }
    return false;
}


function GetGradeLastUpdated($idStudents, PDO $db) {
    try {
        //Attributes
        $query = $db->prepare("SELECT * FROM students WHERE idStudents = :idStudents");
        $query->bindParam(':idStudents', $idStudents);
        $query->execute();
        $row = $query->fetch();
        return $row['GradeLastUpdated'];
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetStudent_Classes_Grade($idStudent, $idClass, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM student_classes_grade WHERE Student_Classes_Student_idStudent = :idStudent AND Student_Classes_Classes_idClasses = :idClasses");
        $query->bindParam('idStudent', $idStudent);
        $query->bindParam('idClasses', $idClass);
        $query->execute();
        return $query->fetchAll();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetStudent_Classes_GradeNumericGrade($idStudentClassesGrade, PDO $db) {
    try {
        $query = $db->prepare("SELECT NumericGrade FROM student_classes_grade WHERE idStudent_Classes_Grade = :idStudent_Classes_Grade");
        $query->bindParam(':idStudent_Classes_Grade', $idStudentClassesGrade);
        $query->execute();
        return $query->fetchColumn();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetAllStudent_Classes_GradeNumericGradesForStudent($idStudent, PDO $db) {
    try {
        $query = $db->prepare("SELECT idStudent_Classes_Grade FROM student_classes_grade WHERE Student_Classes_Student_idStudent = :idStudent");
        $query->bindParam(':idStudent', $idStudent);
        $query->execute();

        $NumericGrades = array();

        while ($row = $query->fetch()) {
            array_push($NumericGrades, GetStudent_Classes_GradeNumericGrade($row['idStudent_Classes_Grade'], $db));
        }
        return $NumericGrades;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetAllStudent_Classes_GradeNumericGradesForClass($idClass, PDO $db) {
    try {
        $query = $db->prepare("SELECT idStudent_Classes_Grade FROM student_classes_grade WHERE Student_Classes_Classes_idClasses = :idClass");
        $query->bindParam(':idClass', $idClass);
        $query->execute();

        $NumericGrades = array();

        while ($row = $query->fetch()) {
            array_push($NumericGrades, GetStudent_Classes_GradeNumericGrade($row['idStudent_Classes_Grade'], $db));
        }
        return $NumericGrades;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetUserLevel($idUserLevel, PDO $db) {
    try {
        $query = $db->prepare("SELECT UserLevelDescription FROM userlevel WHERE idUserLevel = :idUserLevel");
        $query->bindParam(':idUserLevel', $idUserLevel);
        $query->execute();
        return $query->fetchColumn();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetUserLoginNumber($idUser, PDO $db) {
    try {
        $query = $db->prepare("SELECT LoginNumber FROM users WHERE idUsers = :idUser");
        $query->bindParam(':idUser', $idUser);
        $query->execute();
        return $query->fetchColumn();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetLoginIP($idUser, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM loginip WHERE Users_idUsers = :idUser");
        $query->bindParam(':idUser', $idUser);
        $query->execute();
        $row = $query->fetch();

        $LoginIP = new LoginIP();

        $LoginIP->idUser = $idUser;
        $LoginIP->IP = php_aes_decrypt($row['IP'], S2);
        $LoginIP->Time = $row['Time'];

        return $LoginIP;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

//Accept either ServiceType in ID form or String Name Form
function GetUserIDFromLoginID($ID, PDO $db, $ServiceType = -1, $ServiceName = '-1') {
    try {
        if ($ServiceName != '-1') {
            $query = $db->prepare("SELECT * FROM loginservicetype WHERE LoginServiceName = :ServiceName");
            $query->bindParam(':ServiceName', $ServiceName);
            $query->execute();
            $row = $query->fetch();

            if (!isset($row['idLoginServiceType'])) {
                print "Error on " . __FUNCTION__ . "!: Service Not Found!<br/>";
                die();
            }
        } else {
            if ($ServiceType == -1) {
                return null; //Neither were set, so just fail instead.
            }
            $row['idLoginServiceType'] = $ServiceType;
        }

        $query = $db->prepare("SELECT * FROM loginservice WHERE LoginServiceID = :ID AND LoginServiceType_idLoginServiceType = :ServiceTypeID");
        $query->bindParam(':ID', $ID);
        $query->bindParam(':ServiceTypeID', $row['idLoginServiceType']);
        $query->execute();
        $row = $query->fetch();
        return $row['Users_idUsers'];
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetLoginService($idUser, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM loginservice WHERE Users_idUsers = :idUser");
        $query->bindParam(':idUser', $idUser);
        $query->execute();
        $row = $query->fetch();

        $LoginService = new LoginService();

        $LoginService->idLoginService = $row['idLoginService'];
        $LoginService->idLoginServiceType = $row['LoginServiceType_idLoginServiceType'];
        $LoginService->LoginServiceID = $row['LoginServiceID'];
        $LoginService->idUser = $row['Users_idUsers'];

        $LoginService->LoginServiceType = GetLoginServiceType($LoginService->idLoginServiceType, $db);

        return $LoginService;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetLoginServiceType($idLoginServiceType, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM loginservicetype WHERE idLoginServiceType = :idLoginServiceType");
        $query->bindParam(':idLoginServiceType', $idLoginServiceType);
        $query->execute();
        $row = $query->fetch();

        $LoginServiceType = new LoginServiceType();

        $LoginServiceType->idLoginServiceType = $idLoginServiceType;
        $LoginServiceType->LoginServiceName = $row['LoginServiceName'];
        $LoginServiceType->LoginServiceDomain = $row['LoginServiceDomain'];
        $LoginServiceType->LoginServiceTechnology = $row['LoginTechnology'];

        return $LoginServiceType;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetLoginServiceTypeByTechnology($technology, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM loginservicetype WHERE LoginTechnology = :idLoginServiceType");
        $query->bindParam(':idLoginServiceType', $technology);
        $query->execute();
        $row = $query->fetch();

        $LoginServiceType = new LoginServiceType();

        $LoginServiceType->idLoginServiceType = $row['idLoginServiceType'];
        $LoginServiceType->LoginServiceName = $row['LoginServiceName'];
        $LoginServiceType->LoginServiceDomain = $row['LoginServiceDomain'];
        $LoginServiceType->LoginServiceTechnology = $technology;

        return $LoginServiceType;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetUser($idUser, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM users WHERE idUsers = :idUser");
        $query->bindParam(':idUser', $idUser);
        $query->execute();
        $row = $query->fetch();

        $User = new User();
        $User->idUsers = $idUser;
        $User->idUserLevel = $row['UserLevel_idUserLevel'];

        $User->FirstName = php_aes_decrypt($row['FirstName'], S4);
        $User->LastName = php_aes_decrypt($row['LastName'], S4);
        $User->Email = php_aes_decrypt($row['Email'], S4);
        $User->DateJoined = $row['DateJoined'];
        $User->LastLogin = $row['LastLogin'];
        $User->UserLevel = GetUserLevel($row['UserLevel_idUserLevel'], $db);

        $User->LoginIP = GetLoginIP($idUser, $db);
        $User->LoginService = GetLoginService($idUser, $db);

        return $User;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetUserIDFromStudentID($idStudents, PDO $db) {
    try {
        $query = $db->prepare("SELECT Users_idUsers FROM students_users WHERE Student_idStudent = :idStudents");
        $query->bindParam(':idStudents', $idStudents);
        $query->execute();
        return $query->fetchColumn();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetStudentIDFromUserID($idUser, PDO $db) {
    try {
        $query = $db->prepare("SELECT Student_idStudent FROM students_users WHERE Users_idUsers = :idUser");
        $query->bindParam(':idUser', $idUser);
        $query->execute();
        return $query->fetchColumn();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetAllEarnedAchievements($idStudents, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM student_achievements WHERE Students_idStudents = :idStudents");
        $query->bindParam(':idStudents', $idStudents);
        $query->execute();

        $AllAchievements = array();

        while ($row = $query->fetch()) {
            $Achievements_idAchievement = $row['Achievements_idAchievements'];
            $Achievement = GetEarnedAchievement($Achievements_idAchievement, $db);
            array_push($AllAchievements, $Achievement);
        }

        return $AllAchievements;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetAllNotifications($idStudent, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM notifications WHERE Students_idStudents = :idStudents ORDER BY NotificationTime DESC LIMIT 0, 150"); //TODO: Set limit as argument
        $query->bindParam(':idStudents', $idStudent);
        $query->execute();

        $AllNotifications = array();

        while ($row = $query->fetch()) {
            $Notification = new Notification();
            $Notification->idNotificationType = $row['NotificationType_idNotificationType'];
            $Notification->idNotifications = $row['idNotifications'];
            $Notification->idStudents = $idStudent;
            $Notification->NotificationURL = $row['NotificationURL'];
            $Notification->Time = $row['NotificationTime'];
            $Notification->Notification = php_aes_decrypt($row['Notification'],S1);
            $Notification->NotificationType = GetNotificationType($row['NotificationType_idNotificationType'], $db); //TODO: Don't fetch this every time

            array_push($AllNotifications, $Notification);
        }

        return $AllNotifications;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetNotificationType($idNotificationType, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM notificationtype WHERE idNotificationTypes = :idNotificationType");
        $query->bindParam(':idNotificationType', $idNotificationType);
        $query->execute();
        $row = $query->fetch();

        $NotificationType = new NotificationType();
        $NotificationType->idnotificationType = $row['idNotificationTypes'];
        $NotificationType->ImagePath = $row['ImagePath'];
        return $NotificationType;

    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetNumericGrade($idClass, $idStudent, PDO $db) {
    try {
        $query = $db->prepare("SELECT Student_Classes_NumericGrade FROM student_classes WHERE Student_idStudent = :idStudent AND Classes_idClasses = :idClass");
        $query->bindParam(':idClass', $idClass);
        $query->bindParam(':idStudent', $idStudent);
        $query->execute();

        return $query->fetchColumn();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetAllClasses($idStudent, PDO $db, $TermNumber = false) {
    try {
        $query = $db->prepare("SELECT Classes_idClasses FROM student_classes JOIN classes ON student_classes.Classes_idClasses = classes.idClasses WHERE Student_idStudent = :idStudent".($TermNumber!==false?' AND TermNumbers_idTermNumbers = :idTerm':''));
        $query->bindParam(':idStudent', $idStudent);
        if($TermNumber!==false){
            $query->bindParam(':idTerm', $TermNumber);
        }
        $query->execute();
        //$row = $query->fetch();

       $Classes = array();

        while ($row = $query->fetch()) {
            $Class = GetClass($idStudent, $row['Classes_idClasses'], $db);
            array_push($Classes, $Class);
        }
        return $Classes;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}


/**
 * Gets Class Object with only public information
 * @param $idClasses
 * @param PDO $db
 * @return _Class
 */
function GetStrictClass($idClasses, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM classes WHERE idClasses = :idClasses");
        $query->bindParam(':idClasses', $idClasses);
        $query->execute();
        $row = $query->fetch();
        $ClassObj = new _Class();
        $ClassObj->idClasses = $row['idClasses'];
        $ClassObj->idCourses = $row['Courses_idCourses'];
        $ClassObj->idTeachers = $row['Teachers_idTeachers'];
        $ClassObj->TermNumber = $row['TermNumbers_idTermNumbers'];
        $ClassObj->RoomNumber = $row['RoomNumber'];
        $ClassObj->Population = $row['Population'];
        $ClassObj->isWeighted = $row['isWeighted'];
        $ClassObj->ClassAverage = GetAllClassAveragesForClass($idClasses,$db);
        $ClassObj->Teacher = GetTeacher($row['Teachers_idTeachers'], $db);
        $ClassObj->Course = GetCourse($row['Courses_idCourses'], $db);
        $ClassObj->Categories = GetAllCategories($row['idClasses'], $db);
        return $ClassObj;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

/**
 * Gets class information with personal information added
 * @param $idStudent
 * @param $idClass
 * @param PDO $db
 * @return _Class|bool
 */
function GetClass($idStudent, $idClass, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM student_classes WHERE Student_idStudent = :idStudents AND Classes_idCLasses= :idClasses");
        $query->bindParam(':idStudents', $idStudent);
        $query->bindParam(':idClasses', $idClass);
        $query->execute();
        $row = $query->fetch();

        $Class = GetStrictClass($idClass, $db);
        $Class->idStudent = $idStudent;
        $Class->Period = $row['Student_Classes_Period'];
        $Class->NumericGrade = round($row['Student_Classes_NumericGrade'], 5); //fix for now, might consider DECIMAL data type
        $Class->LetterGrade = $row['Student_Classes_LetterGrade'];
        $Class->GradeDiff = round($row['Student_Classes_GradeDiff'],4);
        $Class->Assignments = GetAllAssignmentsFromClassForStudent($idClass, $idStudent, $db);
        $Class->Assignments = array_merge($Class->Assignments, GetAllUserAddedAssignmentsFromClassForStudent($idClass, $idStudent, $db)); //Add user added assignments too
        $Class->Categories = array_merge($Class->Categories, GetAllModifiedCategories($idClass, $idStudent, $db)); //Add Modified Categories

        $Class->GradeHistory = GetStudent_Classes_Grade($idStudent, $idClass, $db);
        //$Class->Categories = GetAllCategories($idClass, $db); //Already called in strict class

        /* @var $CurAssignment Assignment */
        /* @var $CurCategory Category */
        foreach($Class->Assignments as &$CurAssignment){ //Set Categories by reference
            foreach($Class->Categories as &$CurCategory){
                if($CurCategory->idCategory
                    == $CurAssignment->idCategory){
                    $CurAssignment->Category = $CurCategory;
                }
            }
            $CurAssignment->Classes = $Class;
        }

        return $Class;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}



/*
function GetGlobalCourseIDFromName($GlobalCourseName, PDO $db) { //TODO: Use Join Operation Here
    try {
        $query = $db->prepare("SELECT idGlobalCourses FROM globalcourses_courseattributes WHERE GlobalCourseName = :GlobalCourseName");
        $query->bindParam(':GlobalCourseName', $GlobalCourseName);
        $query->execute();
        return $query->fetchColumn();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}*/

function GetCourseIDFromName($CourseName, PDO $db) {
    try {
        $query = $db->prepare("SELECT idCourses FROM courses WHERE CourseName = :CourseName");
        $query->bindValue(':CourseName', php_aes_encrypt($CourseName, S3));
        $query->execute();
        return $query->fetchColumn();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetRapidUpdateInterval($idStudent, PDO $db) {
    try {
        $query = $db->prepare("SELECT UpdateInterval FROM rapidupdatestudents WHERE Students_idStudents = :idStudent");
        $query->bindParam(':idStudent', $idStudent);
        $query->execute();
        return $query->fetchColumn();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetCourseAttributesIDFromCourseID($idCourse, PDO $db) {
    try {
        $query = $db->prepare("SELECT CourseAttributes_idCourseAttributes FROM courses_courseattributes WHERE Courses_idCourses = :idCourse");
        $query->bindParam(':idCourse', $idCourse);
        $query->execute();
        return $query->fetchColumn();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetCourseAttributes($idCourseAttribute, PDO $db) {
    try {
        $query = $db->prepare("SELECT Attribute FROM courseattributes WHERE idCourseAttributes = :idCourseAttribute");
        $query->bindParam(':idCourseAttribute', $idCourseAttribute);
        $query->execute();
        return $query->fetchColumn();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetAllCourses($idSchool, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM courses WHERE Schools_idSchools = :idSchool");
        $query->bindParam(':idSchool', $idSchool);
        $query->execute();
        //$row = $query->fetch();

        $AllCourses = array();

        while ($row = $query->fetch()) {
            $Course = new Course();
            $Course->idCourses = $row['idCourses'];
            $Course->idGlobalCourse = $row['GlobalCourses_idGlobalCourses'];
            $Course->idSchools = $row['Schools_idSchools'];
            $Course->CourseName = php_aes_decrypt($row['CourseName'], S3);
            $Course->CourseDescription = $row['CourseDescription'];
            $Course->Population = $row['Population'];

            $Course->GlobalCourse = GetGlobalCourse($row['GlobalCourses_idGlobalCourses'], $db);

            array_push($AllCourses, $Course);
        }

        return $AllCourses;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetCourse($idCourse, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM courses WHERE idCourses = :idCourse");
        $query->bindParam(':idCourse', $idCourse);
        $query->execute();
        $row = $query->fetch();

        $Course = new Course();
        $Course->idCourses = $idCourse;
        $Course->idGlobalCourse = $row['GlobalCourses_idGlobalCourses'];
        $Course->idSchools = $row['Schools_idSchools'];
        $Course->CourseName = php_aes_decrypt($row['CourseName'], S3);
        $Course->CourseDescription = $row['CourseDescription'];
        $Course->Population = $row['Population'];

        //$Course->School = GetSchool($row['Schools_idSchools'], $db);
        $Course->GlobalCourse = GetGlobalCourse($row['GlobalCourses_idGlobalCourses'], $db);

        return $Course;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetCourseAttributesFromArrayID($idCourseAttributes, PDO $db) {
    try {
        $CourseAttributes = array();
        foreach ($idCourseAttributes as $idCourseAttribute) {
            $CourseAttribute = GetCourseAttributes($idCourseAttribute, $db);
            array_push($CourseAttributes, $CourseAttribute);
        }
        return $CourseAttributes;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetAllGlobalCourses(PDO $db) { //TODO: MUST HAVE ADDITIONAL SELECTOR
    try {
        $query = $db->prepare("SELECT * FROM globalcourses");
        $query->execute();
        //$row = $query->fetch();

        $GlobalCourses = array();
        while ($row = $query->fetch()) {
            $GlobalCourse = new GlobalCourse();
            $GlobalCourse->idGlobalCourses = $row['idGlobalCourses'];

            $GlobalCourse->GlobalCourseName = $row['GlobalCourseName'];
            $GlobalCourse->GlobalCourseDescription = $row['GlobalCourseDescription'];
            $GlobalCourse->Population = $row['Population'];

            $GlobalCourse->idCourseAttributes = GetGlobalCourseAttributesIDFromGlobalCourseID($row['idGlobalCourses'], $db); //TODO: Do this in a single query
            $GlobalCourse->CourseAttributes = GetCourseAttributesFromArrayID($GlobalCourse->idCourseAttributes, $db);

            array_push($GlobalCourses, $GlobalCourse);
        }
        return $GlobalCourses;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetGlobalCourse($idGlobalCourse, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM globalcourses_courseattributes WHERE GlobalCourses_idGlobalCourses = :idGlobalCourse");
        $query->bindParam(':idGlobalCourse', $idGlobalCourse);
        $query->execute();
        $row = $query->fetch();

        $GlobalCourse = new GlobalCourse();
        $GlobalCourse->idGlobalCourses = $idGlobalCourse;

        $GlobalCourse->GlobalCourseName = $row['GlobalCourseName'];
        $GlobalCourse->GlobalCourseDescription = $row['GlobalCourseDescription'];
        $GlobalCourse->Population = $row['Population'];

        $GlobalCourse->idCourseAttributes = GetGlobalCourseAttributesIDFromGlobalCourseID($idGlobalCourse, $db);
        $GlobalCourse->CourseAttributes = GetCourseAttributesFromArrayID($GlobalCourse->idCourseAttributes, $db);

        return $GlobalCourse;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetGlobalCourseAttributesIDFromGlobalCourseID($idGlobalCourse, PDO $db) {
    try {
        $query = $db->prepare("SELECT CourseAttributes_idCourseAttributes FROM globalcourses_courseattributes WHERE GlobalCourses_idGlobalCourses = :idGlobalCourse");
        $query->bindParam(':idGlobalCourse', $idGlobalCourse);
        $query->execute();
        //$row = $query->fetch();
        $idCourseAttributes = array();

        while ($row = $query->fetch()) {
            $idCourseAttribute = $row['CourseAttributes_idCourseAttributes'];
            array_push($idCourseAttributes, $idCourseAttribute);
        }

        return $idCourseAttributes;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetTeacher($idTeacher, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM teachers WHERE idTeachers = :idTeacher");
        $query->bindParam(':idTeacher', $idTeacher);
        $query->execute();
        $row = $query->fetch();

        if($row === false){ //If not found, just return false
            return false;
        }

        $Teacher = new Teacher();
        //$Teacher->School = GetSchool($row['Schools_idSchools'], $db); //TODO: Optimization Don't call Get School for every teacher to speed up function and reduce mysql queries
        $Teacher->TeacherEmail = php_aes_decrypt($row['TeacherEmail'], S3);
        $Teacher->TeacherName = php_aes_decrypt($row['TeacherName'],S3);
        $Teacher->idSchools = $row['Schools_idSchools'];
        $Teacher->idTeachers = $idTeacher;

        return $Teacher;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetTeacherByName($TeacherName, $idSchool, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM teachers WHERE TeacherName = :TeacherName AND Schools_idSchools = :idSchool");
        $query->bindValue(':TeacherName', php_aes_encrypt($TeacherName, S3));
        $query->bindParam(':idSchool', $idSchool);
        $query->execute();
        $row = $query->fetch();

        if($row === false){ //If not found, just return false
            return false;
        }

        $Teacher = new Teacher();
        //$Teacher->School = GetSchool($row['Schools_idSchools'], $db); //TODO: Optimization Don't call Get School for every teacher to speed up function and reduce mysql queries
        $Teacher->TeacherEmail = php_aes_decrypt($row['TeacherEmail'],S3);
        $Teacher->TeacherName = php_aes_decrypt($row['TeacherName'], S3);
        $Teacher->idSchools = $row['Schools_idSchools'];
        $Teacher->idTeachers = $row['idTeachers'];

        return $Teacher;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetAllTermNumbersOfSchool($idSchool, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM termnumbers WHERE Schools_idSchools = :idSchool");
        $query->bindParam(':idSchool', $idSchool);
        $query->execute();

        $TermNumbers = array();
        while ($row = $query->fetch()) {
            $tempTermNumber = new TermNumber();
            $tempTermNumber->idTermNumbers = $row['idTermNumbers'];
            $tempTermNumber->StartDate = $row['StartDate'];
            $tempTermNumber->EndDate = $row['EndDate'];
            $tempTermNumber->Name = $row['Name'];
            $tempTermNumber->Year = $row['TermYear'];
            $tempTermNumber->idSchools = $idSchool;
            array_push($TermNumbers, $tempTermNumber);
        }

        return $TermNumbers;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetAllTeachersFromSchool($idSchool, PDO $db) {
    try {
        $query = $db->prepare("SELECT idTeachers FROM teachers WHERE Schools_idSchools = :idSchool");
        $query->bindParam(':idSchool', $idSchool);
        $query->execute();

        $Teachers = array();
        while ($row = $query->fetch()) {
            $Teacher = GetTeacher($row['idTeachers'], $db);
            array_push($Teachers, $Teacher);
        }

        return $Teachers;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetAllTeachersFromCourse($idCourse, PDO $db) {
    try {
        $query = $db->prepare("SELECT Teachers_idTeachers FROM classes WHERE Courses_idCourses = :idCourse");
        $query->bindParam(':idCourse', $idCourse);
        $query->execute();

        $Teachers = array();
        while ($row = $query->fetch()) {
            $Teacher = GetTeacher($row['Teachers_idTeachers'], $db);
            array_push($Teachers, $Teacher);
        }

        return $Teachers;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetSchool($idSchool, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM schools WHERE idSchools = :idSchool");
        $query->bindParam(':idSchool', $idSchool);
        $query->execute();
        $row = $query->fetch();

        if($row === false)
            return false;

        $School = new School();
        $School->Population = $row['Population'];
        $School->PowerschoolRootURL = $row['PowerschoolRootURL'];
        $School->SchoolName = $row['SchoolName'];
        $School->SchoolCity = $row['SchoolCity'];
        $School->SchoolState = $row['SchoolState'];
        $School->SchoolStreetAddress = $row['SchoolStreetAddress'];
        $School->SchoolZipCode = $row['SchoolZipCode'];
        $School->idSchools = $idSchool;

        return $School;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetMobileCarrier($idMobileCarrier, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM mobilecarrier WHERE idMobileCarrier = :idMobileCarrier");
        $query->bindParam(':idMobileCarrier', $idMobileCarrier);
        $query->execute();
        $row = $query->fetch();
        $MobileCarrier = new MobileCarrier();
        $MobileCarrier->MobileCarrierName = $row['MobileCarrierName'];
        $MobileCarrier->MobileCarrierGateway = $row['MobileCarrierGateway'];
        return $MobileCarrier;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetAchievement($idAchievement, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM achievements WHERE idAchievements = :idAchievement");
        $query->bindParam(':idAchievements', $idAchievement);
        $query->execute();
        $row = $query->fetch();
        $Achievement = new Achievement();
        $Achievement->AchievementDescription = $row['AchievementDescription'];
        $Achievement->AchievementName = $row['AchievementName'];
        $Achievement->AchievementReward = $row['AchievementReward'];
        return $Achievement;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetEarnedAchievement($idAchievement, PDO $db) {
    try {
        $query = $db->prepare("SELECT TimeEarned FROM student_achievements WHERE Achievements_idAchievements = :Achievement_idAchievement");
        $query->bindParam(':Achievement_idAchievements', $idAchievement);
        $query->execute();
        $Achievement = GetAchievement($idAchievement, $db);
        $Achievement->TimeEarned = $query->fetchColumn();
        return $Achievement;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetAllEarnedAchievementsForStudent($idStudent, PDO $db) {
    try {
        $query = $db->prepare("SELECT Achievements_idAchievements FROM student_achievements WHERE Students_idStudents = :idStudent");
        $query->bindParam(':idStudent', $idStudent);
        $query->execute();

        $Achievement = GetEarnedAchievement($query->fetchColumn(), $db);

        return $Achievement;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetAllAssignmentsFromClass($idClass, PDO $db) {
    try {
        $query = $db->prepare("SELECT idAssignments FROM assignments WHERE Classes_idClasses = :idClass");
        $query->bindParam(':idClass', $idClass);
        $query->execute();
        //$row = $query->fetch();

        $Assignments = array();

        while ($row = $query->fetch()) {
            $Assignment = GetStrictAssignment($row['idAssignments'], $db);
            array_push($Assignments, $Assignment);
        }
        return $Assignments;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetAllAssignmentsFromClassForStudent($idClass, $idStudent, PDO $db, $ClassObject = null) {
    try {
        $query = $db->prepare("SELECT idAssignments FROM assignments WHERE Classes_idClasses = :idClass ");
        $query->bindParam(':idClass', $idClass);
        $query->execute();

        $Assignments = array();

        while ($row = $query->fetch()) {
            $Assignment = GetAssignment($row['idAssignments'], $idStudent, $db);
            if($Assignment === false){ //If they don't have this assignment score
                continue; //TODO: Possibly insert the strict assignment and ask them to update
            }
            array_push($Assignments, $Assignment);
        }
        return $Assignments;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

//TODO: is this actually used?
function GetAllAssignmentsFromClassForAssignment($idAssignment, PDO $db) {
    try {
        $query = $db->prepare("SELECT Student_idStudent FROM assignmentscore WHERE Assignments_idAssignments = :idAssignment ");
        $query->bindParam(':idAssignment', $idAssignment);
        $query->execute();
        //$row = $query->fetch();

        $Assignments = array();

        while ($row = $query->fetch()) {
            $Assignment = GetAssignment($idAssignment, $row['Student_idStudent'], $db);
            array_push($Assignments, $Assignment);
        }
        return $Assignments;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetStrictAssignment($idAssignment, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM assignments WHERE idAssignments = :idAssignment");
        $query->bindParam(':idAssignment', $idAssignment);
        $query->execute();
        $row = $query->fetch();

        $Assignment = new Assignment();
        $Assignment->idAssignment = $idAssignment;
        $Assignment->AssignmentName = php_aes_decrypt($row['AssignmentName'], S3);
        $Assignment->AssignmentDate = strtotime($row['AssignmentDate']);
        $Assignment->Population = $row['Population'];
        $Assignment->idCategory = $row['Category_idCategory'];
        $Assignment->idClasses = $row['Classes_idClasses'];

        $Assignment->Population = $row['Population'];
        $Assignment->AveragePercent = $row['AssignmentAverage'];
        $Assignment->StandardDeviation = $row['AssignmentStandardDeviation'];
        //$Assignment->Category = GetCategory($Assignment->idCategory, $db); //Set later

        return $Assignment;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetAssignmentIDFromNameAndDate($AssignmentName, $AssignmentDate, $idClass, PDO $db) {
    try {
        $query = $db->prepare("SELECT idAssignments FROM assignments WHERE AssignmentName = :AssignmentName AND AssignmentDate = :AssignmentDate AND Classes_idClasses = :idClass");
        $query->bindValue(':AssignmentName', php_aes_encrypt($AssignmentName, S3));
        $query->bindParam(':AssignmentDate', $AssignmentDate);
        $query->bindParam(':idClass', $idClass);

        $query->execute();
        return $query->fetchColumn();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetUserAddedAssignmentIDFromName($UserAddedAssignmentName, $idClass, PDO $db) {
    try {
        $query = $db->prepare("SELECT idUserAddedAssignments FROM useraddedassignments WHERE UserAddedAssignmentName = :UserAddedAssignmentName AND Classes_idClasses = :idClass");
        $query->bindParam(':UserAddedAssignmentName', $UserAddedAssignmentName);
        $query->bindParam(':idClass', $idClass);

        $query->execute();
        return $query->fetchColumn();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetBookmarkStatus($idAssignment, $idStudent, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM assignmentscore WHERE Assignments_idAssignments = :idAssignment AND Student_idStudent = :idStudent");
        $query->bindParam(':idAssignment', $idAssignment);
        $query->bindParam(':idStudent', $idStudent);
        $query->execute();
        $row = $query->fetch();
        return $row['AssignmentBookmarked'];
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

//Takes in idStudent to ensure that they aren't just guessing idAssignment. Always take idStudent from $_Session to ensure they can't just brute force an idAssignment/idStudent Combo
function GetAssignment($idAssignment, $idStudent, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM assignmentscore WHERE Assignments_idAssignments = :idAssignment AND Student_idStudent = :idStudent");
        $query->bindParam(':idAssignment', $idAssignment);
        $query->bindParam(':idStudent', $idStudent);
        $query->execute();
        $row = $query->fetch();

        if($row === false){
            return false; //If no rows found, just return false
        }

        $Assignment = GetStrictAssignment($idAssignment, $db);
        $Assignment->idStudents = $idStudent;
        //$Assignment->Student = GetStudent($idStudent, $db);
        $Assignment->idAssignmentScore = $row['idAssignmentScore'];
        $Assignment->AssignmentEarnedPoints = round($row['AssignmentEarnedPoints'], 5);
        $Assignment->AssignmentPossiblePoints = round($row['AssignmentPossiblePoints'], 5);
        $Assignment->AssignmentBookmarked = $row['AssignmentBookmarked'];
        $Assignment->AssignmentNewAlert = $row['AssignmentNewAlert'];
        $Assignment->UserAddedAssignment = false;

        $ModifiedAssignmentScore = GetModifiedAssignmentScore($row['idAssignmentScore'], $idStudent, $db);

        if($ModifiedAssignmentScore !== false){
            $Assignment->idModifiedAssignmentScore = $ModifiedAssignmentScore['idModifiedAssignmentScore'];
            $Assignment->ModifiedAssignmentEarnedPoints = $ModifiedAssignmentScore['AssignmentEarnedPoints'];
            $Assignment->ModifiedAssignmentPossiblePoints = $ModifiedAssignmentScore['AssignmentPossiblePoints'];
            $Assignment->AssignmentDisabled = $ModifiedAssignmentScore['AssignmentDisabled'];
        }

        return $Assignment;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetAssignmentScore($idAssignmentScore, $idStudent, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM assignmentscore WHERE idAssignmentScore = :idAssignment AND Student_idStudent = :idStudent");
        $query->bindParam(':idAssignment', $idAssignmentScore);
        $query->bindParam(':idStudent', $idStudent);
        $query->execute();
        $row = $query->fetch();

        if($row === false){
            return false; //If no rows found, just return false
        }
        $Assignment = new Assignment();

        $Assignment->idStudents = $idStudent;
        $Assignment->idAssignmentScore = $row['idAssignmentScore'];
        $Assignment->AssignmentEarnedPoints = round($row['AssignmentEarnedPoints'], 5);
        $Assignment->AssignmentPossiblePoints = round($row['AssignmentPossiblePoints'], 5);
        $Assignment->AssignmentBookmarked = $row['AssignmentBookmarked'];
        $Assignment->AssignmentNewAlert = $row['AssignmentNewAlert'];
        $Assignment->UserAddedAssignment = false;

        return $Assignment;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

//Returns the rows queried
function GetModifiedAssignmentScore($idAssignmentScore, $idStudent, PDO $db){
    try {
        $query = $db->prepare("SELECT * FROM modifiedassignmentscore WHERE AssignmentScore_idAssignmentScore = :idAssignmentScore AND Students_idStudents = :idStudent");
        $query->bindParam(':idAssignmentScore', $idAssignmentScore);
        $query->bindParam(':idStudent', $idStudent);
        $query->execute();
        $row = $query->fetch();
        return $row;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetUserAddedAssignment($idUserAddedAssignment, $idStudent, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM useraddedassignments WHERE idUserAddedAssignments = :idUserAddedAssignment AND Students_idStudents = :idStudent");
        $query->bindParam(':idUserAddedAssignment', $idUserAddedAssignment);
        $query->bindParam(':idStudent', $idStudent);
        $query->execute();
        $row = $query->fetch();

        if($row===false){
            return false;
        }

        $UserAddedAssignment = new Assignment();
        $UserAddedAssignment->idClasses = $row['Classes_idClasses'];
        $UserAddedAssignment->idStudents = $row['Students_idStudents'];
        if(isset($row['ModifiedCategory_idModifiedCategory'])){
            $UserAddedAssignment->idCategory = $row['ModifiedCategory_idModifiedCategory'];
            //$UserAddedAssignment->Category = GetModifiedCategory($UserAddedAssignment->idCategory, $db);
            $UserAddedAssignment->isModifiedCategory = true;
        }
        elseif(isset($row['Category_idCategory'])){
            $UserAddedAssignment->idCategory = $row['Category_idCategory'];
            //$UserAddedAssignment->Category = GetCategory($UserAddedAssignment->idCategory, $db);
            $UserAddedAssignment->isModifiedCategory = false;
        }
        else{
            return false;
        }
        $UserAddedAssignment->idAssignment = $idUserAddedAssignment;

        //$UserAddedAssignment->Student = GetStudent($UserAddedAssignment->idStudents, $db);
        //$UserAddedAssignment->Classes = GetClass($UserAddedAssignment->idStudents,$UserAddedAssignment->idClasses, $db);

        $UserAddedAssignment->AssignmentName = $row['UserAddedAssignmentName'];
        $UserAddedAssignment->AssignmentDate = time(); //TODO: Possibly change this to a better time, but having the assignment be the most recent time allows added assignments to be on top

        $UserAddedAssignment->ModifiedAssignmentEarnedPoints = $row['UserAddedAssignmentEarnedPoints'];
        $UserAddedAssignment->ModifiedAssignmentPossiblePoints = $row['UserAddedAssignmentPossiblePoints'];

        $UserAddedAssignment->AssignmentDisabled = $row['AssignmentDisabled'];
        $UserAddedAssignment->AssignmentBookmarked = $row['AssignmentBookmarked'];
        $UserAddedAssignment->AssignmentNewAlert = false;
        $UserAddedAssignment->UserAddedAssignment = true;

        return $UserAddedAssignment;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetAllUserAddedAssignmentsFromClassForStudent($idClass, $idStudent, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM useraddedassignments WHERE Classes_idClasses = :idClass AND Students_idStudents = :idStudent");
        $query->bindParam(':idClass', $idClass);
        $query->bindParam(':idStudent', $idStudent);
        $query->execute();

        $UserAddedAssignments = array();

        while ($row = $query->fetch()) {
            $UserAddedAssignment = new Assignment();
            $UserAddedAssignment->idClasses = $row['Classes_idClasses'];
            $UserAddedAssignment->idStudents = $row['Students_idStudents'];
            if(isset($row['ModifiedCategory_idModifiedCategory'])){
                $UserAddedAssignment->idCategory = $row['ModifiedCategory_idModifiedCategory'];
                //$UserAddedAssignment->Category = GetModifiedCategory($UserAddedAssignment->idCategory, $db);
                $UserAddedAssignment->isModifiedCategory = true;
            }
            elseif(isset($row['Category_idCategory'])){
                $UserAddedAssignment->idCategory = $row['Category_idCategory'];
                //$UserAddedAssignment->Category = GetCategory($UserAddedAssignment->idCategory, $db);
                $UserAddedAssignment->isModifiedCategory = false;
            }
            else{
                return false;
            }
            $UserAddedAssignment->idAssignment = $row['idUserAddedAssignments'];

            $UserAddedAssignment->AssignmentName = $row['UserAddedAssignmentName'];
            $UserAddedAssignment->AssignmentDate = time(); //TODO: Possibly change this to a better time, but having the assignment be the most recent time allows added assignments to be on top

            $UserAddedAssignment->ModifiedAssignmentEarnedPoints = $row['UserAddedAssignmentEarnedPoints'];
            $UserAddedAssignment->ModifiedAssignmentPossiblePoints = $row['UserAddedAssignmentPossiblePoints'];

            $UserAddedAssignment->AssignmentDisabled = $row['AssignmentDisabled'];
            $UserAddedAssignment->AssignmentBookmarked = $row['AssignmentBookmarked'];
            $UserAddedAssignment->AssignmentNewAlert = false;
            $UserAddedAssignment->UserAddedAssignment = true;

            array_push($UserAddedAssignments, $UserAddedAssignment);
        }
        return $UserAddedAssignments;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetModifiedCategory($idModifiedCategory, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM modifiedcategory WHERE idModifiedCategory = :idModifiedCategory");
        $query->bindParam(':idModifiedCategory', $idModifiedCategory);
        $query->execute();
        $row = $query->fetch();

        $ModifiedCategory = new Category();

        $ModifiedCategory->idCategory = $idModifiedCategory;
        $ModifiedCategory->idClasses = $row['Classes_idClasses'];
        $ModifiedCategory->idStudent = $row['Students_idStudents'];
        $ModifiedCategory->CategoryName = $row['CategoryName'];
        $ModifiedCategory->CategoryNameAbrv = $row['CategoryNameAbrv'];
        $ModifiedCategory->CategoryWeight = $row['CategoryWeight'];

//         $ModifiedCategory->Class = GetStrictClass($ModifiedCategory->idClasses, $db);
//TODO see Category.php

        $ModifiedCategory->isUserCreated = $row['isUserCreated'];
        $ModifiedCategory->isDisabled = $row['isDisabled'];


        return $ModifiedCategory;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetAllModifiedCategories($idClass, $idStudent, PDO $db) {
    try {
        $query = $db->prepare("SELECT idModifiedCategory FROM modifiedcategory WHERE Classes_idClasses = :idClass AND Students_idStudents = :idStudent");
        $query->bindParam(':idClass', $idClass);
        $query->bindParam(':idStudent', $idStudent);
        $query->execute();
        //$row = $query->fetch();

        $ModifiedCategories = array();
        while ($row = $query->fetch()) {
            $ModifiedCategory = GetModifiedCategory($row['idModifiedCategory'], $db); //TODO: Make this a single query
            array_push($ModifiedCategories, $ModifiedCategory);
        }
        return $ModifiedCategories;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetCategory($idCategory, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM category WHERE idCategory = :idCategory");
        $query->bindParam(':idCategory', $idCategory);
        $query->execute();
        $row = $query->fetch();

        $Category = new Category();

        $Category->idCategory = $idCategory;
        $Category->idClasses = $row['Classes_idClasses'];

        $Category->CategoryName = php_aes_decrypt($row['CategoryName'], S3);
        $Category->CategoryNameAbrv = php_aes_decrypt($row['CategoryNameAbrv'], S3);
        $Category->CategoryWeight = $row['CategoryWeight'];

        $Category->isUserCreated = false;
        $Category->isDisabled = false;
//         $Category->Class = GetStrictClass($Category->idClasses, $db);
//TODO see Category.php

        return $Category;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetAllCategories($idClass, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM category WHERE Classes_idClasses = :idClass");
        $query->bindParam(':idClass', $idClass);
        $query->execute();

        $Categories = array();
        while ($row = $query->fetch()) {
            $Category = new Category();

            $Category->idCategory = $row['idCategory'];
            $Category->idClasses = $row['Classes_idClasses'];

            $Category->CategoryName = php_aes_decrypt($row['CategoryName'], S3);
            $Category->CategoryNameAbrv =  php_aes_decrypt($row['CategoryNameAbrv'], S3);
            $Category->CategoryWeight = $row['CategoryWeight'];

            $Category->isUserCreated = false;
            $Category->isDisabled = false;

            array_push($Categories, $Category);
        }
        return $Categories;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetAllClassAveragesForClass($idClass, PDO $db) {
    try {
        $query = $db->prepare("SELECT idClassAverageGrade FROM classaverage WHERE Classes_idClasses = :idClass ORDER BY RecordedTime ASC");
        $query->bindParam(':idClass', $idClass);
        $query->execute();
        //$row = $query->fetch();

        $ClassAverages = array();

        while ($row = $query->fetch()) {
            $ClassAverage = GetClassAverage($row['idClassAverageGrade'], $db);
            array_push($ClassAverages, $ClassAverage);
        }

        return $ClassAverages;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetClassAverage($idClassAverage, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM classaverage WHERE idClassAverageGrade = :idClassAverage AND NumericGrade IS NOT NULL ORDER BY RecordedTime ASC");
        $query->bindParam(':idClassAverage', $idClassAverage);
        $query->execute();
        $row = $query->fetch();
        /*
        $ClassAverage = new Average();

        $ClassAverage->NumericGrade = $row['NumericGrade'];
        $ClassAverage->RecordedTime = $row['RecordedTime'];
        $ClassAverage->idOwner = $row['idClassAverageGrade'];*/

        return $row;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetLastClassAverage($idClass, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM classaverage WHERE Classes_idClasses = :idClasses ORDER BY RecordedTime DESC LIMIT 1");
        $query->bindParam(':idClasses', $idClass);
        $query->execute();
        $row = $query->fetch();
        $ReturnAvg = new Average();
        $ReturnAvg->RecordedTime = $row['RecordedTime'];
        $ReturnAvg->NumericGrade = $row['NumericGrade'];
        $ReturnAvg->idOwner = $row['Classes_idClasses'];
        return $ReturnAvg;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetAllLastClassAveragesForCourse($idCourse, PDO $db) {
    try {
        $query = $db->prepare("SELECT idClasses FROM classes WHERE Courses_idCourses = :idCourse");
        $query->bindParam(':idCourse', $idCourse);
        $query->execute();

        $ClassAverages = array();

        while ($row = $query->fetch()) {
            $ClassAverage = GetLastClassAverage($row['idClasses'], $db);
            array_push($ClassAverages, $ClassAverage);
        }

        return $ClassAverages;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetAllClassAveragesForTeacher($idTeacher, PDO $db) {
    try {
        $query = $db->prepare("SELECT idClasses FROM classes WHERE Teachers_idTeachers = :idTeacher");
        $query->bindParam(':idTeacher', $idTeacher);
        $query->execute();
        //$row = $query->fetch();

        $ClassAverages = array();

        while ($row = $query->fetch()) {
            $ClassAverage = GetClassAverage($row['idClasses'], $db);
            array_push($ClassAverages, $ClassAverage);
        }

        return $ClassAverages;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetClassReview($idClass, $idStudent, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM classreviews WHERE Students_idStudents = :idStudent AND Classes_idClasses =: idClass");
        $query->bindParam(':idClass', $idClass);
        $query->bindParam(':idStudent', $idStudent);
        $query->execute();
        $row = $query->fetch();
        $ClassReview = new ClassReview();

        $ClassReview->idClassReview = $row['idClassReviews'];
        $ClassReview->AvgTimeConsumed = $row['AvgTimeConsumed'];
        $ClassReview->TestQuizFrequency = $row['TestQuizFrequency'];
        $ClassReview->ProjectPresentationImportance = $row['ProjectPresentationImportance'];
        $ClassReview->HowWellLearnFromClass = $row['HowWellLearnFromClass'];
        $ClassReview->ExtraCreditImportance = $row['ExtraCreditImportance'];
        $ClassReview->OutsideResourceImportance = $row['OutsideResourceImportance'];
        $ClassReview->GeneralClassRating = $row['GeneralClassRating'];
        $ClassReview->DifficultyIndex = $row['DifficultyIndex'];
        $ClassReview->TextReview = $row['TextReview'];
        $ClassReview->isAnonymous = $row['isAnonymous'];
        $ClassReview->idClass = $idClass;
        $ClassReview->idStudent = $idStudent;
        $ClassReview->CategoryReviews = GetAllCategoryReviewsOfClass($ClassReview->idClass, $ClassReview->idClassReview, $db);

        return $ClassReview;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetCategoryReview($idCategory, $idClassReview, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM categoryreviews WHERE ClassReviews_idClassReviews = :idClassReview AND Category_idCategory =: idCategory");
        $query->bindParam(':idCategory', $idCategory);
        $query->bindParam(':idClassReview', $idClassReview);
        $query->execute();
        $row = $query->fetch();
        $CategoryReview = new CategoryReview();

        $CategoryReview->idCategoryReview = $row['idCategoryReview'];
        $CategoryReview->WorkLoadRanking = $row['WorkLoadRanking'];
        $CategoryReview->idCategory = $idCategory;
        $CategoryReview->idClassReview = $idClassReview;

        return $CategoryReview;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetAllCategoryReviewsOfClass($idClass, $idClassReview, PDO $db) {
    try {
        $Categories = GetAllCategories($idClass, $db);
        $CategoryReviews = array();
        foreach ($Categories as $Category) {
            $idCategory = $Category->idCategory;
            array_push($CategoryReviews, GetCategoryReview($idCategory, $idClassReview, $db));
        }


        return $CategoryReviews;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetLatestStudent_Classes_Grades($idClass, $endDate, PDO $db){
    //http://dev.mysql.com/doc/refman/5.6/en/example-maximum-column-group-row.html
    try {
    	$stmt = "SELECT s1.* FROM student_classes_grade s1
        JOIN (SELECT Student_Classes_Student_idStudent, MAX(RecordedTime) AS RecordedTime FROM student_classes_grade ";
    	if(isset($endDate)) $stmt .= "WHERE RecordedTime BETWEEN 0 AND :endDate ";
    	$stmt .= "GROUP BY Student_Classes_Student_idStudent) AS s2
        ON s1.Student_Classes_Student_idStudent = s2.Student_Classes_Student_idStudent AND s1.RecordedTime= s2.RecordedTime WHERE s1.Student_Classes_Classes_idClasses = :idClass AND s1.NumericGrade != 0"; //Make sure we don't record the 0's from the beginning of a year

    	$query = $db->prepare($stmt);
        $query->bindParam(':idClass', $idClass);
        $query->bindParam(':endDate', $endDate);
        $query->execute();
        return $query->fetchAll();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetAssignmentStatistics($idAssignment, PDO $db){
    try {
        $query = $db->prepare("SELECT STDDEV(AssignmentEarnedPoints/AssignmentPossiblePoints), AVG(AssignmentEarnedPoints/AssignmentPossiblePoints), COUNT(AssignmentEarnedPoints/AssignmentPossiblePoints) FROM assignmentscore
        WHERE Assignments_idAssignments = :idAssignment AND AssignmentEarnedPoints != -1 AND AssignmentPossiblePoints != 0"); //I'm not sure why you would want to know how you're doing when the assignment is out of 0...
        //TODO: Shrink this down to 1 call and do statistics php side
        $query->bindParam(':idAssignment', $idAssignment);
        $query->execute();
        return $query->fetch();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function GetClassStatistics($idClass, PDO $db){
    try {
        $query = $db->prepare("SELECT STDDEV(Student_Classes_NumericGrade), AVG(Student_Classes_NumericGrade), COUNT(Student_Classes_NumericGrade) FROM student_classes
        WHERE Classes_idClasses = :idClass AND Student_Classes_NumericGrade != 0"); //Make sure we don't record the 0's from the beginning of a year
        $query->bindParam(':idClass', $idClass);
        $query->execute();
        return $query->fetch();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function CheckLegacyStatus($legacyID, $Email, $FirstName, $LastName, PDO $db){
    try {
        $query = $db->prepare("SELECT idStudents FROM students WHERE LegacyID = :legacyID AND Email = :email
            AND StudentDistrictID = :sID AND StudentDistrictPassword = :sPW AND FirstName = :fname AND LastName = :lname"); //I'm not sure why you would want to know how you're doing when the assignment is out of 0...
        $query->bindParam(':legacyID', $legacyID);
        $query->bindValue(':email', php_aes_encrypt($Email, S1));
        $query->bindValue(':fname', php_aes_encrypt($FirstName, S1));
        $query->bindValue(':lname', php_aes_encrypt($LastName, S1));
        $query->bindValue('sID', php_aes_encrypt('', S2));
        $query->bindValue(':sPW', php_aes_encrypt('', S2));
        $query->execute();
        return $query->fetch();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function CheckIfStudentExists($StudentDistrictID, $StudentDistrictPassword, $Schools_idSchools, PDO $db){
    try {
        $query = $db->prepare("SELECT idStudents FROM students WHERE StudentDistrictID = :districtID AND StudentDistrictPassword = :districtPassword AND Schools_idSchools = :idSchools"); //I'm not sure why you would want to know how you're doing when the assignment is out of 0...
        $query->bindValue(':districtID', php_aes_encrypt($StudentDistrictID, S2));
        $query->bindValue(':districtPassword', php_aes_encrypt($StudentDistrictPassword, S2));
        $query->bindParam(':idSchools', $Schools_idSchools);
        $query->execute();
        return $query->fetch();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

?>
