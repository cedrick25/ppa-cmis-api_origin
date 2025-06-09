<?php 
	
class Expansion_model extends CI_Model
{

	public function __construct() {
        header('Access-Control-Allow-Origin: *');
    	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    	parent::__construct();
	}

	public function communitySSP()
	{

        $this->expansion_db = $this->load->database('expansion', TRUE);
		$docket_number = $_GET['docket_number'];
		$first_name = $_GET['first_name'];
		$middle_name = $_GET['middle_name'];
		$last_name = $_GET['last_name'];
		$cc_number = $_GET['cc_number'];
		$court_of_origin = $_GET['court_of_origin'];
		$assigned_officer = $_GET['assigned_officer'];
		$start_date = $_GET['start_date'];
		$end_date = $_GET['end_date'];
		$field_office = $_GET['field_office'];
		$year = $_GET['year'] ?? '';  // Ensure $year is set

		// Initialize the base query
		$query = "(SELECT f53t10.id, f53t10.docket_number, CONCAT(client_profile.first_name, ' ', IFNULL(client_profile.middle_name, ''), ' ', client_profile.last_name, ' ', IFNULL(client_profile.suffix, '')) AS full_name, 
		            f53t10.criminal_case_number, f53t10.court_of_origin, f53t10.assigned_officer, f53t10.date_received_by_ppo, f53t10.community_service_start, f53t10.community_service_end, f53t10.field_office,
		            CASE
		                WHEN f53t10.community_service_start IS NULL AND f53t10.community_service_end IS NULL THEN ''
		                WHEN f53t10.community_service_start IS NULL THEN CONCAT(YEAR(f53t10.community_service_end))
		                WHEN f53t10.community_service_end IS NULL THEN CONCAT(YEAR(f53t10.community_service_start))
		                ELSE CONCAT(YEAR(f53t10.community_service_start), '-', YEAR(f53t10.community_service_end))
		            END AS year_range
		            FROM f53t10
		            JOIN client_profile ON f53t10.profile_id = client_profile.id
		            WHERE f53t10.status = 1";

		// Add search condition for docket_number
		if (isset($docket_number) && !empty($docket_number)) {
		    $query .= " AND f53t10.docket_number LIKE '%" . $this->expansion_db->escape_like_str($docket_number) . "%'";
		}

		// Add search condition for first_name
		if (isset($first_name) && !empty($first_name)) {
		    $query .= " AND client_profile.first_name LIKE '%" . $this->expansion_db->escape_like_str($first_name) . "%'";
		}

		// Add search condition for middle_name
		if (isset($middle_name) && !empty($middle_name)) {
		    $query .= " AND client_profile.middle_name LIKE '%" . $this->expansion_db->escape_like_str($middle_name) . "%'";
		}

		// Add search condition for last_name
		if (isset($last_name) && !empty($last_name)) {
		    $query .= " AND client_profile.last_name LIKE '%" . $this->expansion_db->escape_like_str($last_name) . "%'";
		}

		// Add search condition for CC number (criminal case number)
		if (isset($cc_number) && !empty($cc_number)) {
		    $query .= " AND f53t10.criminal_case_number LIKE '%" . $this->expansion_db->escape_like_str($cc_number) . "%'";
		}

		// Add search condition for court of origin
		if (isset($court_of_origin) && !empty($court_of_origin)) {
		    $query .= " AND f53t10.court_of_origin LIKE '%" . $this->expansion_db->escape_like_str($court_of_origin) . "%'";
		}

		// Add search condition for assigned officer
		if (isset($assigned_officer) && !empty($assigned_officer)) {
		    $query .= " AND f53t10.assigned_officer LIKE '%" . $this->expansion_db->escape_like_str($assigned_officer) . "%'";
		}

		// Add search conditions for start date and end date
		if (isset($start_date) && !empty($start_date)) {
		    $query .= " AND f53t10.community_service_start >= '" . $this->expansion_db->escape_str($start_date) . "'";
		}
		if (isset($end_date) && !empty($end_date)) {
		    $query .= " AND f53t10.community_service_end <= '" . $this->expansion_db->escape_str($end_date) . "'";
		}
		if (isset($field_office) && $field_office !== 'ALL' && !empty($field_office)) {
		    $query .= " AND f53t10.field_office LIKE '%" . $this->expansion_db->escape_like_str($field_office) . "%'";
		}
		if (isset($year) && !empty($year)) {
		    $query .= " AND (
		                    (YEAR(f53t10.community_service_start) <= " . $this->expansion_db->escape($year) . " 
		                    AND YEAR(f53t10.community_service_end) >= " . $this->expansion_db->escape($year) . ") 
		                    OR (YEAR(f53t10.community_service_start) = " . $this->expansion_db->escape($year) . ") 
		                    OR (YEAR(f53t10.community_service_end) = " . $this->expansion_db->escape($year) . ")
		                )";
		}
	    // Close the query
	    $query .= " ORDER BY id DESC) temp";

	    $primaryKey = 'id';

	    // Define the columns for DataTables
	    $columns = array(
		    array('db' => 'id', 'dt' => 0),
		    array('db' => 'docket_number', 'dt' => 1),
		    array('db' => 'full_name', 'dt' => 2), // Concatenated first_name, middle_name, last_name, suffix
		    array('db' => 'criminal_case_number', 'dt' => 3),
		    array('db' => 'court_of_origin', 'dt' => 4),
		    array('db' => 'assigned_officer', 'dt' => 5),
		    array('db' => 'date_received_by_ppo', 'dt' => 6),
		    array('db' => 'year_range', 'dt' => 7),
		    array('db' => 'community_service_start', 'dt' => 8),
		    array('db' => 'community_service_end', 'dt' => 9),
		    array('db' => 'field_office', 'dt' => 10),
		);

	    // Set SQL details for the 'expansion' database
	    $sql_details = array(
	        'user' => $this->expansion_db->username,
	        'pass' => $this->expansion_db->password,
	        'db'   => $this->expansion_db->database,
	        'host' => $this->expansion_db->hostname,
	    );

	    // Output the data in JSON format
	    echo json_encode(
	        SSP::simple($_GET, $sql_details, $query, $primaryKey, $columns)
	    );
	}
	
	public function community_json()
	{
	    $this->expansion_db = $this->load->database('expansion', TRUE);

	    $this->expansion_db->select("f53t10.id, f53t10.docket_number, 
	        CONCAT(client_profile.first_name, ' ', IFNULL(client_profile.middle_name, ''), ' ', client_profile.last_name, ' ', IFNULL(client_profile.suffix, '')) AS full_name, 
	        f53t10.criminal_case_number, f53t10.court_of_origin, f53t10.assigned_officer, 
	        f53t10.date_received_by_ppo, f53t10.community_service_start, f53t10.community_service_end, f53t10.field_office,
	        CASE
	            WHEN f53t10.community_service_start IS NULL AND f53t10.community_service_end IS NULL THEN ''
	            WHEN f53t10.community_service_start IS NULL THEN CONCAT(YEAR(f53t10.community_service_end))
	            WHEN f53t10.community_service_end IS NULL THEN CONCAT(YEAR(f53t10.community_service_start))
	            ELSE CONCAT(YEAR(f53t10.community_service_start), '-', YEAR(f53t10.community_service_end))
	        END AS year_range
	    ");
	    $this->expansion_db->from('f53t10');
	    $this->expansion_db->join('client_profile', 'f53t10.profile_id = client_profile.id');
	    $this->expansion_db->where('f53t10.status', 1);

	    // Get JSON input from POST
	    $json = file_get_contents('php://input');
	    $input = json_decode($json, true);

	    // Define filterable fields
	    $filters = [
	        'docket_number' => 'f53t10.docket_number',
	        'first_name' => 'client_profile.first_name',
	        'middle_name' => 'client_profile.middle_name',
	        'last_name' => 'client_profile.last_name',
	        'cc_number' => 'f53t10.criminal_case_number',
	        'court_of_origin' => 'f53t10.court_of_origin',
	        'assigned_officer' => 'f53t10.assigned_officer',
	        'field_office' => 'f53t10.field_office'
	    ];

	    foreach ($filters as $key => $column) {
	        if (!empty($input[$key])) {
	            if ($key === 'field_office' && strtoupper($input[$key]) === 'ALL') {
		            continue;
		        }
		        $this->expansion_db->like($column, $input[$key]);
	        }
	    }

	    // Filter for year
	    if (!empty($input['year'])) {
	        $year = $input['year'];
	        $this->expansion_db->group_start()
	            ->where("YEAR(f53t10.community_service_start) <=", $year)
	            ->where("YEAR(f53t10.community_service_end) >=", $year)
	            ->or_where("YEAR(f53t10.community_service_start)", $year)
	            ->or_where("YEAR(f53t10.community_service_end)", $year)
	            ->group_end();
	    }

	    // Date range filters
	    if (!empty($input['start_date'])) {
	        $this->expansion_db->where('f53t10.community_service_start >=', $input['start_date']);
	    }

	    if (!empty($input['end_date'])) {
	        $this->expansion_db->where('f53t10.community_service_end <=', $input['end_date']);
	    }

	    // Pagination
	    $limit = isset($input['limit']) ? (int)$input['limit'] : 100;
	    $offset = isset($input['offset']) ? (int)$input['offset'] : 0;
	    $this->expansion_db->limit($limit, $offset);

	    $this->expansion_db->order_by('f53t10.id', 'DESC');
	    $query = $this->expansion_db->get();

	    echo json_encode($query->result_array());
	}

}


?>