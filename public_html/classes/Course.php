<?php
/**

 * User: Mike
 * Date: 7/5/13
 * Time: 2:28 PM

 */

class Course {
    public $idSchools;
    public $idGlobalCourse;
    public $idCourses; //only on retrieval

    //Course Information
    public $CourseName;
    public $CourseDescription;
    public $CourseAttributes; //Array of CourseAttribute
    public $Population;

    //Objects
    public $GlobalCourse;
    /**
     * Object might not be set
     * @var School
     */
    public $School;
}