<?php
/**

 * User: Mike
 * Date: 7/5/13
 * Time: 2:58 PM

 */

class Category {
    public $idCategory;//on retrieval
    public $idClasses;
    public $idStudent; //only on modified category

    //Attributes
    public $CategoryName;
    public $CategoryWeight;
    public $CategoryNameAbrv;
    public $isUserCreated;//only on modified categories
    public $isDisabled;

    //Objs
//     public $Class;//not needed for add
//TODO find an elegant way to resolve 2-way reference (Class <-> Category)
//which leads to infinite loop when getting from database (Class -> Category -> Class -> ...)

    public $TotalPointsEarned;
    public $TotalPointsPossible;
    public $Percentage;

    public $ModifiedTotalPointsEarned;
    public $ModifiedTotalPointsPossible;
    public $ModifiedPercentage;

    //public static $Colors = array('#CA1511', '#ECA02E', '#367DA3', '#578625', '#9F45BA', '#9CE159', '#64B3DF', '#FA5F5E,','#F1D030');
    //public static $FadedColors = array('#C97270', '#EAC68F', '#6B8EA0', '#6E8457', '#AC86B7', '#C9E0B3', '#B1CEDD', '#F9B6B6', '#EFE2A7');


    public static $Colors = array(
        array('#599AD3', '#B8D2EB', '#89B6DF'), //Blue
        array('#F1595F', '#F2AEAC', '#F28486'), //Red
        array('#79C36A', '#D8E4AA', '#A9D48A'), //Green
        array('#F9A65A', '#F2D190', '#F6BC85'), //OJ
        array('#9E66AB', '#D4B2D3', '#B98CBF'), //Purple
        array('#CD7058', '#DDB8A9', '#D59481'), //Poop
        array('#D77FB3', '#EBBFD9', '#E19FC6'), //Violet
        array('#727272', '#CCCCCC', '#9F9F9F'), //Gray
    );

    /* @var $Haystack Category[]
     * @var $Needle Category
     * @return boolean
     */
    public static function CategoryArrayContains($Haystack, $Needle){
        foreach($Haystack as $Hay){
            if($Hay->CategoryName == $Needle->CategoryName && $Hay->idClasses == $Needle->idClasses){
                return $Hay;
            }
        }
        return false;
    }

    public function SumCategories($Assignments){
        $this->TotalPointsEarned = 0;
        $this->TotalPointsPossible = 0;
        foreach($Assignments as $Assignment)
        {
            if($Assignment->AssignmentEarnedPoints >= 0 && $Assignment->idCategory == $this->idCategory) //not -1 / blank
            {
                $this->TotalPointsEarned += $Assignment->AssignmentEarnedPoints;
                $this->TotalPointsPossible += $Assignment->AssignmentPossiblePoints;
            }
        }
        $this->Percentage = $this->TotalPointsPossible == 0 ? 1 : round(((double) $this->TotalPointsEarned)/$this->TotalPointsPossible, 4);
    }
}