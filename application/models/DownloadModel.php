<?php
class DownloadModel extends CI_Model
{

    public $idSupplier;

    public function __construct()
    {
        parent::__construct();
        $this->idSupplier = $this->session->userdata('id_supplier');
    }

    public function store_excel_file($file_data)
    {
        $data = array(
            'id_supplier' => $this->idSupplier,
            'file_data' => $file_data,
            'statut' => 'progress'
        );
        $table_name = 'stock';
        $this->db->insert($table_name, $data);
        return $this->db->insert_id();
    }
    public function store_excel_file_path($file_path)
    {
        $data = array(
            'id_supplier' => $this->idSupplier,
            'file_path' => $file_path,
            'statut' => 'progress'
        );
        $table_name = 'product_stock';
        $this->db->insert($table_name, $data);
        return $this->db->insert_id();
    }
    public function store_excel_file_quantity_path($file_path)
    {
        $data = array(
            'id_supplier' => $this->idSupplier,
            'file_path' => $file_path,
            'statut' => 'progress'
        );
        $table_name = 'quantity_stock';
        $this->db->insert($table_name, $data);
        return $this->db->insert_id();
    }

    public function get_excel_file()
    {

        $table_name = 'product_stock';

        $this->db->select('file_path');
        $this->db->from($table_name);
        $this->db->where('statut', 'progress');
        // $this->db->or_where('statut', 'pending'); 
        $this->db->where('id_supplier', $this->idSupplier);
        $this->db->limit(1);

        $query = $this->db->get();
        return $query->row_array();
    }
    public function get_last_excel_file_id()
    {

        $table_name = 'product_stock';

        $this->db->select('id');
        $this->db->from($table_name);
        $this->db->order_by('file_name', 'DESC');
        $this->db->where('id_supplier', $this->idSupplier);
        $this->db->limit(1);

        $query = $this->db->get();
        return $query->row_array();
    }
    public function get_excel_file_id()
    {

        $table_name = 'product_stock';
        $this->db->select('id');
        $this->db->from($table_name);
        $this->db->where('statut', 'progress');
        $this->db->where('id_supplier', $this->idSupplier);
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }
    public function get_excel_file_id_Auto($id_supplier)
    {

        $table_name = 'product_stock';
        $this->db->select('id');
        $this->db->from($table_name);
        $this->db->where('statut', 'progress');
        $this->db->where('id_supplier', $id_supplier);
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_excel_file_by_id($id_file)
    {
        $table_name = 'stock';

        $this->db->select('file_data');
        $this->db->from($table_name);
        $this->db->where('id', $id_file);
        $this->db->limit(1);

        $query = $this->db->get();
        return $query->row_array();
    }
    public function get_excel_file_data_by_id($id_file)
    {
        $table_name = 'product_stock';

        $this->db->select('file_path');
        $this->db->from($table_name);
        $this->db->where('id', $id_file);
        $this->db->limit(1);

        $query = $this->db->get();
        $file_path = $query->row_array();
        $file_data = file_get_contents($file_path['file_path']);
        return $file_data;
    }
    public function getDataFromExcel()
    {
        $fileContents = $this->get_excel_file();
        $tempFilePath = tempnam(sys_get_temp_dir(), 'excel');
        file_put_contents($tempFilePath, $fileContents);
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

        $spreadsheet = $reader->load($tempFilePath);
        $sheet = $spreadsheet->getActiveSheet();

        $excelData = [];

        // Iterate through each row and store data in array
        foreach ($sheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $rowData = [];
            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getValue();
            }

            // Assuming the first cell contains the row ID
            $rowId = $rowData[0];
            $excelData[$rowId] = $rowData;
        }
        return $excelData;
    }

    public function searchByEan($ean)
    {
        $array = $this->getDataFromExcel();
        $found = false;
        foreach ($array as $product) {


            if ($product[0] == $ean) {


                $found = true;
                return ($product);
            }
        }
        if (!$found) {

            return false;
        }
    }
}
