<?php
/**

 * User: Mike
 * Date: 7/5/13
 * Time: 2:21 PM

 */

class _Class {
    //IDs
    public $idClasses;
    public $idCourses;
    public $idTeachers;
    public $idStudent;
    public $idTermNumber;

    //Class General Information
    public $TermNumber;
    public $RoomNumber;
    public $Population;
    public $ClassAverage; //Array constructed from ClassAverage for History

    public $isWeighted;

    //Objects
    /* @var $Teacher Teacher*/
    public $Teacher;
    /* @var $Course Course*/
    public $Course;
    /* @var $Assignments Assignment[]*/
    public $Assignments;
    /* @var $Categories Category[]*/
    public $Categories;

    //Student Personal Information
    public $Period;
    public $NumericGrade;
    public $LetterGrade;
    public $GradeDiff;
    public $GradeHistory; //$row[] data returned from student_classes_grade query

    public $ClassTotalPoints;
    public $ClassEarnedPoints;
    public $ClassCalculatedPercentage;

    public $ClassEditedTotalPoints;
    public $ClassEditedEarnedPoints;
    public $ClassEditedCalculatedPercentage;

    public static function ClassSort(_Class $a, _Class $b){
        return  $a->Period - $b->Period;
    }

    public function NewAssignmentAlertInClass(){
        foreach($this->Assignments as $Assignment){
            if($Assignment->AssignmentNewAlert)
                return true;
        }
    }

    public function CalculateGrade(){
    	$this->ClassEarnedPoints = 0;
    	$this->ClassTotalPoints = 0;
        if($this->isWeighted){
        	$this->ClassCalculatedPercentage = 0;
        	$SumCategoryWeights = 0;
            foreach($this->Categories as $Category){ //Still provide raw totaled points
                $this->ClassEarnedPoints += $Category->TotalPointsEarned;
                $this->ClassTotalPoints += $Category->TotalPointsPossible;
                if($Category->TotalPointsPossible > 0){
                    $this->ClassCalculatedPercentage += $Category->CategoryWeight*($Category->TotalPointsEarned/(double)$Category->TotalPointsPossible);
                	$SumCategoryWeights += $Category->CategoryWeight;
                }
            }
            if($SumCategoryWeights != 0){ //If sum of weights is 0, divide by 1
                $this->ClassCalculatedPercentage /= $SumCategoryWeights;
            }
        }
        else{
            foreach($this->Categories as $Category){
                $this->ClassEarnedPoints += $Category->TotalPointsEarned;
                $this->ClassTotalPoints += $Category->TotalPointsPossible;
            }
            if($this->ClassTotalPoints > 0){
                $this->ClassCalculatedPercentage = round($this->ClassEarnedPoints/(double)$this->ClassTotalPoints,4);
            }
            else{
                $this->ClassCalculatedPercentage=1; //Set as 100%
            }
        }
    }

    public function CalculateEditedGrade(){
    	$this->ClassEditedEarnedPoints = 0;
    	$this->ClassEditedTotalPoints = 0;
        if($this->isWeighted){
        	$this->ClassEditedCalculatedPercentage = 0;
        	$SumCategoryWeights = 0;
            foreach($this->Categories as $Category){ //Still provide raw totaled points
                $this->ClassEditedEarnedPoints += $Category->ModifiedTotalPointsEarned;
                $this->ClassEditedTotalPoints += $Category->ModifiedTotalPointsPossible;
                if($Category->ModifiedTotalPointsPossible > 0){
                    $this->ClassEditedCalculatedPercentage += $Category->CategoryWeight*($Category->ModifiedTotalPointsEarned/$Category->ModifiedTotalPointsPossible);
                    $SumCategoryWeights += $Category->CategoryWeight;
                }
            }
            if($SumCategoryWeights != 0){ //If the sum isn't 0, divide by weights
                $this->ClassEditedCalculatedPercentage /= $SumCategoryWeights;
            }
        }
        else{
            foreach($this->Categories as $Category){
                $this->ClassEditedEarnedPoints += $Category->ModifiedTotalPointsEarned;
                $this->ClassEditedTotalPoints += $Category->ModifiedTotalPointsPossible;
            }
            if($this->ClassEditedTotalPoints > 0){
                $this->ClassEditedCalculatedPercentage = round($this->ClassEditedEarnedPoints/(double)$this->ClassEditedTotalPoints,4);
            }
            else{
                $this->ClassEditedCalculatedPercentage=1; //Set as 100%
            }
        }
    }

    /* @var $Haystack _Class[]
     * @var $Needle _Class
     * @return boolean
     */
    public static function ClassArrayContains($Haystack, $Needle){
        foreach($Haystack as $Hay){
            if($Hay->idCourses == $Needle->idCourses && $Hay->idTeachers == $Needle->idTeachers && $Hay->idTermNumber == $Needle->idTermNumber){
                return $Hay;
            }
        }
        return false;
    }
}
