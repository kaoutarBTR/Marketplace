<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once './libraries/xml2json.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductModel extends CI_Model
{

    private $webService;
    public $idSupplier;
    public $downloadModel;

    public function __construct()
    {
        parent::__construct();
        $this->webService = new PrestaShopWebservice(apiUrl, apiKey, isDebug);
        $this->idSupplier = $this->session->userdata('id_supplier');
        $this->load->model('DownloadModel');
        $this->downloadModel = new DownloadModel;
    }



    public function getProduct($limit, $offset)
    {

        // returns all product of current supplier 
        // null if nothing is found
        if ($this->idSupplier == 0) {
            return null;
        }
        try {
            if ($limit == 0) {
                // no limit
                $productsXml = $this->webService->get([
                    'resource' => 'products',
                    'display' => 'full',
                    'sort' => '[date_add_DESC]',
                    'filter[id_supplier]'  =>  $this->idSupplier,
                    'date' => '1',
                ]);
            } else {
                $productsXml = $this->webService->get([
                    'resource' => 'products',
                    'display' => 'full',
                    'limit'  => "$offset,$limit",
                    'sort' => '[date_add_DESC]',
                    'filter[id_supplier]'  =>  $this->idSupplier,

                    'date' => '1'
                ]);
            }
            // ["products"]["product"];
            // $arrayData = xmlToArray($productsXml);

            // if (isset($arrayData['prestashop']['products']['product'])) {
            //     return $arrayData['prestashop']['products']['product'];
            // } else return null;
            $xmlString = $productsXml->asXML();
            $xml = simplexml_load_string($xmlString);
            $products = json_decode(json_encode($xml), true);

            if (isset($products['products']['product'])) {
                return $products['products']['product'];
            } else return null;
        } catch (PrestaShopWebserviceException $e) {
            echo "PrestaShop WebService Exception: " . $e->getMessage();
        }
    }

    public function getProductImage($id)
    {

        // returns default image of product with the id given
        // null if nothing is found

        try {
            $productsXml = $this->webService->get([
                'resource' => 'products',
                'display' => '[id_default_image]',
                'id' => $id
            ]);
            $arrayData = xmlToArray($productsXml);

            if (isset($arrayData['prestashop']['product']['id_default_image']['$'])) {
                return $arrayData['prestashop']['product']['id_default_image']['$'];
            } else {
                return null;
            }
        } catch (PrestaShopWebserviceException $e) {
            echo 'Error:' . $e->getMessage();
            return null;
        }
    }
    public function getProductIds()
    {
        // returns product ids of current supplier
        // null if nothing is found

        try {


            $productsXml = $this->webService->get([
                'resource' => 'products',
                'display' => '[id,id_supplier]',
                'filter[id_supplier]'  =>  $this->idSupplier,


            ]);




            $arrayData = xmlToArray($productsXml);

            if (isset($arrayData['prestashop']['products']['product'])) {
                $prod = $arrayData['prestashop']['products']['product'];
                $productIds = array_column($prod, 'id');

                return $productIds;
            } else return null;
        } catch (PrestaShopWebserviceException $e) {
            echo "PrestaShop WebService Exception: " . $e->getMessage();
        }
    }
    public function getProductIds_Auto($id_supplier)
    {
        // auto -> used in auto task functions
        // returns product ids of current supplier
        // null if nothing is found
        try {


            $productsXml = $this->webService->get([
                'resource' => 'products',
                'display' => '[id,id_supplier]',
                'filter[id_supplier]' => $id_supplier

            ]);




            $arrayData = xmlToArray($productsXml);

            if (isset($arrayData['prestashop']['products']['product'])) {
                $prod = $arrayData['prestashop']['products']['product'];

                $productIds = array_column($prod, 'id');

                return $productIds;
            } else return null;
        } catch (PrestaShopWebserviceException $e) {
            echo "PrestaShop WebService Exception: " . $e->getMessage();
        }
    }

    public function getStockFromProductId($id_product)
    {
        // retunrs stock id of product with the id given 
        // 
        $stockAvailables = $this->webService->get([

            'resource' => 'stock_availables',
            'display' =>  '[id,id_product,id_product_attribute]',
            // 'filter[id_product_attribute]'=>0

        ]);

        $array = xmlToArray($stockAvailables);

        if (isset($array['prestashop']['stock_availables']['stock_available'])) {
            $stock = $array['prestashop']['stock_availables']['stock_available'];
            $filteredStock = array_filter($stock, function ($stock) use ($id_product) {
                if (isset($stock['id_product']['$'])) {
                    return $stock['id_product']['$'] == $id_product;
                }
            });


            $filteredStock = array_column($filteredStock, 'id');
            return $filteredStock;
        }
        return null;
    }


    public function getStockFromIdAttribute($id_attribute)
    {

        // returns an associative array of stock having the id_attribute given
        // null if empty
        // id_stock -> x
        // id_product -> x
        // id_product_attribute -> x
        $stockAvailables = $this->webService->get([

            'resource' => 'stock_availables',
            'display' => 'full',

        ]);

        $array = xmlToArray($stockAvailables);

        if (isset($array['prestashop']['stock_availables']['stock_available'])) {
            $stock = $array['prestashop']['stock_availables']['stock_available'];
            $filteredStock = array_filter($stock, function ($stock) use ($id_attribute) {
                if (isset($stock['id_product_attribute']['$'])) {
                    return $stock['id_product_attribute']['$'] == $id_attribute;
                }
            });

            if (!empty($filteredStock)) {
                $filteredId = array_column($filteredStock, 'id');
                $filtered['id_stock'] = $filteredId[0];
                $filteredTest = array_column($filteredStock, 'id_product');
                $filteredProductId = array_column($filteredTest, '$');
                $filtered['id_product'] = $filteredProductId[0];
                $filtered['id_product_attribute'] = $id_attribute;
                return $filtered;
            }
            return null;
        }
        return null;
    }
    public function getIdAttributeFromVariantEan($ean13)
    {
        // returns id of attribute having the ean13 given 
        // null if not found
        $stockAvailables = $this->webService->get([

            'resource' => 'combinations',
            'display' => 'full',
            'filter[ean13]' => $ean13

        ]);

        $array = xmlToArray($stockAvailables);

        if (isset($array['prestashop']['combinations']['combination']['id'])) {
            $combination = $array['prestashop']['combinations']['combination']['id'];
            return $combination;
        }

        return null;
    }
    public function getStockFromVariantEan($ean13)
    {
        // returns stock from ean13 of variant
        // first we get the id of attribute using the ean13
        $idAttribute = $this->getIdAttributeFromVariantEan($ean13);
        // then we search for variant s stock using the id_attribute
        return $this->getStockFromIdAttribute($idAttribute);
    }
    public function getTaxe()
    {

        // this function is used to get taxed price
        // we can find the id_tax_rules_group in products ressource
        // explanation:
        // The id_tax in the taxes ressource corresponds to the id_tax in the tax_rules ressource, linking each tax rule to its respective tax rate
        // When a match is found :the id_tax in tax_rules matches the id in taxes, it maps the tax rate to its respective id_tax_rules_group in an associative array called $map.
        // returns $map or null

        try {

            $taxe_rule = $this->webService->get([
                'resource' => 'tax_rules',
                'display' => 'full'
            ]);
            $taxe =  $this->webService->get([
                'resource' => 'taxes',
                'display' => 'full'
            ]);
            $arrayTaxes = xmlToArray($taxe);
            $arrayTaxeRules = xmlToArray($taxe_rule);


            $taxes = $arrayTaxes['prestashop']['taxes']['tax'];
            $taxeRule = $arrayTaxeRules['prestashop']['tax_rules']['tax_rule'];
            $map = array();

            foreach ($taxeRule as $tr) {
                foreach ($taxes as $tx) {

                    if ($tx['id'] == $tr['id_tax']) {
                        $map[$tr['id_tax_rules_group']['$']] = $tx['rate'];
                    }
                }
            }

            return $map;
        } catch (PrestaShopWebserviceException $ex) {
            echo 'Other error: <br />' . $ex->getMessage();
            return null;
        }
    }

    public function addProduct($xmlString)
    {
        // this function makes a call to the webservice to create a new product using the xml given
        // returns null in case of failure, and id of the new product if it is successfully created
        try {

            $product = $this->webService->add(array('resource' => 'products', 'postXml' => $xmlString));
            $message = 'Products iserted successfully!';

            $product = xmlToArray($product);

            if (isset($product['prestashop']['product']['id'])) {
                $data['idProd'] = $product['prestashop']['product']['id'];
                return $data;
            } else {
                return null;
            }
        } catch (PrestaShopWebserviceException $ex) {
            return null;
        }
    }

    public function productXmlGeneretor($productManufacturer, $productSupplier, $productCategoryDefault, $productPrice, $productName, $productCategory, $productRef)
    {

        // this function returns a generated xml for product ressource
        // add more parameters to keep old values if needed
        if (($productPrice == null) || ($productPrice <= 0)) {
            // if product price is bellow or = 0 or not defined, the product won't be created and will be listed with failed products
            return null;
        }

        // replace values here
        $xmlString = '<product>
        <id_manufacturer><![CDATA[' . $productManufacturer . ']]></id_manufacturer>
        <id_supplier><![CDATA[' . $productSupplier . ']]></id_supplier>
        <id_category_default><![CDATA[' . $productCategoryDefault . ']]></id_category_default>
        <new><![CDATA[]]></new>
        <id_default_combination><![CDATA[]]></id_default_combination>
        <id_tax_rules_group><![CDATA[]]></id_tax_rules_group>
        <type><![CDATA[]]></type>
        <id_shop_default><![CDATA[]]></id_shop_default>
        <reference><![CDATA[]]></reference>
        <supplier_reference><![CDATA[]]></supplier_reference>
        <ean13><![CDATA[' . $productRef . ']]></ean13>
        <state><![CDATA[1]]></state>
        <product_type><![CDATA[standard]]></product_type>
        <price><![CDATA[' . $productPrice . ']]></price>
        <unit_price><![CDATA[]]></unit_price>
        <active><![CDATA[1]]></active>
        <meta_description>
            <language id="1"><![CDATA[]]></language>
        </meta_description>
        <meta_keywords>
            <language id="1"><![CDATA[]]></language>
        </meta_keywords>
        <meta_title>
            <language id="1"><![CDATA[]]></language>
        </meta_title>
        <link_rewrite>
            <language id="1"><![CDATA[]]></language>
        </link_rewrite>
        <name>
            <language id="1"><![CDATA[' . $productName . ']]></language>
        </name>
        <description>
            <language id="1"><![CDATA[]]></language>
        </description>
        <description_short>
            <language id="1"><![CDATA[]]></language>
        </description_short>
        <associations>
            <categories>
                <category>
                    <id><![CDATA[' . $productCategory . ']]></id>
                </category>
            </categories>
        </associations>
    </product>';

        return $xmlString;
    }
    public function combinationXmlGeneretor($productId, $ean_variant, $variant_ref, $productAttribute, $idImage)
    {
        // add more parameters to keep old values if needed
        // this function generates xml to create variants
        if (!$idImage) {
            // if the user did not specify the image path
            $idImage = '';
        }

        // in case we had more than one option value in this variant
        $option_values = '<product_option_values nodeType="product_option_value" api="product_option_values">';
        if (is_array($productAttribute)) {
            foreach ($productAttribute as $attributeId) {
                if ($attributeId == null) {
                    return null;
                }
                $option_values .= '
                <product_option_value>
                    <id><![CDATA[' . $attributeId . ']]></id>
                </product_option_value>';
            }
        } else {
            if ($productAttribute == null) {
                // if no option value was specified -> no variant will be created ->listed with failed products
                return null;
            }
            $option_values .= '<product_option_value>
            <id><![CDATA[' . $productAttribute . ']]></id>
            </product_option_value>';
        }
        $option_values .= '</product_option_values>';

        // replace values here
        $xmlString = '<?xml version="1.0" encoding="UTF-8"?>
        <prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
            <combination>
                <id_product><![CDATA[' . $productId . ']]></id_product>
                <ean13><![CDATA[' . $ean_variant . ']]></ean13>
                <mpn><![CDATA[123456]]></mpn>
                <reference><![CDATA[' . $variant_ref . ']]></reference>
                <supplier_reference><![CDATA[mfr_1]]></supplier_reference>
                <price><![CDATA[10.000000]]></price>
                <minimal_quantity><![CDATA[1]]></minimal_quantity>
                <associations>
                    ' . $option_values . '
                    <images>
                        <image>
                        <id><![CDATA[' . $idImage . ']]></id>
                        </image>
                    </images>
                </associations>
            </combination>
        </prestashop>';

        return $xmlString;
    }

    public function stockXmlGenerator($idStockAvailable, $idProduct, $id_product_attribute, $quantity)
    {

        // add more parameters to keep old values if needed
        // generates stock xml to update stock (product or variant quantity)
        // if the updated product is a simple product, replace the id_attribute with 0
        $xmlString = '<prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
                        <stock_available>
                          <id><![CDATA[' . $idStockAvailable . ']]></id>
                          <id_product><![CDATA[' . $idProduct . ']]></id_product>
                          <id_product_attribute><![CDATA[' . $id_product_attribute . ']]></id_product_attribute>
                          <id_shop><![CDATA[1]]></id_shop>
                          <id_shop_group><![CDATA[0]]></id_shop_group>
                          <quantity><![CDATA[' . $quantity . ']]></quantity>
                          <depends_on_stock><![CDATA[0]]></depends_on_stock>
                          <out_of_stock><![CDATA[2]]></out_of_stock>
                          <location><![CDATA[]]></location>
                        </stock_available>
                      </prestashop>
                    ';
        return $xmlString;
    }

    public function updateStock($xmlString, $idStockAvailable)
    {
        // this function makes a call to the webservice to update a stock using the xml given + id_stock
        // returns a message XD
        try {
            $updatedXml = $this->webService->edit(
                [
                    'resource' => 'stock_availables',
                    'id' => (int)$idStockAvailable,
                    'putXml' => $xmlString,

                ]
            );
            $message = 'Stock updated successfully!';
            $data['message'] = $message;
            $data['color'] = 'success';
            return $data;
        } catch (PrestaShopWebserviceException $ex) {

            $message = 'Failed to update stock, please try again :( ';
            $data['message'] = $message;
            $data['color'] = 'danger';
            return $data;
        }
    }



    public function stockOnHold($data_to_insert)
    {
        // to insert into stock_on_hold all products that need to be created before updating there stock
        $table_name = 'stock_on_hold';

        $sql = "INSERT INTO " . $table_name . " (ean, quantity,id_supplier) VALUES ";
        $values = array();

        foreach ($data_to_insert as $data) {

            $values[] = "('" . $data['ean'] . "', '" . $data['quantity'] . "','" . $this->idSupplier . "')";
        }
        $sql .= implode(", ", $values);
        // $sql .= " ON DUPLICATE KEY UPDATE quantity = VALUES(quantity)";

        return $this->db->query($sql);
    }

    public function stockOnHold_Auto($data_to_insert, $id_supplier)
    {
        // auto -> used in auto task functions
        // to insert into stock_on_hold all products that need to be created before updating there stock

        $table_name = 'stock_on_hold';

        $sql = "INSERT INTO " . $table_name . " (ean, quantity,id_supplier) VALUES ";
        $values = array();

        foreach ($data_to_insert as $data) {

            $values[] = "('" . $data['ean'] . "', '" . $data['quantity'] . "','" . $id_supplier . "')";
        }
        $sql .= implode(", ", $values);
        // $sql .= " ON DUPLICATE KEY UPDATE quantity = VALUES(quantity)";

        return $this->db->query($sql);
    }
    public function insertProductIds($data_to_insert)
    {
        // THIS FUNCTION IS TO INSERT ONLY PRODUCTs ean13 into db to create them later  (auto tasks)
        // we take data from the excel uploaded in database
        $table_name = 'product';
        if (!empty($data_to_insert)) {
            $sql = "INSERT INTO " . $table_name . " (id_supplier,ean_product,product_state,ean_variant,variant_state, id_stock) VALUES ";
            $values = array();
            foreach ($data_to_insert as $data) {

                if ($data[1] == 0) {
                    $variant_state = 'none';
                } else {
                    $variant_state = 'pending';
                }
                $values[] = "('" . $this->idSupplier . "','" . $data[0] . "','pending', '" . $data[1] . "','" . $variant_state . "', '" . $data[2] . "')";
            }
            $sql .= implode(", ", $values);

            return $this->db->query($sql);
        } else {
            return null;
        }
    }
    public function getPendingProducts()
    {
        // this function searches for products in pending (not created yet) of current supplier
        // with a limit of 10 
        $stock_id = $this->downloadModel->get_excel_file_id();
        if (isset($stock_id['id'])) {

            $table_name = 'product';
            $this->db->distinct();
            $this->db->select('ean_product,id_stock');
            $this->db->from($table_name);
            $this->db->where('product_state', 'pending');
            $this->db->where('id_supplier', $this->idSupplier);
            $this->db->where('id_stock', $stock_id['id']);
            $this->db->limit(10);
            $query = $this->db->get();
            return $query->result_array();
        } else return null;
    }
    public function getPendingProducts_Auto($id_supplier)
    {
        // auto -> used in auto tasks functions
        // returns pending products of supplier having the id given
        // with a limit of 10
        $stock_id = $this->downloadModel->get_excel_file_id_Auto($id_supplier);
        if (isset($stock_id['id'])) {

            $table_name = 'product';
            $this->db->distinct();
            $this->db->select('ean_product,id_stock');
            $this->db->from($table_name);
            $this->db->where('product_state', 'pending');
            $this->db->where('id_supplier', $id_supplier);
            $this->db->where('id_stock', $stock_id['id']);
            $this->db->limit(10);
            $query = $this->db->get();
            return $query->result_array();
        } else return null;
    }
    public function getPendingVariants($ean_product, $stock_id)
    {

        // returns variants not created yes of current supplier having the product ean13 given 
        if (isset($stock_id)) {
            $table_name = 'product';
            // $this->db->distinct();
            $this->db->select('ean_product,ean_variant,id_stock');
            $this->db->from($table_name);
            // $this->db->where('product_state', 'done'); 
            $this->db->where('ean_product', $ean_product);
            $this->db->where('variant_state', 'pending');
            $this->db->where('id_supplier', $this->idSupplier);
            $this->db->where('id_stock', $stock_id);
            $query = $this->db->get();
            return $query->result_array();
        } else return null;
    }
    public function getPendingVariants_Auto($ean_product, $stock_id, $id_supplier)
    {
        // auto->used in auto tasks functions
        // returns variants not created yet of given supplier and product ean13 
        if (isset($stock_id)) {
            $table_name = 'product';
            // $this->db->distinct();
            $this->db->select('ean_product,ean_variant,id_stock');
            $this->db->from($table_name);
            // $this->db->where('product_state', 'done'); 
            $this->db->where('ean_product', $ean_product);
            $this->db->where('variant_state', 'pending');
            $this->db->where('id_supplier', $id_supplier);
            $this->db->where('id_stock', $stock_id);
            $query = $this->db->get();
            return $query->result_array();
        } else return null;
    }
    public function createVariants($xmlString)
    {
        // creates variants using the xml given
        // return id of new varinat or null in case of failure
        try {

            $combination = $this->webService->add(array(
                'resource' => 'combinations',
                'postXml' => $xmlString
            ));

            $combination = xmlToArray($combination);

            if (isset($combination['prestashop']['combination']['id'])) {
                $data['idCombinations'][] = $combination['prestashop']['combination']['id'];
                return $data;
            } else {
                return false;
            }
        } catch (PrestaShopWebserviceException $ex) {


            return false;
        }
    }

    public function createProducts($PendingProducts)
    {

        // get products that need to be created 
        if (!empty($PendingProducts)) 
        {

            foreach ($PendingProducts as $product) {
                // check if the product exists or not
                if ($this->productExist($product['ean_product']))
                {
                    // the product exists already
                    // change statsus to done
                    $this->productAfterCreate($product['ean_product']);
                    $idProduct = $this->getIdFromReference($product['ean_product']);
                    // get its variants that need to be created
                    $pendingVariants = $this->getPendingVariants($product['ean_product'], $product['id_stock']);

                    if (!empty($pendingVariants)) 
                    {
                        // 
                        foreach ($pendingVariants as $variant) 
                        {
                            // check if the variant exists already
                            $variantExists = $this->variantExist($variant['ean_variant']);
                            if ($variantExists) 
                            {
                                if ($variantExists == $idProduct) 
                                {
                                    // variant exists for this product
                                    // -->done
                                    $this->variantAfterCreate($variant['ean_variant']);
                                } else {
                                    // variante exists but for a different product
                                    // -->fail
                                    $this->variantFailedToCreate($variant['ean_variant']);
                                    $GLOBALS['fail'] = true;
                                }
                            } else {
                                // variantdoes not exist
                                $variantData = $this->getVariantData($variant['ean_variant'], $variant['id_stock']);
                                // add the image 
                                $imageId = $this->createProductImage($idProduct, $variantData['image_path']);
                                // generate the xml
                                $xmlString = $this->combinationXmlGeneretor($idProduct, $variant['ean_variant'], $variantData['ref'], $variantData['attribute'], $imageId);
                                // send it to webservice
                                $status = $this->createVariants($xmlString);
                                if ($status != false) {
                                    // the variant was reated
                                    // ->done
                                    $this->variantAfterCreate($variant['ean_variant']);
                                } else {
                                    // an error occured
                                    // ->fail
                                    $this->variantFailedToCreate($variant['ean_variant']);
                                    $GLOBALS['fail'] = true;
                                }
                            }
                        }
                    }
                } else {
                    // if the product does not exist
                    // generate the xml 
                    $xml = '<?xml version="1.0" encoding="UTF-8"?>
    <prestashop xmlns:xlink="http://www.w3.org/1999/xlink">';
                    // get product data
                    $data = $this->getProductData($product['ean_product'], $product['id_stock']);
                    $xml .= $this->productXmlGeneretor(1, $this->idSupplier, $data['categoryDefault'], $data['price'], $data['name'], $data['category'], $product['ean_product']);
                    $xml .= '</prestashop>';
                    // send to webservice
                    $success = $this->addProduct($xml);
                    if ($success != false) 
                    {
                        // product created successfully
                        // ->done
                        $this->productAfterCreate($product['ean_product']);
                        // search for variants
                        $pendingVariants = $this->getPendingVariants($product['ean_product'], $product['id_stock']);
                        // 
                        if (!empty($pendingVariants)) {
                            foreach ($pendingVariants as $variant) {
                                // create variants
                                $variantData = $this->getVariantData($variant['ean_variant'], $variant['id_stock']);
                                $imageId = $this->createProductImage($success['idProd'], $variantData['image_path']);

                                $xmlString = $this->combinationXmlGeneretor($success['idProd'], $variant['ean_variant'], $variantData['ref'], $variantData['attribute'], $imageId);
                                $status = $this->createVariants($xmlString);
                                if ($status != false) {
                                    // the variant was created
                                    // ->done
                                    $this->variantAfterCreate($variant['ean_variant']);
                                } else {
                                    // an error occured
                                    // ->fail
                                    $this->variantFailedToCreate($variant['ean_variant']);
                                    $GLOBALS['fail'] = true;
                                }
                            }
                        }
                    } else {
                        // an error occured
                        // the product was not created 
                        // ->fail
                        $this->productFailedToCreate($product['ean_product']);
                        $GLOBALS['fail'] = true;
                    }
                }
            }
            return 'done';
        } else {
            // if empty->  no product to create
            return 'done';
        }
    }

    public function createProducts_Auto($PendingProducts, $id_supplier)
    {
        // get products that need to be created 
        if (!empty($PendingProducts)) {

            foreach ($PendingProducts as $product) 
            {   
                // check if the product exists or not 
                if ($this->productExist_Auto($product['ean_product'], $id_supplier)) {

                    // the product exists already
                    // change statsus to done
                    $this->productAfterCreate_Auto($product['ean_product'], $id_supplier);
                    $idProduct = $this->getIdFromReference_Auto($product['ean_product'], $id_supplier);
                    // get its variants that need to be created

                    $pendingVariants = $this->getPendingVariants_Auto($product['ean_product'], $product['id_stock'], $id_supplier);

                    if (!empty($pendingVariants)) {
                        foreach ($pendingVariants as $variant) {
                            // check if the variant exists already
                            $variantExists = $this->variantExist($variant['ean_variant']);
                            if ($variantExists) {
                                if ($variantExists == $idProduct) {
                                    /// variant exists for this product
                                    // -->done
                                    $this->variantAfterCreate_Auto($variant['ean_variant'], $id_supplier);
                                } else {
                                    // variante exists but for a different product
                                    // -->fail
                                    $this->variantFailedToCreate_Auto($variant['ean_variant'], $id_supplier);
                                    $GLOBALS['fail'] = true;
                                }
                            } else {
                                // variant does not exist
                                $variantData = $this->getVariantData($variant['ean_variant'], $variant['id_stock']);
                                $imageId = $this->createProductImage($idProduct, $variantData['image_path']);
                                $xmlString = $this->combinationXmlGeneretor($idProduct, $variant['ean_variant'], $variantData['ref'], $variantData['attribute'], $imageId);
                                $status = $this->createVariants($xmlString);
                                if ($status != false) {
                                    // the variant was reated
                                    // ->done
                                    $this->variantAfterCreate_Auto($variant['ean_variant'], $id_supplier);
                                } else {
                                    // an error occured
                                    // ->fail
                                    $this->variantFailedToCreate_Auto($variant['ean_variant'], $id_supplier);
                                    $GLOBALS['fail'] = true;
                                }
                            }
                        }
                    }
                } else {
                    // if the product does not exist
                    // generate the xml 
                    $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <prestashop xmlns:xlink="http://www.w3.org/1999/xlink">';
                    // get product data

                    $data = $this->getProductData($product['ean_product'], $product['id_stock']);
                    $xml .= $this->productXmlGeneretor(1, $id_supplier, $data['categoryDefault'], $data['price'], $data['name'], $data['category'], $product['ean_product']);
                    $xml .= '</prestashop>';

                    $success = $this->addProduct($xml);
                    if ($success != false) {
                         // product created successfully
                        // ->done
                        $this->productAfterCreate_Auto($product['ean_product'], $id_supplier);
                        $pendingVariants = $this->getPendingVariants_Auto($product['ean_product'], $product['id_stock'], $id_supplier);
                        // search for variants

                        if (!empty($pendingVariants)) {
                            foreach ($pendingVariants as $variant) {
                                // create variants
                                $variantData = $this->getVariantData($variant['ean_variant'], $variant['id_stock']);
                                $imageId = $this->createProductImage($success['idProd'], $variantData['image_path']);
                                $xmlString = $this->combinationXmlGeneretor($success['idProd'], $variant['ean_variant'], $variantData['ref'], $variantData['attribute'], $imageId);
                                $status = $this->createVariants($xmlString);
                                if ($status != false) {
                                    // the variant was created
                                    // ->done
                                    $this->variantAfterCreate_Auto($variant['ean_variant'], $id_supplier);
                                } else {
                                     // an error occured
                                    // ->fail
                                    $this->variantFailedToCreate_Auto($variant['ean_variant'], $id_supplier);
                                    $GLOBALS['fail'] = true;
                                }
                            }
                        }
                    } else {
                        // an error occured
                        // the product was not created 
                        // ->fail
                        $this->productFailedToCreate_Auto($product['ean_product'], $id_supplier);
                        $GLOBALS['fail'] = true;
                    }
                }
            }
            return 'done';
        } else {
            // if empty->  no product to create
            return 'done';
        }
    }
    public function createProductImage($id_product, $image_path)
    {
        // create products images
        // returns image id ro null in case of failure
        // if image path is not specified, no image will be created
        if ($image_path == '') {
            return false;
        }
        $apiKey = apiKey;
        $productId = $id_product;
        $imageFilePath = $image_path;
        // this is the api url for products images
        $apiUrl = apiUrl . "/api/images/products/" . $productId;

        // Prepare the cURL session
        $ch = curl_init();

        // Set the cURL options
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ':');
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        // Prepare the file for upload
        $file = new CURLFile($imageFilePath);
        $postFields = ['image' => $file];

        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute the request
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            return false;
        } else {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode == 200) {
                $xml = simplexml_load_string($response);
                if ($xml !== false) {
                    // Extract the image ID from the response
                    $imageId = (string) $xml->image->id;
                    return $imageId;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        curl_close($ch);
    }


    public function productAfterCreate($ean_product)
    {
        // changes product status to done 
        $this->db->set('product_state', 'done');
        $this->db->where('ean_product', $ean_product);
        $this->db->where('id_supplier', $this->idSupplier);
        $this->db->where('product_state', 'pending');

        $this->db->update('product');
    }
    public function productFailedToCreate($ean_product)
    {
        // changes product status to failed
        $this->db->set('product_state', 'failed');
        $this->db->where('ean_product', $ean_product);
        $this->db->where('id_supplier', $this->idSupplier);
        $this->db->where('product_state', 'pending');


        $this->db->update('product');
    }
    public function productAfterCreate_Auto($ean_product, $id_supplier)
    {
        // chnages product to done
        // used in auto tasks functions
        $this->db->set('product_state', 'done');
        $this->db->where('ean_product', $ean_product);
        $this->db->where('id_supplier', $id_supplier);
        $this->db->where('product_state', 'pending');

        $this->db->update('product');
    }
    public function productFailedToCreate_Auto($ean_product, $id_supplier)
    {
        // chnages product status to failed
        // used in auto tasks functions
        $this->db->set('product_state', 'failed');
        $this->db->where('ean_product', $ean_product);
        $this->db->where('id_supplier', $id_supplier);
        $this->db->where('product_state', 'pending');


        $this->db->update('product');
    }
    public function fileRed()
    {
        // changes product file status to done
        $this->db->set('statut', 'done');
        $this->db->where('statut', 'progress');
        $this->db->where('id_supplier', $this->idSupplier);
        $this->db->update('product_stock');
    }
    public function fileRed_Auto($id_supplier)
    {
        // used in auto tasks functions
        // changes product file status to done
        $this->db->set('statut', 'done');
        $this->db->where('statut', 'progress');
        $this->db->where('id_supplier', $id_supplier);
        $this->db->update('product_stock');
    }
    public function stockFileRed_Auto($id_supplier)
    {
        // chnanges stock file status to done
        $this->db->set('statut', 'done');
        $this->db->where('statut', 'progress');
        $this->db->where('id_supplier', $id_supplier);
        $this->db->update('quantity_stock');
    }
    public function getFailedProducts($stock_id)
    {
        // returns products ean13 that we failed to create

        $this->db->select('ean_product');
        $this->db->from('product');
        $this->db->where('product_state', 'failed');
        $this->db->where('id_supplier', $this->idSupplier);
        $this->db->where('id_stock', $stock_id);

        $query = $this->db->get();
        return array_column($query->result_array(), 'ean_product');
    }
    public function getFailedVariants($stock_id)
    {
        // returns variants that we failed to create
        $this->db->select('ean_variant,ean_product');
        $this->db->from('product');
        $this->db->where('variant_state', 'failed');
        $this->db->where('product_state', 'done');
        $this->db->where('id_supplier', $this->idSupplier);
        $this->db->where('id_stock', $stock_id);

        $query = $this->db->get();
        return $query->result_array();
    }

    public function variantAfterCreate($ean_variant)
    {
        // chnages varinats status to done
        $this->db->set('variant_state', 'done');
        $this->db->where('ean_variant', $ean_variant);
        $this->db->where('id_supplier', $this->idSupplier);
        $this->db->where('variant_state', 'pending');

        $this->db->update('product');
    }
    public function variantFailedToCreate($ean_variant)
    {
        // chnages variannts status to failed
        $this->db->set('variant_state', 'failed');
        $this->db->where('ean_variant', $ean_variant);
        $this->db->where('id_supplier', $this->idSupplier);
        $this->db->where('variant_state', 'pending');

        $this->db->update('product');
    }
    public function variantAfterCreate_Auto($ean_variant, $id_supplier)
    {
        // chnages varinats status to done
        // auto->used in auto tasks functions
        $this->db->set('variant_state', 'done');
        $this->db->where('ean_variant', $ean_variant);
        $this->db->where('id_supplier', $id_supplier);
        $this->db->where('variant_state', 'pending');

        $this->db->update('product');
    }
    public function variantFailedToCreate_Auto($ean_variant, $id_supplier)
    {
        // chnages variannts status to failed
        // auto->used in auto tasks functions
        $this->db->set('variant_state', 'failed');
        $this->db->where('ean_variant', $ean_variant);
        $this->db->where('id_supplier', $id_supplier);
        $this->db->where('variant_state', 'pending');

        $this->db->update('product');
    }
    public function getProductData($ean_product, $id_stock)
    {
        // gets products data and infos from excel file using ean13 and returns it
        $file_data = $this->downloadModel->get_excel_file_data_by_id($id_stock);

        if ($file_data) {
            // Écrire le contenu du fichier dans un fichier temporaire
            $temp_file = tempnam(sys_get_temp_dir(), 'excel_');
            file_put_contents($temp_file, $file_data);

            // Lecture du fichier Excel avec PhpSpreadsheet
            require_once 'vendor/autoload.php';

            $spreadsheet = IOFactory::load($temp_file);
            $sheetdata = $spreadsheet->getActiveSheet()->toArray();
            $sheetcount = count($sheetdata);



            $table = [];
            for ($i = 1; $i < $sheetcount; $i++) {
                if ($sheetdata[$i][0] == $ean_product) {
                    $price = $sheetdata[$i][1];
                    $name = $sheetdata[$i][2];
                    $category = $sheetdata[$i][3];
                    $categoryDefault = $sheetdata[$i][4];
                    $table['price'] = $price;
                    $table['name'] = $name;
                    $table['category'] = $category;
                    $table['categoryDefault'] = $categoryDefault;
                    return $table;
                }
            }
        }
    }
    public function getVariantData($ean_variant, $id_stock)
    {
        // gets variants data and infos from excel file using ean13 and returns it

        $file_data = $this->downloadModel->get_excel_file_data_by_id($id_stock);

        if ($file_data) {
            // Écrire le contenu du fichier dans un fichier temporaire
            $temp_file = tempnam(sys_get_temp_dir(), 'excel_');
            file_put_contents($temp_file, $file_data);

            // Lecture du fichier Excel avec PhpSpreadsheet
            require_once 'vendor/autoload.php';

            $spreadsheet = IOFactory::load($temp_file);
            $sheetdata = $spreadsheet->getActiveSheet()->toArray();
            $sheetcount = count($sheetdata);

            $table = [];
            for ($i = 1; $i < $sheetcount; $i++) {
                if ($sheetdata[$i][5] == $ean_variant) {
                    // attributes are seperated with : in the excel file
                    $attribute = explode(":", $sheetdata[$i][6]);
                    $ref = $sheetdata[$i][7];
                    // in case the user did not specify an image path
                    if (isset($sheetdata[$i][8])) {
                        $image_path = $sheetdata[$i][8];
                    } else {
                        $image_path = '';
                    }
                    $table['attribute_ids'] = $sheetdata[$i][6];
                    foreach ($attribute as $att) {

                        // we need the id of the attribute
                        $table['attribute'][] =  $this->getIdAttributeFromName($att);
                    }
                    $table['ref'] = $ref;
                    $table['image_path'] = $image_path;

                    return $table;
                }
            }
        }
    }

    public function productExist($ean_product)
    {
        // check if product exists already or needs to be created
        // search for its id using ean13
        $idProduct = $this->getIdFromReference($ean_product);

        // check if its id exists in product ids belonging to this ssupplier
        if (isset($idProduct)) {
            $array = $this->getProductIds();
            return array_search($idProduct, $array);
        } else return false;
    }
    public function productExist_Auto($ean_product, $id_supplier)
    {
        // check if product exists already or needs to be created
        // search for its id using ean13
        $idProduct = $this->getIdFromReference_Auto($ean_product, $id_supplier);
        // check if its id exists in product ids belonging to this supplier
        if (isset($idProduct)) {
            $array = $this->getProductIds_Auto($id_supplier);
            return array_search($idProduct, $array);
        } else return false;
    }
    public function variantExist($eanVariant)
    {
        // search for a combination having the ean13 given 
        try {

            $product =  $this->webService->get([
                'resource' => 'combinations',
                'display' => 'full',
                'filter[ean13]' => $eanVariant
            ]);

            $product = xmlToArray($product);
            if (isset($product['prestashop']['combinations']['combination']['id_product']['$'])) {
                return $product['prestashop']['combinations']['combination']['id_product']['$'];
            }
            return false;
        } catch (PrestaShopWebserviceException $ex) {
            return false;
        }
    }
    public function getStockOnHold()
    {
        // returns data in stock_on_hold table
        $this->db->distinct();
        $this->db->select('ean,quantity');
        $this->db->from('stock_on_hold');
        $this->db->where('id_supplier', $this->idSupplier);
        $query = $this->db->get();
        return $query->result_array();
    }


    public function failExcel($id)
    {
        // changes product excel status to fail 
        $this->db->set('statut', 'fail');
        $this->db->where('id', $id);
        $this->db->update('product_stock');
    }



    public function getIdAttributeFromName($name)
    {
        // returns the id of the attribute having the name given
        try {

            $attribute = $this->webService->get([
                'resource' => 'product_option_values',
                'display' => 'full'
            ]);

            $arrayAttributes = xmlToArray($attribute);



            if (isset($arrayAttributes['prestashop']['product_option_values']['product_option_value'])) {
                $attribute = $arrayAttributes['prestashop']['product_option_values']['product_option_value'];
                $filteredSupp = array_filter($attribute, function ($attribute) use ($name) {
                    if (isset($attribute['name']['language']['$']) && $attribute['name']['language']['$'] == $name) {
                        return $attribute['name']['language']['$'];
                    }
                });
                $attributeId = implode(', ', array_column($filteredSupp, 'id'));
                return $attributeId;
            }
        } catch (PrestaShopWebserviceException $ex) {
            echo 'Other error: <br />' . $ex->getMessage();
        }
    }

    public function getStocks()
    {
        // returns all stock or null if empty

        try {
            $stocksXml = $this->webService->get([
                'resource' => 'stock_availables',
                'display' => 'full',
                // 'filter[id_product]' =>$id_product,
                'filter[id_product_attribute]' => 0


            ]);

            $xmlString = $stocksXml->asXML();
            $xml = simplexml_load_string($xmlString);
            $stocks = json_decode(json_encode($xml), true);

            if (isset($stocks['stock_availables']['stock_available'])) {
                return $stocks['stock_availables']['stock_available'];
            } else return null;

            // $arrayStocks = xmlToArray($stocksXml);


            // if (isset($arrayStocks['prestashop']['stock_availables']['stock_available'])) {
            //     return $arrayStocks['prestashop']['stock_availables']['stock_available'];
            // } else return null;
        } catch (PrestaShopWebserviceException $e) {
            echo "PrestaShop WebService Exception: " . $e->getMessage();
        }
    }
    public function getSales($id_product)
    {

        // returns specific_prices of a specific price, null if not found
        try {


            $saleXml = $this->webService->get([
                'resource' => 'specific_prices',
                'display' => 'full'

            ]);


            $arraySales = xmlToArray($saleXml);


            if (isset($arraySales['prestashop']['specific_prices']['specific_price'])) {
                $sale = $arraySales['prestashop']['specific_prices']['specific_price'];
                $filteredSale = array_filter($sale, function ($sale) use ($id_product) {
                    if (isset($sale['id_product']['$'])) {
                        return $sale['id_product']['$'] == $id_product;
                    }
                });
                return $filteredSale;
            }
        } catch (PrestaShopWebserviceException $e) {
            echo "PrestaShop WebService Exception: " . $e->getMessage();
            return null;
        }
    }

    public function getIdFromReference($ean13)
    {
        // returns product id using its ean13, null if not found
        try {
            $referencesXml = $this->webService->get([
                'resource' => 'products',

                'display' => 'full',
                'filter[ean13]' => $ean13,

            ]);


            $arrayStocks = xmlToArray($referencesXml);


            if (isset($arrayStocks['prestashop']['products']['product']['id_supplier']['$'])) {


                $prod = $arrayStocks['prestashop']['products']['product']['id_supplier']['$'];
                if ($prod == $this->session->userdata('id_supplier')) {
                    return $arrayStocks['prestashop']['products']['product']['id'];
                } else
                    return null;
            } else return null;
        } catch (PrestaShopWebserviceException $e) {
            echo "PrestaShop WebService Exception: " . $e->getMessage();
        }
    }
    public function getIdFromReference_Auto($ean13, $id_supplier)
    {
        // returns product id using its ean13, null if not found
        // auto->used in auto tasks functions
        try {
            $referencesXml = $this->webService->get([
                'resource' => 'products',

                'display' => 'full',
                'filter[ean13]' => $ean13,

            ]);


            $arrayStocks = xmlToArray($referencesXml);


            if (isset($arrayStocks['prestashop']['products']['product']['id_supplier']['$'])) {


                $prod = $arrayStocks['prestashop']['products']['product']['id_supplier']['$'];
                if ($prod == $id_supplier) {
                    return $arrayStocks['prestashop']['products']['product']['id'];
                } else
                    return null;
            } else return null;
        } catch (PrestaShopWebserviceException $e) {
            echo "PrestaShop WebService Exception: " . $e->getMessage();
        }
    }
    public function getCombinations($id_product)
    {
        // returns variants having the id_product given

        try {

            $combinationsXml = $this->webService->get([
                'resource' => 'combinations',
                'display' => 'full',
                'filter[id_product]' => $id_product,

            ]);


            $arrayCombinations = xmlToArray($combinationsXml);

            if (isset($arrayCombinations['prestashop']['combinations']['combination'])) {

                return $arrayCombinations['prestashop']['combinations']['combination'];
            } else return null;
        } catch (PrestaShopWebserviceException $e) {
            echo "PrestaShop WebService Exception: " . $e->getMessage();
        }
    }


    public function getOptions($id)
    {
        // returns all options (used in variants attributes)

        try {
            $optionsXml = $this->webService->get([
                'resource' => 'product_option_values',
                'display' => '[id,name]',
                'filter[id]' => $id

            ]);

            $arrayOptions = xmlToArray($optionsXml);
            if (isset($arrayOptions['prestashop']['product_option_values']['product_option_value'])) {
                $Option = $arrayOptions['prestashop']['product_option_values']['product_option_value'];
                // $filteredOptions = array_filter($Option, function ($Option)  {
                //     if (isset($Option['name']['language']['$'])) {
                //         return $Option['name']['language']['@id'] == 1;
                //     }
                // });
    

                // print_r( $Option);
                // die();

                return $Option;
            } else return null;
        } catch (PrestaShopWebserviceException $e) {
            echo "PrestaShop WebService Exception: " . $e->getMessage();
        }
    }
}
