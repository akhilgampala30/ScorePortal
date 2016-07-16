<?php
/**
 * User: Mike
 * Date: 8/5/13
 * Time: 9:29 PM
 *
 * Checks if the login service ID is registered or needs to be registered.
 */
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/paths.php';
require $path['Database.php'];
require $path['OpenID.php'];

$dbConnection = connect();
if(!isset($_GET['ServiceID'])){
    die("No Service ID Set");
}

$ServiceID = $_GET['ServiceID'];
$_SESSION['ServiceID'] = $ServiceID;
$Validate = false;
$UserID = null;

if(!isset($ServiceID)){ //If Invalid Request, Go back to root
    header('Location: ' . '/');
    die();
}

switch($ServiceID){
    case 0:
        //If Google OpenID
        /*$LoginServiceType = GetLoginServiceTypeByTechnology(0, $dbConnection);
        $openid = new LightOpenID($_SERVER['HTTP_HOST']); //TODO: Change this to the proper url later
        $openid->identity = $LoginServiceType->LoginServiceDomain;
        $openid->required = array('namePerson/first', 'namePerson/last', 'contact/email');
        if($openid->validate()){
            $attributes = $openid->getAttributes();
            $id = explode("?id=", $openid->identity);
            $LoginID = $id[1];
            $UserFirstName = $attributes['namePerson/first'];
            $UserLastName = $attributes['namePerson/last'];
            $UserEmail = $attributes['contact/email'];
            $Validate = true;

            $_SESSION['AuthUserFirstName'] = $UserFirstName;
            $_SESSION['AuthUserLastName'] = $UserLastName;
            $_SESSION['AuthUserEmail'] = $UserEmail;
            $_SESSION['LoginID'] = $LoginID;
        }
        else{header('Location: ' . $openid->authUrl());die();} //Redirect to OpenID page.
        */
        
        $UserID = $_GET['UserID'];
        if($UserID!=null) {
            $Validate = true;
        } else {
            $Validate = false;
        }

        break;
}
//Only run when the user has been validated.
if($Validate){
    //print_r($LoginID);
    //echo "<script>alert('".$LoginID."')</script>";
    //$UserID = GetUserIDFromLoginID($LoginID, $dbConnection, $ServiceID);
    //echo $LoginID,'<br/>',$UserID; //DEBUG
    if($UserID==null){
        //User Not Found, Create a new User
        $_SESSION['LoginID'] = $LoginID;
        header('Location: /register');
        exit;
    }
    $_SESSION['UserID'] = $UserID; //Set User ID To Sesh
    require 'UserLoginInit.php';
}
