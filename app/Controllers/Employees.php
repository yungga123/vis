<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EmployeesModel;
use monken\TablesIgniter;

class Employees extends BaseController
{

    public function index()
    {
        if (session('logged_in') == true) {

            $data['title'] = 'Add Employee';
            $data['page_title'] = 'Add Employee';
            $data['uri'] = service('uri');

            return view('employees/add_employees',$data);
        } else {
            return redirect()->to('login');
        }
    }

    public function employee_menu()
    {

        if (session('logged_in') == false) {
            return redirect()->to('login');
        }

        switch (session('access')) {
            case 'admin':

                break;

            case 'hr':

                break;

            case 'manager':

                break;
            
            default:
                $data['title'] = 'Invalid Access!!';
                $data['page_title'] = 'Invalid Access!!';
                $data['href'] = site_url('dashboard');

                return view('templates/offlimits',$data);
                break;
        }

        $data['title'] = 'Employee Menu';
        $data['page_title'] = 'Employee Menu';
        $data['uri'] = service('uri');

        return view('employees/employee_menu', $data);
        return redirect()->to('login');

            
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

    public function employees_list()
    {
        if (session('logged_in') == true) {

            $data['title'] = 'List of Employees';
            $data['page_title'] = 'List of Employees';
            $data['uri'] = service('uri');

            return view('employees/employee_list',$data);
        } else {
            return redirect()->to('login');
        }
    }

    public function getEmployees() {
        $employeesModel = new EmployeesModel();
        $employeesTable = new TablesIgniter();

        $employeesTable->setTable($employeesModel->noticeTable())
                       ->setSearch([
                            "employee_id", 
                            "employee_name", 
                            "address", 
                            "gender", 
                            "civil_status", 
                            "date_of_birth", 
                            "place_of_birth", 
                            "position", 
                            "employment_status", 
                            "date_hired", 
                            "language", 
                            "contact_number", 
                            "email_address", 
                            "sss_no", 
                            "tin_no", 
                            "philhealth_no", 
                            "pag_ibig_no", 
                            "educational_attainment", 
                            "course", "emergency_name", 
                            "emergency_contact_no", 
                            "emergency_address", 
                            "name_of_spouse", 
                            "spouse_contact_no", 
                            "no_of_children", 
                            "spouse_address"
                       ])
                       ->setOrder([
                            "employee_id",
                            null,
                            "employee_name", 
                            "address", 
                            "gender", 
                            "civil_status", 
                            "date_of_birth", 
                            "place_of_birth", 
                            "position", 
                            "employment_status", 
                            "date_hired", 
                            "language", 
                            "contact_number", 
                            "email_address", 
                            "sss_no", 
                            "tin_no", 
                            "philhealth_no", 
                            "pag_ibig_no", 
                            "educational_attainment", 
                            "course", "emergency_name", 
                            "emergency_contact_no", 
                            "emergency_address", 
                            "name_of_spouse", 
                            "spouse_contact_no", 
                            "no_of_children", 
                            "spouse_address"
                       ])
                       ->setOutput(
                        [
                            "employee_id",
                            $employeesModel->buttonEdit(),
                            "employee_name", 
                            "address", 
                            "gender", 
                            "civil_status", 
                            "date_of_birth", 
                            "place_of_birth", 
                            "position", 
                            "employment_status", 
                            "date_hired", 
                            "language", 
                            "contact_number", 
                            "email_address", 
                            "sss_no", 
                            "tin_no", 
                            "philhealth_no", 
                            "pag_ibig_no", 
                            "educational_attainment", 
                            "course", 
                            "emergency_name", 
                            "emergency_contact_no", 
                            "emergency_address", 
                            "name_of_spouse", 
                            "spouse_contact_no", 
                            "no_of_children", 
                            "spouse_address"
                        ]);

        return $employeesTable->getDatatable();
    }

    public function edit_employee($id)
    {
        if (session('logged_in') == true) {

            $employeesModel = new EmployeesModel();
            $data['title'] = 'Update Employee';
            $data['page_title'] = 'Update Employee';
            $data['uri'] = service('uri');
            $data['employee_details'] = $employeesModel->find($id);
            $data['id'] = $id;
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

    public function employee_edit()
    {
        $employeesModel = new EmployeesModel();

        $validate = [
            "success" => false,
            "messages" => ''
        ];
        $id = $this->request->getPost("id");
        $data = [
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

        if (!$employeesModel->update($id,$data)) {
            $validate['messages'] = $employeesModel->errors();
        } else {
            $validate['success'] = true;
        }

        echo json_encode($validate);
    }

    public function delete_employee($id) {
        if (session('logged_in') == true) {

            $employeesModel = new EmployeesModel();
            
            $data['title'] = 'Delete employee';
            $data['page_title'] = 'Delete employee';
            $data['uri'] = service('uri');
            $data['href'] = site_url('employee-list');
            $employeesModel->delete($id);

            return view('templates/deletepage',$data);
        } else {
            return redirect()->to('login');
        }
    }

}
