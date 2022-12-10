<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EmployeesModel;

class Employees extends BaseController
{
    public function index()
    {
        if (session('logged_in') == true) {

            $data['title'] = 'Add Employee';
            $data['page_title'] = 'Add Employee';
            echo view('templates/header', $data);
            echo view('employees/header');
            echo view('templates/navbar');
            echo view('templates/sidebar');
            echo view('employees/add_employees');
            echo view('templates/footer');
            echo view('employees/script');
        } else {
            return redirect()->to('login');
        }
    }

    public function employee_menu()
    {
        if (session('logged_in') == true) {

            $data['title'] = 'Employee Menu';
            $data['page_title'] = 'Employee Menu';
            echo view('templates/header', $data);
            echo view('employees/header');
            echo view('templates/navbar');
            echo view('templates/sidebar');
            echo view('employees/employee_menu.php');
            echo view('templates/footer');
            echo view('employees/script');
        } else {
            return redirect()->to('login');
        }
    }

    public function employee_add()
    {
        $employeesModel = new EmployeesModel();

        $validate = [
            "success" => false,
            "messages" => ''
        ];

        $data = [
            "employee_id"                   => $this->request->getPost("employee_id"),
            "firstname"                     => $this->request->getPost("firstname"),
            "middlename"                    => $this->request->getPost("middlename"),
            "lastname"                      => $this->request->getPost("lastname"),
            "gender"                        => $this->request->getPost("gender"),
            "civil_status"                  => $this->request->getPost("civil_status"),
            "date_of_birth"                 => $this->request->getPost("date_of_birth"),
            "place_of_birth"                => $this->request->getPost("place_of_birth"),
            "postal_code"                   => $this->request->getPost("postal_code"),
            "language"                      => $this->request->getPost("language"),
            "address_province"              => $this->request->getPost("address_province"),
            "address_city"                  => $this->request->getPost("address_city"),
            "address_brgy"                  => $this->request->getPost("address_brgy"),
            "address_sub"                   => $this->request->getPost("address_sub"),
            "contact_number"                => $this->request->getPost("contact_number"),
            "email_address"                 => $this->request->getPost("email_address"),
            "sss_no"                        => $this->request->getPost("sss_no"),
            "tin_no"                        => $this->request->getPost("tin_no"),
            "philhealth_no"                 => $this->request->getPost("philhealth_no"),
            "pag_ibig_no"                   => $this->request->getPost("pag_ibig_no"),
            "educational_attainment"        => $this->request->getPost("educational_attainment"),
            "course"                        => $this->request->getPost("course"),
            "emergency_name"                => $this->request->getPost("emergency_name"),
            "emergency_contact_no"          => $this->request->getPost("emergency_contact_no"),
            "emergency_address"             => $this->request->getPost("emergency_address"),
            "name_of_spouse"                => $this->request->getPost("name_of_spouse"),
            "spouse_contact_no"             => $this->request->getPost("spouse_contact_no"),
            "no_of_children"                => $this->request->getPost("no_of_children"),
            "spouse_address"                => $this->request->getPost("spouse_address"),
            "position"                      => $this->request->getPost("position"),
            "employment_status"             => $this->request->getPost("employment_status"),
            "date_hired"                    => $this->request->getPost("date_hired"),
            "date_resigned"                 => $this->request->getPost("date_resigned")

        ];

        if (!$employeesModel->insert($data)) {
            $validate['messages'] = $employeesModel->errors();
        } else {
            $validate['success'] = true;
        }

        echo json_encode($validate);
    }
}