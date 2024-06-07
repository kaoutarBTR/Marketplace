<?php

class LoginModel extends CI_Model
{

    public $idSupplier;
    private $webService;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->dbforge();
        $this->webService = new PrestaShopWebservice(apiUrl, apiKey, isDebug);
    }

    public function getEmployees($email)
    {
        // returns an array of employee having the email given or null if not found

        try {
            $EmployeeXml = $this->webService->get([
                'resource' => 'employees',
                'display' => 'full',
                'filter[email]' => $email

            ]);
            $arrayEmployees = xmlToArray($EmployeeXml);
            if (isset($arrayEmployees['prestashop']['employees']['employee'])) {
                return $arrayEmployees['prestashop']['employees']['employee'];
            } else return null;
        } catch (PrestaShopWebserviceException $e) {
            echo "PrestaShop WebService Exception: " . $e->getMessage();
            return null;
        }
    }

    public function login_user()
    {

        // gets email and password 
        $email = strtolower($this->input->post('email'));
        $pass = $this->input->post('passwd');
        // search for the employee by email
        $employee = $this->getEmployees($email);

        $correct_input = false;

        if (isset($employee)) {
            // if the email is correct, get the name, password and id_supplier 
            $passTest = $employee['passwd'];
            $nameTest = $employee['firstname'] . ' ' . $employee['lastname'];
            $this->idSupplier = $employee['id_supplier'];
            if (password_verify($pass, $passTest)) {
                // if the password is correct stock name, email and id_supplier in session and redirect to dashboard
                $this->session->set_userdata('name', $nameTest);
                $this->session->set_userdata('firstname',  $employee['firstname']);
                $this->session->set_userdata('lastname', $employee['lastname']);
                $this->session->set_userdata('email', $email);
                $this->session->set_userdata('passwd', $pass);
                $this->session->set_userdata('id_employee', $employee['id']);
                $this->session->set_userdata('loggedIn', true);
                $this->session->set_userdata('id_supplier', $this->idSupplier);
                $session_identifier = rand();
                $this->session->set_userdata('session_identifier', $session_identifier);

                redirect(base_url('/dashboard'));
                $correct_input = true;
            }
        }
        // if password or email is incorrect, show an error message and redirect to login 
        if (!$correct_input) {
            $this->session->set_flashdata('error', 'Wrong email or password, try again');
            $this->session->set_userdata('loggedIn', false);
            redirect(base_url('/login'));
        }
    }

    public function storeData($id_supplier, $session_id, $role)
    {
        // this function is called when the supplier clicks on upload file in the 2 views: addProducts and stockProduct
        // we can't use session variables in CLI functions (auto Tasks), so I tought of storing id_supplier with role in database to use it instead :)
        // the role can be add_product or update_stock
        $data = array(
            'id_supplier' => $id_supplier,
            'id_session' => $session_id,
            'roles' => $role
        );
        return $this->db->insert('users', $data);
    }
    public function getCurrentSuppliers($role)
    {
        // this function is called during cli functions (for auto Tasks) to get supplier id and use it to get the excel file from database
        // use role to distinguish between addingProduct excel files or updating Stock excel files
        $this->db->select('id_supplier');
        $this->db->from('users');
        $this->db->where('roles', $role);
        $query = $this->db->get();
        return $query->result_array();
    }
    public function deleteUserdata($id_supplier, $role)
    {

        // this function is called in the end of the operation to delete the supplier from users
        // only suppliers that are waiting for there products to be created or stock to be updated are seen in this table
        // if the operation is completed we delete there info form this table
        $this->db->where('id_supplier', $id_supplier);
        $this->db->where('roles', $role);
        $this->db->delete('users');
    }


    public function getEmailFromIdSupplier($id_supplier)
    {
        // this function returns employee email where id_supplier = the id given
        // this function is used to send email in the end of auto tasks

        try {
            $employeesXml = $this->webService->get([
                'resource' => 'employees',
                'display' => '[email]',
                'filter[email]' => 'kaoutarbaitar@gmail.com'
            ]);
            $arrayData = xmlToArray($employeesXml);
            if (isset($arrayData['prestashop']['employees']['employee']['email'])) {
                return $arrayData['prestashop']['employees']['employee']['email'];
            }

            return false;
        } catch (PrestaShopWebserviceException $e) {
            echo 'Error:' . $e->getMessage();
            return false;
        }
    }
}
