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
	function remove_string(string|array|null $subject, string $search, string $replace = ''): string|array|null
	{
        if (! empty($subject)) {
            if (is_array($subject)) {
                $arr = [];
                foreach ($subject as $val) {
                    $arr[] = str_replace($search, $replace, $val);
                }
    
                $subject = $arr;
            } else {
                $subject = str_replace($search, $replace, $subject);
            }
        }

        return $subject;
	}
}

if (! function_exists('current_date'))
{
    /**
     * Get current date - default format 'Y-m-d'
     */
	function current_date(string $format = 'Y-m-d'): string
	{
        return date($format);
	}
}

if (! function_exists('current_datetime'))
{
    /**
     * Get current date & time - default format 'Y-m-d H:i:s'
     */
	function current_datetime(string $format = 'Y-m-d H:i:s'): string
	{
        return date($format);
	}
}

if (! function_exists('current_date'))
{
    /**
     * Get current data - default format 'Y-m-d'
     */
	function current_date(string $format = 'Y-m-d'): string
	{
        return date($format);
	}
}

if (! function_exists('format_date'))
{
    /**
     * Format date - default format 'M d, Y' (ex. Jan 1, 2023)
     */
	function format_date(string $date, string $format = 'M d, Y'): string
	{
        if (! is_date_valid($date)) return '';
        return !empty($date) ? date($format, strtotime($date)) : '';
	}
}

if (! function_exists('format_time'))
{
    /**
     * Format time - default format 'h:i A' (ex. 12:00 PM)
     */
	function format_time(string $time, string $format = 'h:i A', bool $print = false): string
	{
        if (! is_date_valid($time)) return '';
        if ($print) {
            if (empty($time) || $time == '00:00:00') return ''; 
        }

        return !empty($time) ? date($format ? $format : 'h:i A', strtotime($time)) : '';
	}
}

if (! function_exists('format_datetime'))
{
    /**
     * Format datetime - default format 'M d, Y h:i A' (ex. Jan 1, 2023 12:00 PM)
     */
	function format_datetime(string $datetime, string $format = 'M d, Y h:i A'): string
	{
        if (! is_date_valid($datetime)) return '';
        return !empty($datetime) ? date($format, strtotime($datetime)) : '';
	}
}

if (! function_exists('is_date_valid'))
{
    /**
     * Check date or datetime if valid
     */
	function is_date_valid(string $datetime): string
	{
        $check = strtotime($datetime);
        return ($check > 0);
	}
}

if (! function_exists('has_empty_value'))
{
    /**
     * Check if array has an empty value
     */
	function has_empty_value(array $array): bool
	{
        foreach ($array as $value) {
            if (empty($value)) return true; // Found an empty value
        }
        return false; // No empty values found
	}
}

if (! function_exists('is_array_multi_dimen'))
{
    /**
     * Check if array is multi-dimensional
     */
	function is_array_multi_dimen(array $array): bool
	{
        foreach ($array as $element) {
            if (is_array($element)) return true; // Found a nested array
        }
        return false; // No nested arrays found
	}
}

if (! function_exists('clean_input'))
{
    /**
     * Clean input using trim default function
     */
	function clean_input(string|array $input): string|array
	{
        if (is_array($input)) {
            $arr = [];
            foreach ($input as $key => $val) {
                $arr[$key] = trim($val);
            }

            return $arr;
        }

        return trim($input);
	}
}

if (! function_exists('has_empty_value'))
{
    /**
     * Check if array has an empty value
     */
	function has_empty_value(array $array): bool
	{
        foreach ($array as $value) {
            if (empty($value)) return true; // Found an empty value
        }
        return false; // No empty values found
	}
}

if (! function_exists('is_array_multi_dimen'))
{
    /**
     * Check if array is multi-dimensional
     */
	function is_array_multi_dimen(array $array): bool
	{
        foreach ($array as $element) {
            if (is_array($element)) return true; // Found a nested array
        }
        return false; // No nested arrays found
	}
}

if (! function_exists('clean_input'))
{
    /**
     * Clean input using trim default function
     */
	function clean_input(string|array $input): string|array
	{
        if (is_array($input)) {
            $arr = [];
            foreach ($input as $key => $val) {
                $arr[$key] = trim($val);
            }

            return $arr;
        }

        return trim($input);
  }
}

if (! function_exists('has_html_tags'))
{
    /**
     * Check string if has html tags
     */
	function has_html_tags(string $string): bool
	{
        return preg_match('/<[^>]+>/', $string) === 1;
	}
}