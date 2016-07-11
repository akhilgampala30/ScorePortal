<?php
/**
 * User: Mike
 * Date: 12/28/13
 * Time: 8:17 PM
 */
session_start();
session_unset(); //Clear Session
session_destroy(); //Destroy session data
header('Location: /'); //Send them on to home
die;