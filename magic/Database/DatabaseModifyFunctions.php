<?php

function UnlinkUserFromStudent($idStudent, $idUser, PDO $db){
    try {
        $i = 1;
        $query = $db->prepare("DELETE FROM students_users WHERE Student_idStudent = ? AND Users_idUsers = ?");
        $query->bindValue($i++, $idStudent);
        $query->bindValue($i++, $idUser);
        $query->execute();

    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function ImportXMLModifyStudent(Student $StudentObj, PDO $db)
{
    try {
        $i = 1;
        $query = $db->prepare("UPDATE students SET FirstName=?, LastName=?, GPA=?, GradeLevel=?, GradesLastUpdated=? WHERE idStudents = ?");
        $query->bindValue($i++, php_aes_encrypt($StudentObj->FirstName, S1));
        $query->bindValue($i++, php_aes_encrypt($StudentObj->LastName, S1));
        $query->bindParam($i++, $StudentObj->GPA);
        $query->bindParam($i++, $StudentObj->GradeLevel);
        $query->bindParam($i++, $StudentObj->GradeLastUpdated);
        $query->bindParam($i++, $StudentObj->idStudents);

        $query->execute();

    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function StudentAsmntListModifyStudent($GPA, $idStudents, PDO $db)
{
    try {
        $i = 1;
        $query = $db->prepare("UPDATE students SET GPA=?, GradesLastUpdated=NOW() WHERE idStudents = ?");
        $query->bindParam($i++, $GPA);
        $query->bindParam($i++, $idStudents);

        $query->execute();

    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function ModifyStudentDistrictPassword($NewPassword, $idStudent, PDO $db)
{
    try {
        $i = 1;
        $query = $db->prepare("UPDATE students SET StudentDistrictPassword=? WHERE idStudents = ?");
        $query->bindValue($i++, php_aes_encrypt($NewPassword, S2));
        $query->bindValue($i++, $idStudent);

        $query->execute();

    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function ModifyStudent(Student $StudentObj, PDO $db)
{
    try {
        $i = 1;
        $query = $db->prepare("UPDATE students SET FirstName=?, LastName=?, Email=?, GPA=?, StudentDistrictID=?, StudentDistrictPassword=?, Schools_idSchools=?, Points=?, GradeLevel=? WHERE idStudents = ?");
        $query->bindValue($i++, php_aes_encrypt($StudentObj->FirstName, S1));
        $query->bindValue($i++, php_aes_encrypt($StudentObj->LastName, S1));
        $query->bindValue($i++, php_aes_encrypt($StudentObj->Email, S1));
        $query->bindParam($i++, $StudentObj->GPA);
        $query->bindValue($i++, php_aes_encrypt($StudentObj->SchoolDistrictID, S2));
        $query->bindValue($i++, php_aes_encrypt($StudentObj->SchoolDistrictPassword, S2));
        $query->bindParam($i++, $StudentObj->idSchools);
        $query->bindParam($i++, $StudentObj->Points);
        $query->bindParam($i++, $StudentObj->GradeLevel);
        $query->bindParam($i++, $StudentObj->idStudents);

        $query->execute();

    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function ModifyAssignment($AssignmentObj, PDO $db)
{
    try {
        $i = 1;
        $query = $db->prepare("UPDATE assignments SET AssignmentDate=?, Category_idCategory=? WHERE idAssignments=?");
        $query->bindParam($i++, $AssignmentObj->AssignmentDate);
        $query->bindParam($i++, $AssignmentObj->idCategory);
        $query->bindParam($i++, $AssignmentObj->idAssignment);
        $query->execute();

    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function RemoveAllModifiedAssignmentsForClass($idClass, $idStudent, PDO $db)
{
    try {
        $i = 1;
        $query = $db->prepare("DELETE MAS FROM modifiedassignmentscore MAS JOIN assignmentscore OAS ON OAS.idAssignmentScore = MAS.AssignmentScore_idAssignmentScore JOIN assignments a ON a.idAssignments = OAS.Assignments_idAssignments WHERE MAS.Students_idStudents = ? AND a.Classes_idClasses = ?");
        $query->bindParam($i++, $idStudent);
        $query->bindParam($i++, $idClass);
        return $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function RemoveAllAddedAssignmentsForClass($idClass, $idStudent, PDO $db)
{
    try {
        $i = 1;
        $query = $db->prepare("DELETE FROM useraddedassignments WHERE Students_idStudents=? AND Classes_idClasses=?");
        $query->bindParam($i++, $idStudent);
        $query->bindParam($i++, $idClass);
        return $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function RemoveNewAlertFlag($idAssignment, $idStudent, PDO $db)
{
    try {
        $i = 1;
        $query = $db->prepare("UPDATE assignmentscore SET AssignmentNewAlert=0 WHERE Assignments_idAssignments = ? AND Student_idStudent = ?");
        $query->bindParam($i++, $idAssignment);
        $query->bindParam($i++, $idStudent);
        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function BookmarkAssignment($idAssignment, $idStudent, $Bookmark, PDO $db)
{
    try {
        $i = 1;
        $query = $db->prepare("UPDATE assignmentscore SET AssignmentBookmarked=? WHERE Assignments_idAssignments = ? AND Student_idStudent = ?");
        $query->bindParam($i++, $Bookmark);
        $query->bindParam($i++, $idAssignment);
        $query->bindParam($i++, $idStudent);
        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function BookmarkUserAddedAssignment($idAssignment, $idStudent, $Bookmark, PDO $db)
{
    try {
        $i = 1;
        $query = $db->prepare("UPDATE useraddedassignments SET AssignmentBookmarked=? WHERE idUserAddedAssignments = ? AND Students_idStudents = ?");
        $query->bindParam($i++, $Bookmark);
        $query->bindParam($i++, $idAssignment);
        $query->bindParam($i++, $idStudent);
        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function DisableUserAddedAssignment($idAssignment, $idStudent, $Disabled, PDO $db)
{
    try {
        $i = 1;
        $query = $db->prepare("UPDATE useraddedassignments SET AssignmentDisabled=? WHERE idUserAddedAssignments =? AND Students_idStudents = ?");
        $query->bindParam($i++, $Disabled);
        $query->bindParam($i++, $idAssignment);
        $query->bindParam($i++, $idStudent);
        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function DeleteUserAddedAssignment($idAssignment, $idStudent, PDO $db)
{
    try {
        $i = 1;
        $query = $db->prepare("DELETE FROM useraddedassignments WHERE idUserAddedAssignments =? AND Students_idStudents = ?");
        $query->bindParam($i++, $idAssignment);
        $query->bindParam($i++, $idStudent);
        return $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function DisableModifiedAssignment($idAssignmentScore, $idStudent, $disabled, PDO $db)
{
    try {
        $AssignmentScore = GetAssignmentScore($idAssignmentScore, $idStudent, $db); //Get regular assignment for points
        $i = 1;
        $query = $db->prepare("INSERT INTO modifiedassignmentscore (AssignmentEarnedPoints, AssignmentPossiblePoints, AssignmentDisabled, AssignmentScore_idAssignmentScore, Students_idStudents) VALUES (?,?,?,?,?)
        ON DUPLICATE KEY UPDATE  AssignmentDisabled = ?");
        $query->bindParam($i++, $AssignmentScore->AssignmentEarnedPoints);
        $query->bindParam($i++, $AssignmentScore->AssignmentPossiblePoints);
        $query->bindParam($i++, $disabled);
        $query->bindParam($i++, $AssignmentScore->idAssignmentScore);
        $query->bindParam($i++, $idStudent);
        $query->bindParam($i++, $disabled);
        if ($query->execute())
            return $db->lastInsertId();
        else
            return false;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        //$db = null;
        //die();
        return -1;
    }
}

//Equivalent of resetting the modified assignment
function DeleteModifiedAssignmentScore($idAssignmentScore, $idStudent, PDO $db)
{
    try {
        $i = 1;
        $query = $db->prepare("DELETE FROM modifiedassignmentscore WHERE AssignmentScore_idAssignmentScore =? AND Students_idStudents = ?");
        $query->bindParam($i++, $idAssignmentScore);
        $query->bindParam($i++, $idStudent);
        $query->execute();
        return true;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return false;
    }
}

function ModifyAssignmentScore($AssignmentObj, $idStudent, PDO $db)
{ //THIS IS TO MODIFY A ASSIGNMENT SCORE NOT MODIFY MODIFIEDASSIGNMENTSCORE
    try {
        $i = 1;
        $query = $db->prepare("UPDATE assignmentscore SET AssignmentEarnedPoints=?, AssignmentPossiblePoints=?, AssignmentBookmarked=? WHERE Assignments_idAssignments = ? AND Student_idStudent = ?");
        $query->bindParam($i++, $AssignmentObj->AssignmentEarnedPoints);
        $query->bindParam($i++, $AssignmentObj->AssignmentPossiblePoints);
        $query->bindParam($i++, $AssignmentObj->AssignmentBookmarked);
        $query->bindParam($i++, $AssignmentObj->idAssignment);
        $query->bindParam($i++, $idStudent);
        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

//Modify Functions
function ModifyUserAddedAssignment(Assignment $AssignmentObj, $idStudent, PDO $db)
{
    try {
        $i = 1;
        if (isset($AssignmentObj->isModifiedCategory) && $AssignmentObj->isModifiedCategory) {

            $query = $db->prepare("UPDATE useraddedassignments SET UserAddedAssignmentName=?, UserAddedAssignmentEarnedPoints = ?, UserAddedAssignmentPossiblePoints=?, ModifiedCategory_idModifiedCategory=? WHERE idUserAddedAssignments =? AND Students_idStudents = ?");
        } else {
            $query = $db->prepare("UPDATE useraddedassignments SET UserAddedAssignmentName=?, UserAddedAssignmentEarnedPoints = ?, UserAddedAssignmentPossiblePoints=?, Category_idCategory=? WHERE idUserAddedAssignments =? AND Students_idStudents = ?");
        }
        $query->bindParam($i++, $AssignmentObj->AssignmentName);
        $query->bindParam($i++, $AssignmentObj->ModifiedAssignmentEarnedPoints);
        $query->bindParam($i++, $AssignmentObj->ModifiedAssignmentPossiblePoints);
        $query->bindParam($i++, $AssignmentObj->idCategory);
        $query->bindParam($i++, $AssignmentObj->idAssignment);
        $query->bindParam($i++, $idStudent);
        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

//Ask for idStudent to prevent brute force Assignment idAssignment and idStudent
function ModifyModifiedAssignmentScore(Assignment $AssignmentObj, PDO $db)
{
    try {
        $i = 1;
        $query = $db->prepare("INSERT INTO modifiedassignmentscore (AssignmentScore_idAssignmentScore, Students_idStudents, AssignmentEarnedPoints, AssignmentPossiblePoints) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE AssignmentEarnedPoints=?, AssignmentPossiblePoints=?");
        $query->bindParam($i++, $AssignmentObj->idAssignmentScore);
        $query->bindParam($i++, $AssignmentObj->idStudents);
        $query->bindParam($i++, $AssignmentObj->ModifiedAssignmentEarnedPoints);
        $query->bindParam($i++, $AssignmentObj->ModifiedAssignmentPossiblePoints);
        $query->bindParam($i++, $AssignmentObj->ModifiedAssignmentEarnedPoints);
        $query->bindParam($i++, $AssignmentObj->ModifiedAssignmentPossiblePoints);
        $query->execute();
        return true;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return false;
    }
}

function ModifyCategoryWeight($idCategory, $idClass, $Weight, $idStudent, PDO $db)
{
    try {
        if (!StudentIsEnrolledInClass($idStudent, $idClass, $db)) { //Ensure student has the permissions to do this action
            return false;
        }
        $i = 1;
        $query = $db->prepare("UPDATE category SET CategoryWeight=? WHERE idCategory = ? AND Classes_idClasses = ?");
        $query->bindParam($i++, $Weight);
        $query->bindParam($i++, $idCategory);
        $query->bindParam($i++, $idClass);
        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function ModifyClassIsWeightedFlag($flag, $idClass, $idStudent, PDO $db)
{
    try {
        if (!StudentIsEnrolledInClass($idStudent, $idClass, $db)) { //Ensure student has the permissions to do this action
            return false;
        }
        $i = 1;
        $query = $db->prepare("UPDATE classes SET isWeighted=? WHERE idClasses = ?");
        $query->bindParam($i++, $flag);
        $query->bindParam($i++, $idClass);
        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function ModifyModifiedCategory($CategoryObj, PDO $db)
{
    try {
        $query = $db->prepare("INSERT INTO modifiedcategory (idModifiedCategory, CategoryName, CategoryWeight, CategoryNameAbrv, isUserCreated, isDisabled) VALUES (:idModifiedCategory,:CategoryName,:CategoryWeight,:CategoryNameAbrv,:isUserCreated,:isDisabled) ON DUPLICATE KEY UPDATE CategoryName=:CategoryName, CategoryWeight=:CategoryWeight, CategoryNameAbrv=:CategoryNameAbrv, isUserCreated=:isUserCreated, isDisabled=:isDisabled");
        $query->bindParam(':idModifiedCategory', $CategoryObj->idCategory);
        $query->bindParam(':CategoryName', $CategoryObj->CategoryName);
        $query->bindParam(':CategoryWeight', $CategoryObj->CategoryWeight);
        $query->bindParam(':CategoryNameAbrv', $CategoryObj->CategoryNameAbrv);
        $query->bindParam(':isUserCreated', $CategoryObj->isUserCreated);
        $query->bindParam(':isDisabled', $CategoryObj->isDisabled);

        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function ModifyStrictClass($ClassObj, PDO $db)
{
    try {
        $i = 1;
        $query = $db->prepare("UPDATE classes SET Courses_idCourses=?, Teachers_idTeachers=?, TermNumbers_idTermNumbers=?, RoomNumber=?, Population=? WHERE idClasses = ? ");
        $query->bindParam($i++, $ClassObj->idCourses);
        $query->bindParam($i++, $ClassObj->idTeachers);
        $query->bindParam($i++, $ClassObj->TermNumber);
        $query->bindParam($i++, $ClassObj->RoomNumber);
        $query->bindParam($i++, $ClassObj->Population);
        $query->bindParam($i++, $ClassObj->idClasses);
        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function ModifyStudent_Classes($Period, $NumericGrade, $LetterGrade, $idClasses, $idStudent, PDO $db)
{
    try {
        $query = $db->prepare("UPDATE student_classes SET Student_Classes_GradeDiff= (:gradeDiff - Student_Classes_NumericGrade) , Student_Classes_Period=:period,
        Student_Classes_NumericGrade=:numericGrade, Student_Classes_LetterGrade=:letterGrade WHERE Classes_idClasses = :idClass AND Student_idStudent = :idStudent");
        $query->bindParam(':gradeDiff', $NumericGrade);
        $query->bindParam(':period', $Period);
        $query->bindParam(':numericGrade', $NumericGrade);
        $query->bindParam(':letterGrade', $LetterGrade);
        //$GradeDiff = $NumericGrade - GetNumericGrade($idClasses, $idStudent, $db);
        $query->bindParam(':idClass', $idClasses);
        $query->bindParam(':idStudent', $idStudent);
        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function ModifyStudent_Classes_Period($Period, $idClasses, $idStudent, $db)
{
    try {
        $i = 1;
        $query = $db->prepare("UPDATE student_classes SET Student_Classes_Period=? WHERE Classes_idClasses = ? AND Student_idStudent = ?");
        $query->bindParam($i++, $Period);
        $query->bindParam($i++, $idClasses);
        $query->bindParam($i++, $idStudent);
        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

//Used by ImportAssignmentList.php to update numeric/letter grade without touching period
function ModifyStudent_Classes_ClassGrade($NumericGrade, $LetterGrade, $idClasses, $idStudent, PDO $db)
{
    try {
        $query = $db->prepare("UPDATE student_classes SET Student_Classes_GradeDiff= (:gradeDiff - Student_Classes_NumericGrade),
        Student_Classes_NumericGrade=:numericGrade, Student_Classes_LetterGrade=:letterGrade WHERE Classes_idClasses = :idClass AND Student_idStudent = :idStudent");
        $query->bindParam(':gradeDiff', $NumericGrade);
        $query->bindParam(':numericGrade', $NumericGrade);
        $query->bindParam(':letterGrade', $LetterGrade);
        $query->bindParam(':idClass', $idClasses);
        $query->bindParam(':idStudent', $idStudent);
        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function ModifyStudent_Classes_Grade($NumericGrade, $idClasses, $idStudent, PDO $db)
{
    try {
        $query = $db->prepare("UPDATE student_classes_grade SET NumericGrade = :NumericGrade WHERE Student_Classes_Classes_idClasses = :idClass AND Student_Classes_Student_idStudent = :idStudent");
        $query->bindParam(':NumericGrade', $NumericGrade);
        $query->bindParam(':idClass', $idClasses);
        $query->bindParam(':idStudent', $idStudent);
        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function ModifyLoginService($LoginService, PDO $db)
{
    try {
        $i = 1;
        $query = $db->prepare("UPDATE loginservice SET LoginServiceType_idLoginServiceType=?, LoginServiceID=? WHERE Users_idUsers = ?");
        $query->bindParam($i++, $LoginService->idLoginServiceType);
        $query->bindParam($i++, $LoginService->idUser);
        $query->bindParam($i++, $LoginService->idUser);
        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function ModifyMobileNumber($MobileNumber, $idMobileCarrier, $idStudent, PDO $db)
{
    try {

        //TODO: Must Encrypt Mobile Number If This is Used
        print "ModifyMobileNumber(): Please Implement AES_ENCRYPT";
        return -1;

        $i = 1;
        $query = $db->prepare("UPDATE students SET MobileNumber=?, MobileCarrier_idMobileCarrier=? WHERE idStudents = ?");
        $query->bindParam($i++, $MobileNumber);
        $query->bindParam($i++, $idMobileCarrier);

        $query->bindParam($i++, $idStudent);
        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function ModifySchoolDistrictLogin($SchoolDistrictID, $SchoolDistrictPassword, $idStudent, PDO $db)
{
    try {
        $i = 1;
        $query = $db->prepare("UPDATE students SET StudentDistrictID=?, StudentDistrictPassword=? WHERE idStudents = ?");
        $query->bindValue($i++, php_aes_encrypt($SchoolDistrictID, S2));
        $query->bindValue($i++, php_aes_encrypt($SchoolDistrictPassword, S2));

        $query->bindParam($i++, $idStudent);
        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function ModifyMobileCarrier($MobileCarrierObj, PDO $db)
{
    try {
        $i = 1;
        $query = $db->prepare("UPDATE mobilecarrier SET MobileCarrierName=?, MobileCarrierGateway=? WHERE idMobileCarrier = ?");
        $query->bindParam($i++, $MobileCarrierObj->MobileCarrierName);
        $query->bindParam($i++, $MobileCarrierObj->MobileCarrierGateway);

        $query->bindParam($i++, $MobileCarrierObj->idMobileCarrier);
        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function ModifyRapidUpdateInterval($UpdateInterval, $LastUpdate, $idStudent, PDO $db)
{
    try {
        $i = 1;
        $query = $db->prepare("UPDATE rapidupdatestudents SET UpdateInterval=?, LastUpdate=? WHERE Students_idStudents = ?");
        $query->bindParam($i++, $UpdateInterval);
        $query->bindParam($i++, $LastUpdate);

        $query->bindParam($i++, $idStudent);
        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function ModifyUserLevel($idUserLevel, $idUser, PDO $db)
{
    try {
        $i = 1;
        $query = $db->prepare("UPDATE users SET UserLevel_idUserLevel=:UserLevel_idUserLevel WHERE idUsers = :idUser");
        $query->bindParam(':UserLevel_idUserLevel', $idUserLevel);
        $query->bindParam(':idUser', $idUser);
        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function ModifyUserEmail($Email, $idUser, PDO $db)
{
    try {
        $i = 1;
        $query = $db->prepare("UPDATE users SET Email=:Email WHERE idUsers = :idUser");
        $query->bindValue(':Email', php_aes_encrypt($Email, S4));
        $query->bindParam(':idUser', $idUser);
        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function IncrementUserLoginNumber($idUser, PDO $db)
{
    try {
        $query = $db->prepare("UPDATE users SET LoginNumber=LoginNumber+1 WHERE idUsers = :idUser");
        $query->bindParam(':idUser', $idUser);
        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

//Assumes Called on login time
function ModifyLastLoginTime($idUser, PDO $db)
{
    try {
        $query = $db->prepare("UPDATE users SET LastLogin=now() WHERE idUsers = :idUser");
        $query->bindParam(':idUser', $idUser);
        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function ModifySchool($SchoolObj, PDO $db)
{
    try {
        $i = 1;
        $query = $db->prepare("UPDATE schools SET SchoolName=?, SchoolStreetAddress=?, SchoolState=?, SchoolCity=?, Population=?, PowerschoolRootURL=? WHERE idSchools = ? ");
        $query->bindParam($i++, $SchoolObj->SchoolName);
        $query->bindParam($i++, $SchoolObj->SchoolStreetAddress);
        $query->bindParam($i++, $SchoolObj->SchoolState);
        $query->bindParam($i++, $SchoolObj->SchoolCity);
        $query->bindParam($i++, $SchoolObj->Population);
        $query->bindParam($i++, $SchoolObj->PowerschoolRootURL);
        $query->bindParam($i++, $SchoolObj->idSchools);
        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function ModifySchoolPopulation($SchoolPopulation, $idSchool, PDO $db)
{
    try {
        $i = 1;
        $query = $db->prepare("UPDATE schools SET Population = ? WHERE idSchools = ? ");
        $query->bindParam($i++, $SchoolPopulation);
        $query->bindParam($i++, $idSchool);
        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function ModifyCourse($CourseObj, PDO $db)
{
    try {
        $i = 1;
        $query = $db->prepare("UPDATE courses SET Schools_idSchools=?, GlobalCourses_idGlobalCourses=?, CourseName=?, CourseDescription=?, Population=? WHERE idCourses = ? ");
        $query->bindParam($i++, $CourseObj->idSchools);
        $query->bindParam($i++, $CourseObj->idGlobalCourses);
        $query->bindValue($i++, php_aes_encrypt($CourseObj->CourseName, S3));
        $query->bindParam($i++, $CourseObj->CourseDescription);
        $query->bindParam($i++, $CourseObj->Population);
        $query->bindParam($i++, $CourseObj->idCourses);
        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function ModifyGlobalCourse($GlobalCourseObj, PDO $db)
{
    try {
        $i = 1;
        $query = $db->prepare("UPDATE globalcourses SET GlobalCourseName=?, GlobalCourseDescription=?, Population=? WHERE idGlobalCourses = ? ");
        $query->bindParam($i++, $GlobalCourseObj->CourseName);
        $query->bindParam($i++, $GlobalCourseObj->CourseDescription);
        $query->bindParam($i++, $GlobalCourseObj->Population);
        $query->bindParam($i++, $GlobalCourseObj->idGlobalCourses);
        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function ModifyCoursePopulation($CoursePopulation, $idCourse, PDO $db)
{
    try {
        $i = 1;
        $query = $db->prepare("UPDATE courses SET Population = :Population WHERE idCourses = :idCourse ");
        $query->bindParam(':Population', $CoursePopulation);

        $query->bindParam(':idCourse', $idCourse);
        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function ModifyGlobalCoursePopulation($GlobalCoursePopulation, $idGlobalCourse, PDO $db)
{
    try {
        $i = 1;
        $query = $db->prepare("UPDATE globalcourses SET Population = ? WHERE idGlobalCourses = ? ");
        $query->bindParam($i++, $GlobalCoursePopulation);

        $query->bindParam($i++, $idGlobalCourse);
        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function ModifyCourseAttribute($CourseAttributeObj, PDO $db)
{
    try {
        $query = $db->prepare("UPDATE courseattributes SET Attribute = :Attribute WHERE idCourseAttributes = :idCourseAttribute");
        $query->bindParam(':Attribute', $CourseAttributeObj->Attribute);
        $query->bindParam(':idCourseAttribute', $CourseAttributeObj->idCourseAttributes);
        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

//TODO: Figure out what these functions are used for
/*
function ModifyCourse_CourseAttributes($idCourse, $idCourseAttributes, PDO $db) {
    try {
		$i=1;
        $query = $db->prepare("DELETE FROM Courses_CourseAttributes WHERE Courses_idCourses = :idCourse ");
        $query->bindParam(':idCourse', $idCourse);
        $query->execute();

        AddCourses_CourseAttributes($idCourse, $idCourseAttributes, $db);
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function ModifyGlobalCourse_CourseAttributes($idGlobalCourse, $idGlobalCourseAttributes, PDO $db) {
    try {
		$i=1;
        $query = $db->prepare("DELETE FROM GlobalCourses_CourseAttributes WHERE CourseAttributes_idCourseAttributes = :idGlobalCourse ");
        $query->bindParam(':idGlobalCourse', $idGlobalCourse);
        $query->execute();

        AddGlobalCourses_CourseAttributes($idGlobalCourse, $idGlobalCourseAttributes, $db);
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}
*/

function ModifyClassReview($ClassReviewObj, PDO $db)
{
    try {
        $query = $db->prepare("UPDATE classreviews SET AvgTimeConsumed=?, TestQuizFrequency=?, ProjectPresentationImportance=?, HowWellLearnFromClass=?, ExtraCreditImportance=?, OutsideResourceImportance=?, GeneralClassRating=?, DifficultyIndex=?, TextReview=?, isAnonymous=?, Classes_idClasses=?, Students_idStudents=? WHERE idClassReviews = ?");
        $i = 1;
        $query->bindParam($i++, $ClassReviewObj->AvgTimeConsumed);
        $query->bindParam($i++, $ClassReviewObj->TestQuizFrequency);
        $query->bindParam($i++, $ClassReviewObj->ProjectPresentationImportance);
        $query->bindParam($i++, $ClassReviewObj->HowWellLearnFromClass);
        $query->bindParam($i++, $ClassReviewObj->ExtraCreditImportance);
        $query->bindParam($i++, $ClassReviewObj->OutsideResourceImportance);
        $query->bindParam($i++, $ClassReviewObj->GeneralClassRating);
        $query->bindParam($i++, $ClassReviewObj->DifficultyIndex);
        $query->bindParam($i++, $ClassReviewObj->TextReview);
        $query->bindParam($i++, $ClassReviewObj->isAnonymous);
        $query->bindParam($i++, $ClassReviewObj->idClass);
        $query->bindParam($i++, $ClassReviewObj->idStudent);
        $query->bindParam($i++, $ClassReviewObj->idClassReivew);
        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function ModifyCategoryReview($CategoryReviewObj, PDO $db)
{
    try {
        $i = 1;
        $query = $db->prepare("UPDATE categoryreviews SET WorkLoadRanking = :WorkLoadRanking WHERE idCategoryReviews = :idCategoryReview");
        $query->bindParam(':WorkLoadRanking', $CategoryReviewObj->WorkLoadRanking);
        $query->bindParam("idCategoryReview", $CategoryReviewObj->idCategoryReview);

        $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function ModifyArrayCategoryReview($CategoryReviews, PDO $db)
{
    try {
        $i = 1;
        foreach ($CategoryReviews as $CategoryReview) {
            ModifyCategoryReview($CategoryReview, $db);
        }
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function IncrementClassPopulation($idClass, PDO $db)
{
    try {
        $query = $db->prepare("UPDATE classes SET Population = Population+1 WHERE idClasses = :idClasses");
        $query->bindParam(':idClasses', $idClass);

        return $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function IncrementAssignmentPopulation($idAssignment, PDO $db)
{
    try {
        $query = $db->prepare("UPDATE assignments SET Population = Population+1 WHERE idAssignments = :idAssignment");
        $query->bindParam(':idAssignment', $idAssignment);

        return $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function IncrementCoursePopulation($idCourse, PDO $db)
{
    try {
        $query = $db->prepare("UPDATE courses SET Population = Population+1 WHERE idCourses = :idCourses");
        $query->bindParam(':idCourses', $idCourse);

        return $query->execute();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

function UpdateAssignmentStatistics($idAssignment, PDO $db)
{
    try {
        $stats = GetAssignmentStatistics($idAssignment, $db);
        if ($stats === false) {
            return false;
        }
        $i = 1;
        $sd = $stats['STDDEV(AssignmentEarnedPoints/AssignmentPossiblePoints)'];
        $avg = $stats['AVG(AssignmentEarnedPoints/AssignmentPossiblePoints)'];
        $n = $stats['COUNT(AssignmentEarnedPoints/AssignmentPossiblePoints)'];

        $query = $db->prepare("UPDATE assignments SET AssignmentAverage=?, AssignmentStandardDeviation=?, Population=? WHERE idAssignments = ?");
        $query->bindParam($i++, $avg);
        $query->bindParam($i++, $sd);
        $query->bindParam($i++, $n);
        $query->bindParam($i++, $idAssignment);

        return $query->execute();

    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        return -1;
    }
}

?>
