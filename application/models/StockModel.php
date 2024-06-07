<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

require 'vendor/autoload.php';
require_once './libraries/PhpXlsxGenerator.php';

class StockModel extends CI_Model
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('ProductModel');
        $this->load->model('LoginModel');
    }

    public function get_excel_file_stock_id_Auto($id_supplier)
    {

        // auto --> used in auto tasks functions
        // this function is for stock files
        // there is only one excel file in progress for each supplier 
        // this function returns the id of the excel file having the id_supplier given
        // with status = progress
        $table_name = 'quantity_stock';
        $this->db->select('id');
        $this->db->from($table_name);
        $this->db->where('statut', 'progress');
        $this->db->where('id_supplier', $id_supplier);
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }
    public function get_excel_file_stock_id($id_supplier)
    {
        // there is only one excel file in progress for each supplier 
        // this function is for stock files
        // this function returns the id of the excel file having the id_supplier given
        // with status = progress
        $table_name = 'quantity_stock';
        $this->db->select('id');
        $this->db->from($table_name);
        $this->db->where('statut', 'progress');
        $this->db->where('id_supplier', $id_supplier);
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }
    public function get_excel_file_stock_path_Auto($id_supplier)
    {
        // auto --> used in auto tasks functions
        // this function is for stock files
        // this function returns the path of the excel file having the id_supplier given
        // with status = progress
        $table_name = 'quantity_stock';
        $this->db->select('file_path');
        $this->db->from($table_name);
        $this->db->where('statut', 'progress');
        $this->db->where('id_supplier', $id_supplier);
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getNotCreatedProducts_Auto($id_supplier)
    {


        // auto --> used in auto tasks functions
        // this function returns products that need to be created before updating there stock
        // each product/variant had an id_supplier
        // this function will be called during each product creation
        // if an ean13 is found in the product excel file, it will be created and have its stock updated directly
        //Get products in batches of 10
        $table_name = 'stock_on_hold';
        $this->db->distinct();
        $this->db->select('ean,quantity');
        $this->db->from($table_name);
        $this->db->where('id_supplier', $id_supplier);
        $this->db->limit(10);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function update_stock_Auto($id_supplier)
    {
        // auto --> used in auto tasks functions

        $productModel = new ProductModel;
        $loginModel = new LoginModel;

        $file_path = $this->get_excel_file_stock_path_Auto($id_supplier);
        // get the path of the excel file
        if (empty($file_path)) {

            // if empty -> no stock to be updated
            return;
        }

        $file_data = file_get_contents($file_path['file_path']);
       
        if ($file_data) {
            // Écrire le contenu du fichier dans un fichier temporaire
            $temp_file = tempnam(sys_get_temp_dir(), 'excel_');
            file_put_contents($temp_file, $file_data);
            $spreadsheet = IOFactory::load($temp_file);
            $sheetdata = $spreadsheet->getActiveSheet()->toArray();
            $sheetcount = count($sheetdata);

            $data = [];
            try {
                for ($i = 1; $i < $sheetcount; $i++) {
                    // get all product ids of this supplier
                    $idArray = $productModel->getProductIds_Auto($id_supplier);
                    // get id_product from ean13
                    $id_product = $productModel->getIdFromReference_Auto($sheetdata[$i][0], $id_supplier);
                    $quantity = $sheetdata[$i][1];
                    if ($id_product != null) {
                        // product belongs to this supplier
                        $stockId = $productModel->getStockFromProductId((int)$id_product);

                        if (count($stockId) != 1) {
                            // ean13 of product with variant -> can't change its quantity
                            $tempData['ean'] = $sheetdata[$i][0];
                            $tempData['quantity'] = $quantity;
                            $insertData[] = $tempData;

                            continue;
                        }
                        // ean13 of a simple product 
                        $data[] = [$stockId[0], (int)$id_product, 0, $sheetdata[$i][1]];
                    } else {
                        $stock = $productModel->getStockFromVariantEan($sheetdata[$i][0]);
                        if ($stock != null) {
                            // variant exists
                            $key = array_search($stock['id_product'], $idArray);
                            if ($key) {
                                // product belongs to this supplier
                                // we can update variant quantity 
                                $data[] = [$stock['id_stock'], $stock['id_product'], $stock['id_product_attribute'], $sheetdata[$i][1]];
                            } else {
                                // does not belong to this supplier
                                // must create product and variant
                                $tempData['ean'] = $sheetdata[$i][0];
                                $tempData['quantity'] = $quantity;
                                $insertData[] = $tempData;
                            }
                        } else {
                            // variant/product does not exist
                            $tempData['ean'] = $sheetdata[$i][0];
                            $tempData['quantity'] = $quantity;
                            $insertData[] = $tempData;

                            // must create variant or product
                        }
                    }
                }
                foreach ($data as $product) {
                    // update quantity of products and variants that exist
                    // generate an xml
                    $xmlString = $productModel->stockXmlGenerator($product[0], $product[1], $product[2], $product[3]);
                  
                    // send to webservice
                    $data = $productModel->updateStock($xmlString, $product[0]);
                }

                if (isset($insertData)) {
                    // if some products need to be created first
                    $GLOBALS['failStock'] = true;
                    // insert into database products that we failed to update their quantity
                    $productModel->stockOnHold_Auto($insertData, $id_supplier);
                    $data['message'] = 'Some products are missing, please create them!';
                    $data['color'] = 'warning';
                }
                // change file status to done
                $productModel->stockFileRed_Auto($id_supplier);
                // delete supplier sata from users table
                // reminder: the users table contains suppliers_ids that clicked on upload file to insert products or update stock
                // this table was created bcz we can't get session data from cmd
                $loginModel->deleteUserdata($id_supplier, 'update_stock');
                // get email address and send email 
                $email = $loginModel->getEmailFromIdSupplier($id_supplier);
                $this->sendEmail($email);
            } catch (Exception $e) {
                $data['message'] = 'Failed to upload stock,Uploaded file does not match the expected template' . count($sheetdata[0]);
                $data['color'] = 'danger';
            }
        }
    }
    public function sendEmail($receiver_email)
    {

        // this function is for sending an email to the supplier after end of process 
        // NB : you can get the generated app paswword for your email address in mail workspace->search for application password / mot de passe des applications and create one
        // you can see the following youtube video for more details: https://youtu.be/4TmD4ly7V_E?si=F0zocmxEyDhC-nmu

        $config = array(
            'protocol' => 'smtp',
            'smtp_host' => 'smtp.googlemail.com',
            'smtp_port' => 587,
            'smtp_user' => 'kaoutarbaitar@gmail.com', // use your email address here
            'smtp_pass' => 'jkki lqmy znbv jqpn', // Use your generated App Password
            'charset' => 'utf-8',
            'mailtype' => 'html',
            'smtp_crypto' => 'tls',
            'newline' => "\r\n",
            'validation' => TRUE
        );
        $this->load->library('email');
        $this->email->initialize($config);
        $this->email->from('kaoutarbaitar@gmail.com', 'Kaoutar'); // use your email address here
        $this->email->to($receiver_email);
        $this->email->subject('Opération terminée');
        $this->email->message('Votre stock a été mis à jour .
        Vous pouvez désormais consulter les nouvelles quantités disponibles sur votre espace fournisseur.
        Si certains produits n ont pas été mis à jour, vous trouverez un fichier Excel détaillant les produits concernés.
        Merci de votre confiance et de votre fidélité.
        Cordialement,
        Equipe My Outlet Store'); // you can change the message here

        if ($this->email->send()) {
            echo 'Your email was sent.';
        } else {
            echo 'Your email could not be sent.';
            echo $this->email->print_debugger();
        }
    }

    public function update_stock_after_create_Auto($id_supplier)
    {
        // auto --> used in auto tasks functions
        // this function is used to update products stock in product creation function
        // if a new product is created, we search in stock_on_hold to update its quantity
        // stock_on_hold contains products that are waiting to be created to update there stock
        $productModel = new ProductModel;
        // get products that need to be created in order to update there stock
        $file_data = $this->getNotCreatedProducts_Auto($id_supplier);

        if (empty($file_data)) {
            // if it is empty, no product needs to be created
            return;
        }
        foreach ($file_data as $product) {
            // same process in update_stock_auto is done
            // get all product ids of this supplier
            $idArray = $productModel->getProductIds_Auto($id_supplier);
            // get id_product from ean13
            $id_product = $productModel->getIdFromReference_Auto($product['ean'], $id_supplier);
            $quantity = $product['quantity'];

            if ($id_product != null) {
                // product belongs to this supplier
                $stockId = $productModel->getStockFromProductId((int)$id_product);

                if (count($stockId) != 1) {
                    // ean13 of product with variant -> can't change its quantity
                    continue;
                }
                // ean13 of a simple product 
                $data[] = [$stockId[0], (int)$id_product, 0, $product['quantity'], $product['ean']];
            } else {
                $stock = $productModel->getStockFromVariantEan($product['ean']);
                if ($stock != null) {
                    // variant exists

                    $key = array_search($stock['id_product'], $idArray);
                    if ($key) {
                        // product belongs to this supplier
                        // we can update variant quantity 
                        $data[] = [$stock['id_stock'], $stock['id_product'], $stock['id_product_attribute'], $product['quantity']];
                    }
                }
            }
            // any other condition -> product still not created, nothing happens
        }


        try {

            if(isset($data)){
                foreach ($data as $products) {

                    // update stock of newly created products and delete them from stock_on_hold table in db
                    $xmlString = $productModel->stockXmlGenerator($products[0], $products[1], $products[2], $products[3]);
                    $data = $productModel->updateStock($xmlString, $products[0]);
                    $this->emptyStock($products[4], $id_supplier);
                }
            }
            
        } catch (Exception $e) {

            return $e->getMessage();
        }
    }
    public function emptyStock($ean, $id_supplier)
    {
        // delete the ean13 of the products after creating them
        $this->db->where('ean', $ean);
        $this->db->where('id_supplier', $id_supplier);
        $this->db->delete('stock_on_hold');
    }
}
