<?php
if (! function_exists('has_flashdata'))
{
    /**
     * Check if has session flashdata
     */
	function has_flashdata(string $key): bool
	{
        return !empty(session()->getFlashdata($key));
	}
}

if (! function_exists('set_flashdata'))
{
    /**
     * Get session flashdata
     */
	function set_flashdata(string|array $key, mixed $message = null): mixed
	{
        return session()->setFlashdata($key, $message);
	}
}

if (! function_exists('get_flashdata'))
{
    /**
     * Get session flashdata
     */
	function get_flashdata(string|array $key): mixed
	{
        return session()->getFlashdata($key);
	}
}

if (! function_exists('clear_flashdata'))
{
    /**
     * Get session flashdata
     */
	function clear_flashdata(string|array $key): mixed
	{
        return session()->unmarkFlashdata($key);
	}
}

if (! function_exists('has_validation_errors'))
{
    /**
     * Check if has validation errors session flashdata
     */
    function has_validation_errors(): bool
    {
        return !empty(validation_errors());
    }
}

if (! function_exists('get_validation_errors'))
{
    /**
     * Get validation errors session flashdata
     */
	function get_validation_errors(bool $array = false): string|array
	{
        return $array 
            ? session()->getFlashdata('_ci_validation_errors')
            : validation_list_errors();
	}
}