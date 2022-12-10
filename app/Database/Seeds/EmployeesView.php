<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EmployeesView extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $db->query(
            "CREATE VIEW employees_view AS SELECT
                employee_id,
                CONCAT(firstname,' ',middlename,' ',lastname) AS employee_name,
                CONCAT_WS(', ',address_province,address_city,address_brgy,address_sub,postal_code) as address,
                gender,
                civil_status,
                date_of_birth,
                place_of_birth,
                position,
                employment_status,
                date_hired,
                date_resigned
                language,
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
