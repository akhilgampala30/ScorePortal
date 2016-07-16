<?php
/**
 * User: Mike
 * Date: 12/31/13
 * Time: 2:49 PM
 */

define('ENV', 'Mike'); // Change this to 'Production' in prod

$config['AssignmentCutOff'] = 5;
$config['ClassAverageHistoryCutOff'] = 5;

//Database configuration
switch (ENV) {
    case 'Mike':
        $config['Connection'] = 'localhost';
        $config['Database'] = 'spdev_scoreportalunity';
        $config['Username'] = 'root';
        $config['Password'] = 'password';
        break;
    case 'Production':
        $config['Connection'] = 'localhost';
        $config['Database'] = 'db_name_here';
        $config['Username'] = 'mysql_username_here';
        $config['Password'] = 'mysql_user_password_here (super secure)';
        break;
}

define ('S1', 'insert an encryption key here'); //fName
define ('S2', 'anotha one'); //Di
define ('S3', 'make sure these are really fucking secure btw'); //CSTRING
define ('S4', 'something like 64 characters or something'); //UfName
