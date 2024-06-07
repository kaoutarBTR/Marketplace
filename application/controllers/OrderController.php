<?php
require_once('./vendor/autoload.php');
require_once './libraries/xml2json.php';

defined('BASEPATH') or exit('No direct script access allowed');


class OrderController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('ClientModel');
        $this->load->model('AddressModel');
        $this->load->model('OrderModel');
    }
    public function modifierStatut()
    {

        // get order_id and new status 
        $order_id = $this->input->post('order_id');
        $new_status = $this->input->post('new_status');
        $total = $this->input->post('order_total');

        print_r($order_id.'   '.$new_status);
        die();
        $orderModel = new OrderModel();
        // update status using xml 
        $result = $orderModel->updateStatus($order_id, $new_status,$total);
        // redirect to the manage order view 
        redirect('orders');
    }

    public function gerer()
    {
        $prod = new ProductModel;
        $clientModel = new ClientModel;
        $addressModel = new AddressModel;
        $orderModel = new OrderModel;
        $orders = $orderModel->getOrder(0);
        // get all orders 
        $supplierProducts = $prod->getProductIds();
        $ordersTab = [];
        

        if ($orders != null) {


            foreach ($orders as $ord) {
                // get customer of current order 
                $cust = $clientModel->manageClient($ord['id_customer']['$']);
                $ordersTable['total'] = 0;
                $total = 0;

                foreach ($ord['associations'] as $order_rows) {
                    $quantity = [];
                    if(is_array($supplierProducts)){
                        if (isset($order_rows['order_row']['product_id'])) {
                            // stock the product_id in productIds
                            $productIds = $order_rows['order_row']['product_id']['$'];
                            $productNames[$productIds] = $order_rows['order_row']['product_name'];
                            // search for the productId in supplier products ids 
                            if (in_array($productIds, $supplierProducts)) {
                                // if found -> count total price and quantity of this product
                                $total += $order_rows['order_row']['unit_price_tax_incl'] * $order_rows['order_row']['product_quantity'];
                                $ordersTable['total'] = $total;
                                $quantity[$productNames[$productIds]] = $order_rows['order_row']['product_quantity'];
                            }
                            // if not found, total price not changed
                        } else {
                            // if there is more than one product in this order -> order row is an array with severla id_product
    
                            foreach ($order_rows['order_row'] as $orders) {
                                // stock the product_id in productIds
                                $productIds = $orders['product_id']['$'];
                                // search for the productId in supplier products ids 
                                $productNames[$productIds] = $orders['product_name'];
                                if (in_array($productIds, $supplierProducts)) {
                                    // if found -> count total price and quantity of this product
                                    $quantity[$productNames[$productIds]] = $orders['product_quantity'];
                                    $total += $orders['unit_price_tax_incl'] * $orders['product_quantity'];
                                    $ordersTable['total'] = $total;
                                }
                                // if not found, total price not changed
    
                            }
                        }
                    }
                    // if there is only one product in this order -> only one order row 
                    
                }
                // if the total price of current order is still = 0 it means that there was no product of the current supplier -> move to nextorder 
                // this order will not be shown in the view 
                if ($ordersTable['total'] == 0) {
                    continue;
                }
                $ordersTable['products'] = $quantity;

                $ordersTable['id'] = $ord['id'];


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

        $this->load->view('components/header');
        $this->load->view('components/nav');
        $this->load->view('components/userPanel');
        $this->load->view('components/sec');
        $this->load->view('manageOrder', $data);
        $this->load->view('components/footer');
    }
}
