<?php
/**
 * User: Mike
 * Date: 7/5/13
 * Time: 8:34 PM
 */

include $_SERVER['DOCUMENT_ROOT'].'/paths.php';
include $path['LoadClasses.php'];

require $path['config.php']; //All files should have a config loaded

define('DatabaseIncludeLoaded', true);

//Changed to "absolute" path for includes
require $path['DatabaseGetFunctions.php'];
require $path['DatabaseAddFunctions.php'];
require $path['DatabaseModifyFunctions.php'];
require $path['DatabaseValidateFunctions.php'];

//For statistics
require $path['Statistics.php'];

$Connection = $config['Connection'];
$Database = $config['Database'];
$Username = $config['Username'];
$Password = $config['Password'];

function connect()
{
    try
    {
        global $Connection, $Database, $Username, $Password;
        $db = new PDO("mysql:host={$Connection};dbname={$Database}", $Username, $Password);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(ENV != 'Mike')
            $db->query('SET time_zone = `America/Los_Angeles`'); //Set the timezone to Los Angeles so NOW() is in the correct time zone
        return $db;
    } catch(PDOException $e)
    {
        print "Error on ".__FUNCTION__."!: " . $e->getMessage() . "<br/>";
        $db = null;
        die();
    }
}

//http://mac-blog.org.ua/mysql-php-aes/

function php_aes_encrypt($text, $key) {
    $text = base64_encode($text);
    $key = mysql_aes_key($key);
    $pad_value = 16 - (strlen($text) % 16);
    $text = str_pad($text, (16 * (floor(strlen($text) / 16) + 1)), chr($pad_value));
    return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_DEV_URANDOM));
}

function php_aes_decrypt($text, $key) {
    $key = mysql_aes_key($key);
    $text = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_DEV_URANDOM));
    //TODO "find out what is going on with the assignments"
    return preg_replace('/[^\x20-\x7E]/','',rtrim(base64_decode($text), "\0..\16"));
}

function mysql_aes_key($key) {
    $new_key = str_repeat(chr(0), 16);
    for($i=0,$len=strlen($key);$i<$len;$i++)
    {
        $new_key[$i%16] = $new_key[$i%16] ^ $key[$i];
    }
    return $new_key;
}

function mysql_aes_encrypt($text, $key, PDO $db) {
    $stmt = $db->prepare("SELECT AES_ENCRYPT(?, ?)");
    $stmt->execute(array($text, $key));
    return $stmt->fetchColumn(0);
}

function mysql_aes_decrypt($text, $key, PDO $db) {
    $stmt = $db->prepare("SELECT AES_DECRYPT(?, ?)");
    $stmt->execute(array($text, $key));
    return $stmt->fetchColumn(0);
}

function dieJSON($code, $msg){
    $Return['code'] = $code;
    $Return['msg'] = $msg;
    die(json_encode($Return));
}

?>