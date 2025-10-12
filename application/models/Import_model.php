<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Import_model extends CI_Model {

    private $table = 'masterlist'; // ✅ actual table name

    public function insert_if_not_exists($row) {
        // check duplicate based on ALL 5 fields
        $this->db->where('SDOCKETNO', $row['SDOCKETNO']);
        $this->db->where('LASTNAME', $row['LASTNAME']);
        $this->db->where('FIRSTNAME', $row['FIRSTNAME']);
        $this->db->where('MIDDLENAME', $row['MIDDLENAME']);
        $this->db->where('SUPVOFFICE', $row['SUPVOFFICE']);

        $query = $this->db->get($this->table);

        if ($query === false) {
            $error = $this->db->error();
            log_message('error', 'DB query failed: ' . json_encode($error));
            return false;
        }

        if ($query->num_rows() > 0) {
            // exact same record exists → skip
            return false;
        } else {
            // insert new record
            $inserted = $this->db->insert($this->table, $row);

            if (!$inserted) {
                $error = $this->db->error();
                log_message('error', 'Insert failed: ' . json_encode($error));
                return false;
            }

            return true;
        }
    }

}
