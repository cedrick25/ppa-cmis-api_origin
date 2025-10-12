<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;

class Import extends CI_Controller {

    public function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

        $this->load->model('Import_model');
    }

    // Excel Upload Endpoint
    public function excel_upload() {
        if (isset($_FILES['file']['name'])) {
            $file_tmp = $_FILES['file']['tmp_name'];

            try {
                // Load Excel file
                $spreadsheet = IOFactory::load($file_tmp);
                $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

                $imported = 0;
                $skipped = 0;
                $skipped_rows = []; // store duplicates

                foreach ($sheetData as $key => $row) {
                    if ($key == 1) continue; // skip header row

                    $data = [
                        'SDOCKETNO'   => $row['C'],
                        'LASTNAME'    => $row['D'],
                        'FIRSTNAME'   => $row['E'],
                        'MIDDLENAME'  => $row['F'],
                        'ALIAS'       => $row['G'],
                        'SUPVOFFICE'  => $row['H'],
                        'REMARKS'     => $row['I'],
                        'YEAR'        => $row['P'],
                        'REGION'      => $row['A'],
                        'STARTMM'     => $row['J'],
                        'STARTDD'     => $row['K'],
                        'STARTYY'     => $row['L'],
                        'ENDMM'       => $row['M'],
                        'ENDDD'       => $row['N'],
                        'ENDYY'       => $row['O'],
                        'STATUS'      => "1"
                    ];

                    if ($this->Import_model->insert_if_not_exists($data)) {
                        $imported++;
                    } else {
                        $skipped++;
                        $skipped_rows[] = [
                            "row_number" => $key,
                            "SDOCKETNO"  => $row['C'],
                            "LASTNAME"   => $row['D'],
                            "FIRSTNAME"  => $row['E'],
                            'MIDDLENAME'  => $row['F'],
                            "SUPVOFFICE"  => $row['H'],
                        ];
                    }
                }

                echo json_encode([
                    "success" => true,
                    "message" => "Data import finished",
                    "imported" => $imported,
                    "skipped_duplicates" => $skipped,
                    "skipped_rows" => $skipped_rows
                ]);

            } catch (Exception $e) {
                echo json_encode([
                    "success" => false,
                    "message" => "Error reading file: " . $e->getMessage()
                ]);
            }
        } else {
            echo json_encode([
                "success" => false,
                "message" => "No file uploaded"
            ]);
        }
    }
}
