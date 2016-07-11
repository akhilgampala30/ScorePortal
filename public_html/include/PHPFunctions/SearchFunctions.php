<?php
/**
 * User: Jacky
 * Date: 1/1/14
 * Time: 4:52 AM
 */
include_once $_SERVER['DOCUMENT_ROOT'] . '/paths.php';
require_once $path['LoadClasses.php'];
session_start();

//shamelessly stolen from http://stackoverflow.com/a/6243331
function html_entity_decode_numeric($string, $quote_style = ENT_COMPAT, $charset = "utf-8")
{
	$string = html_entity_decode($string, $quote_style, $charset);
	$string = preg_replace_callback('~&#x([0-9a-fA-F]+);~i', "chr_utf8_callback", $string);
	$string = preg_replace('~&#([0-9]+);~e', 'chr_utf8("\\1")', $string);
	return $string;
}
function chr_utf8_callback($matches)
{
	return chr_utf8(hexdec($matches[1]));
}
function chr_utf8($num)
{
	if ($num < 128) return chr($num);
	if ($num < 2048) return chr(($num >> 6) + 192) . chr(($num & 63) + 128);
	if ($num < 65536) return chr(($num >> 12) + 224) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
	if ($num < 2097152) return chr(($num >> 18) + 240) . chr((($num >> 12) & 63) + 128) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
	return '';
}

$returnArray = array();
$StudentClasses = $_SESSION['StudentObj']->Classes;

if ($_POST['opt'] == 1) {
    foreach ($StudentClasses as $tempClass) {
        $courseName = $tempClass->Course->CourseName;
        $returnArray[$courseName] = $courseName;

        $assignments = $tempClass->Assignments;
        foreach ($assignments as $tempAssignment) {
            $returnArray[html_entity_decode_numeric($tempAssignment->AssignmentName)] = $courseName;
        }
    }
} elseif ($_POST['opt'] == 0) {
    foreach ($StudentClasses as $tempClass) {
        $classID = $tempClass->idClasses;
        $courseName = $tempClass->Course->CourseName;
        $returnArray[$classID] = $courseName;
    }
}

echo json_encode($returnArray);

?>
