<?php
class AddressModel extends CI_Model
{
    private $webService;

    public function __construct()
    {
        parent::__construct();
        $this->webService = new PrestaShopWebservice(apiUrl, apiKey, isDebug);
    }

    public function getAddress($idAdressDeliv)
    {
        // returns the postcode of the address given 
        try {
            $AddressesXml = $this->webService->get([
                'resource' => 'addresses',
                'display' => '[id,id_customer,postcode]',
                'filter[id]' => $idAdressDeliv

            ]);

            $arrayAddresses = xmlToArray($AddressesXml);
            if (isset($arrayAddresses['prestashop']['addresses']['address']['postcode'])) {
                $addresses = $arrayAddresses['prestashop']['addresses']['address']['postcode'];
            } else {
                $addresses = '__';
            }

            return  $addresses;
        } catch (PrestaShopWebserviceException $e) {
            echo "PrestaShop WebService Exception: " . $e->getMessage();
            
        }
    }
}
