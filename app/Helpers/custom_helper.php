<?php
// Helper functions for sidebar rendering
require APPPATH.'Helpers/extend/sidebar.php';

// Helper functions for user related functionality
require APPPATH.'Helpers/extend/user_mngmt.php';

// Helper functions for datatable related functionality
require APPPATH.'Helpers/extend/datatable.php';

// Helper functions for select/options related
require APPPATH.'Helpers/extend/select_options.php';

// Mixed helper functions - start from here
if (! function_exists('check_string_contains'))
{
    /**
     * Check string if contains the passed value
     */
	function check_string_contains(string $string, string $val): bool
	{
        return (strpos($string, $val) !== false);
	}
}

if (! function_exists('remove_string'))
{
    /**
     * Clear variable based on the passed params
     */
	function remove_string(string|array $subject, string $search, string $replace = ''): string|array
	{
        if (is_array($subject)) {
            $arr = [];
            foreach ($subject as $val) {
                $arr[] = str_replace($search, $replace, $val);
            }

            $subject = $arr;
        } else {
            $subject = str_replace($search, $replace, $subject);
        }

        return $subject;
	}
}