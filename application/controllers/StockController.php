<?php
require_once './libraries/xml2json.php';

defined('BASEPATH') or exit('No direct script access allowed');


class StockController extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('StockModel');
        $this->load->model('LoginModel');
    }
    public function index()
    {
    }

    public function stock_update_auto()
    {

        // this function is used in the task manager to update stock
        $stockModel = new StockModel();
        $loginModel = new LoginModel();
        // get supplier ids that have uploaded an excel file 
        $suppliers = $loginModel->getCurrentSuppliers('update_stock');
        // var_dump($this->session->userdata('id_supplier'));
        foreach ($suppliers as $supplier) {

            // update stock for each supplier 
            print_r(PHP_EOL . 'currently updating stock for supplier: ' . $supplier['id_supplier']);
            echo PHP_EOL . '----------------------';
            $stockModel->update_stock_Auto($supplier['id_supplier']);
            echo (PHP_EOL . 'stock updated for supplier: ' . $supplier['id_supplier']);
        }
    }
}
