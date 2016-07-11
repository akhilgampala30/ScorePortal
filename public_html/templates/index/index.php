<?php
/**
 * User: Mike
 * Date: 7/25/13
 * Time: 12:43 PM
 */
require('header.php');

//Insert Switch Here
switch ($PageName) {
    case 'register':
        require($_SERVER['DOCUMENT_ROOT'] . '/pages/register/register.php');
        break;
    case 'about':
        require($_SERVER['DOCUMENT_ROOT'] . '/pages/about/about.php');
        break;
    case 'tos':
        require($_SERVER['DOCUMENT_ROOT'] . '/pages/tos/tos.php');
        break;
    case 'faq':
        require($_SERVER['DOCUMENT_ROOT'] . '/pages/faq/faq.php');
        break;
    case 'error':
        require($_SERVER['DOCUMENT_ROOT'] . '/pages/error/error.php');
        break;
    default:
    case 'index':
        require($_SERVER['DOCUMENT_ROOT'] . '/pages/index/index.php');
        break;

}
//require($_SERVER['DOCUMENT_ROOT'].'/pages/index/index.php');
//require('/pages/register/register.php');

require('footer.php');