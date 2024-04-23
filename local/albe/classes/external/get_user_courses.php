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
            'courseIds' => new external_multiple_structure(
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

        return ['courseIds' => [1, 2, 3]];
    }
}
