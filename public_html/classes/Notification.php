<?php
/**

 * User: Mike
 * Date: 7/5/13
 * Time: 2:53 PM

 */

class Notification {
    //IDs
    public $idNotifications;//only on retrieval
    public $idStudents;
    public $idNotificationType;

    //Attrib
    public $NotificationURL;
    public $Notification;
    public $Time;//only on retrieval

    //Objs
    /* @var $NotificationType NotificationType*/
    public $NotificationType;//only on retrieval
    //public $Student;//only on retrieval

    //Sort Desc
    public static function OrderNotificationTime($a, $b){
        return strtotime($b->Time)-strtotime($a->Time);
    }

}