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