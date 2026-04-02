<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
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
                $spreadsheet = IOFactory::load($file_tmp);
                $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

                $imported = 0;
                $skipped = 0;
                $skipped_rows = [];

                foreach ($sheetData as $key => $row) {
                    if ($key == 1) continue; // skip header row

                    // Skip empty rows
                    if (empty($row['C']) && empty($row['D'])) continue;

                    $data = [
                        'SDOCKETNO'   => trim($row['C']),

                        // ✅ CLEANED FIELDS ONLY
                        'LASTNAME'    => $this->clean_text($row['D']),
                        'FIRSTNAME'   => $this->clean_text($row['E']),
                        'MIDDLENAME'  => $this->clean_text($row['F']),

                        // ❌ normal trim only
                        'ALIAS'       => trim($row['G']),
                        'SUPVOFFICE'  => trim($row['H']),
                        'REMARKS'     => trim($row['I']),
                        'YEAR'        => trim($row['P']),
                        'REGION'      => trim($row['A']),
                        'STARTMM'     => trim($row['J']),
                        'STARTDD'     => trim($row['K']),
                        'STARTYY'     => trim($row['L']),
                        'ENDMM'       => trim($row['M']),
                        'ENDDD'       => trim($row['N']),
                        'ENDYY'       => trim($row['O']),
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
                            "FIRSTNAME"  => $row['E']
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
                    "message" => $e->getMessage()
                ]);
            }
        } else {
            echo json_encode([
                "success" => false,
                "message" => "No file uploaded"
            ]);
        }
    }
    private function clean_text($text) {
        $text = preg_replace('/[\r\n\t]+/', ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }
    public function excel_upload_csm() {
        if (!isset($_FILES['file']['name'])) {
            echo json_encode(["success" => false, "message" => "No file uploaded"]);
            return;
        }

        try {
            $spreadsheet = IOFactory::load($_FILES['file']['tmp_name']);
            // Use true for the second parameter to calculate formulas, and null for empty cells
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            $batch_data = [];
            foreach ($sheetData as $key => $row) {
                // Skip the header row (Row 1 in your image)
                if ($key <= 1) continue; 

                // Skip completely empty rows
                if (empty($row['B']) && empty($row['C'])) continue;

                $batch_data[] = [
                    'region'                  => trim($row['A']),
                    'docket_number'           => trim($row['B']),
                    'last_name'               => trim($row['C']),
                    'first_name'              => trim($row['D']),
                    'middle_name'             => trim($row['E']),
                    'alias'                   => trim($row['F']),
                    'criminal_case_number'    => trim($row['G']),
                    'court_of_origin'         => trim($row['H']),
                    'assigned_officer'        => trim($row['I']),
                    'date_received_by_ppo'    => $this->parse_excel_date($row['J']),
                    'community_service_start' => $this->parse_excel_date($row['L']),
                    'community_service_end'   => $this->parse_excel_date($row['M']),
                    'field_office'            => trim($row['N']),
                    'remarks'                 => trim($row['O']),
                    'status'                  => 1
                ];
            }

            $result = $this->Import_model->insert_batch_csm($batch_data);
            echo json_encode(array_merge(["success" => true, "rows_processed" => count($batch_data)], $result));

        } catch (Exception $e) {
            echo json_encode(["success" => false, "message" => $e->getMessage()]);
        }
    }
    
    private function parse_excel_date($value) {
        if (empty($value) || trim($value) == '') return null;
        
        $value = trim($value);

        // 1. Handle Excel Numeric/Serial format
        if (is_numeric($value)) {
            return date('Y-m-d H:i:s', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($value));
        }

        // 2. Fix the DD/MM/YYYY format (e.g., 26/02/2021)
        // PHP's strtotime() expects MM/DD/YYYY if slashes are used.
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $value, $matches)) {
            // Rearrange to YYYY-MM-DD
            return "{$matches[3]}-{$matches[2]}-{$matches[1]} 00:00:00";
        }

        // 3. Handle standard text dates (e.g., "December 09, 2020")
        $ts = strtotime($value);
        if ($ts) {
            return date('Y-m-d H:i:s', $ts);
        }

        return null; // Fallback for unparseable dates
    }

}
