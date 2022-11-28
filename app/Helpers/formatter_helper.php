<?php

if (! function_exists('set_status'))
{
	function set_status(string $value, array $row): string
	{
		return $value === '1' ? 'Active' : 'Inactive';
	}
}

if (! function_exists('action_links'))
{
	function action_links(string $value, array $row): string
	{
		return '<a href="'.base_url('customers/'.$value).'">View</a>';
	}
}