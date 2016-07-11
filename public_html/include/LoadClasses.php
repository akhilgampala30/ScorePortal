<?php
/**
 * User: Mike
 * Date: 11/28/13
 * Time: 3:55 PM
 */
/**@define "$documentRoot" "E:/Users/Mike/Dropbox/PhpstormProjects/ScorePortalUnity"**/
//$_SERVER['DOCUMENT_ROOT'] = "E:/Users/Mike/Dropbox/PhpstormProjects/ScorePortalUnity/";
include $_SERVER['DOCUMENT_ROOT'].'/paths.php';

include $path['ClassesDirectory'].'_Class.php';
function __autoload($class_name) {
    global $path;
    include $path['ClassesDirectory']. $class_name . '.php';
}