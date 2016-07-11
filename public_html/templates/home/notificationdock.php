<?php
/**
 * User: Mike
 * Date: 7/25/13
 * Time: 7:48 PM
 */
?>
<div id="NotificationDock">
    <?php

    /* @var $GlobalStudentObject Student */
    $Notifications = $GlobalStudentObject->Notifications;
    usort($Notifications, array('Notification','OrderNotificationTime'));
    $Notifications = array_slice($Notifications, 0, 100); //Display only the first 100 notifications
    foreach($Notifications as $Notification){
        ?>
        <div class="NotificationItem">
            <img src="<?php echo $Notification->NotificationType->ImagePath; ?>">
            <?php echo $Notification->Notification; ?>
        </div>
        <?php
    }
    ?>
</div>