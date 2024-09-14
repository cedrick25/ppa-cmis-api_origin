<?php 
	
class Records_check_model extends CI_Model
{

	public function __construct() {
        header('Access-Control-Allow-Origin: *');
    	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    	parent::__construct();
	}

	public function f5t1()
	{
	    $this->load->database();
	    
	    // Get filter values from the request
	    $docket_no = $_GET['docket_no'] ?? '';
	    $petitioner = $_GET['petitioner'] ?? '';
	    $year = $_GET['year'] ?? '';
	    $field_office = $_GET['field_office'] ?? '';
	    $month = $_GET['month'] ?? '';
		$quarter = $_GET['quarter'] ?? '';

	    // Initialize the base query
	    $query = "(SELECT id, docket_no, petitioner, 
	                date_rcv, investigating_officer, Y_M, field_office, created_date
	                FROM F5T1
	                WHERE status = 1";

	    // Add filters based on inputs
	    if (!empty($docket_no)) {
	        $query .= " AND docket_no LIKE '%" . $this->db->escape_like_str($docket_no) . "%'";
	    }
	    if (!empty($petitioner)) {
	        $query .= " AND petitioner LIKE '%" . $this->db->escape_like_str($petitioner) . "%'";
	    }
	    if (!empty($year)) {
		    $query .= " AND SUBSTRING(Y_M, 1, 4) = '" . $this->db->escape_str($year) . "'";
		}
		if (!empty($month)) {
		    $query .= " AND SUBSTRING(Y_M, 6, 2) = '" . str_pad($this->db->escape_str($month), 2, '0', STR_PAD_LEFT) . "'";
		}
		if (!empty($quarter)) {
		    switch ($quarter) {
		        case '1':
		            $query .= " AND SUBSTRING(Y_M, 6, 2) BETWEEN '01' AND '03'";
		            break;
		        case '2':
		            $query .= " AND SUBSTRING(Y_M, 6, 2) BETWEEN '04' AND '06'";
		            break;
		        case '3':
		            $query .= " AND SUBSTRING(Y_M, 6, 2) BETWEEN '07' AND '09'";
		            break;
		        case '4':
		            $query .= " AND SUBSTRING(Y_M, 6, 2) BETWEEN '10' AND '12'";
		            break;
		    }
		}
	    if (!empty($field_office) && $field_office !== 'ALL') {
	        $query .= " AND field_office LIKE '%" . $this->db->escape_like_str($field_office) . "%'";
	    }

	    // Close the query
	    $query .= " ORDER BY id DESC) temp";

	    // Define the primary key and columns for DataTables
	    $primaryKey = 'id';

	    $columns = array(
	        array('db' => 'id', 'dt' => 0),
	        array('db' => 'docket_no', 'dt' => 1),
	        array('db' => 'petitioner', 'dt' => 2), 
	        array('db' => 'date_rcv', 'dt' => 3),
	        array('db' => 'investigating_officer', 'dt' => 4),
	        array('db' => 'field_office', 'dt' => 5),
	        array('db' => 'Y_M', 'dt' => 6)
	    );

	    // Database connection information
	    $sql_details = array(
	        'user' => $this->db->username,
	        'pass' => $this->db->password,
	        'db'   => $this->db->database,
	        'host' => $this->db->hostname,
	    );

	    // Output the data in JSON format using SSP (server-side processing for DataTables)
	    echo json_encode(
	        SSP::simple($_GET, $sql_details, $query, $primaryKey, $columns)
	    );
	}
}
?>