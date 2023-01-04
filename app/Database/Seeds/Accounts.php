<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Accounts extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $table = 'accounts';
        $data = [
            [
                'employee_id'   => "SOFTWAREDEV",
                'username'      => 'yungga',
                'password'      => 'yunggabells',
                'access_level'  => 'admin'
            ]
        ];
        $db->table($table)->insertBatch($data);

        $softdev_account = [
            [
                "employee_id"                   => "SOFTWAREDEV",
                "firstname"                     => "Reynan",
                "middlename"                    => "Gallardo",
                "lastname"                      => "Jardin",
                "gender"                        => "Male",
                "civil_status"                  => "Filipino",
                "date_of_birth"                 => "1997-01-14",
                "place_of_birth"                => "Muntinlupa",
                "postal_code"                   => "1772",
                "language"                      => "Tagalog, English",
                "address_province"              => "Metro Manila",
                "address_city"                  => "Muntinlupa",
                "address_brgy"                  => "Putatan",
                "address_sub"                   => "",
                "contact_number"                => "09955609253",
                "email_address"                 => "yungga321@gmail.com",
                "sss_no"                        => "",
                "tin_no"                        => "",
                "philhealth_no"                 => "",
                "pag_ibig_no"                   => "",
                "educational_attainment"        => "College",
                "course"                        => "IT",
                "emergency_name"                => "",
                "emergency_contact_no"          => "",
                "emergency_address"             => "",
                "name_of_spouse"                => "",
                "spouse_contact_no"             => "",
                "no_of_children"                => "",
                "spouse_address"                => "",
                "position"                      => "",
                "employment_status"             => "",
                "date_hired"                    => "",
                "date_resigned"                 => "",
            ]
        ];
        $db->table('employees')->insertBatch($softdev_account);
    }
}
