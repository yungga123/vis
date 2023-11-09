<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EmployeesView extends Seeder
{
    public function run()
    {
        $this->db->query("
            DROP VIEW IF EXISTS
                employees_view
        ");

        $this->db->query(
            "CREATE VIEW 
                employees_view 
            AS SELECT
                emp.id,
                emp.employee_id,
                CONCAT(emp.firstname,' ',emp.middlename,' ',emp.lastname) AS employee_name,
                CONCAT(
                    IF(emp.address_province = '' || emp.address_province IS NULL, '', CONCAT(emp.address_province, ', ')),
                    IF(emp.address_city = '' || emp.address_city IS NULL, '', CONCAT(emp.address_city, ', ')),
                    IF(emp.address_brgy = '' || emp.address_brgy IS NULL, '', CONCAT(emp.address_brgy, ', ')),
                    IF(emp.address_sub = '' || emp.address_sub IS NULL, '', CONCAT(emp.address_sub, ', ')),
                    IF(emp.postal_code = '' || emp.postal_code IS NULL, '', emp.postal_code)
                ) AS address,
                emp.gender,
                emp.civil_status,
                DATE_FORMAT(emp.date_of_birth,'%b %d, %Y') as date_of_birth,
                emp.place_of_birth,
                emp.position,
                emp.employment_status,
                DATE_FORMAT(emp.date_hired,'%b %d, %Y') as date_hired,
                DATE_FORMAT(emp.date_resigned,'%b %d, %Y') as date_resigned,
                emp.contact_number,
                emp.email_address,
                emp.sss_no,
                emp.tin_no,
                emp.philhealth_no,
                emp.pag_ibig_no,
                emp.educational_attainment,
                emp.course,
                emp.emergency_name,
                emp.emergency_contact_no,
                emp.emergency_address,
                emp.name_of_spouse,
                emp.spouse_contact_no,
                emp.no_of_children,
                emp.spouse_address,
                DATE_FORMAT(emp.created_at, '%b %e, %Y at %h:%i %p') AS created_at
            FROM
                employees AS emp
            WHERE
                deleted_at IS NULL
            "
        );
    }
}
