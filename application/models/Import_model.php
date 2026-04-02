<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Import_model extends CI_Model {

    private $table = 'masterlist';
    private $table2 = 'community_service_masterlist';
    private $db_expansion; // Variable to hold the 2nd DB connection

    public function __construct() {
        parent::__construct();
        // Load expansion DB
        $this->db_expansion = $this->load->database('expansion', TRUE);

        // DEBUG: Check if connection is actually alive
        if (!$this->db_expansion->conn_id) {
            log_message('error', 'Expansion database connection failed!');
        }
    }

    public function insert_if_not_exists($row) {

        $where_array = [
            'SDOCKETNO'  => $row['SDOCKETNO'],
            'LASTNAME'   => $row['LASTNAME'],
            'FIRSTNAME'  => $row['FIRSTNAME'],
            'MIDDLENAME' => $row['MIDDLENAME'],
            'SUPVOFFICE' => $row['SUPVOFFICE']
        ];

        $this->db->where($where_array);
        $query = $this->db->get($this->table);

        // 🔥 ADD THIS
        if ($query === false) {
            $error = $this->db->error();
            log_message('error', 'QUERY FAILED: ' . json_encode($error));
            return false;
        }

        // 🔥 ADD THIS DEBUG
        log_message('error', 'ROWS FOUND: ' . $query->num_rows());

        if ($query->num_rows() == 0) {
            $inserted = $this->db->insert($this->table, $row);

            if (!$inserted) {
                $error = $this->db->error();
                log_message('error', 'INSERT FAILED: ' . json_encode($error));
                return false;
            }

            return true;
        }

        return false;
    }
    public function insert_batch_csm($data) {
        if (empty($data)) return ['inserted' => 0, 'skipped' => 0];

        $final_inserts = [];
        $skipped_count = 0;

        // Start a transaction to ensure data integrity
        $this->db_expansion->trans_start();

        foreach ($data as $row) {
            $this->db_expansion->where([
                'docket_number' => $row['docket_number'],
                'last_name'     => $row['last_name'],
                'first_name'    => $row['first_name'],
                'middle_name'   => $row['middle_name'],
                'field_office'  => $row['field_office'],
            ]);
            
            $query = $this->db_expansion->get($this->table2);

            if ($query && $query->num_rows() > 0) {
                $skipped_count++;
            } else {
                $final_inserts[] = $row;
            }
        }

        if (!empty($final_inserts)) {
            $this->db_expansion->insert_batch($this->table2, $final_inserts);
        }

        // Complete the transaction
        $this->db_expansion->trans_complete();

        if ($this->db_expansion->trans_status() === FALSE) {
            log_message('error', 'Expansion Batch Insert Failed');
            return ['inserted' => 0, 'skipped' => 0, 'error' => 'Transaction Failed'];
        }

        return [
            'inserted' => count($final_inserts),
            'skipped'  => $skipped_count
        ];
    }
}
