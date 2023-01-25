<?php

use App\Models\CustomersModel;


//Customers ID Format
if (! function_exists('customer_id_format'))
{
	function customer_id_format(string $value, array $row): string
	{
		return 'C'.$value;
	}
}

//Task Lead Format
if (! function_exists('status_percent'))
{
	function status_percent(int $value, array $row): string
	{
		$status = "";

		if ($value <= 10) {
			$status = "Identified";
		} 
		elseif($value <= 30)
		{
			$status = "Qualified";
		}
		elseif($value <= 50)
		{
			$status = "Developed Solution";
		}
		elseif($value <= 70)
		{
			$status = "Evaluation";
		}
		elseif($value <= 90)
		{
			$status = "Negotiation";
		}
		elseif($value <= 100)
		{
			$status = "Booked";
		}

		return $status;
	}
} 


if (! function_exists('customers_name'))
{
	function customers_name(string $value, array $row): string
	{
		
		$customersModel = new CustomersModel();
		$customerNameFind = $customersModel->find($value);
		$customerName = $customerNameFind['customer_name'];

		return $customerName;
	}
}

if (! function_exists('user_access_level'))
{
	function user_access_level(mixed $params = null): mixed
	{
		$access_levels = [
			'super_admin' 	=> 'Super Admin',
			'admin' 		=> 'Administrator',
			'executive' 	=> 'Executive',
			'managers' 		=> 'Manager',
			'operation' 	=> 'Admin/Opertation',
			'supervisor'	=> 'Supervisory',
			'user' 			=> 'General User',
		];

		if (! empty($params)) {
			if (is_string($params)) {
				return $access_levels[$params];
			} 

			if (is_array($params)) {
				$arr = [];
				foreach ($access_levels as $key => $val) {
					if (in_array($key, $params)) {
						$arr[$key] = $val;
					}
				}

				return $arr;
			}
		}

		return $access_levels;
	}
}

if (! function_exists('get_avatar'))
{
	function get_avatar(string|null $param = null): string|array
	{
		$avatars = [
			'female' 	=> 'assets/dist/img/avatar3.png',
			'male' 		=> 'assets/dist/img/avatar5.png',
		];

		return $param ? $avatars[$param] : $avatars;
	}
}