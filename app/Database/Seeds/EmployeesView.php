<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EmployeesView extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $db->query("
            DROP VIEW IF EXISTS
                employees_view
        ");

        $db->query(
            "CREATE VIEW 
                employees_view 
            AS SELECT
                id,
                employee_id,
                CONCAT(firstname,' ',middlename,' ',lastname) AS employee_name,
                CONCAT_WS(', ',address_province,address_city,address_brgy,address_sub,postal_code) as address,
                gender,
                civil_status,
                DATE_FORMAT(date_of_birth,'%b %d, %Y') as date_of_birth,
                place_of_birth,
                position,
                employment_status,
                DATE_FORMAT(date_hired,'%b %d, %Y') as date_hired,
                DATE_FORMAT(date_resigned,'%b %d, %Y') as date_resigned,
                contact_number,
                email_address,
                sss_no,
                tin_no,
                philhealth_no,
                pag_ibig_no,
                educational_attainment,
                course,
                emergency_name,
                emergency_contact_no,
                emergency_address,
                name_of_spouse,
                spouse_contact_no,
                no_of_children,
                spouse_address,
                deleted_at
            FROM
                employees
            WHERE
                deleted_at IS NULL
            "
        );
    }
}
