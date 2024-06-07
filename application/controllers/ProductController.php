<?php
require_once('./vendor/autoload.php');
require_once './libraries/xml2json.php';
require_once 'vendor/autoload.php'; // Assurez-vous que le chemin est correct
use PhpOffice\PhpSpreadsheet\IOFactory;

defined('BASEPATH') or exit('No direct script access allowed');


class ProductController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('pagination');
        $this->load->model('ProductModel');
        $this->load->model('DownloadModel');
        $this->load->model('LoginModel');
        $this->load->model('StockModel');
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
        $this->email->subject('Email Test');
        $this->email->message('Vots produits ont été mis à jour .
        Vous pouvez désormais consulter les nouveaux produits disponibles sur votre espace fournisseur.
        Si certains produits n ont pas été créés, vous trouverez un fichier Excel détaillant les produits concernés.
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
    public function gerer()
    {


        $productModel = new ProductModel;

        // get all products count for pagination

        $allProducts = $productModel->getProduct(0, 0);
        $total = 0;
        if ($allProducts != null) {
            $total = count($allProducts);
        }

        $config['base_url'] = base_url('ProductController/gerer');

        $perPage = 10;
        $page = $this->input->get('page');
        $page = ($page && is_numeric($page) && $page > 0) ? (intval($page) / 10) + 1 : 1;

        $start_index = 0;
        if ($page != 0) {
            $start_index = $perPage * ($page - 1);
        }
        $config['per_page'] = $perPage;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['prev_link'] = '&laquo;';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_link'] = '&raquo;';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['first_link'] = 'First';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = 'Last';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['total_rows'] = $total;

        $this->pagination->initialize($config);

        $data['links'] = $this->pagination->create_links();
        $data['page'] = $page;

        //Get products in batches of 10
        $products = $productModel->getProduct($perPage, $start_index);


        $stocks = $productModel->getStocks();
        $rates = $productModel->getTaxe();

        // in case we only have 1 product in database --> not array
        if ($products != null && isset($products['id'])) {

            $prod = $products;

            // initialize arrays 
            $optionQuantity = [];
            $optionImage = [];
            $productsTable['marque'] = '--';
            $productsTable[$prod['id']]['options'] = array();
            $productsTable[$prod['id']]['option_images'] = array();
            $productOptionsQuantities[$prod['id']] = array();
            $allOptions = [];
            $optionIds = [];
            $optionIds[0] = '';

            $productsTable['id'] = $prod['id'];
            // manufacturer name is an array if no manufacturer was selected 
            if (isset($prod['manufacturer_name']) & !is_array($prod['manufacturer_name'])) {

                $productsTable['marque'] = $prod['manufacturer_name'];
            }



            //!done remove $ here

            foreach ($stocks as $stc) {

                if (isset($stc['id_product'])) {
                    if ($stc['id_product'] == $prod['id']) {

                        if ($stc['quantity'] > 0) {
                            $productsTable['total_quantity'] = $stc['quantity'];
                        } else {
                            $productsTable['total_quantity'] = 0;
                        }
                        break;
                    }
                }
            }

            // !log execution time 
            foreach ($prod['associations'] as $options_values) {

                if (isset($options_values['product_option_value'])) {

                    if (isset($options_values['product_option_value']['id'])) {
                        // in case only one variant exists 
                        $optionId = $options_values['product_option_value']['id'];
                        //todo remove $ here
                        $options = $productModel->getOptions($optionId);
                        // $options contains option details of this variant , or null 
                        if ($options != null) {
                            // fill the $alloptions :  id->name 
                            $allOptions[$options['id']] = ($options['name']['language']['$']);
                            //fill $optionIds with only option ids
                            $optionIds[] = ($options['id']);
                        }
                    } else {
                        // in case if we have more than 1 variant 
                        foreach ($options_values['product_option_value'] as $options_value) {

                            $optionId = $options_value['id'];
                            //todo remove $ here
                            $options = $productModel->getOptions($optionId);
                            // $options contains option details of this variant , or null 
                            // fill the $alloptions :  id->name 
                            $allOptions[$options['id']] = ($options['name']['language']['$']);
                            //fill $optionIds with only option ids
                            $optionIds[] = ($options['id']);
                        }
                    }
                }
            }

            // get combinations of current product 
            $combinations = $productModel->getCombinations($prod['id']);
            // $combinations contains product combinations , or null 
            if ($combinations != null) {


                // if we have only one combination 
                if (isset($combinations['associations']['product_option_values']['product_option_value'])) {

                    // Get product variant image 
                    if (isset($combinations['associations']['images']['image']['id'])) {
                        // if image exists get image id
                        $option_imageId = $combinations['associations']['images']['image']['id'];
                        // if image id = 123 --> then the folder of the product image is prestashop/p/img/p/1/2/3/123 
                        $option_imageFolder = implode('/', str_split($option_imageId));
                        $option_imageLink = 'http://localhost/prestashop_/img/p/' . $option_imageFolder . '/' . $option_imageId . '.jpg';
                    } else {
                        // if no image is found the image will be a default product image in img folder 
                        $option_imageLink = base_url('/img/product.png');
                    }


                    $OptionName = [];
                    // if we have only one option value --> product option value has only one id
                    if (isset($combinations['associations']['product_option_values']['product_option_value']['id'])) {
                        
                        
                        // get the name of the option using id_option and the array filled before (alloptions)
                        $OptionName[] = $allOptions[$combinations['associations']['product_option_values']['product_option_value']['id']];
                        $OptionNames = implode(', ', $OptionName);
                       
                        // fill optionQuantity : option name-> quantity that will help us to view variant informations in model
                        $optionQuantity[$OptionNames] = $combinations['quantity'];
                        // same for optionImage 
                        $optionImage[$OptionNames] = $option_imageLink;
                    } else {
                        // if we have more than one option value --> product option value is array

                        foreach ($combinations['associations']['product_option_values']['product_option_value'] as $combo) {



                            if (isset($combo['id'])) {
                                // fill the array with option names using option id and alloptions array 
                                $OptionName[] = $allOptions[$combo['id']];
                            }
                        }
                        $OptionNames = implode(', ', $OptionName);
                        // fill option quantity and option image same way 
                        $optionQuantity[$OptionNames] = $combinations['quantity'];
                        $optionImage[$OptionNames] = $option_imageLink;
                    }
                } else {
                    // if we have more than 1 combination 
                    foreach ($combinations as $comb) {

                        // get product image 
                        if (isset($comb['associations']['images']['image']['id'])) {
                            $option_imageId = $comb['associations']['images']['image']['id'];
                            $option_imageFolder = implode('/', str_split($option_imageId));
                            $option_imageLink = 'http://localhost/prestashop_/img/p/' . $option_imageFolder . '/' . $option_imageId . '.jpg';
                        } else {

                            $option_imageLink = base_url('/img/product.png');
                        }
                        // empty option name array for each combination
                        $OptionName = [];


                        if (isset($comb['associations']['product_option_values']['product_option_value'])) {



                            // one option value  --> product option value has only one id
                            if (isset($comb['associations']['product_option_values']['product_option_value']['id'])) {

                                $OptionName[] = $allOptions[$comb['associations']['product_option_values']['product_option_value']['id']];
                                $OptionNames = implode(', ', $OptionName);
                                $optionQuantity[$OptionNames] = $comb['quantity'];
                                $optionImage[$OptionNames] = $option_imageLink;
                            } else {
                                // more than one option value  --> product option value is array
                                foreach ($comb['associations']['product_option_values']['product_option_value'] as $combo) {
                                    if (isset($combo['id'])) {
                                        $OptionName[] = $allOptions[$combo['id']];
                                    }
                                }
                                $OptionNames = implode(', ', $OptionName);
                                $optionQuantity[$OptionNames] = $comb['quantity'];
                                $optionImage[$OptionNames] = $option_imageLink;
                            }
                        }
                    }
                }
                // if variant exists it will fill the table with id->option 
                $productsTable[$prod['id']]['options'] = $optionQuantity;
                $productsTable[$prod['id']]['option_images'] = $optionImage;
            }




            $productsTable['id'] = $prod['id'];
            $productsTable['statut'] = $prod['active'];
            $productsTable['image_id'] = $productModel->getProductImage($prod['id']);
            if ($productsTable['image_id'] != null) {
                $productsTable['image_folder'] = implode('/', str_split($productsTable['image_id']));
                $productsTable['image_link'] = 'http://localhost/prestashop_/img/p/' . $productsTable['image_folder'] . '/' . $productsTable['image_id'] . '.jpg';
            } else {
                $productsTable['image_link'] = base_url('/img/product.png');
            }

            if (isset($prod['id_tax_rules_group']) & isset($rates[$prod['id_tax_rules_group']])) {

                $prodTax = $prod['id_tax_rules_group'];
                $taxedPrice = $prod['price'] * (1 + $rates[$prodTax] / 100);
                $productsTable['prix'] = $taxedPrice;
            } else {
                $productsTable['prix'] = number_format($prod['price'], 2);
            }

            $sales = $productModel->getSales($prod['id']);


            $promo = false;
            if ($sales == null) {
                $promo = false;
            } else $promo = true;
            $productsTable['promo'] = $promo ? 'oui' : 'non';
            $productsTable['condition'] = $prod['condition'];


        
            if (isset($prod['ean13'])&!is_array($prod['ean13'])) {
                $productsTable['reference'] = $prod['ean13'];
            } else {
                $productsTable['reference'] = '--';
            }
            
            if (isset($prod['name']['language'])) {
                $productsTable['nom'] = $prod['name']['language'];
            } else {
                $productsTable['nom'] = 'not defined';
            }


            $productsTab[] = $productsTable;
        } elseif ($products != null && !isset($products['id'])) {
            // in case of an array of products --> more than 1 product
            // same procedure

            foreach ($products as $prod) {


                // initialize arrays 
                $optionQuantity = [];
                $optionImage = [];
                $productsTable['marque'] = '--';
                $productsTable[$prod['id']]['options'] = array();
                $productsTable[$prod['id']]['option_images'] = array();
                $productOptionsQuantities[$prod['id']] = array();
                $allOptions = [];
                $optionIds = [];
                $optionIds[0] = '';

                $productsTable['id'] = $prod['id'];
                // manufacturer name is an array if no manufacturer was selected 
                if (isset($prod['manufacturer_name']) & !is_array($prod['manufacturer_name'])) {

                    $productsTable['marque'] = $prod['manufacturer_name'];
                }



                //!done remove $ here

                foreach ($stocks as $stc) {

                    if (isset($stc['id_product'])) {
                        if ($stc['id_product'] == $prod['id']) {

                            if ($stc['quantity'] > 0) {
                                $productsTable['total_quantity'] = $stc['quantity'];
                            } else {
                                $productsTable['total_quantity'] = 0;
                            }
                            break;
                        }
                    }
                }

                // !log execution time 
                foreach ($prod['associations'] as $options_values) {

                    if (isset($options_values['product_option_value'])) {

                        if (isset($options_values['product_option_value']['id'])) {
                            // in case only one variant exists 
                            $optionId = $options_values['product_option_value']['id'];
                            //todo remove $ here
                            $options = $productModel->getOptions($optionId);
                            // $options contains option details of this variant , or null 
                            if ($options != null) {
                                // fill the $alloptions :  id->name 
                                $allOptions[$options['id']] = ($options['name']['language']['$']);
                                //fill $optionIds with only option ids
                                $optionIds[] = ($options['id']);
                            }
                        } else {
                            // in case if we have more than 1 variant 
                            foreach ($options_values['product_option_value'] as $options_value) {

                                $optionId = $options_value['id'];
                                //todo remove $ here
                                $options = $productModel->getOptions($optionId);
                                // $options contains option details of this variant , or null 
                                // fill the $alloptions :  id->name 
                                $allOptions[$options['id']] = ($options['name']['language']['$']);
                                // print_r($allOptions);
                                // die();
                                //fill $optionIds with only option ids
                                $optionIds[] = ($options['id']);
                            }
                        }
                    }
                }

                // get combinations of current product 
                $combinations = $productModel->getCombinations($prod['id']);
                // $combinations contains product combinations , or null 
                if ($combinations != null) {


                    // if we have only one combination 
                    if (isset($combinations['associations']['product_option_values']['product_option_value'])) {

                        // Get product variant image 
                        if (isset($combinations['associations']['images']['image']['id'])) {
                            // if image exists get image id
                            $option_imageId = $combinations['associations']['images']['image']['id'];
                            // if image id = 123 --> then the folder of the product image is prestashop/p/img/p/1/2/3/123 
                            $option_imageFolder = implode('/', str_split($option_imageId));
                            $option_imageLink = 'http://localhost/prestashop_/img/p/' . $option_imageFolder . '/' . $option_imageId . '.jpg';
                        } else {
                            // if no image is found the image will be a default product image in img folder 
                            $option_imageLink = base_url('/img/product.png');
                        }


                        $OptionName = [];
                        // if we have only one option value --> product option value has only one id
                        if (isset($combinations['associations']['product_option_values']['product_option_value']['id'])) {

                            // get the name of the option using id_option and the array filled before (alloptions)
                            $OptionName[] = $allOptions[$combinations['associations']['product_option_values']['product_option_value']['id']];
                            $OptionNames = implode(', ', $OptionName);

                            // print_r($OptionName);
                            // die();
                            // fill optionQuantity : option name-> quantity that will help us to view variant informations in model
                            $optionQuantity[$OptionNames] = $combinations['quantity'];
                            // same for optionImage 
                            $optionImage[$OptionNames] = $option_imageLink;
                        } else {
                            // if we have more than one option value --> product option value is array

                            foreach ($combinations['associations']['product_option_values']['product_option_value'] as $combo) {

                                if (isset($combo['id'])) {
                                    // fill the array with option names using option id and alloptions array 
                                    $OptionName[] = $allOptions[$combo['id']];
                                }
                            }
                            $OptionNames = implode(', ', $OptionName);
                            // print_r($OptionName);
                            // die();
                            // fill option quantity and option image same way 
                            $optionQuantity[$OptionNames] = $combinations['quantity'];
                            $optionImage[$OptionNames] = $option_imageLink;
                        }
                    } else {
                        // if we have more than 1 combination 
                        foreach ($combinations as $comb) {

                            // get product image 
                            if (isset($comb['associations']['images']['image']['id'])) {
                                $option_imageId = $comb['associations']['images']['image']['id'];
                                $option_imageFolder = implode('/', str_split($option_imageId));
                                $option_imageLink = 'http://localhost/prestashop_/img/p/' . $option_imageFolder . '/' . $option_imageId . '.jpg';
                            } else {

                                $option_imageLink = base_url('/img/product.png');
                            }
                            // empty option name array for each combination
                            $OptionName = [];


                            if (isset($comb['associations']['product_option_values']['product_option_value'])) {



                                // one option value  --> product option value has only one id
                                if (isset($comb['associations']['product_option_values']['product_option_value']['id'])) {

                                    $OptionName[] = $allOptions[$comb['associations']['product_option_values']['product_option_value']['id']];
                                    $OptionNames = implode(', ', $OptionName);
                                    $optionQuantity[$OptionNames] = $comb['quantity'];
                                    $optionImage[$OptionNames] = $option_imageLink;
                                } else {
                                    // more than one option value  --> product option value is array
                                    foreach ($comb['associations']['product_option_values']['product_option_value'] as $combo) {
                                        if (isset($combo['id'])) {
                                            $OptionName[] = $allOptions[$combo['id']];
                                        }
                                    }
                                    $OptionNames = implode(', ', $OptionName);
                                    // print_r($OptionName);
                                    // die();
                                    $optionQuantity[$OptionNames] = $comb['quantity'];
                                    $optionImage[$OptionNames] = $option_imageLink;
                                }
                            }
                        }
                    }
                    // if variant exists it will fill the table with id->option 
                    $productsTable[$prod['id']]['options'] = $optionQuantity;
                    $productsTable[$prod['id']]['option_images'] = $optionImage;
                }




                $productsTable['id'] = $prod['id'];
                $productsTable['statut'] = $prod['active'];
                $productsTable['image_id'] = $productModel->getProductImage($prod['id']);
                if ($productsTable['image_id'] != null) {
                    $productsTable['image_folder'] = implode('/', str_split($productsTable['image_id']));
                    $productsTable['image_link'] = 'http://localhost/prestashop_/img/p/' . $productsTable['image_folder'] . '/' . $productsTable['image_id'] . '.jpg';
                } else {
                    $productsTable['image_link'] = base_url('/img/product.png');
                }

                if (isset($prod['id_tax_rules_group']) & isset($rates[$prod['id_tax_rules_group']])) {

                    $prodTax = $prod['id_tax_rules_group'];
                    $taxedPrice = $prod['price'] * (1 + $rates[$prodTax] / 100);
                    $productsTable['prix'] = $taxedPrice;
                } else {
                    $productsTable['prix'] = number_format($prod['price'], 2);
                }

                $sales = $productModel->getSales($prod['id']);


                $promo = false;
                if ($sales == null) {
                    $promo = false;
                } else $promo = true;
                $productsTable['promo'] = $promo ? 'oui' : 'non';
                $productsTable['condition'] = $prod['condition'];


                if (isset($prod['ean13'])&!is_array($prod['ean13'])) {
                    $productsTable['reference'] = $prod['ean13'];
                } else {
                    $productsTable['reference'] = '--';
                }
                if (isset($prod['manufacturer_name'])) {
                    $productsTable['manufacturer'] = $prod['manufacturer_name'];
                } else {
                    $productsTable['manufacturer'] = '__';
                }
                if (isset($prod['name']['language'])) {
                    $productsTable['nom'] = $prod['name']['language'];
                } else {
                    $productsTable['nom'] = 'not defined';
                }


                $productsTab[] = $productsTable;
            }
        } else
            $productsTab = null;
        $data['productsTab'] = $productsTab;

        // $start_time = microtime(true);
        // $end_time = microtime(true);
        // $execution_time = $end_time - $start_time;
        // echo "Temps d'exécution produits --------------------------------------------------------------------------------: {$execution_time} secondes\n";

        $this->load->view('components/header');
        $this->load->view('components/nav');
        $this->load->view('components/userPanel');
        $this->load->view('components/sec');
        $this->load->view('manageProduct', $data);
        $this->load->view('components/footer');
    }

    public function stocker()
    {

        // inserting products used in button method 

        $productModel = new ProductModel;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $GLOBALS['failStock'] = false;
            $config['upload_path']          = './uploads/';
            $config['allowed_types']        = 'csv|xls|xlsx';


            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('excel')) {
                // failed to upload file
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

                // get data from excel file
                foreach ($data as $dataUploaded) {

                    $file_path = $dataUploaded['full_path'];
                }
                $file_data = file_get_contents($file_path);
                if ($file_data) {
                    $temp_file = tempnam(sys_get_temp_dir(), 'excel_');
                    file_put_contents($temp_file, $file_data);
                    $spreadsheet = IOFactory::load($temp_file);
                    $sheetdata = $spreadsheet->getActiveSheet()->toArray();
                    $sheetcount = count($sheetdata);

                    $data = [];
                    try {

                        // fill the arrays with data from excel
                        for ($i = 1; $i < $sheetcount; $i++) {

                            $idArray = $productModel->getProductIds();
                            $id_product = $productModel->getIdFromReference($sheetdata[$i][0]);

                            $quantity = $sheetdata[$i][1];


                            if ($id_product != null) {
                                $stockId = $productModel->getStockFromProductId((int)$id_product);

                                // stockId!=1 ---> product does not exist or has variants 
                                if (count($stockId) != 1) {
                                    $tempData['ean'] = $sheetdata[$i][0];
                                    $tempData['quantity'] = $quantity;
                                    $insertData[] = $tempData;
                                    continue;
                                }
                                $data[] = [$stockId[0], (int)$id_product, 0, $sheetdata[$i][1]];
                            } else {
                                $stock = $productModel->getStockFromVariantEan($sheetdata[$i][0]);
                                if ($stock != null) {
                                    // variant exists

                                    $key = array_search($stock['id_product'], $idArray);
                                    if ($key) {
                                        // product belongs to this supplier
                                        // update variant quantity 
                                        $data[] = [$stock['id_stock'], $stock['id_product'], $stock['id_product_attribute'], $sheetdata[$i][1]];
                                    } else {
                                        // must create product and variant
                                        $tempData['ean'] = $sheetdata[$i][0];
                                        $tempData['quantity'] = $quantity;
                                        $insertData[] = $tempData;
                                    }
                                } else {
                                    $tempData['ean'] = $sheetdata[$i][0];
                                    $tempData['quantity'] = $quantity;
                                    $insertData[] = $tempData;

                                    // must create variant or product
                                }
                                // variant or a product which does not exist

                            }
                        }


                        foreach ($data as $product) {

                            // generate an xml 
                            $xmlString = $productModel->stockXmlGenerator($product[0], $product[1], $product[2], $product[3]);
                            // update stock with generated xml 
                            $data = $productModel->updateStock($xmlString, $product[0]);
                        }

                        if (isset($insertData)) {
                            // if we have products that are not found 
                            $GLOBALS['failStock'] = true;
                            $productModel->stockOnHold($insertData);
                            $data['message'] = 'Some products are missing, please create them!';
                            $data['color'] = 'warning';
                        }
                    } catch (Exception $e) {
                        // other error 
                        $data['message'] = 'Failed to upload stock,Uploaded file does not match the expected template' . count($sheetdata[0]);
                        $data['color'] = 'danger';
                    }
                }
                $this->load->view('components/header');
                $this->load->view('components/nav');
                $this->load->view('components/userPanel');
                $this->load->view('components/sec');
                $this->load->view('stockProduct', $data);
                $this->load->view('components/footer');
            }
        } else {
            // file data contains products that we couldn't update there stock because they were not found 
            // if it is not empty the supplier will see a button to download an excel file of the current products in stock_on_hold
            
            $file_data = $productModel->getStockOnHold();
            if ($file_data != null) {
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
    public function stocker_Auto()
    {
        // inserting products used in task manager 

        $productModel = new ProductModel;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $GLOBALS['failStock'] = false;
            $config['upload_path']          = './uploads/';
            $config['allowed_types']        = 'csv|xls|xlsx';


            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('excel')) {
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

                // get data from excel file 
                foreach ($data as $dataUploaded) {

                    $file_path = $dataUploaded['full_path'];
                }
                $file_data = file_get_contents($file_path);
                if ($file_data) {
                    $temp_file = tempnam(sys_get_temp_dir(), 'excel_');
                    file_put_contents($temp_file, $file_data);
                    $spreadsheet = IOFactory::load($temp_file);
                    $sheetdata = $spreadsheet->getActiveSheet()->toArray();
                    $sheetcount = count($sheetdata);

                    $data = [];
                    try {


                        for ($i = 1; $i < $sheetcount; $i++) {

                            $idArray = $productModel->getProductIds();
                            $id_product = $productModel->getIdFromReference($sheetdata[$i][0]);
                            // product belongs to this supplier

                            $quantity = $sheetdata[$i][1];


                            if ($id_product != null) {
                                $stockId = $productModel->getStockFromProductId((int)$id_product);

                                if (count($stockId) != 1) {
                                    $tempData['ean'] = $sheetdata[$i][0];
                                    $tempData['quantity'] = $quantity;
                                    $insertData[] = $tempData;
                                    continue;
                                }
                                // product with variants/simple
                                $data[] = [$stockId[0], (int)$id_product, 0, $sheetdata[$i][1]];
                            } else {
                                $stock = $productModel->getStockFromVariantEan($sheetdata[$i][0]);
                                if ($stock != null) {
                                    // variant exists

                                    $key = array_search($stock['id_product'], $idArray);
                                    if ($key) {
                                        // product belongs to this supplier
                                        // update variant quantity 
                                        $data[] = [$stock['id_stock'], $stock['id_product'], $stock['id_product_attribute'], $sheetdata[$i][1]];
                                    } else {
                                        // must create product and variant
                                        $tempData['ean'] = $sheetdata[$i][0];
                                        $tempData['quantity'] = $quantity;
                                        $insertData[] = $tempData;
                                    }
                                } else {
                                    $tempData['ean'] = $sheetdata[$i][0];
                                    $tempData['quantity'] = $quantity;
                                    $insertData[] = $tempData;

                                    // must create variant or product
                                }
                                // variant or a product which does not exist

                            }
                        }


                        foreach ($data as $product) {
                            // update stock of products or variants in data array 
                            $xmlString = $productModel->stockXmlGenerator($product[0], $product[1], $product[2], $product[3]);


                            $data = $productModel->updateStock($xmlString, $product[0]);
                        }

                        if (isset($insertData)) {
                            $GLOBALS['failStock'] = true;
                            // inserting insertData array into database stock_on_hold to create these products later 
                            $productModel->stockOnHold($insertData);
                            $data['message'] = 'Some products are missing, please create them!';
                            $data['color'] = 'warning';
                        }
                    } catch (Exception $e) {
                        $data['message'] = 'Failed to upload stock,Uploaded file does not match the expected template' . count($sheetdata[0]);
                        $data['color'] = 'danger';
                    }
                }
                $this->load->view('components/header');
                $this->load->view('components/nav');
                $this->load->view('components/userPanel');
                $this->load->view('components/sec');
                $this->load->view('stockProduct', $data);
                $this->load->view('components/footer');
            }
        } else {
            
            $file_data = $productModel->getStockOnHold();
            // file data contains products that we couldn't update there stock because they were not found 
            // if it is not empty the supplier will see a button to download an excel file of the current products in stock_on_hold
            if ($file_data != null) {
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



    public function addProductsBySupplier($id_supplier)
    {

        $productModel = new ProductModel;
        $downloadModel = new DownloadModel;
        $loginModel = new LoginModel;
        $stockModel = new StockModel;

        // get pending products aka products not created yet 
        // this function gets products in batches of 10
        $pendingProducts = $productModel->getPendingProducts_Auto($id_supplier);

        if ($pendingProducts != null) {
            // create products that are still in pending 
            $productModel->createProducts_Auto($pendingProducts, $id_supplier);
            // the next code line is written in case if we want to create all products of a supplier in one 
            // $this->addProductsBySupplier($id_supplier);
        } else {
            // if no pending products were found change the status of the excel file to done
            $productModel->fileRed_Auto($id_supplier);
            // update stock for products in stock_on_hold that were not found before 
            $stockModel->update_stock_after_create_Auto($id_supplier);
            // delete userdata from users table in database
            $loginModel->deleteUserdata($id_supplier, 'add_product');
            // send an email to the current supplier 
            $email = $loginModel->getEmailFromIdSupplier($id_supplier);
            $this->sendEmail($email);
        }
    }
    public function ajouterProduits()
    {

        // this is the function called in task manager 

        $loginModel = new LoginModel;

        // get id of the suppliers that uploaded tere files 
        $suppliers = $loginModel->getCurrentSuppliers('add_product');
        foreach ($suppliers as $supplier) {

            // create products for each supplier 
            print_r(PHP_EOL . 'currently adding products for supplier: ' . $supplier['id_supplier']);
            echo PHP_EOL . '----------------------';
            $this->addProductsBySupplier($supplier['id_supplier']);
            echo (PHP_EOL . 'products created for supplier: ' . $supplier['id_supplier']);
        }
    }
}
