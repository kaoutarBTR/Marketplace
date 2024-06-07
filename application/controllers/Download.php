<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once './libraries/PhpXlsxGenerator.php';

class Download extends CI_Controller
{

    public $idSupplier;

    public function __construct()
    {
        parent::__construct();
        $this->idSupplier = $this->session->userdata('id_supplier');
        $this->load->model('ProductModel');
        $this->load->model('DownloadModel');
        $this->load->model('LoginModel');
        $this->load->model('StockModel');
    }
    public function productTemplate()
    {
        // to generate a template for inserting new products 
        // give a name to the file  
        $fileName = 'Product_template.xls';
        // fill the file with some data 
        $excelData[] = array('PRICE', 'NAME', 'ID_MANUFACTURER',    'ID_SUPPLIER',    'CATEGORY', 'CATEGORY_DEFAULT');
        $excelData[] = array(100, 'template', 1, 1, 1, 1);
        // make it donwloadable 
        $xlsx = CodexWorld\PhpXlsxGenerator::fromArray(($excelData));
        $xlsx->downloadAs($fileName);
        exit();
    }
    public function stockTemplate()
    {
        // to generate a template file for stock update 
        // give a name to the file  
        $fileName = 'Stock_template.xls';
        // fill the file with some data 
        $excelData[] = array('EAN13', 'quantity');
        $excelData[] = array('143245677654', 134);

        // make it donwloadable 
        $xlsx = CodexWorld\PhpXlsxGenerator::fromArray(($excelData));
        $xlsx->downloadAs($fileName);
        exit();
    }
    public function productFile()
    {
        // to stock the product excel file in database
        $DownloadModel = new DownloadModel;
        $productModel = new ProductModel;
        $log = new LoginModel;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // stock the id of the supplier that uploaded the file in the users table in database with the role add_product
            $log->storeData($this->session->userdata('id_supplier'), $this->session->userdata('session_identifier'), 'add_product');

            $config['upload_path']          = './uploads/';
            $config['allowed_types']        = 'csv|xls|xlsx';


            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('excel')) {
                // failed to upoad excel file 
                $data['message'] = "Please upload a valid Excel file.";
                $data['color'] = 'danger';
                $GLOBALS['visible'] = true;
                $GLOBALS['fail'] = false;
                $this->load->view('components/header');
                $this->load->view('components/nav');
                $this->load->view('components/userPanel');
                $this->load->view('components/sec');
                $this->load->view('addProduct', $data);
                $this->load->view('components/footer');
            } else {
                $data = array('upload_data' => $this->upload->data());

                foreach ($data as $dataUploaded) {

                    // get the path of the excel file 
                    $file_path = $dataUploaded['full_path'];
                }
                // stock the path in database 
                $id_stock = $DownloadModel->store_excel_file_path($file_path);
                // get content to store products ean13 
                $file_data = file_get_contents($file_path);
                $GLOBALS['fail'] = false;
                if ($file_data) {
                    // Écrire le contenu du fichier dans un fichier temporaire
                    $temp_file = tempnam(sys_get_temp_dir(), 'excel_');
                    file_put_contents($temp_file, $file_data);

                    require_once 'vendor/autoload.php';

                    $spreadsheet = IOFactory::load($temp_file);
                    $sheetdata = $spreadsheet->getActiveSheet()->toArray();
                    $sheetcount = count($sheetdata);

                    if (count($sheetdata[0]) != 8) {
                        $data['messageTemplate'] = "Uploaded file does not match the expected template.";
                    }

                    $table = [];
                    // store the products ean13 and varinats ean13 in table productId->variantId->idStock
                    for ($i = 1; $i < $sheetcount; $i++) {
                        // if the first cell is empty, meanning we are currently in the same product with a different vriant 
                        if ($sheetdata[$i][0] == 0) {
                            // takes the last product ean13
                            $ean_product = $ean_product;
                        } else {
                            $ean_product = $sheetdata[$i][0];
                        }
                        $ean_variant = $sheetdata[$i][5];
                        $table[] = [$ean_product, $ean_variant, $id_stock];
                    }

                    // store table array in database 
                    $success = $productModel->insertProductIds($table);

                    if ($success) {
                        $GLOBALS['visible'] = false;

                        $data['message'] = "Nous avons bien reçu votre fichier. Vous recevrez une notification dès que le traitement sera terminé. Merci de votre patience !";
                        $data['color'] = 'success';
                    } else {
                        // an error occured, change status to fail
                        $GLOBALS['visible'] = true;
                        $productModel->failExcel($inserted_file_id);

                        $data['message'] = "Une erreur s'est produite lors du traitement du fichier. Veuillez réessayer ultérieurement ou contacter le support technique pour obtenir de l'aide.";
                        $data['color'] = 'danger';
                    }
                } else {
                    $data['message'] = "Aucun fichier n'a été trouvé.";
                    $data['color'] = 'danger';
                }

                $this->load->view('components/header');
                $this->load->view('components/nav');
                $this->load->view('components/userPanel');
                $this->load->view('components/sec');
                $this->load->view('addProduct', $data);
                $this->load->view('components/footer');
            }
        } else {
            // get last excel file id from database 
            $id_stocks = $DownloadModel->get_last_excel_file_id();
            $id_stock = 0;
            if (($id_stocks) != null) {
                $id_stock = $id_stocks['id'];
            }
            // if failed products or variants exist in database show a download button for failed products excel file
            if ($productModel->getFailedVariants($id_stock) != null || $productModel->getFailedProducts($id_stock) != null) {
                $GLOBALS['fail'] = true;
            } else {
                $GLOBALS['fail'] = false;
            }
            // if an excel file with status pending is still in database, the user does not have the right to upload an other file until its status is changed to fail or done
            if ($DownloadModel->get_excel_file() != null) {
                $GLOBALS['visible'] = false;
            } else {
                $GLOBALS['visible'] = true;
            };

            $this->load->view('components/header');
            $this->load->view('components/nav');
            $this->load->view('components/userPanel');
            $this->load->view('components/sec');
            $this->load->view('addProduct');
            $this->load->view('components/footer');
        }
    }
    public function stockFile()
    {
        // to stock the stockUpdate excel file in database
        $log = new LoginModel;

        $productModel = new ProductModel;
        $DownloadModel = new DownloadModel;
        $stockModel = new StockModel;



        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $GLOBALS['failStock'] = false;

            $GLOBALS['visibleStock'] = false;
            // stock the id of the supplier that uploaded the file in the users table in database with the role update_stock

            $log->storeData($this->session->userdata('id_supplier'), $this->session->userdata('session_identifier'), 'update_stock');


            $config['upload_path']          = './uploads/';
            $config['allowed_types']        = 'csv|xls|xlsx';


            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('excel')) {
                $GLOBALS['visibleStock'] = true;
                $data['message'] = "Please upload a valid Excel file.";
                $data['color'] = 'danger';

                $this->load->view('components/header');
                $this->load->view('components/nav');
                $this->load->view('components/userPanel');
                $this->load->view('components/sec');
                $this->load->view('stockProduct', $data);
                $this->load->view('components/footer');
            } else {
                $data = array('upload_data' => $this->upload->data());

                foreach ($data as $dataUploaded) {
                    // get the path of the excel file 

                    $file_path = $dataUploaded['full_path'];
                }
                // stock the path in database 

                $id_stock = $DownloadModel->store_excel_file_quantity_path($file_path);
                // search for products that we failed to upload their stock 
                $file_data = $productModel->getStockOnHold();
                if ($file_data != null) {
                    // if not null a download button will be shown
                    $GLOBALS['failStock'] = true;
                } else {
                    $GLOBALS['failStock'] = false;
                }
                $this->load->view('components/header');
                $this->load->view('components/nav');
                $this->load->view('components/userPanel');
                $this->load->view('components/sec');
                $this->load->view('stockProduct', $data);
                $this->load->view('components/footer');
            }
        } else {

            // if an excel file not treated yet is still in database ,the user is not allowed to upload an other one
            if ($stockModel->get_excel_file_stock_id($this->idSupplier)) {
                $GLOBALS['visibleStock'] = false;
            } else {
                $GLOBALS['visibleStock'] = true;
            }
            // search for products that we failed to upload their stock 
            $file_data = $productModel->getStockOnHold();
            if ($file_data != null) {
                // if not null a download button will be shown
                $GLOBALS['failStock'] = true;
            } else {
                $GLOBALS['failStock'] = false;
            }



            $this->load->view('components/header');
            $this->load->view('components/nav');
            $this->load->view('components/userPanel');
            $this->load->view('components/sec');
            $this->load->view('stockProduct');
            $this->load->view('components/footer');
        }
    }
    public function failExcel()
    {
        // create an excel file with failed products and variants during creation
        $productModel = new ProductModel;
        $downloadModel = new DownloadModel;
        $id_stocks = $downloadModel->get_last_excel_file_id();
        $id_stock = $id_stocks['id'];

        $fileName = 'FailedProducts.xls';
        $excelData[] = array('ean_product', 'price', 'name', 'category',    'category_default', 'ean_variant	', 'attribute', 'ref');
        $failedProducts = $productModel->getFailedProducts($id_stock);
        $failedVariants = $productModel->getFailedVariants($id_stock);
        if ($failedProducts != null) {
            // get data from the excel file to show the whole line 
            // if it is a failed product, we only need to show the product data 
            foreach ($failedProducts as $product) {
                $dta = $productModel->getProductData($product, $id_stock);
                $excelData[] = array($product, $dta['price'], $dta['name'], $dta['category'], $dta['categoryDefault']);
            }
        }
        if ($failedVariants != null) {
            // get data from the excel file to show the whole line 
            // if it is a failed variant we need to show the product data too
            foreach ($failedVariants as $variant) {
                $variantData = $productModel->getVariantData($variant['ean_variant'], $id_stock);
                $productData = $productModel->getProductData($variant['ean_product'], $id_stock);


                $excelData[] = array($variant['ean_product'], $productData['price'], $productData['name'], $productData['category'], $productData['categoryDefault'], $variant['ean_variant'], $variantData['attribute_ids'], $variantData['ref']);
            }
        }
        // make it downloadable
        $xlsx = CodexWorld\PhpXlsxGenerator::fromArray(($excelData));
        $xlsx->downloadAs($fileName);
        exit();
    }

    public function failExcelStock()
    {
        // create an excel file with failed products and variants during stock update
        $productModel = new ProductModel();
        // get the products and varinats from stockOnHold
        $file_data = $productModel->getStockOnHold();

        $fileName = 'FailedStockUpdate.xlsx';
        if (ob_get_length()) ob_end_clean();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // fill the excel file with ean13 an quantity of each product or variant 
        // Set the headers
        $sheet->setCellValue('A1', 'ean_product');
        $sheet->setCellValue('B1', 'quantity');

        // Fill the data
        if ($file_data != null) {
            $GLOBALS['failStock'] = true;
            $rowNumber = 2;
            foreach ($file_data as $product) {
                $sheet->setCellValue('A' . $rowNumber, $product['ean']);
                $sheet->setCellValue('B' . $rowNumber, $product['quantity']);
                $rowNumber++;
            }
        }

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
    }
}
