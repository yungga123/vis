<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Employees extends Migration
{
    public function up()
    {
        
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'employee_id' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'firstname' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'middlename' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'lastname' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'gender' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'civil_status' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'date_of_birth' => [
                'type' => 'DATE'
            ],
            'place_of_birth' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'postal_code' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'language' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'address_province' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'address_city' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'address_brgy' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'address_sub' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'contact_number' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'email_address' => [
                'name' => 'email_address',
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true
            ],
            'sss_no' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'tin_no' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'philhealth_no' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'pag_ibig_no' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'educational_attainment' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'course' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'emergency_name' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'emergency_contact_no' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'emergency_address' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'name_of_spouse' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'spouse_contact_no' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'no_of_children' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'spouse_address' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'position' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'employment_status' => [
                'type' => 'VARCHAR',
                'constraint' => 500
            ],
            'date_hired' => [
                'type' => 'DATE'
            ],
            'date_resigned' => [
                'type' => 'DATE'
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
            'deleted_at datetime default null'
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('employees');
    }

    public function down()
    {
        $this->forge->dropTable('employees');
    }
}
