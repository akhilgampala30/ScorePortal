<?php
/**
 * User: Mike
 * Date: 12/22/13
 * Time: 4:10 PM
 */
function CalculateAssignmentCategoryImpact(Assignment &$AssignmentObj){
    if($AssignmentObj->AssignmentEarnedPoints < 0){  //If the assignment has a negative earned points (--)
        return $AssignmentObj->FluctuationOnCategory = 0;
    }
    $CategoryEarned = $AssignmentObj->Category->TotalPointsEarned - $AssignmentObj->AssignmentEarnedPoints;
    $CategoryPossible =  $AssignmentObj->Category->TotalPointsPossible - $AssignmentObj->AssignmentPossiblePoints;

    if($CategoryPossible > 0){
        $AssignmentObj->FluctuationOnCategory = round(($AssignmentObj->Category->TotalPointsEarned/$AssignmentObj->Category->TotalPointsPossible)-($CategoryEarned/$CategoryPossible),4);
    }else{
    	$AssignmentObj->FluctuationOnCategory = 0;
    }
    return $AssignmentObj->FluctuationOnCategory;
}

function CalculateAssignmentGradeImpact(Assignment &$AssignmentObj, $ClassObj){
    if($AssignmentObj->AssignmentEarnedPoints < 0){  //If the assignment has a negative earned points (--)
        return $AssignmentObj->FluctuationOnGrade = 0;
    }
    if($ClassObj->isWeighted)
    {
        $ClassChangedPercent = 0;
        $SumCategoryWeights = 0;
        foreach($ClassObj->Categories as $Category){
            if($Category->idCategory == $AssignmentObj->Category->idCategory){ //This is the Assignment's Category
                $CategoryEarned = $AssignmentObj->Category->TotalPointsEarned - $AssignmentObj->AssignmentEarnedPoints;
                $CategoryPossible =  $AssignmentObj->Category->TotalPointsPossible - $AssignmentObj->AssignmentPossiblePoints;
            }
            else{ //Some category I don't really care about
                $CategoryEarned = $Category->TotalPointsEarned;
                $CategoryPossible =  $Category->TotalPointsPossible;
            }
            if($CategoryPossible>0){
                $ClassChangedPercent += $Category->CategoryWeight * ($CategoryEarned/$CategoryPossible);
                $SumCategoryWeights += $Category->CategoryWeight;
            }
        }
		if($SumCategoryWeights > 0)
		{
			$ClassChangedPercent /= $SumCategoryWeights;
			$AssignmentObj->FluctuationOnGrade = round($ClassObj->ClassCalculatedPercentage - $ClassChangedPercent,4);
		}else{
			$AssignmentObj->FluctuationOnGrade = 0;
		}
    }
    else{
        $TotalEarned = $ClassObj->ClassEarnedPoints - $AssignmentObj->AssignmentEarnedPoints;
        $TotalPossible = $ClassObj->ClassTotalPoints - $AssignmentObj->AssignmentPossiblePoints;
        if($TotalPossible >0){
            $AssignmentObj->FluctuationOnGrade = round($ClassObj->ClassCalculatedPercentage - $TotalEarned/$TotalPossible,4);
        }else{
            $AssignmentObj->FluctuationOnGrade = 0;
        }
    }
    return $AssignmentObj->FluctuationOnGrade;
}


function CalculateModifiedAssignmentCategoryImpact(Assignment &$AssignmentObj){
    if(isset($AssignmentObj->ModifiedAssignmentEarnedPoints)){
        $AssignmentEarnedPoints = ($AssignmentObj->ModifiedAssignmentEarnedPoints);
        $AssignmentPossiblePoints = ($AssignmentObj->ModifiedAssignmentPossiblePoints);
    } else{
        $AssignmentEarnedPoints = ($AssignmentObj->AssignmentEarnedPoints);
        $AssignmentPossiblePoints = ($AssignmentObj->AssignmentPossiblePoints);
    }
    if($AssignmentEarnedPoints < 0 || $AssignmentPossiblePoints < 1){ //If the assignment has a negative earned points (--) or possible points out of 0 or (--)
        $AssignmentObj->FluctuationOnCategory = 0;
        return $AssignmentObj->FluctuationOnCategory;
    }
    $CategoryEarned = $AssignmentObj->Category->ModifiedTotalPointsEarned - $AssignmentEarnedPoints;
    $CategoryPossible =  $AssignmentObj->Category->ModifiedTotalPointsPossible - $AssignmentPossiblePoints;
    $AssignmentObj->FluctuationOnCategory = 0;
    $mult = 1; if($AssignmentObj->AssignmentDisabled){$mult = -1;} // If it's disabled, simply flip the sign
    if($CategoryPossible > 0 && $AssignmentObj->Category->ModifiedTotalPointsPossible > 0){ //Prevent divide by 0's
        $AssignmentObj->FluctuationOnCategory = $mult * round(($AssignmentObj->Category->ModifiedTotalPointsEarned/$AssignmentObj->Category->ModifiedTotalPointsPossible)-($CategoryEarned/$CategoryPossible),4);
    }
    return $AssignmentObj->FluctuationOnCategory;
}

/* @var $ClassObj _Class*/
function CalculateModifiedAssignmentGradeImpact(Assignment &$AssignmentObj, $ClassObj){
    if(isset($AssignmentObj->ModifiedAssignmentEarnedPoints)){
        $AssignmentEarnedPoints = ($AssignmentObj->ModifiedAssignmentEarnedPoints);
        $AssignmentPossiblePoints = ($AssignmentObj->ModifiedAssignmentPossiblePoints);
    } else{
        $AssignmentEarnedPoints = ($AssignmentObj->AssignmentEarnedPoints);
        $AssignmentPossiblePoints = ($AssignmentObj->AssignmentPossiblePoints);
    }
    if($AssignmentEarnedPoints<0 || $AssignmentPossiblePoints<1){ //If the assignment has a negative earned points (--) or possible points out of 0 or (--)
        $AssignmentObj->FluctuationOnGrade = 0;
        return $AssignmentObj->FluctuationOnGrade;
    }
    $mult = 1;
    if($AssignmentObj->AssignmentDisabled){ //If disabled, simply flip signs
        $mult = -1; //If disabled, reverse the subtraction by multiplying by -1
    }
    if($ClassObj->isWeighted)
    {
        $ClassChangedPercent = 0;
        $SumCategoryWeights = 0;
        foreach($ClassObj->Categories as $Category){
            if($Category->idCategory == $AssignmentObj->Category->idCategory){ //This is the Assignment's Category, so we remove it from the category
                $CategoryEarned = $AssignmentObj->Category->ModifiedTotalPointsEarned - $AssignmentEarnedPoints;
                $CategoryPossible =  $AssignmentObj->Category->ModifiedTotalPointsPossible - $AssignmentPossiblePoints;
            }
            else{ //Some category I don't really care about
                $CategoryEarned = $Category->ModifiedTotalPointsEarned;
                $CategoryPossible =  $Category->ModifiedTotalPointsPossible;
            }
            if($CategoryPossible>0){
                $ClassChangedPercent += $Category->CategoryWeight * ($CategoryEarned/$CategoryPossible);
                $SumCategoryWeights += $Category->CategoryWeight;
            }
        }
        $ClassChangedPercent /= $SumCategoryWeights;
        $AssignmentObj->FluctuationOnGrade = $mult*round($ClassObj->ClassEditedCalculatedPercentage - $ClassChangedPercent,4);
    }
    else{
        $TotalEarned = $ClassObj->ClassEditedEarnedPoints - $AssignmentEarnedPoints;
        $TotalPossible = $ClassObj->ClassEditedTotalPoints - $AssignmentPossiblePoints;
        if($TotalPossible >0){
            $AssignmentObj->FluctuationOnGrade = $mult*round($ClassObj->ClassEditedCalculatedPercentage - $TotalEarned/$TotalPossible,4);
        }else{
            $AssignmentObj->FluctuationOnGrade = 0;
        }
    }
    return $AssignmentObj->FluctuationOnGrade;
}

/* @var $ClassObj _Class*/
function SumCategories(&$ClassObj){
    //TODO: Could check if category totals are already set to prevent recalling this every time
    $TotalPoints = 0;
    foreach($ClassObj->Categories as &$Category)
    {
        /*
        $Category->TotalPointsEarned = 0;
        $Category->TotalPointsPossible = 0;
        foreach($ClassObj->Assignments as $Assignment)
        {
            if($Assignment->AssignmentEarnedPoints >= 0 && $Assignment->idCategory == $Category->idCategory) //not -1 / blank
            {
                $Category->TotalPointsEarned += $Assignment->AssignmentEarnedPoints;
                $Category->TotalPointsPossible += $Assignment->AssignmentPossiblePoints;
            }
        }
        $Category->Percentage = $Category->TotalPointsPossible == 0 ? 1 : round(((double) $Category->TotalPointsEarned)/$Category->TotalPointsPossible, 4);*/
        $Category->SumCategories($ClassObj->Assignments);
        //print_r($Category);
        $TotalPoints += $Category->TotalPointsPossible;
    }
    unset($Category);
    return $TotalPoints;
}

/* @var $ClassObj _Class*/
function SumModifiedCategories(&$ClassObj){
    $TotalPoints = 0;
    foreach($ClassObj->Categories as &$Category)
    {
        $Category->ModifiedTotalPointsEarned = 0;
        $Category->ModifiedTotalPointsPossible = 0;
        foreach($ClassObj->Assignments as $Assignment)
        {
            if(isset($Assignment->ModifiedAssignmentEarnedPoints) && $Assignment->ModifiedAssignmentEarnedPoints>=0 && $Assignment->idCategory == $Category->idCategory){
                if(((isset($Assignment->AssignmentDisabled) && !$Assignment->AssignmentDisabled) || !isset($Assignment->AssignmentDisabled))){ //If assignment disabled flag is set and is not marked as disabled or flag is not set)
                    $Category->ModifiedTotalPointsEarned += $Assignment->ModifiedAssignmentEarnedPoints;
                    $Category->ModifiedTotalPointsPossible += $Assignment->ModifiedAssignmentPossiblePoints;
                }
            }
            else{
                if($Assignment->AssignmentEarnedPoints >= 0 && $Assignment->idCategory == $Category->idCategory) //not -1 / blank
                {
                    $Category->ModifiedTotalPointsEarned += $Assignment->AssignmentEarnedPoints;
                    $Category->ModifiedTotalPointsPossible += $Assignment->AssignmentPossiblePoints;
                }
            }
        }
        $Category->ModifiedPercentage = $Category->ModifiedTotalPointsPossible == 0 ? 1 : round(((double) $Category->ModifiedTotalPointsEarned)/$Category->ModifiedTotalPointsPossible, 4);
        $TotalPoints += $Category->ModifiedTotalPointsPossible;
    }
    unset($Category);
    return $TotalPoints;
}

/* @var $ClassObj _Class */
function PieChartJSON($ClassObj, $TotalPoints, $IsEditedGradesPage){
    $PieChartData = array();
    $CategoryCounter = 1;
    foreach($ClassObj->Categories as $Category)
    {
    	if(isset($IsEditedGradesPage) && $IsEditedGradesPage)
    	{
    		$TotalPointsEarned = $Category->ModifiedTotalPointsEarned;
	    	$TotalPointsPossible = $Category->ModifiedTotalPointsPossible;
	    	$Percentage = $Category->ModifiedPercentage;
    	}else
    	{
    		$TotalPointsEarned = $Category->TotalPointsEarned;
    		$TotalPointsPossible = $Category->TotalPointsPossible;
    		$Percentage = $Category->Percentage;
    	}

    	$data = 0; $missed = 0;
    	if($TotalPointsPossible > 0) //they didn't "miss" any points if there were none to begin with
    	{
    		if(isset($ClassObj->isWeighted) && $ClassObj->isWeighted)
    		{
    			$total = $Category->CategoryWeight;
    		}else
    		{
    			$total = 100 * (double)$TotalPointsPossible / $TotalPoints;
    		}
    		$data = round($total * min(1, $Percentage), 2);
    		$missed = round($total - $data, 2);
    	}

        $DataPoint = array(
            'label'=>$Category->CategoryName, //TODO: Could use abbrv name for better formatting.
            'data'=> $data,
            'color' => Category::$Colors[$CategoryCounter%8][0], //Don't run out of colors with mod
            //'highlightColor'=> Category::$Colors[$CategoryCounter%8][2] //Lighter color for hover
        );
        $CategoryMissed = array(
            'label'=> 'Category Missed',
            'data' =>  $missed,
            'color' => Category::$Colors[$CategoryCounter++%8][1]
        );
        array_push($PieChartData, $DataPoint);
        array_push($PieChartData, $CategoryMissed);
    }
    return $PieChartData;
}