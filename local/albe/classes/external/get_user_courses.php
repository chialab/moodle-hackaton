<?php

namespace local_albe\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_multiple_structure;

class get_user_courses extends external_api
{
    /**
     * Describe the expected parameters of the function.
     *
     * @return \external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters
    {
        return new external_function_parameters([
            'myzId' => new external_value(PARAM_TEXT, 'MyZ ID of the user', VALUE_REQUIRED, null, false),
        ]);
    }

    /**
     * Describe the values returned by the function.
     *
     * @return \external_single_structure
     */
    public static function execute_returns(): external_single_structure
    {
        return new external_single_structure([
            'courses' => new external_multiple_structure(
                new external_value(PARAM_INT, 'Course IDs', VALUE_REQUIRED, null, false),
            ),
        ]);
    }

    /**
     * Performs the expected actions.
     *
     * @param string $myzId User's MyZ ID
     * @return array
     * @throws \invalid_parameter_exception
     */
    public static function execute(string $myzId): array
    {
        /** @var array{myzId: string} $params */
        $params = static::validate_parameters(static::execute_parameters(), ['myzId' => $myzId]);

        $cfg = get_config('local_albe');
        $token = file_get_contents('https://testmy.zanichelli.it/api/login', context: stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query(['username' => $cfg->myz_api_user, 'password' => $cfg->myz_api_password]),
            ],
        ]));
        // var_dump($token);die;

        return ['courses' => static::get_courses([1])];
    }

    protected static function get_courses(array $categories): array
    {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/course/lib.php');
        require_once($CFG->libdir . '/filterlib.php');

        $courses = $DB->get_records_list('course', 'category', $categories, 'id ASC');

        $results = [];
        foreach ($courses as $course) {
            $context = \context_course::instance($course->id);
            $canupdatecourse = has_capability('moodle/course:update', $context);
            $canviewhiddencourses = has_capability('moodle/course:viewhiddencourses', $context);

            // Check if the course is visible in the site for the user.
            if (!$course->visible and !$canviewhiddencourses and !$canupdatecourse) {
                continue;
            }

            // Now, check if we have access to the course, unless it was already checked.
            try {
                if (empty($course->contextvalidated)) {
                    self::validate_context($context);
                }
            } catch (\Exception $e) {
                // User can not access the course, check if they can see the public information about the course and return it.
                if (!\core_course_category::can_view_course_info($course)) {
                    continue;
                }
            }

            $results[] = $course->id;
        }

        return $results;
    }
}
