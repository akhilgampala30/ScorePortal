<?php

//Validate Functions
function ValidateSchoolID($Schools_idSchools, PDO $db) {
    try {
        $query = $db->prepare("SELECT idSchools FROM schools WHERE idSchools = :id");
        $query->bindParam(':id', $Schools_idSchools);
        $query->execute();
        return $db->query("SELECT FOUND_ROWS()")->fetchColumn();
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

//Makes sure that assignment names per class are unique
function VerifyUniqueModifiedAssignmentName(Assignment $AssignmentObj, PDO $db){
    try {
        $query = $db->prepare("SELECT * FROM useraddedassignments WHERE Classes_idClasses = :idClass AND UserAddedAssignmentName = :assignmentName AND Students_idStudents = :idStudent");
        $query->bindParam(':idClass', $AssignmentObj->idClasses);
        $query->bindParam(':assignmentName', $AssignmentObj->AssignmentName);
        $query->bindParam(':idStudent', $AssignmentObj->idStudents);
        $query->execute();
        return ($db->query("SELECT FOUND_ROWS()")->fetchColumn() == 0 ? true : false);
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function AssignmentExists($AssignmentObj, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM assignments WHERE Classes_idClasses = :idClass AND Category_idCategory = :idCategory AND AssignmentName = :assignmentName");
        $query->bindParam(':idClass', $AssignmentObj->idClasses);
        $query->bindParam(':idCategory', $AssignmentObj->idCategory);
        $query->bindValue(':assignmentName', php_aes_decrypt($AssignmentObj->AssignmentName, S3));
        $query->execute();
        return ($db->query("SELECT FOUND_ROWS()")->fetchColumn() == 0 ? false : true);
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function AssignmentRecordExists($AssignmentObj, PDO $db){
    try {
        $query = $db->prepare("SELECT idAssignmentScore FROM assignmentscore WHERE Assignments_idAssignments = :idAssignment AND Student_idStudent = :idStudent");
        $query->bindParam(':idAssignment', $AssignmentObj->idAssignment);
        $query->bindParam(':idStudent', $AssignmentObj->idStudents);
        $query->execute();
        $row=$query->fetch();

        if($row === false){
            return false; //If no rows found, just return false
        }

        return true;
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function AssignmentScoreAgrees($AssignmentObj, PDO $db){
    try {
        $query = $db->prepare("SELECT * FROM assignmentscore WHERE Assignments_idAssignments = :idAssignment AND Student_idStudent = :idStudent"); //TODO: Use single select statement
        $query->bindParam(':idAssignment', $AssignmentObj->idAssignment);
        $query->bindParam(':idStudent', $AssignmentObj->idStudents);
        $query->execute();
        $row=$query->fetch();

        if($row === false){
            return false; //If no rows found, just return false
        }

        return ($row['AssignmentEarnedPoints']==$AssignmentObj->AssignmentEarnedPoints && $row['AssignmentPossiblePoints'] ==$AssignmentObj->AssignmentPossiblePoints);
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function AssignmentPropertyAgrees($AssignmentObj, PDO $db){
    try {
        $query = $db->prepare("SELECT * FROM assignments WHERE idAssignments = :idAssignment");
        $query->bindParam(':idAssignment', $AssignmentObj->idClasses);
        $query->execute();
        $row=$query->fetch();
        return ($row['Category_idCategory']==$AssignmentObj->idCategory && $row['AssignmentDate'] ==$AssignmentObj->AssignmentDate);
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function CourseExists($CourseName, $idSchool, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM courses WHERE Schools_idSchools = :idSchool AND CourseName = :courseName");
        $query->bindParam(':idSchool', $idSchool);
        $query->bindValue(':courseName', php_aes_encrypt($CourseName, S3));
        $query->execute();
        return ($db->query("SELECT FOUND_ROWS()")->fetchColumn() == 0 ? false : true);
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function ClassExists($CourseName, $TeacherName, $idSchool, PDO $db){
    try {
        $CourseID = GetCourseID($CourseName,$idSchool,$db);
        $Teacher = GetTeacherByName($TeacherName,$idSchool,$db);
        $query = $db->prepare("SELECT * FROM classes WHERE Teachers_idTeachers = :idTeachers AND Courses_idCourses = :courseID");
        $query->bindParam(':idTeachers', $Teacher->idTeachers);
        $query->bindParam(':courseID', $CourseID);
        $query->execute();
        return ($db->query("SELECT FOUND_ROWS()")->fetchColumn() == 0 ? false : true);
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

/**
 * Uses IDs to determine if class exists, reduces redundant calls when ID is already known
 * @param Int $idCourse
 * @param Int $idTeacher
 * @param Int $idSchool
 * @param Int $idTermNumbers
 * @param PDO $db
 * @return bool
 */
function ClassExistsFromID($idCourse, $idTeacher, $idSchool, $idTermNumbers, PDO $db){
    try {
        $query = $db->prepare("SELECT * FROM classes WHERE Teachers_idTeachers = :idTeachers AND Courses_idCourses = :courseID AND TermNumbers_idTermNumbers = :idTermNumbers");
        $query->bindParam(':idTeachers', $idTeacher);
        $query->bindParam(':courseID', $idCourse);
        $query->bindParam(':idTermNumbers', $idTermNumbers);
        $query->execute();
        return ($db->query("SELECT FOUND_ROWS()")->fetchColumn() == 0 ? false : true);
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function TeacherExists($TeacherName, $idSchool, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM teachers WHERE Schools_idSchools = :idSchool AND TeacherName = :teacherName");
        $query->bindParam(':idSchool', $idSchool);
        $query->bindValue(':teacherName', php_aes_encrypt($TeacherName, S3));
        $query->execute();
        return ($db->query("SELECT FOUND_ROWS()")->fetchColumn() == 0 ? false : true);
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function CategoryExists($CategoryName, $idClass, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM category WHERE CategoryName = :CategoryName AND Classes_idClasses = :idClass");
        $query->bindParam(':idClass', $idClass);
        $query->bindValue(':CategoryName', php_aes_encrypt($CategoryName, S3));
        $query->execute();
        return ($db->query("SELECT FOUND_ROWS()")->fetchColumn() == 0 ? false : true);
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function idCategoryIsValid($idCategory, $idClass, PDO $db){
    try {
        $query = $db->prepare("SELECT * FROM category WHERE idCategory = :idCategory AND Classes_idClasses = :idClass");
        $query->bindParam(':idCategory', $idCategory);
        $query->bindParam(':idClass', $idClass);
        $query->execute();
        return ($db->query("SELECT FOUND_ROWS()")->fetchColumn() == 0 ? false : true);
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}


function idModifiedCategoryIsValid($idCategory, $idClass, $idStudent, PDO $db){
    try {
        $query = $db->prepare("SELECT * FROM modifiedcategory WHERE idModifiedCategory = :idCategory AND Classes_idClasses = :idClass AND Students_idStudents = :idStudent");
        $query->bindParam(':idCategory', $idCategory);
        $query->bindParam(':idClass', $idClass);
        $query->bindParam(':idStudent', $idStudent);
        $query->execute();
        return ($db->query("SELECT FOUND_ROWS()")->fetchColumn() == 0 ? false : true);
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function StudentExists($idStudent, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM students WHERE idStudents = :idStudent");
        $query->bindParam(':idStudent', $idStudent);
        $query->execute();
        return ($db->query("SELECT FOUND_ROWS()")->fetchColumn() == 0 ? false : true);
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function StudentIsEnrolledInClass($idStudent, $idClass, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM student_classes WHERE Student_idStudent = :idStudent and Classes_idClasses = :idClass");
        $query->bindParam(':idStudent', $idStudent);
        $query->bindParam(':idClass', $idClass);

        $query->execute();
        return ($db->query("SELECT FOUND_ROWS()")->fetchColumn() == 0 ? false : true);
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function isAchievementEarned($idAchievement, $idStudent, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM student_achievements WHERE Achievements_idAchievements = :idAchievement AND Students_idStudents = :idStudent");
        $query->bindParam(':idClass', $idAchievement);
        $query->bindParam(':idStudent', $idStudent);
        $query->execute();
        return ($db->query("SELECT FOUND_ROWS()")->fetchColumn() == 0 ? false : true);
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

function isClassReviewed($idClass, $idStudent, PDO $db) {
    try {
        $query = $db->prepare("SELECT * FROM classreviews WHERE Classes_idClasses = :idClass AND Students_idStudents = :idStudent");
        $query->bindParam(':idClass', $idClass);
        $query->bindParam(':idStudent', $idStudent);
        $query->execute();
        return ($db->query("SELECT FOUND_ROWS()")->fetchColumn() == 0 ? false : true);
    } catch (PDOException $e) {
        print "Error on " . __FUNCTION__ . "!: " . $e->getMessage() . "<br/>";
        die();
    }
}

?>
