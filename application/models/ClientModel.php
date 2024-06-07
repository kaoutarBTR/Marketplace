<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once('./vendor/autoload.php');

class ClientModel extends CI_Model
{
    private $webService;

    public function __construct()
    {
        parent::__construct();
        $this->webService = new PrestaShopWebservice(apiUrl, apiKey, isDebug);
    }

    public function manageClient($customerId)
    {

        // returns an array of id, firstname, lastname and email of customer having the id given 
        try {
            $CustomersXml = $this->webService->get([
                'resource' => 'customers',
                'display' => '[id,firstname,lastname,email]',
                'filter[id]' => $customerId,


            ]);
            $arrayCustomers = xmlToArray($CustomersXml);
            if (isset($arrayCustomers['prestashop']['customers']['customer'])) {
                return $arrayCustomers['prestashop']['customers']['customer'];
            } else return null;
        } catch (PrestaShopWebserviceException $e) {
            echo "PrestaShop WebService Exception: " . $e->getMessage();
        }
    }
}
