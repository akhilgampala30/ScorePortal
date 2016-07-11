<?php
/**
 * User: Mike
 * Date: 7/5/13
 * Time: 3:08 PM
 */

class Achievement {
    //IDs
    public $idAchievements;//only on retrieval

    //General Attrib
    public $AchievementName;
    public $AchievementDescription;
    public $AchievementReward;

    //User Unique Attrib
    public $TimeEarned;//only earned achievements
}