<?php

//Hide true population size
function OutputRange($int){
    $intervals = array(4,14,32,45);
    $elements = count($intervals);
    for($i=0; $i<$elements-1; $i++){
        if($intervals[$i]<$int && $int<=$intervals[$i+1]){
            return $intervals[$i].'-'.$intervals[$i+1];
        }
    }
    return $intervals[$elements-1].'+';
}

function roundToNearest($n,$x=5) {
    return round(($n)/$x)*$x;
}

function GetTValue($df){ //This is 90% Confidence Interval
    //http://easycalculation.com/statistics/t-distribution-critical-value-table.php
    $TCriticalValues90 = array(6.3138, 2.92, 2.3534, 2.1319, 2.015, 1.9432, 1.8946, 1.8595, 1.8331, 1.8124, 1.7959, 1.7823, 1.7709, 1.7613, 1.753, 1.7459, 1.7396, 1.7341, 1.7291, 1.7247, 1.7207,
    1.7172, 1.7139, 1.7109, 1.7081, 1.7056, 1.7033, 1.7011, 1.6991, 1.6973, 1.6955, 1.6939, 1.6924, 1.6909, 1.6896, 1.6883, 1.6871, 1.6859, 1.6849, 1.6839, 1.6829, 1.682, 1.6811, 1.6802, 1.6794,
    1.6787, 1.6779, 1.6772, 1.6766, 1.6759, 1.6753, 1.6747, 1.6741, 1.6736, 1.673, 1.6725, 1.672, 1.6715, 1.6711, 1.6706, 1.6702, 1.6698, 1.6694, 1.669, 1.6686, 1.6683, 1.6679, 1.6676, 1.6673,
    1.6669, 1.6666, 1.6663, 1.666, 1.6657, 1.6654, 1.6652, 1.6649, 1.6646, 1.6644, 1.6641, 1.6639, 1.6636, 1.6634, 1.6632, 1.663, 1.6628, 1.6626, 1.6623, 1.6622, 1.662, 1.6618, 1.6616, 1.6614, 1.6612,
    1.661, 1.6609, 1.6607, 1.6606, 1.6604, 1.6602, 1.6601, 1.6599, 1.6598, 1.6596, 1.6595, 1.6593, 1.6592, 1.6591, 1.6589, 1.6588, 1.6587, 1.6586, 1.6585, 1.6583, 1.6582, 1.6581, 1.658, 1.6579, 1.6578,
    1.6577, 1.6575, 1.6574, 1.6573, 1.6572, 1.6571, 1.657, 1.657, 1.6568, 1.6568, 1.6567, 1.6566, 1.6565, 1.6564, 1.6563, 1.6562, 1.6561, 1.6561, 1.656, 1.6559, 1.6558, 1.6557, 1.6557, 1.6556, 1.6555,
    1.6554, 1.6554, 1.6553, 1.6552, 1.6551, 1.6551, 1.655, 1.6549, 1.6549, 1.6548, 1.6547, 1.6547, 1.6546, 1.6546, 1.6545, 1.6544, 1.6544, 1.6543, 1.6543, 1.6542, 1.6542, 1.6541, 1.654, 1.654, 1.6539,
    1.6539, 1.6538, 1.6537, 1.6537, 1.6537, 1.6536, 1.6536, 1.6535, 1.6535, 1.6534, 1.6534, 1.6533, 1.6533, 1.6532, 1.6532, 1.6531, 1.6531, 1.6531, 1.653, 1.6529, 1.6529, 1.6529, 1.6528, 1.6528, 1.6528,
    1.6527, 1.6527, 1.6526, 1.6526, 1.6525, 1.6525); //df = n-1

    return $TCriticalValues90[$df-1];
}

/**
 * @param $values float[]
 * @return array
 */
function MeanAndSD($values){
    $sum = array_sum($values);
    $n = count($values);
    $mean = $sum/$n;
    $sdSum = 0;
    foreach($values as $value){
        $sdSum+=($value-$mean)*($value-$mean);
    }
    $sd = sqrt($sdSum/($n-1));
    return array($mean, $sd, $n);
}

/* $values float[][]*/
function WeightedMeanAndSD($values){
    $mean = 0;
    $weightsum = 0;
    $weightcount =  count($values);
    if($weightcount <= 1){ //If there's only 1 person in the population, there's no point in counting anything
        return array(($weightcount==0 ? 0 : $values[0][1]), 0, $weightcount);
    }
    foreach($values as $value){
        if($value[0] == 0){ //Course has no assignment but has a grade ex. PE
            $mean += $value[1];
            $weightsum += 1;
        }else{
            $mean += $value[0]*$value[1];
            $weightsum += $value[0];
        }
    }
    $mean /= $weightsum;

    $sd = 0;
    foreach($values as $value){
        $sd+=$value[0]*($value[1]-$mean)*($value[1]-$mean);
    }
    $sd /= (($weightcount-1)*$weightsum)/$weightcount;
    $sd = sqrt($sd);
    return array($mean, $sd, $weightcount);
}

function ConfInterval($mean, $sd, $n){
    if($n>200){return false;} //TODO: Just use Z Values
    $dev = GetTValue($n-1)*($sd/sqrt($n));
    return array($mean-$dev, $mean+$dev);
}

function CalculatePercentile($target, $mean, $sd, $samplesize){ //90% CI
    if($sd==0){ return .99;}
    $zscore = ($target-$mean)/($sd);
    return cumnormdist($zscore);
}

function cumnormdist($x)
{
    $b1 =  0.319381530;
    $b2 = -0.356563782;
    $b3 =  1.781477937;
    $b4 = -1.821255978;
    $b5 =  1.330274429;
    $p  =  0.2316419;
    $c  =  0.39894228;

    if($x >= 0.0) {
        $t = 1.0 / ( 1.0 + $p * $x );
        return (1.0 - $c * exp( -$x * $x / 2.0 ) * $t *
            ( $t *( $t * ( $t * ( $t * $b5 + $b4 ) + $b3 ) + $b2 ) + $b1 ));
    }
    else {
        $t = 1.0 / ( 1.0 - $p * $x );
        return ( $c * exp( -$x * $x / 2.0 ) * $t *
            ( $t *( $t * ( $t * ( $t * $b5 + $b4 ) + $b3 ) + $b2 ) + $b1 ));
    }
}

/***************************************************************************
 *                                inverse_ncdf.php
 *                            -------------------
 *   begin                : Friday, January 16, 2004
 *   copyright            : (C) 2004 Michael Nickerson
 *   email                : nickersonm@yahoo.com
 *
 ***************************************************************************/

function inverse_ncdf($p) {
    //Inverse ncdf approximation by Peter John Acklam, implementation adapted to
    //PHP by Michael Nickerson, using Dr. Thomas Ziegler's C implementation as
    //a guide.  http://home.online.no/~pjacklam/notes/invnorm/index.html
    //I have not checked the accuracy of this implementation.  Be aware that PHP
    //will truncate the coeficcients to 14 digits.

    //You have permission to use and distribute this function freely for
    //whatever purpose you want, but please show common courtesy and give credit
    //where credit is due.

    //Input paramater is $p - probability - where 0 < p < 1.

    //Coefficients in rational approximations
    $a = array(1 => -3.969683028665376e+01, 2 => 2.209460984245205e+02,
        3 => -2.759285104469687e+02, 4 => 1.383577518672690e+02,
        5 => -3.066479806614716e+01, 6 => 2.506628277459239e+00);

    $b = array(1 => -5.447609879822406e+01, 2 => 1.615858368580409e+02,
        3 => -1.556989798598866e+02, 4 => 6.680131188771972e+01,
        5 => -1.328068155288572e+01);

    $c = array(1 => -7.784894002430293e-03, 2 => -3.223964580411365e-01,
        3 => -2.400758277161838e+00, 4 => -2.549732539343734e+00,
        5 => 4.374664141464968e+00, 6 => 2.938163982698783e+00);

    $d = array(1 => 7.784695709041462e-03, 2 => 3.224671290700398e-01,
        3 => 2.445134137142996e+00, 4 => 3.754408661907416e+00);

    //Define break-points.
    $p_low =  0.02425;									 //Use lower region approx. below this
    $p_high = 1 - $p_low;								 //Use upper region approx. above this

    //Define/list variables (doesn't really need a definition)
    //$p (probability), $sigma (std. deviation), and $mu (mean) are user inputs
    $q = NULL; $x = NULL; $y = NULL; $r = NULL;

    //Rational approximation for lower region.
    if (0 < $p && $p < $p_low) {
        $q = sqrt(-2 * log($p));
        $x = ((((($c[1] * $q + $c[2]) * $q + $c[3]) * $q + $c[4]) * $q + $c[5]) *
            $q + $c[6]) / (((($d[1] * $q + $d[2]) * $q + $d[3]) * $q + $d[4]) *
            $q + 1);
    }

    //Rational approximation for central region.
    elseif ($p_low <= $p && $p <= $p_high) {
        $q = $p - 0.5;
        $r = $q * $q;
        $x = ((((($a[1] * $r + $a[2]) * $r + $a[3]) * $r + $a[4]) * $r + $a[5]) *
            $r + $a[6]) * $q / ((((($b[1] * $r + $b[2]) * $r + $b[3]) * $r +
            $b[4]) * $r + $b[5]) * $r + 1);
    }

    //Rational approximation for upper region.
    elseif ($p_high < $p && $p < 1) {
        $q = sqrt(-2 * log(1 - $p));
        $x = -((((($c[1] * $q + $c[2]) * $q + $c[3]) * $q + $c[4]) * $q +
            $c[5]) * $q + $c[6]) / (((($d[1] * $q + $d[2]) * $q + $d[3]) *
            $q + $d[4]) * $q + 1);
    }

    //If 0 < p < 1, return a null value
    else {
        $x = NULL;
    }

    return $x;
    //END inverse ncdf implementation.
}


?>