<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EmployeeAddField extends Migration
{
    public function up()
    {
        $fields = [
            'gender' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'after' => 'lastname'
            ],
            'civil_status' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'after' => 'gender'
            ],
            'date_of_birth' => [
                'type' => 'DATE',
                'after' => 'civil_status'
            ],
            'place_of_birth' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'after' => 'date_of_birth'
            ],
            'postal_code' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'after' => 'place_of_birth'
            ],
            'language' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'after' => 'postal_code'
            ],
            'address_province' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'after' => 'language'
            ],
            'address_city' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'after' => 'address_province'
            ],
            'address_brgy' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'after' => 'address_city'
            ],
            'address_sub' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'after' => 'address_brgy'
            ],
            'contact_number' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'after' => 'address_sub'
            ],
            'email_address' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'after' => 'contact_number'
            ],
            'sss_no' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'after' => 'email_address'
            ],
            'tin_no' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'after' => 'sss_no'
            ],
            'philhealth_no' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'after' => 'tin_no'
            ],
            'pag_ibig_no' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'after' => 'philhealth_no'
            ],
            'educational_attainment' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'after' => 'pag_ibig_no'
            ],
            'course' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'after' => 'educational_attainment'
            ],
            'emergency_name' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'after' => 'course'
            ],
            'emergency_contact_no' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'after' => 'emergency_name'
            ],
            'emergency_address' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'after' => 'emergency_contact_no'
            ],
            'name_of_spouse' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'after' => 'emergency_address'
            ],
            'spouse_contact_no' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'after' => 'name_of_spouse'
            ],
            'no_of_children' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'after' => 'spouse_contact_no'
            ],
            'spouse_address' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'after' => 'no_of_children'
            ]
        ];
        $this->forge->addColumn('employees', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('employees', 'gender');
        $this->forge->dropColumn('employees', 'civil_status');
        $this->forge->dropColumn('employees', 'date_of_birth');
        $this->forge->dropColumn('employees', 'place_of_birth');
        $this->forge->dropColumn('employees', 'postal_code');
        $this->forge->dropColumn('employees', 'language');
        $this->forge->dropColumn('employees', 'address_province');
        $this->forge->dropColumn('employees', 'address_city');
        $this->forge->dropColumn('employees', 'address_brgy');
        $this->forge->dropColumn('employees', 'address_sub');
        $this->forge->dropColumn('employees', 'contact_number');
        $this->forge->dropColumn('employees', 'email_address');
        $this->forge->dropColumn('employees', 'sss_no');
        $this->forge->dropColumn('employees', 'tin_no');
        $this->forge->dropColumn('employees', 'philhealth_no');
        $this->forge->dropColumn('employees', 'pag_ibig_no');
        $this->forge->dropColumn('employees', 'educational_attainment');
        $this->forge->dropColumn('employees', 'course');
        $this->forge->dropColumn('employees', 'emergency_name');
        $this->forge->dropColumn('employees', 'emergency_contact_no');
        $this->forge->dropColumn('employees', 'emergency_address');
        $this->forge->dropColumn('employees', 'name_of_spouse');
        $this->forge->dropColumn('employees', 'spouse_contact_no');
        $this->forge->dropColumn('employees', 'no_of_children');
        $this->forge->dropColumn('employees', 'spouse_address');

    }
}
