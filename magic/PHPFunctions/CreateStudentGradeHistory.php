<?php
/**
 * User: Mike
 * Date: 12/26/13
 * Time: 1:07 PM
 */

/**
 * Gets the average course history
 *
 * @param $ClassObj _Class
 * @param $cutoff int
 * @return array
 */
function GetClassAverageHistory($ClassObj, $assignments, $cutoff=5){
    usort($assignments, 'SortGradeHistory');
    $TableHistory = $ClassObj->ClassAverage;
    if($TableHistory === false) //If not set
        return false;
    if($ClassObj->Population < $cutoff){ //If there are less than 5 people, the statistics will be inaccurate/revealing
        //return false;
    }
    //TODO: Only once per day
    $CalculatedGradeHistory = array(); //Hold Array(Timestamp, Grade) Data
    for($i=0; $i<count($assignments); $i++){
        $CurrentTimestamp = $assignments[$i]['AssignmentDate'];
        $ClassAverageBuffer = array();
        foreach($TableHistory as $row){
            if($row['NumericGrade'] == 0) //Don't count 0's
                continue;
            if(round(strtotime($row['RecordedTime'])/86400) < round($CurrentTimestamp/1000/86400) &&
                ($i-1==-1 || round(strtotime($row['RecordedTime'])/86400) > round($assignments[$i-1]['AssignmentDate']/1000/86400))){
                $ClassAverageBuffer[] = $row['NumericGrade'];
            }elseif(round($CurrentTimestamp/1000/86400) < round(strtotime($row['RecordedTime'])/86400)){ //Assignment is before current time
                if(count($ClassAverageBuffer)>0){
                    $CalculatedGradeHistory[] = array($CurrentTimestamp, round(array_sum($ClassAverageBuffer)/count($ClassAverageBuffer),2));
                }
                break; //Move on to the next assignment
            }
        }
    }
    return $CalculatedGradeHistory;
}

function SortGradeHistory($a,$b){
    return $a["AssignmentDate"]/1000 - $b["AssignmentDate"]/1000;
}

/**
 * Pulls grade history from ClassObj
 *
 * @param $ClassObj _Class
 * @return boolean, array(array(timestamp, grade))
 */
function GetRecordedGradeHistory($ClassObj){
    $TableHistory = $ClassObj->GradeHistory;
    if($TableHistory === false) //If not set
        return false;

    $CalculatedGradeHistory = array(); //Hold Array(Timestamp, Grade) Data

    $PrevTimeStamp = 0;
    foreach($TableHistory as $row){
        if($row['NumericGrade'] == 0)
            continue;
        $CurrentTimestamp = (int)strtotime($row['RecordedTime'])*1000;
        $LastNumericGrade = end($CalculatedGradeHistory);
        if(date('Ymd', $PrevTimeStamp/1000) == date('Ymd', $CurrentTimestamp/1000)
            && abs($row['NumericGrade']-$LastNumericGrade[1]) < 1){ //Skip the same update on the same day and percent change is less than 1 to reduce graph clutter
            continue;
        }
        $PrevTimeStamp = $CurrentTimestamp;
        $CalculatedGradeHistory[] = array($CurrentTimestamp, round($row['NumericGrade'],2));
    }

    return $CalculatedGradeHistory;
}

/**
 * Returns an array of assignments[]=array(Timestamp,Name,Percent)
 *
 * @param $ClassObj
 * @param $EndDate
 * @param int $StartDate
 * @return array
 */
function ReturnAssignmentArray($ClassObj, $EndDate, $StartDate = 0){
    $CalculatedAssignmentHistory = array(); //Hold Array[Date]=AssignmentLabel Data

    $EndDateTimeStamp = $EndDate - ($EndDate%86400);
    $StartDateTimeStamp = $StartDate - ($StartDate%86400);

    $AssignmentsPerDay = array(); //All assignments in the day
    foreach($ClassObj->Assignments as $Assignment){
    	$AssignmentDate = $Assignment->AssignmentDate - ($Assignment->AssignmentDate % 86400);
        if(!isset($Assignment->AssignmentEarnedPoints) || $Assignment->AssignmentEarnedPoints < 0)
            continue; //Don't count empty assignments
        if($AssignmentDate > $EndDateTimeStamp || $AssignmentDate < $StartDateTimeStamp){
            continue; //If not in date range, continue the loop
        }
        $CalculatedAssignmentHistory[] =  array(
            'AssignmentDate' => $Assignment->AssignmentDate*1000,
            'AssignmentName'=> $Assignment->AssignmentName,
            'AssignmentPercent' => $Assignment->GetPercentage()
        );
    }

    return $CalculatedAssignmentHistory;
}

function CalculatedGradeHistory($ClassObj, $EndDate, $StartDate = 0, $weighted = false){
	usort($ClassObj->Assignments,'AssignmentDateCompareAsc'); //Sort assignments so it's oldest first

	$CalculatedGradeHistory = array(); //Hold Array(Timestamp, Grade) Data

	$EndDateTimeStamp = $EndDate - ($EndDate % 86400);
	$StartDateTimeStamp = $StartDate - ($StartDate % 86400);

	$CategoryHistories = array(); //Categories for the class, updating to use arrays instead of classes
	foreach($ClassObj->Categories as $Category){
		$CategoryWeight = isset($Category->CategoryWeight) ? $Category->CategoryWeight : 0; //Check if weight is set
		$newCategoryHistory = array(
				'TotalEarnedPoints'=> 0,
				'TotalPossiblePoints'=> 0,
				'CategoryWeight' => $CategoryWeight
		);
		$CategoryHistories[$Category->idCategory] = $newCategoryHistory;
	}

	$PrevDateTimestamp = $ClassObj->Assignments[0]->AssignmentDate; //Initialize first date as the first assignment date
	foreach($ClassObj->Assignments as $Assignment){
		$AssignmentDate = $Assignment->AssignmentDate - ($Assignment->AssignmentDate % 86400);
		$AssignmentEarnedPoints = $Assignment->AssignmentEarnedPoints;
		if(!isset($AssignmentEarnedPoints) || $AssignmentEarnedPoints < 0 //Don't count empty assignments
			|| $AssignmentDate > $EndDateTimeStamp || $AssignmentDate < $StartDateTimeStamp) //If not in date range, skip assignment
			continue;

		if($AssignmentDate > $PrevDateTimestamp){ //If it's a new day :D
			$CurrentGrade = 100 * CalculateCurrentGrade($CategoryHistories, $weighted);
			if($CurrentGrade >= 0) $CalculatedGradeHistory[] = array($PrevDateTimestamp * 1000, round($CurrentGrade, 2));
			$PrevDateTimestamp = $Assignment->AssignmentDate; //Advance previous day timestamp to current timestamp
		}

		//Add to the category, don't reset cause we just keep building.
		$AssignmentPossiblePoints = $Assignment->AssignmentPossiblePoints;
		$CategoryHistories[$Assignment->idCategory]['TotalEarnedPoints'] += $AssignmentEarnedPoints;
		$CategoryHistories[$Assignment->idCategory]['TotalPossiblePoints'] += $AssignmentPossiblePoints;
	}
	//Account for last assignment
	$CurrentGrade = 100 * CalculateCurrentGrade($CategoryHistories, $weighted);
	if($CurrentGrade >= 0) $CalculatedGradeHistory[] = array($PrevDateTimestamp * 1000, round($CurrentGrade, 2));
	$CalculatedGradeHistory[] = array((time() - (time() % 86400)) * 1000, round($CurrentGrade, 2)); //Today's date

	return $CalculatedGradeHistory;
}

function CalculateCurrentGrade($CategoryHistories, $weighted)
{
	if($weighted)
	{
		$CurrentGrade = 0;
		$SumCategoryWeights = 0;
		foreach($CategoryHistories as $CategoryHistory){
			if($CategoryHistory['TotalPossiblePoints'] > 0){
				$CurrentGrade += round($CategoryHistory['CategoryWeight'] * 1.0 * ($CategoryHistory['TotalEarnedPoints']/$CategoryHistory['TotalPossiblePoints']), 4);
				$SumCategoryWeights += $CategoryHistory['CategoryWeight'];
			}
		}
		if($SumCategoryWeights == 0) return -1;
		return $CurrentGrade / $SumCategoryWeights;
	}else
	{
		$SumEarnedPoints = 0;
		$SumPossiblePoints = 0;
		foreach($CategoryHistories as $CategoryHistory){
			//allow categories to have xx/0 since they aren't weighted
			$SumEarnedPoints += $CategoryHistory['TotalEarnedPoints'];
			$SumPossiblePoints += $CategoryHistory['TotalPossiblePoints'];
		}
		if($SumPossiblePoints == 0) return -1;
		return (double)$SumEarnedPoints / $SumPossiblePoints;
	}
}

function AssignmentString( $AssignmentNames){
    $Output='';
    foreach($AssignmentNames as $AssignmentName){
        $Output .= $AssignmentName['AssignmentName'].': '.$AssignmentName['AssignmentPercent']*100 .'%<br>'; //TODO: Trim last BR
    }
    return $Output;
}

function AssignmentDateCompareDesc(Assignment $a, Assignment $b){ //Newest First
    return $b->AssignmentDate - $a->AssignmentDate;
}
function AssignmentDateCompareAsc(Assignment $a, Assignment $b){ //Oldest First
    return $a->AssignmentDate - $b->AssignmentDate;
}