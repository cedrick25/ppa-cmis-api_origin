<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Community_Service extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        // 1. Set API Headers
        header('Content-Type: application/json');
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

        // 2. Handle Preflight OPTIONS request
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        $this->load->model('Community_model'); 
    }

    // READ: GET /community_service or /community_service/index/5
    public function index($id = NULL) {
        $result = $this->Community_model->get_all($id);
        
        if ($id && empty($result)) {
            $this->output->set_status_header(404);
            echo json_encode(["status" => "error", "message" => "Record not found"]);
            return;
        }
        
        echo json_encode($result);
    }

    // CREATE: POST /community_service/store
    public function store() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (empty($input)) {
            $this->output->set_status_header(400);
            echo json_encode(["status" => "error", "message" => "No data provided"]);
            return;
        }

        $fields = [
            'client_profile_id', 'profile_status', 'first_name', 'last_name', 
            'middle_name', 'alias', 'suffix', 'f53t10_id', 'created_by', 
            'created_date', 'docket_number', 'y_m', 'source', 'status', 
            'updated_by', 'updated_date', 'assigned_officer', 'profile_id', 
            'community_service_end', 'community_service_start', 'court_of_origin', 
            'criminal_case_number', 'date_received_by_ppo', 'field_office', 
            'field_office_id', 'region', 'remarks'
        ];

        $data = [];
        foreach ($fields as $field) {
            $data[$field] = isset($input[$field]) ? $input[$field] : NULL;
        }

        if (empty($data['created_date'])) {
            $data['created_date'] = date('Y-m-d H:i:s');
        }

        $new_id = $this->Community_model->insert_data($data);

        if ($new_id) {
            $this->output->set_status_header(201);
            echo json_encode(["status" => "success", "inserted_id" => $new_id]);
        } else {
            $this->output->set_status_header(500);
            echo json_encode(["status" => "error", "message" => "Database insert failed"]);
        }
    }

    // UPDATE: PUT /community_service/update/5
    public function update($id = NULL) {
        if (!$id) {
            $this->output->set_status_header(400);
            echo json_encode(["status" => "error", "message" => "ID is required"]);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            echo json_encode(["status" => "error", "message" => "No update data provided"]);
            return;
        }

        $input['updated_date'] = date('Y-m-d H:i:s');

        if ($this->Community_model->update_data($id, $input)) {
            echo json_encode(["status" => "success", "message" => "Record updated successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Update failed"]);
        }
    }

    // DELETE: DELETE /community_service/delete/5
    public function delete($id = NULL) {
        if (!$id) {
            $this->output->set_status_header(400);
            echo json_encode(["status" => "error", "message" => "ID is required"]);
            return;
        }

        if ($this->Community_model->delete_data($id)) {
            echo json_encode(["status" => "success", "message" => "Record $id deleted"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Delete failed"]);
        }
    }
}