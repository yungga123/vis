<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class OldToNewDBCustomer extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $db->query(
            "INSERT INTO vcmmis.customers_vt (id, customer_name,contact_person,address_sub,contact_number,email_address,source,notes) 
            SELECT CustomerID, CompanyName, ContactPerson, Address, ContactNumber, EmailAddress, source, notes FROM vinculum.customer_vt"
        );
    }
}
