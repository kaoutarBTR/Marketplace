<?php
defined('BASEPATH') or exit('No direct script access allowed');

class OrderModel extends CI_Model
{

    private $webService;
    public $idSupplier;
    public  $productModel;


    public function __construct()
    {
        parent::__construct();
        $this->webService = new PrestaShopWebservice(apiUrl, apiKey, isDebug);
        $this->idSupplier = $this->session->userdata('id_supplier');
        $this->load->model('ProductModel');
        $this->productModel = new ProductModel;
    }

    public function updateStatus($idOrder, $newState,$total)
    {

        // function to change order state
        // returns false in case of failure
        //todo :add paramters like address, customer, and payment to keep same values
        $xml = '<prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
        <order>
          <id><![CDATA[' . $idOrder . ']]></id>
          <id_address_delivery><![CDATA[7]]></id_address_delivery>
          <id_address_invoice><![CDATA[7]]></id_address_invoice>
          <id_cart><![CDATA[7]]></id_cart>
          <id_currency><![CDATA[1]]></id_currency>
          <id_lang><![CDATA[1]]></id_lang>
          <id_customer><![CDATA[3]]></id_customer>
          <id_carrier><![CDATA[5]]></id_carrier>
          <id_shop_group><![CDATA[1]]></id_shop_group>
          <id_shop><![CDATA[1]]></id_shop>
          <current_state><![CDATA[' . $newState . ']]></current_state>
          <module><![CDATA[ps_checkpayment]]></module>
          <payment>
            <![CDATA[Chèque]]>
            </payment>
            <total_paid><![CDATA[' . $total . ']]></total_paid>
            <total_paid_tax_incl><![CDATA[' . $total . ']]></total_paid_tax_incl>
            <total_paid_real><![CDATA[0.00]]></total_paid_real>
            <total_products><![CDATA[18.00]]></total_products>
            <total_products_wt><![CDATA[' . $total . ']]></total_products_wt>
            <conversion_rate><![CDATA[1.00]]></conversion_rate>
            </order>
            </prestashop>';
        try {
            $updatedXml =  $this->webService->edit([
                'resource' => 'orders',
                'id' => $idOrder,
                'putXml' => $xml,
            ]);
            return true;
        } catch (PrestaShopWebserviceException $e) {
            return false;
            echo $e->getMessage();
        }
    }

    public function supplierOrders(){
        // returns number of orders concerning this supplier
        $productModel=new ProductModel();
        $productArray=$productModel->getProductIds();
        if(!is_array($productArray)){
            return null;
        }
        
        try {

            $OrdersXml = $this->webService->get([
            'resource' => 'orders',
            'display'=> 'full'
        ]);
       
        $array = xmlToArray($OrdersXml);
        
        
        $order = $array['prestashop']['orders']['order'];

        $filteredOrders = array_filter($order, function ($order) use($productArray) {
            // in case many products were ordered -> array
            if (is_array($order['associations']['order_rows']['order_row'])) {
                $filteredOrder = array_filter($order['associations']['order_rows']['order_row'], function ($orderRow) use($productArray) {
                    return isset($orderRow['product_id']['$']) && in_array($orderRow['product_id']['$'],$productArray)  ;
                });
               
               $orders= ($filteredOrder);
            } 
            if(isset($order['associations']['order_rows']['order_row']['product_id']['$']))
            { 
                // in case only one product was ordered -> string
                 $productID = $order['associations']['order_rows']['order_row']['product_id']['$'];
                 $orders= in_array($productID,$productArray)  ;
                 
             }
             return $orders;
        });
       
        return count($filteredOrders);

        } catch (PrestaShopWebserviceException $e) {
            echo "PrestaShop WebService Exception: " . $e->getMessage();
            return null;
        }
    }

    public function getOrder($limit)
    {

        // returns sorted orders 
        // or null if empty or an error occured
        // if limit = 0 it returns all orders
        try {

            if ($limit == 0) {
                $OrdersXml = $this->webService->get([
                    'resource' => 'orders',
                    'display' => 'full',
                    'sort' => '[date_upd_DESC]',
                    'date' => '1'
                ]);
            } else {
                $OrdersXml =  $this->webService->get([
                    'resource' => 'orders',
                    'display' => 'full',
                    'limit' => $limit,
                    'sort' => '[date_upd_DESC]',
                    'date' => '1'
                ]);
            }



            $arrayOrders = xmlToArray($OrdersXml);

            if (isset($arrayOrders['prestashop']['orders']['order'])) {
                return $arrayOrders['prestashop']['orders']['order'];
            } else return null;
        } catch (PrestaShopWebserviceException $e) {
            echo "PrestaShop WebService Exception: " . $e->getMessage();
            return null;
        }
    }


    public function getOrderState($idOrderState)
    {
        // this function is used to get the name of the state from its id
        // returns null if empty or an error has occured
        try {
            $OrdersXml =  $this->webService->get([
                'resource' => 'order_states',
                'display' => '[id,name]',
                'filter[id]' => $idOrderState
            ]);

            $arrayOrders = xmlToArray($OrdersXml);
            if (isset($arrayOrders['prestashop']['order_states']['order_state'])) {
                return $arrayOrders['prestashop']['order_states']['order_state'];
            } else return null;
        } catch (PrestaShopWebserviceException $e) {
            echo "PrestaShop WebService Exception: " . $e->getMessage();
        }
    }
}
