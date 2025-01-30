<?php
class No_reports_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        // Enabling CORS headers for API
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    }

    // Get all reports
    public function get_reports($type = NULL) {
	    if ($type) {
	        $this->db->where('type', $type); // Filter by type if it's provided
	    }

	    $this->db->order_by('date_created', 'ASC'); // Sort by date_created (ascending)
	    
	    $query = $this->db->get('no_reports');
	    return $query->result_array();
	}

    // Get a specific report by ID
    public function get_report($id) {
	    if (empty($id) || !is_numeric($id)) {
	        return ['status' => 'Invalid ID'];
	    }

	    $query = $this->db->get_where('no_reports', array('id' => $id));

	    $result = $query->row_array();

	    if ($result) {
	        return $result;
	    } else {
	        return ['status' => 'Report not found'];
	    }
	}


    // Create a new report
    public function create_report($data) {
        $this->db->insert('no_reports', $data);
        return $this->db->insert_id(); // Return the ID of the inserted row
    }

    public function update_report($id, $data) {
        // Ensure office_id or other required fields are present in $data
        if (isset($data['office']) && isset($data['first_name'])) {
            $this->db->where('id', $id);
            $this->db->update('no_reports', $data);

            // Return the number of affected rows
            return $this->db->affected_rows();
        }
        return 0;
    }

    // Delete a report
    public function delete_report($id) {
        $this->db->where('id', $id);
        $this->db->delete('no_reports');
        return $this->db->affected_rows(); // Return the number of affected rows
    }
}
?>
