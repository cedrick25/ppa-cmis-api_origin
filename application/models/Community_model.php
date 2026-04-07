<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Community_model extends CI_Model {

    private $table = 'community_service_masterlist';
    private $db_expansion; // Variable to hold the expansion DB connection

    public function __construct() {
        parent::__construct();
        // Load the 'expansion' group defined in your database.php
        $this->db_expansion = $this->load->database('expansion', TRUE);
    }

    public function get_all($id = NULL) {
        if ($id) {
            // Use $this->db_expansion instead of $this->db
            return $this->db_expansion->get_where($this->table, ['f53t10_id' => $id])->row_array();
        }
        return $this->db_expansion->get($this->table)->result_array();
    }

    public function insert_data($data) {
        $this->db_expansion->insert($this->table, $data);
        return $this->db_expansion->insert_id();
    }

    public function update_data($id, $data) {
        $this->db_expansion->where('f53t10_id', $id);
        return $this->db_expansion->update($this->table, $data);
    }

    public function delete_data($id) {
        return $this->db_expansion->delete($this->table, ['f53t10_id' => $id]);
    }
}