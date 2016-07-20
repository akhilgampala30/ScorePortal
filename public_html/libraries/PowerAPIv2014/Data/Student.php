<?php

namespace PowerAPI\Data;

/** Handles fetching the transcript and holding onto its data.
 * @property array $sections contains the student's sections
 * @property arrray $information contains the student's information
 */
class Student extends BaseObject
{
    /** URL for the PowerSchool server
     * @var string
     */
    private $soap_url;

    /** session object as returned by PowerSchool
     * @var array
     */
    private $soap_session;


    /**
     * Attempt to authenticate against the server
     * @param string $soap_url URL for the PowerSchool server
     * @param string $soap_session session object as returned by PowerSchool
     * @param boolean $populate should the transcript be immediately populated?
     */
    public function __construct($soap_url, $soap_session, $populate)
    {
        $this->soap_url = $soap_url;
        $this->soap_session = $soap_session;

        $this->details['information'] = Array();
        $this->details['sections'] = Array();

        if ($populate) {
            $this->populate();
        }
    }

    /**
     * Pull the authenticated user's transcript from the server and parses it.
     * @return null
     */
    public function populate()
    {
        $transcript = $this->fetchTranscript();
        /*$assignmentCategoreis = $this->fetchAssignmentCategories();
        $assignmentScores = $this->fetchAssignmentScores();
        $finalGrades = $this->fetchFinalGrades();
        $reportingTerms = $this->fetchReportingTerms();
        //$teachers = $this->fetchTeachers();*/
        $this->parseTranscript($transcript/*, $assignmentCategoreis, $assignmentScores, $finalGrades, $reportingTerms, $teachers*/);
    }

    /**
     * Fetches the user's transcript from the server and returns it.
     * @return array user's transcript as returned by PowerSchool
     */
    public function fetchTranscript()
    {
        $client = new \SoapClient(null, Array(
            'uri' => 'http://publicportal.rest.powerschool.pearson.com/xsd',
            'location' => $this->soap_url.'pearson-rest/services/PublicPortalServiceJSON',
            'login' => 'pearson',
            'password' => 'm0bApP5',
            'use' => SOAP_LITERAL
        ));

        // This is a workaround for SoapClient not having a WSDL to go off of.
        // Passing everything as an object or as an associative array causes
        // the parameters to not be correctly interpreted by PowerSchool.
        $parameters = Array(
            'userSessionVO' => (object) Array(
                'userId' => $this->soap_session->userId,
                'serviceTicket' => $this->soap_session->serviceTicket,
                'serverInfo' => (object) Array(
                    'apiVersion' => $this->soap_session->serverInfo->apiVersion
                ),
                'serverCurrentTime' => '2012-12-26T21:47:23.792Z', # I really don't know.
                'userType' => '2'
            ),
            'studentIDs' => $this->soap_session->studentIDs,
            'qil' => (object) Array(
                'includes' => '1'
            )
        );

        //var_dump($transcript);

        $transcript = $client->__soapCall('getStudentData', $parameters);
        //var_dump($transcript);

        return $transcript;
    }

    /**
     * Fetches the user's assignment categories from the server and returns it
     * @return array user's assignment categories as returned by PowerSSchool
     */
    public function fetchAssignmentCategories() {
        $client = new \SoapClient(null, Array(
            'uri' => 'http://assignment.rest.powerschool.pearson.com/xsd',
            'location' => $this->soap_url.'pearson-rest/services/AssignmentServiceJSON',
            'login' => 'pearson',
            'password' => 'm0bApP5',
            'use' => SOAP_LITERAL
        ));

        // This is a workaround for SoapClient not having a WSDL to go off of.
        // Passing everything as an object or as an associative array causes
        // the parameters to not be correctly interpreted by PowerSchool.
        $parameters = Array(
            'userSessionVO' => (object) Array(
                'userId' => $this->soap_session->userId,
                'serviceTicket' => $this->soap_session->serviceTicket,
                'serverInfo' => (object) Array(
                    'apiVersion' => $this->soap_session->serverInfo->apiVersion
                ),
                'serverCurrentTime' => '2012-12-26T21:47:23.792Z', # I really don't know.
                'userType' => '2'
            ),
            'studentIDs' => $this->soap_session->studentIDs,
            'qil' => (object) Array(
                'includes' => '1'
            )
        );

        $categories = $client->__soapCall('getAssignmentCategories', $parameters);
        //var_dump($categories);

        return $categories;
    }
    
    /**
     * Fetches the user's assignment scores from the server and returns it
     * @return array user's assignment scores as returned by PowerSSchool
     */
    public function fetchAssignmentScores() {
        $client = new \SoapClient(null, Array(
            'uri' => 'http://assignment.rest.powerschool.pearson.com/xsd',
            'location' => $this->soap_url.'pearson-rest/services/AssignmentServiceJSON',
            'login' => 'pearson',
            'password' => 'm0bApP5',
            'use' => SOAP_LITERAL
        ));

        // This is a workaround for SoapClient not having a WSDL to go off of.
        // Passing everything as an object or as an associative array causes
        // the parameters to not be correctly interpreted by PowerSchool.
        $parameters = Array(
            'userSessionVO' => (object) Array(
                'userId' => $this->soap_session->userId,
                'serviceTicket' => $this->soap_session->serviceTicket,
                'serverInfo' => (object) Array(
                    'apiVersion' => $this->soap_session->serverInfo->apiVersion
                ),
                'serverCurrentTime' => '2012-12-26T21:47:23.792Z', # I really don't know.
                'userType' => '2'
            ),
            'studentIDs' => $this->soap_session->studentIDs,
            'qil' => (object) Array(
                'includes' => '1'
            )
        );

        $scores = $client->__soapCall('getAssignmentScores', $parameters);
        var_dump($scores);

        return $scores;
    }

    /**
     * Fetches the user's final grades from the server and returns it
     * @return array user's final grades as returned by PowerSSchool
     */
    public function fetchFinalGrades() {
        $client = new \SoapClient(null, Array(
            'uri' => 'http://section.rest.powerschool.pearson.com/xsd',
            'location' => $this->soap_url.'pearson-rest/services/SectionServiceJSON',
            'login' => 'pearson',
            'password' => 'm0bApP5',
            'use' => SOAP_LITERAL
        ));

        // This is a workaround for SoapClient not having a WSDL to go off of.
        // Passing everything as an object or as an associative array causes
        // the parameters to not be correctly interpreted by PowerSchool.
        $parameters = Array(
            'userSessionVO' => (object) Array(
                'userId' => $this->soap_session->userId,
                'serviceTicket' => $this->soap_session->serviceTicket,
                'serverInfo' => (object) Array(
                    'apiVersion' => $this->soap_session->serverInfo->apiVersion
                ),
                'serverCurrentTime' => '2012-12-26T21:47:23.792Z', # I really don't know.
                'userType' => '2'
            ),
            'studentIDs' => $this->soap_session->studentIDs,
            'qil' => (object) Array(
                'includes' => '1'
            )
        );

        $grades = $client->__soapCall('getFinalGradesForSection', $parameters);
        var_dump($grades);

        return $grades;
    }

    /**
     * Fetches the user's final grades from the server and returns it
     * @return array user's final grades as returned by PowerSSchool
     */
    public function fetchReportingTerms() {
        $client = new \SoapClient(null, Array(
            'uri' => 'http://finalgradesetup.rest.powerschool.pearson.com/xsd',
            'location' => $this->soap_url.'pearson-rest/services/FinalGradeSetupJSON',
            'login' => 'pearson',
            'password' => 'm0bApP5',
            'use' => SOAP_LITERAL
        ));

        // This is a workaround for SoapClient not having a WSDL to go off of.
        // Passing everything as an object or as an associative array causes
        // the parameters to not be correctly interpreted by PowerSchool.
        $parameters = Array(
            'userSessionVO' => (object) Array(
                'userId' => $this->soap_session->userId,
                'serviceTicket' => $this->soap_session->serviceTicket,
                'serverInfo' => (object) Array(
                    'apiVersion' => $this->soap_session->serverInfo->apiVersion
                ),
                'serverCurrentTime' => '2012-12-26T21:47:23.792Z', # I really don't know.
                'userType' => '2'
            ),
            'studentIDs' => $this->soap_session->studentIDs,
            'qil' => (object) Array(
                'includes' => '1'
            )
        );

        $terms = $client->__soapCall('getReportingTermsForSection', $parameters);
        var_dump($terms);

        return $terms;
    }

    /**
     * Fetches the user's final grades from the server and returns it
     * @return array user's final grades as returned by PowerSSchool
     */
    public function fetchTeachers() {
        $client = new \SoapClient(null, Array(
            'uri' => 'http://finalgradesetup.rest.powerschool.pearson.com/xsd',
            'location' => $this->soap_url.'pearson-rest/services/FinalGradeSetupJSON',
            'login' => 'pearson',
            'password' => 'm0bApP5',
            'use' => SOAP_LITERAL
        ));

        // This is a workaround for SoapClient not having a WSDL to go off of.
        // Passing everything as an object or as an associative array causes
        // the parameters to not be correctly interpreted by PowerSchool.
        $parameters = Array(
            'userSessionVO' => (object) Array(
                'userId' => $this->soap_session->userId,
                'serviceTicket' => $this->soap_session->serviceTicket,
                'serverInfo' => (object) Array(
                    'apiVersion' => $this->soap_session->serverInfo->apiVersion
                ),
                'serverCurrentTime' => '2012-12-26T21:47:23.792Z', # I really don't know.
                'userType' => '2'
            ),
            'studentIDs' => $this->soap_session->studentIDs,
            'qil' => (object) Array(
                'includes' => '1'
            )
        );

        $terms = $client->__soapCall('getReportingTermsForSection', $parameters);
        var_dump($terms);

        return $terms;
    }

    /**
     * Parses the passed transcript and populates $this with its contents.
     * @param object $transcript transcript from fetchTranscript()
     * @return void
     */
    public function parseTranscript($transcript/*, $assignmentCategories, $assignmentScores, $finalGrades, $reportingTerms, $teachers = null*/)
    {
        $studentData = $transcript->studentDataVOs;

        print_r($transcript);

#object(stdClass) 16 (11) { ["attendanceCodes"]=> array(15), ["citizenCodes"]=> array(4), ["feeBalance"]=> NULL ["notificationSettingsVO"]=> NULL ["periods"]=> array(8), ["schools"]=> object(stdClass)#44 (20), ["student"]=> object(stdClass)#46 (14) { ["currentGPA"]=> string(0) "" ["currentMealBalance"]=> string(3) "0.0" ["currentTerm"]=> string(2) "S2" ["dcid"]=> string(5) "20104" ["dob"]=> string(24) "1999-08-12T07:00:00.000Z" ["ethnicity"]=> string(3) "205" ["firstName"]=> string(5) "Akhil" ["gender"]=> string(1) "M" ["gradeLevel"]=> string(2) "12" ["id"]=> string(5) "20104" ["lastName"]=> string(7) "Gampala" ["middleName"]=> NULL ["photoDate"]=> string(24) "2016-02-13T09:32:29.870Z" ["startingMealBalance"]=> string(3) "0.0" } ["studentDcid"]=> string(5) "20104" ["studentId"]=> string(5) "20104" ["terms"]=> array(3) } [""]=> string(4) "2017" } 


#stdClass Object ( [courseRequestRulesVO] => [studentDataVOs] => stdClass Object ( [attendanceCodes] => Array ( [0] => stdClass Object ( [] => [] => 1 [] => Present  => 2465 [] => 1 [] => 1 [] => 2017 ) [1] => stdClass Object ( [] => A [] => 2 [] => Unverified Absence  => 2451 [] => 1 [] => 2 [] => 2017 ) [2] => stdClass Object ( [] => D [] => 2 [] => Office  => 2453 [] => 1 [] => 3 [] => 2017 ) [3] => stdClass Object ( [] => E [] => 1 [] => Excused Tardy  => 2454 [] => 1 [] => 4 [] => 2017 ) [4] => stdClass Object ( [] => F [] => 1 [] => Excess Tardy  => 2455 [] => 1 [] => 5 [] => 2017 ) [5] => stdClass Object ( [] => I [] => 2 [] => Illness  => 2456 [] => 1 [] => 6 [] => 2017 ) [6] => stdClass Object ( [] => L [] => 2 [] => In House  => 2457 [] => 1 [] => 7 [] => 2017 ) [7] => stdClass Object ( [] => O [] => 2 [] => Other  => 2458 [] => 1 [] => 8 [] => 2017 ) [8] => stdClass Object ( [] => R [] => 2 [] => Truant  => 2459 [] => 1 [] => 9 [] => 2017 ) [9] => stdClass Object ( [] => S [] => 2 [] => Suspended  => 2460 [] => 1 [] => 10 [] => 2017 ) [10] => stdClass Object ( [] => T [] => 1 [] => Tardy  => 2461 [] => 1 [] => 11 [] => 2017 ) [11] => stdClass Object ( [] => U [] => 2 [] => Unexcused  => 2462 [] => 1 [] => 12 [] => 2017 ) [12] => stdClass Object ( [] => Y [] => 1 [] => School Activity  => 2464 [] => 1 [] => 13 [] => 2017 ) [13] => stdClass Object ( [] => X [] => 2 [] => Excused  => 2463 [] => 1 [] => 14 [] => 2017 ) [14] => stdClass Object ( [] => C [] => 2 [] => SOS  => 2452 [] => 1 [] => 15 [] => 2017 ) ) [citizenCodes] => Array ( [0] => stdClass Object ( [] => O [] => Outstanding  => 29 [] => 1 ) [1] => stdClass Object ( [] => S [] => Satisfactory  => 30 [] => 2 ) [2] => stdClass Object ( [] => N [] => Needs improvement  => 31 [] => 3 ) [3] => stdClass Object ( [] => U [] => Unsatisfactory  => 32 [] => 4 ) ) [feeBalance] => [notificationSettingsVO] => [periods] => Array ( [0] => stdClass Object ( [] => 1  => 1501 [name] => 1 [periodNumber] => 1 [] => 1 [] => 1 [] => 2017 ) [1] => stdClass Object ( [] => 2  => 1502 [name] => 2 [periodNumber] => 2 [] => 1 [] => 2 [] => 2017 ) [2] => stdClass Object ( [] => 3  => 1503 [name] => 3 [periodNumber] => 3 [] => 1 [] => 3 [] => 2017 ) [3] => stdClass Object ( [] => 4  => 1504 [name] => 4 [periodNumber] => 4 [] => 1 [] => 4 [] => 2017 ) [4] => stdClass Object ( [] => 5  => 1505 [name] => 5 [periodNumber] => 5 [] => 1 [] => 5 [] => 2017 ) [5] => stdClass Object ( [] => 6  => 1506 [name] => 6 [periodNumber] => 6 [] => 1 [] => 6 [] => 2017 ) [6] => stdClass Object ( [] => 7  => 1507 [name] => 7 [periodNumber] => 7 [] => 1 [] => 7 [] => 2017 ) [7] => stdClass Object ( [] => 0  => 1508 [name] => 0 [periodNumber] => 8 [] => 1 [] => 8 [] => 2017 ) ) [schools] => stdClass Object ( [] => AHS [address] => Arcadia High School 180 Campus Dr. Arcadia, CA 91007 [disabledFeatures] => stdClass Object ( [activities] => true [assignments] => false [attendance] => false [citizenship] => false [currentGpa] => false [emailalerts] => false [fees] => true [finalGrades] => false [meals] => true [standards] => false ) [highGrade] => 12 [lowGrade] => 9 [mapMimeType] => [name] => Arcadia High School [schoolDisabled] => false [schoolDisabledMessage] => [schoolDisabledTitle] => [] => 5 [schoolMapModifiedDate] => [schoolNumber] => 1 [schooladdress] => 180 Campus Drive [schoolcity] => Arcadia [schoolcountry] => [schoolfax] => 626-821-1712 [schoolphone] => 626-821-8370 [schoolstate] => CA [schoolzip] => 91007 ) [student] => stdClass Object ( [currentGPA] => [currentMealBalance] => 0.0 [currentTerm] => S2 [dcid] => 20104 [dob] => 1999-08-12T07:00:00.000Z [ethnicity] => 205 [firstName] => Akhil [gender] => M [gradeLevel] => 12  => 20104 [lastName] => Gampala [middleName] => [photoDate] => 2016-02-13T09:32:29.870Z [startingMealBalance] => 0.0 ) [studentDcid] => 20104 [studentId] => 20104 [terms] => Array ( [0] => stdClass Object ( [abbrev] => 16-17 [endDate] => 2017-06-07T07:00:00.000Z  => 2600 [parentTermId] => 0 [schoolNumber] => 1 [startDate] => 2016-08-17T07:00:00.000Z [title] => 2016-2017 ) [1] => stdClass Object ( [abbrev] => S1 [endDate] => 2017-01-08T08:00:00.000Z  => 2601 [parentTermId] => 0 [schoolNumber] => 1 [startDate] => 2016-08-17T07:00:00.000Z [title] => Semester 1 ) [2] => stdClass Object ( [abbrev] => S2 [endDate] => 2017-06-07T07:00:00.000Z  => 2602 [parentTermId] => 0 [schoolNumber] => 1 [startDate] => 2017-01-09T08:00:00.000Z [title] => Semester 2 ) ) [] => 2017 ) [userSessionVO] => )

        $this->details['information'] = $studentData->student;

        /*$assignmentCategories = \PowerAPI\Parser::assignmentCategories($assignmentCategories);
        $assignmentScores = \PowerAPI\Parser::assignmentScores($assignmentScores);
        $finalGrades = \PowerAPI\Parser::finalGrades($finalGrades);
        $reportingTerms = \PowerAPI\Parser::reportingTerms($studentData->reportingTerms);
        $teachers = \PowerAPI\Parser::teachers($studentData->teachers);

        $assignments = \PowerAPI\Parser::assignments(
            $studentData->assignments,
            $assignmentCategories,
            $assignmentScores
        );

        $this->details['sections'] = \PowerAPI\Parser::sections(
            $studentData->sections,
            $assignments,
            $finalGrades,
            $reportingTerms,
            $teachers
        );*/
    }
}
