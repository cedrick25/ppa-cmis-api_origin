<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class No_reports extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Set headers for API access
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    }

    // Get all reports
    public function get_reports($type = NULL) {
        // Call the model's method with the type parameter
        $reports = $this->No_reports_model->get_reports($type);
        
        // Send the response as JSON
        echo json_encode($reports);
    }

    // Get a single report by ID
    public function get_report($id) {
        $report = $this->No_reports_model->get_report($id);
        echo json_encode($report);
    }

    // Create a new report
    public function create_report() {
        // Read the incoming JSON data from the request body
        $payload = json_decode(file_get_contents('php://input'), true);

        if (!$payload || !isset($payload['office']) || !isset($payload['first_name'])) {
            echo json_encode(['status' => 'Invalid Input']);
            return;
        }

        // Call the model to insert the report
        $inserted_id = $this->No_reports_model->create_report($payload);

        if ($inserted_id > 0) {
            echo json_encode(['status' => 'Report Created', 'id' => $inserted_id]);
        } else {
            echo json_encode(['status' => 'Failed to Create Report']);
        }
    }

    // Update a report
    public function update_report($id) {
        $payload = json_decode(file_get_contents('php://input'), true);

	    // Check if the payload and necessary fields are provided
	    if (!$payload || !isset($payload['office']) || !isset($payload['first_name'])) {
	        echo json_encode(['status' => 'Invalid Input']);
	        return;
	    }

	    // Call the model to update the report
	    $affected_rows = $this->No_reports_model->update_report($id, $payload);

	    // Check if the update was successful
	    if ($affected_rows > 0) {
	        echo json_encode(['status' => 'Report Updated', 'affected_rows' => $affected_rows]);
	    } else {
	        echo json_encode(['status' => 'Failed to Update Report']);
	    }
    }

    // Delete a report
    public function delete_report($id) {
        $affected_rows = $this->No_reports_model->delete_report($id);
        echo json_encode(['status' => 'Report Deleted', 'affected_rows' => $affected_rows]);
    }
}
?>
