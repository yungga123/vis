<?php
if (! function_exists('has_flashdata'))
{
    /**
     * Check if has session flashdata
     */
	function has_flashdata(string $name): bool
	{
        return !empty(session()->getFlashdata($name));
	}
}

if (! function_exists('get_flashdata'))
{
    /**
     * Get session flashdata
     */
	function get_flashdata(string $name): mixed
	{
        return session()->getFlashdata($name);
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