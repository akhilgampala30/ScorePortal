<?php
/**
 * User: Mike
 * Date: 7/3/13
 * Time: 12:33 AM

 */

class Assignment {
    //Unique ID Properties
    public $idAssignment;
    public $idCategory; //Stores either modified id or normal id, set flag to specify
    public $idClasses;
    public $idStudents;
    public $idModifiedAssignmentScore;
    //public $idUserAddedAssignments;
    public $idAssignmentScore;

    public $isModifiedCategory; //Store idModifiedCategory in idCategory but set this flag.

    //Objects, not needed for add
    /* @var $Category Category*/
    public $Category;
    /* @var $Classes _Class*/
    public $Classes; //Now set in GetClass

    //Assignment Information
    public $AssignmentName;
    /* @var $AssignmentDate int Timestamp*/
    public $AssignmentDate; 

    //Grade Information
    public $AssignmentEarnedPoints;
    public $AssignmentPossiblePoints;

    public $ModifiedAssignmentEarnedPoints;
    public $ModifiedAssignmentPossiblePoints;

    //Attributes
    public $AssignmentDisabled; 
    public $AssignmentBookmarked;
    public $UserAddedAssignment;
    public $AssignmentNewAlert;
  
    //Statistics
    public $Population;
    public $AveragePercent;
    public $StandardDeviation;

    public $Percentile;
    public $LowerAverage;
    public $UpperAverage;

    //Shows impact on grade/category on the assignment page
    public $FluctuationOnGrade;
    public $FluctuationOnCategory;

    /* @var $_assignment Assignment*/
    public function UpdateAssignmentObject($_assignment){ //Mainly used through UpdateScore.php
        $this->AssignmentEarnedPoints = $_assignment->AssignmentEarnedPoints;
        $this->AssignmentPossiblePoints = $_assignment->AssignmentPossiblePoints;
        $this->AssignmentDisabled = $_assignment->AssignmentDisabled;
        $this->AssignmentBookmarked =  $_assignment->AssignmentBookmarked;
        $this->idAssignment = $_assignment->idAssignment;
    }

    public function GetPercentage(){
        if($this->AssignmentPossiblePoints == 0)
            return 100;
        if(!$this->IsAssignmentSet()){
            return -1;
        }
        return round(($this->AssignmentEarnedPoints/$this->AssignmentPossiblePoints)*100, 2);
    }

    public function IsAssignmentSet(){
        if($this->AssignmentPossiblePoints == -1 || $this->AssignmentEarnedPoints == -1)
            return false;
        return true;
    }

    /* @var $_assignments Assignment[]*/
    public static function GetAssignment($_id,$_assignments){ //Only Get Original Assignments
        foreach($_assignments as $value){
            if(!$value->UserModifiedAssignment && !$value->UserAddedAssignment && $value->idAssignment == $_id)
                return $value;
        }
    }

    //TODO: Combine Functions Later
    /* @var $_assignments Assignment[]*/
    public static function GetModifiedAssignment($_id,$_assignments){
        foreach($_assignments as $value){
            if($value->UserModifiedAssignment && $value->idAssignment == $_id)
                return $value;
        }
    }
    /* @var $_assignments Assignment[]*/
    public static function GetAddedAssignment($_id,$_assignments){
        foreach($_assignments as $value){
            if($value->UserAddedAssignment && $value->idAssignment == $_id)
                return $value;
        }
    }


}