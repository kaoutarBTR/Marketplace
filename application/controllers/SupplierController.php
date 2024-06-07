<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once('./vendor/autoload.php');

class SupplierController extends CI_Controller{

    private $webService;


    public function __construct()
    {
        parent::__construct();
        $this->webService = new PrestaShopWebservice(apiUrl, apiKey, isDebug);

    
    }
    public function index(){

        if ($_SERVER['REQUEST_METHOD'] === 'POST'){

            // get employee input data
            $firstname=$this->input->post('firstname');
            $lastname=$this->input->post('lastname');
            $email=$this->input->post('email');
            $id_employee= $this->session->userdata('id_employee');
            $id_supplier= $this->session->userdata('id_supplier');
            $password=$this->session->userdata('passwd');
            $newPassword=$this->input->post('newPassword');
            if($newPassword!=''){
                // if the new password is empty, don't change password
                $password=$newPassword;
            }
            // generate xml for employee
            $xml='<prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
            <employee>
              <id><![CDATA['.$id_employee.']]></id>
              <id_lang><![CDATA[1]]></id_lang>
              <id_supplier><![CDATA['. $id_supplier.']]></id_supplier>
              <passwd><![CDATA['. $password.']]></passwd>
              <lastname><![CDATA['.$lastname.']]></lastname>
              <firstname><![CDATA['.$firstname.']]></firstname>
              <email><![CDATA['.$email.']]></email>
              <active><![CDATA[1]]></active>
              <id_profile><![CDATA[1]]></id_profile>
            </employee>
          </prestashop>
          ';

       
            try {
                $updatedXml =  $this->webService->edit([
                    'resource' => 'employees',
                    'id' => $id_employee,
                    'putXml' => $xml,
                ]);
                $data['message']='profil updated successfully';
                $data['color']='success';

            } catch (PrestaShopWebserviceException $e) {
                $data['message']='oups, a problem occured!';
                $data['color']='danger';

            }

            $this->load->view('components/header');
            $this->load->view('components/nav');
            $this->load->view('components/userPanel');
            $this->load->view('components/sec');
            $this->load->view('profile',$data);
            $this->load->view('components/footer');
        }else{
            $this->load->view('components/header');
            $this->load->view('components/nav');
            $this->load->view('components/userPanel');
            $this->load->view('components/sec');
            $this->load->view('profile');
            $this->load->view('components/footer');
        }

        

    }

}