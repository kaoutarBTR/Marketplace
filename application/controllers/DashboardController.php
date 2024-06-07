<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once('./vendor/autoload.php');
require_once './libraries/xml2json.php';

class DashboardController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('ProductModel');
        $this->load->model('ClientModel');
        $this->load->model('AddressModel');
        $this->load->model('OrderModel');
    }


    public function index()
    {
        $productModel = new ProductModel;
        $clientModel = new ClientModel;
        $addressModel = new AddressModel;
        $orderModel = new OrderModel;
        $products = $productModel->getProduct(5, 0);
        // get last 5 products
        $allproducts = $productModel->getProduct(0, 0);
        // get all products 
        $allorders = $orderModel->supplierOrders();
        // get all orders 
        $orders = $orderModel->getOrder(5);
        // get last 5 orders 
        $supplierProducts = $productModel->getProductIds();
        // get products ids of the current supplier
        // I followed this method to get only orders that have at least a product of this supplier

        if ($allproducts != null) {
            $data['numProducts'] = count($allproducts);
        } else $data['numProducts'] = 0;

        //todo 
        // done
        if ($allorders != null) {
            $data['numOrders'] = ($allorders);
        } else   $data['numOrders'] = 0;

        // initialize arrays 
        $ordersTable = [];
        $ordersTab = [];

        if ($orders != null) {

            foreach ($orders as $ord) {

                // get customer of current order 
                $cust = $clientModel->manageClient($ord['id_customer']['$']);
                $ordersTable['total'] = 0;
                $total = 0;

                foreach ($ord['associations'] as $order_rows) {
                    // if there is only one product in this order -> only one order row 
                    if (isset($order_rows['order_row']['product_id'])) {
                        // stock the product_id in productIds
                        $productIds = $order_rows['order_row']['product_id']['$'];
                        // search for the productId in supplier products ids 
                        if(is_array($supplierProducts))
                        {if (in_array($productIds, $supplierProducts)) {
                            // if found -> count total price of this product
                            $total += $order_rows['order_row']['unit_price_tax_incl'] * $order_rows['order_row']['product_quantity'];
                            $ordersTable['total'] = $total;
                        }}
                        // if not found, total price not changed
                    } else {
                        // if there is more than one product in this order -> order row is an array with severla id_product

                        foreach ($order_rows['order_row'] as $orders) {
                            // stock the product_id in productIds
                            $productIds = $orders['product_id']['$'];
                            // search for the productId in supplier products ids 
                        if(is_array($supplierProducts))
                           { if (in_array($productIds, $supplierProducts)) {
                                // if found -> count total price of this product
                                $total += $orders['unit_price_tax_incl'] * $orders['product_quantity'];
                                $ordersTable['total'] = $total;
                            }}
                            // if not found, total price not changed
                        }
                    }
                }
                // if the total price of current order is still = 0 it means that there was no product of the current supplier -> move to nextorder 
                // this order will not be shown in the view 
                if ($ordersTable['total'] == 0) {
                    continue;
                }

                $ordersTable['id'] = $ord['id']; { {


                        // if customer is found, stock his name and make ex = true
                        if ($cust != null) {
                            $ex = true;
                            $ordersTable['firstname'] = $cust['firstname'];
                            $ordersTable['lastname'] = $cust['lastname'];

                            $ordersTable['email'] = $cust['email'];
                        } else {
                            //if ex = false, we will show 'client supprimé' in red
                            $ex = false;
                        }
                        $ordersTable['ex'] = $ex;
                        // get postcode of the delivery address 
                        $ordersTable['postcode'] =  $addressModel->getAddress($ord['id_address_delivery']['$']);
                    }
                }

                $ordersTable['date_add'] = $ord['date_add'];
                $ordersTable['delivery_date'] = $ord['delivery_date'];
                // get the state of the order (id)
                $order_states = $orderModel->getOrderState($ord['current_state']['$']);
                // to controll the color of the state
                $ordersTable['id_statut'] = $ord['current_state']['$']; //classe statut
                // to show the current state 
                // todo remove $
                $ordersTable['statut'] = $order_states['name']['language']['$']; //statut

                $ordersTab[] = $ordersTable;
            }
        } else {
            $ordersTab = null;
        }
        $data['ordersTab'] = $ordersTab;


        if ($products != null) {
            foreach ($products as $prod) {
                // get default image of the current product
                $productsTable['image_id'] = $productModel->getProductImage($prod['id']);
                if ($productsTable['image_id'] != null) {
                    // if image id is 123 -> the path of the image is prestashop/img/p/1/2/3/123
                    $productsTable['image_folder'] = implode('/', str_split($productsTable['image_id']));
                    $productsTable['image_link'] = 'http://localhost/prestashop_/img/p/' . $productsTable['image_folder'] . '/' . $productsTable['image_id'] . '.jpg';
                } else {
                    // if null, show the default product image
                    $productsTable['image_link'] = base_url('/img/product.png');
                }
                // ean13 is an array if it does not exist 
                if (isset($prod['ean13']) & !is_array($prod['ean13'])) {
                    $productsTable['reference'] = $prod['ean13'];
                } else {
                    $productsTable['reference'] = '--';
                }
                // manufacturer name is an array if no manufacturer was selected 

                if (isset($prod['manufacturer_name']) & !is_array($prod['manufacturer_name'])) {
                    $productsTable['manufacturer'] = $prod['manufacturer_name'];
                } else {
                    $productsTable['manufacturer'] = '__';
                }
                if (isset($prod['name']['language'])) {
                    $productsTable['nom'] = $prod['name']['language'];
                } else {
                    $productsTable['nom'] = 'not defined';
                }
                $productsTable['description'] = $productsTable['nom'] . '-' . $productsTable['manufacturer'];


                $productsTab[] = $productsTable;
            }
        } else
            $productsTab = null;
        $data['productsTab'] = $productsTab;

        $this->load->view('components/header');
        $this->load->view('components/nav');
        $this->load->view('components/userPanel');
        $this->load->view('components/sec');
        $this->load->view('dashboard', $data);
        $this->load->view('components/footer');
    }
}
