<?php
// Helper functions for sidebar rendering

use GuzzleHttp\Promise\Is;

require APPPATH.'Helpers/extend/sidebar.php';

// Helper functions for user related functionality
require APPPATH.'Helpers/extend/user_mngmt.php';

// Helper functions for datatable related functionality
require APPPATH.'Helpers/extend/datatable.php';

// Helper functions for select/options related
require APPPATH.'Helpers/extend/select_options.php';

// Helper functions checking and getting
// the session flashdata in view
require APPPATH.'Helpers/extend/view_session.php';

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

if (! function_exists('current_time'))
{
    /**
     * Get current time - default format 'H:i:s'
     */
	function current_time(string $format = 'H:i:s'): string
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

if (! function_exists('clean_param'))
{
    /**
     * Clean input using trim default function
     */
	function clean_param(string|array $input, $func_name = '', $trim_chars = ''): string|array
	{
        if (empty($input)) return $input;
        
        if (is_array($input)) {
            $arr = [];
            foreach ($input as $key => $val) {
                if (! is_array($val)) {
                    $val = $trim_chars ? trim($val, $trim_chars) : trim($val);
                }
                if ($func_name) $val = $func_name($val);

                $arr[$key] = $val;
            }

            return $arr;
        }

        $input = $trim_chars ? trim($input, $trim_chars) : trim($input);
        
        if ($func_name) $input = $func_name($input);

        return $input;
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

if (! function_exists('kb_to_mb'))
{
    /**
     * Convert kb to mb
     */
	function kb_to_mb(int $size_in_kb): int
	{
        return $size_in_kb / 1024;
	}
}

if (! function_exists('mb_to_kb'))
{
    /**
     * Convert mb to kb
     */
	function mb_to_kb(int $size_in_mb): int
	{
        return $size_in_mb * 1024;
	}
}

if (! function_exists('get_file_icons'))
{
    /**
     * Get fontawesome file icons
     */
	function get_file_icons(string $param): string
	{
        $icon = 'fas fa-file';
        switch (strtolower($param)) {
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'webp':
                $icon = 'fas fa-file-image';  
                break;
            case 'pdf':
                $icon = 'fas fa-file-pdf';                   
                break;
                break;
            case 'doc':
            case 'docx':
                $icon = 'fas fa-file-word';                   
                break;
            case 'xlx':
            case 'xlsx':
            case 'csv':
                $icon = 'fas fa-file-excel';                   
                break;
        }

        return $icon;
	}
}

if (! function_exists('flatten_array'))
{
    /**
     * Flatten a multidimensional array.
     * Or convert into one dimensional array.
     */
	function flatten_array(array $array, string $param = ''): array
	{
        if (is_array($array) && ! empty($array)) {
            $arr = [];
            foreach ($array as $key => $val) {
                if (is_array($val)) {
                    if ($param && isset($val[$param])) {
                        $_key = $val[$param];
                        unset($val[$param]);
                        $arr[$_key] = $val;
                    } else {
                        $vals = array_values($val);

                        if (count($vals) > 1)
                            $arr[$vals[0]] = $vals[1];
                        else
                            $arr[$vals[0]] = $vals[0];
                    }
                } else
                    $arr[$key] = $val;  
            }
            return $arr;
        }
        return $array;
	}
}

if (! function_exists('_lang'))
{
    /**
     * Add a custom logic in lang() function 
     * before returning the result/response
     */
	function _lang(string $line, array|string $args = [], ?string $locale = null): string
	{
        // Convert $args to array and store in $_args
        $_args      = is_array($args) ? $args : [$args];
        // Get the corresponding line/value
        $string     = lang($line, $_args, $locale);
        // Define the pattern to match placeholders
        $pattern    = '/\{([^}]*)\}/';

        // Match placeholders in the string
        preg_match_all($pattern, $string, $matches);

        // Check if matches
        if (! empty($matches[0])) { 
            $result = $matches[1];

            // Loop through each placeholder if $_args is not empty
            if (! empty($_args)) {
                // Replace the placeholder with value
                for ($i=0; $i < count($result); $i++) { 
                    $search     = $matches[0][$i];
                    $replace    = $_args[$i];
                    $string     = str_replace($search, $replace, $string);
                }
            } else {
                // Replace the placeholder with value
                $replace    = is_string($args) ? $args : 'Data';
                $replace    = strpos($line, 'change') !== false ? 'CHANGE' : $replace;
                $replace    = strpos($line, 'uploaded') !== false ? 'File' : $replace;
                $string     = str_replace($matches[0][0], $replace, $string);
            }
        }

        return $string;
	}
}

if (! function_exists('res_lang'))
{
    /**
     * Custom function for getting the value/line
     * from Response Language (App\Language\en\Response).
     * 
     * You can call it instead of the usual lang() function
     * so that you don't need to add the file name.
     */
	function res_lang(string $line, array|string $args = [], ?string $locale = null): string
	{
        // Add the prefix or the file name
        $line   = 'Response.' . $line;
        $string = _lang($line, $args, $locale);

        return $string;
	}
}

if (! function_exists('check_param'))
{
    /**
     * Determine if passed $needle or $needle2 is existed.
     * If $return is set to true, return the param (either empty string or not) otherwise boolean
     */
	function check_param(array|string $haystack, string $needle, string $needle2 = '', $return = false): mixed
	{
        if (empty($haystack)) 
            return $return ? '' : false;

        if (isset($haystack[$needle])) {
            $param = $haystack[$needle];

            if (! empty($param) && is_array($param)) {
                foreach ($param as $val) {
                    if (isset($val[$needle2]))
                        $param = $val[$needle2];
                }
            }

            if ($return) return $param;

            // Check the value whether empty, null or zero
            return !empty($param);
        }
        
        return $return ? '' : false;
	}
}

if (! function_exists('log_msg'))
{
    /**
     * For logging message using the log_message() function
     * with some little before calling the said method
     */
	function log_msg(mixed $message, array $context = []): bool
	{
        $level = ENVIRONMENT === 'development' ? 'info' : 'error';

        if (empty($context)) {
            $message = is_string($message) ? $message : json_encode($message);
        }

        return log_message($level, 'log_msg: '. $message, $context);
	}
}

if (! function_exists('get_array_duplicate'))
{
    /**
     * Get the duplicate value(s) of an array
     */
	function get_array_duplicate(array $array): array
	{
        $unique     = array_unique($array);
        $duplicates = array_diff_assoc($array, $unique);

        return $duplicates;
	}
}

if (! function_exists('has_internet_connection'))
{
    /**
     * Check if server has internet connection
     */
	function has_internet_connection(): bool
	{
        $url = "http://www.google.com"; // Use a reliable and accessible URL

        $headers = @get_headers($url);

        // Check if there is a response and the response code is 200 OK
        return $headers && strpos($headers[0], '200') !== false;
	}
}

if (! function_exists('get_time_diff'))
{
    /**
     * Get time difference from two different times
     */
	function get_time_diff(string $time_start, string $time_end, $format = ''): int|object|string
	{
        $time_diff = 0;

        if (! empty($time_start) && ! empty($time_end)) {
            // Create DateTime objects for start and end times
            $time_start = new DateTime(format_time($time_start, 'H:i:s'));
            $time_end   = new DateTime(format_time($time_end, 'H:i:s'));

            // Calculate the difference in hours and minutes
            $time_diff  = $time_start->diff($time_end);

            if (! empty($format)) {
                return $time_diff->format($format);
            }
        }

        return $time_diff;
	}
}

if (! function_exists('get_total_hours'))
{
    /**
     * Get total hours from two different times
     * 
     * @param float $break  To less the total hours
     */
	function get_total_hours(string $time_start, string $time_end, float $break = 0, $format = ''): int|string
	{
        $total_hours    = 0;
        $time_diff      = get_time_diff($time_start, $time_end);

        if (! empty($time_diff)) {
            // Calculate total hours
            if (! empty($format)) {
                $time_diff->h = $time_diff->h - $break;

                return $time_diff->format($format);
            }

            // Get hours plus fraction of an hour (mins / 60) 
            $total_hours = $time_diff->h + ($time_diff->i / 60);
            // Minus hr_break time
            $total_hours = $total_hours - $break;
            // Format to decimal with 2 places
            $total_hours = number_format($total_hours, 2);
        }

        return $total_hours;
	}
}

if (! function_exists('get_hours'))
{
    /**
     * Get hours from time
     */
	function get_hours(string|float $time): int|float
	{
        if (! empty($time)) {
            if (is_numeric($time)) {
                return floor($time < 60 ? $time : $time / 60);
            }

            $time = time_to_mins($time);

            return floor($time / 60);
        }
        
        return $time;
	}
}

if (! function_exists('get_minutes'))
{
    /**
     * Get minutes from time
     */
	function get_minutes(string|float $time): int|float
	{
        if (! empty($time)) {
            if (is_numeric($time)) {
                $time   = (float) $time;
                $hours  = floor($time);
                
                return round(($time - $hours) * 60);
            }

            $time = time_to_mins($time);

            return get_minutes($time);
        }

        return $time;
	}
}

if (! function_exists('time_to_mins'))
{
    /**
     * Get total hours from two different times
     */
	function time_to_mins(float|string $time): int|float|string
	{
        if (! empty($time)) {
            if (is_numeric($time)) {
                return number_format(($time * 60), 2);
            }

            // Convert the time string to a DateTime object
            $time_obj = new DateTime(format_time($time, 'H:i'));

            // Get the total minutes from midnight
            $total_mins = $time_obj->format('H') * 60 + $time_obj->format('i');

            return number_format($total_mins, 2);
        }

        return $time;
	}
}

if (! function_exists('compare_times'))
{
    /**
     * Compare two different times based on the pass third param
     */
	function compare_times(string $time, string $time2, string $operator = '='): bool
	{
        // Convert time strings to DateTime objects
        $date_time  = new DateTime(format_time($time, 'H:i'));
        $date_time2 = new DateTime(format_time($time2, 'H:i'));

        // Check if both DateTime objects are valid
        if (!$date_time || !$date_time2) {
            return false; // Invalid time format
        }

        // Perform the comparison based on the specified operator
        switch ($operator) {
            case '=':
                return $date_time == $date_time2;
            case '<':
                return $date_time < $date_time2;
            case '>':
                return $date_time > $date_time2;
            case '>=':
                return $date_time >= $date_time2;
            case '<=':
                return $date_time <= $date_time2;
            case '!=':
                return $date_time != $date_time2;
            default:
                return false; // Invalid operator
        }
	}
}

if (! function_exists('get_date_diff'))
{
    /**
     * Get date difference from two different date
     */
	function get_date_diff(string $start_date, string $end_date, $days = false): int|object|string
	{
        $interval = 0;

        if (! empty($start_date) && ! empty($end_date)) {
            // Create DateTime objects for start and end dates
            $start_date = new DateTime(format_date($start_date, 'Y-m-d'));
            $end_date   = new DateTime(format_date($end_date, 'Y-m-d'));

            // Calculate the difference between the two dates
            $interval  = $start_date->diff($end_date);

            if ($days) {
                // Access the difference in days
                $days =  $interval->days;

                // Include the end date in the count
                return $days += 1;
            }
        }

        return $interval;
	}
}

if (! function_exists('compare_dates'))
{
    /**
     * Compare two different dates based on the pass third param
     */
	function compare_dates(string $date, string $date2, string $operator = '='): bool
	{
        // Convert date strings to Datedate objects
        $date_time  = new DateTime(format_date($date, 'Y-m-d'));
        $date_time2 = new DateTime(format_date($date2, 'Y-m-d'));

        // Check if both DateTime objects are valid
        if (!$date_time || !$date_time2) {
            return false; // Invalid date format
        }

        // Perform the comparison based on the specified operator
        switch ($operator) {
            case '=':
                return $date_time == $date_time2;
            case '<':
                return $date_time < $date_time2;
            case '>':
                return $date_time > $date_time2;
            case '>=':
                return $date_time >= $date_time2;
            case '<=':
                return $date_time <= $date_time2;
            case '!=':
                return $date_time != $date_time2;
            default:
                return false; // Invalid operator
        }
	}
}

if (! function_exists('format_results'))
{
    /**
     * Format query result into one assoc array.
     * This will be mostly use in a table with two columns (key, value)
     * 
     * @param array $result     The array/result to format
     * @param string $key       The key name of the key - eg. $value['key']
     * @param string $val       The key name of the value - eg. $value['value']
     * @param bool $single      Whether to return single array only
     */
	function format_results(array $result, string $key = 'key', string $val = 'value', $single = false): array
	{
        $arr = [];

        if (! empty($result)) {
            foreach ($result as $_key => $value) {
                if ($single) {
                    $arr[] = $value[$key];
                } else {
                    $arr[$value[$key]] = empty($val) ? $value : $value[$val];
                }                
            }
        }

        return $arr;
	}
}

if (! function_exists('get_acronymns'))
{
    /**
     * Get the acronymns (first letter) of the words/string
     * 
     * @param string $words
     */
	function get_acronymns($words) {
        if (empty($words)) return $words;
        
        $regex      = '/(?<=\b)\w/iu';
        $split      = preg_split("/[\s,_-]+/", $words);
        
        preg_match_all($regex, $words, $matches);
        
        $results    = $matches[0];
        
        foreach ($split as $key => $val) {
            if (is_numeric($val)) $results[$key] = $val;
        }
        
        return mb_strtoupper(implode('', $results));
    }
}