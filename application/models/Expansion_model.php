<?php 
	
class Expansion_model extends CI_Model
{

	public function __construct() {
        header('Access-Control-Allow-Origin: *');
    	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    	parent::__construct();
	}

	// public function communitySSP()
	// {

    //     $this->expansion_db = $this->load->database('expansion', TRUE);
	// 	$docket_number = $_GET['docket_number'];
	// 	$first_name = $_GET['first_name'];
	// 	$middle_name = $_GET['middle_name'];
	// 	$last_name = $_GET['last_name'];
	// 	$cc_number = $_GET['cc_number'];
	// 	$court_of_origin = $_GET['court_of_origin'];
	// 	$assigned_officer = $_GET['assigned_officer'];
	// 	$start_date = $_GET['start_date'];
	// 	$end_date = $_GET['end_date'];
	// 	$field_office = $_GET['field_office'];
	// 	$year = $_GET['year'] ?? '';  // Ensure $year is set

	// 	// Initialize the base query
	// 	$query = "(SELECT f53t10.id, f53t10.docket_number, CONCAT(client_profile.first_name, ' ', IFNULL(client_profile.middle_name, ''), ' ', client_profile.last_name, ' ', IFNULL(client_profile.suffix, '')) AS full_name, 
	// 	            f53t10.criminal_case_number, f53t10.court_of_origin, f53t10.assigned_officer, f53t10.date_received_by_ppo, f53t10.community_service_start, f53t10.community_service_end, f53t10.field_office,
	// 	            CASE
	// 	                WHEN f53t10.community_service_start IS NULL AND f53t10.community_service_end IS NULL THEN ''
	// 	                WHEN f53t10.community_service_start IS NULL THEN CONCAT(YEAR(f53t10.community_service_end))
	// 	                WHEN f53t10.community_service_end IS NULL THEN CONCAT(YEAR(f53t10.community_service_start))
	// 	                ELSE CONCAT(YEAR(f53t10.community_service_start), '-', YEAR(f53t10.community_service_end))
	// 	            END AS year_range
	// 	            FROM f53t10
	// 	            JOIN client_profile ON f53t10.profile_id = client_profile.id
	// 	            WHERE f53t10.status = 1";

	// 	// Add search condition for docket_number
	// 	if (isset($docket_number) && !empty($docket_number)) {
	// 	    $query .= " AND f53t10.docket_number LIKE '%" . $this->expansion_db->escape_like_str($docket_number) . "%'";
	// 	}

	// 	// Add search condition for first_name
	// 	if (isset($first_name) && !empty($first_name)) {
	// 	    $query .= " AND client_profile.first_name LIKE '%" . $this->expansion_db->escape_like_str($first_name) . "%'";
	// 	}

	// 	// Add search condition for middle_name
	// 	if (isset($middle_name) && !empty($middle_name)) {
	// 	    $query .= " AND client_profile.middle_name LIKE '%" . $this->expansion_db->escape_like_str($middle_name) . "%'";
	// 	}

	// 	// Add search condition for last_name
	// 	if (isset($last_name) && !empty($last_name)) {
	// 	    $query .= " AND client_profile.last_name LIKE '%" . $this->expansion_db->escape_like_str($last_name) . "%'";
	// 	}

	// 	// Add search condition for CC number (criminal case number)
	// 	if (isset($cc_number) && !empty($cc_number)) {
	// 	    $query .= " AND f53t10.criminal_case_number LIKE '%" . $this->expansion_db->escape_like_str($cc_number) . "%'";
	// 	}

	// 	// Add search condition for court of origin
	// 	if (isset($court_of_origin) && !empty($court_of_origin)) {
	// 	    $query .= " AND f53t10.court_of_origin LIKE '%" . $this->expansion_db->escape_like_str($court_of_origin) . "%'";
	// 	}

	// 	// Add search condition for assigned officer
	// 	if (isset($assigned_officer) && !empty($assigned_officer)) {
	// 	    $query .= " AND f53t10.assigned_officer LIKE '%" . $this->expansion_db->escape_like_str($assigned_officer) . "%'";
	// 	}

	// 	// Add search conditions for start date and end date
	// 	if (isset($start_date) && !empty($start_date)) {
	// 	    $query .= " AND f53t10.community_service_start >= '" . $this->expansion_db->escape_str($start_date) . "'";
	// 	}
	// 	if (isset($end_date) && !empty($end_date)) {
	// 	    $query .= " AND f53t10.community_service_end <= '" . $this->expansion_db->escape_str($end_date) . "'";
	// 	}
	// 	if (isset($field_office) && $field_office !== 'ALL' && !empty($field_office)) {
	// 	    $query .= " AND f53t10.field_office LIKE '%" . $this->expansion_db->escape_like_str($field_office) . "%'";
	// 	}
	// 	if (isset($year) && !empty($year)) {
	// 	    $query .= " AND (
	// 	                    (YEAR(f53t10.community_service_start) <= " . $this->expansion_db->escape($year) . " 
	// 	                    AND YEAR(f53t10.community_service_end) >= " . $this->expansion_db->escape($year) . ") 
	// 	                    OR (YEAR(f53t10.community_service_start) = " . $this->expansion_db->escape($year) . ") 
	// 	                    OR (YEAR(f53t10.community_service_end) = " . $this->expansion_db->escape($year) . ")
	// 	                )";
	// 	}
	//     // Close the query
	//     $query .= " ORDER BY id DESC) temp";

	//     $primaryKey = 'id';

	//     // Define the columns for DataTables
	//     $columns = array(
	// 	    array('db' => 'id', 'dt' => 0),
	// 	    array('db' => 'docket_number', 'dt' => 1),
	// 	    array('db' => 'full_name', 'dt' => 2), // Concatenated first_name, middle_name, last_name, suffix
	// 	    array('db' => 'criminal_case_number', 'dt' => 3),
	// 	    array('db' => 'court_of_origin', 'dt' => 4),
	// 	    array('db' => 'assigned_officer', 'dt' => 5),
	// 	    array('db' => 'date_received_by_ppo', 'dt' => 6),
	// 	    array('db' => 'year_range', 'dt' => 7),
	// 	    array('db' => 'community_service_start', 'dt' => 8),
	// 	    array('db' => 'community_service_end', 'dt' => 9),
	// 	    array('db' => 'field_office', 'dt' => 10),
	// 	);

	//     // Set SQL details for the 'expansion' database
	//     $sql_details = array(
	//         'user' => $this->expansion_db->username,
	//         'pass' => $this->expansion_db->password,
	//         'db'   => $this->expansion_db->database,
	//         'host' => $this->expansion_db->hostname,
	//     );

	//     // Output the data in JSON format
	//     echo json_encode(
	//         SSP::simple($_GET, $sql_details, $query, $primaryKey, $columns)
	//     );
	// }
	public function communitySSP()
	{
	    $this->expansion_db = $this->load->database('expansion', TRUE);

	    // Safer way to get GET values
	    $docket_number    = $this->input->get('docket_number', TRUE);
	    $first_name       = $this->input->get('first_name', TRUE);
	    $middle_name      = $this->input->get('middle_name', TRUE);
	    $last_name        = $this->input->get('last_name', TRUE);
	    $cc_number        = $this->input->get('cc_number', TRUE);
	    $court_of_origin  = $this->input->get('court_of_origin', TRUE);
	    $assigned_officer = $this->input->get('assigned_officer', TRUE);
	    $start_date       = $this->input->get('start_date', TRUE);
	    $end_date         = $this->input->get('end_date', TRUE);
	    $field_office     = $this->input->get('field_office', TRUE);
	    $year             = $this->input->get('year', TRUE);

	    $query = "(SELECT 
	                csm.id,
	                csm.docket_number,
	                CONCAT(csm.first_name, ' ', IFNULL(csm.middle_name, ''), ' ', csm.last_name, ' ', IFNULL(csm.suffix, '')) AS full_name,
	                csm.criminal_case_number,
	                csm.court_of_origin,
	                csm.assigned_officer,
	                csm.date_received_by_ppo,
	                csm.community_service_start,
	                csm.community_service_end,
	                csm.field_office,
	                CASE
	                    WHEN csm.community_service_start IS NULL AND csm.community_service_end IS NULL THEN ''
	                    WHEN csm.community_service_start IS NULL THEN YEAR(csm.community_service_end)
	                    WHEN csm.community_service_end IS NULL THEN YEAR(csm.community_service_start)
	                    ELSE CONCAT(YEAR(csm.community_service_start), '-', YEAR(csm.community_service_end))
	                END AS year_range
	            FROM community_service_masterlist csm
	            WHERE csm.status = 1";

	    // Filters
	    if (!empty($docket_number)) {
	        $query .= " AND csm.docket_number LIKE '%" . $this->expansion_db->escape_like_str($docket_number) . "%'";
	    }

	    if (!empty($first_name)) {
	        $query .= " AND csm.first_name LIKE '%" . $this->expansion_db->escape_like_str($first_name) . "%'";
	    }

	    if (!empty($middle_name)) {
	        $query .= " AND csm.middle_name LIKE '%" . $this->expansion_db->escape_like_str($middle_name) . "%'";
	    }

	    if (!empty($last_name)) {
	        $query .= " AND csm.last_name LIKE '%" . $this->expansion_db->escape_like_str($last_name) . "%'";
	    }

	    if (!empty($cc_number)) {
	        $query .= " AND csm.criminal_case_number LIKE '%" . $this->expansion_db->escape_like_str($cc_number) . "%'";
	    }

	    if (!empty($court_of_origin)) {
	        $query .= " AND csm.court_of_origin LIKE '%" . $this->expansion_db->escape_like_str($court_of_origin) . "%'";
	    }

	    if (!empty($assigned_officer)) {
	        $query .= " AND csm.assigned_officer LIKE '%" . $this->expansion_db->escape_like_str($assigned_officer) . "%'";
	    }

	    if (!empty($start_date)) {
	        $query .= " AND csm.community_service_start >= '" . $this->expansion_db->escape_str($start_date) . "'";
	    }

	    if (!empty($end_date)) {
	        $query .= " AND csm.community_service_end <= '" . $this->expansion_db->escape_str($end_date) . "'";
	    }

	    if (!empty($field_office) && $field_office !== 'ALL') {
	        $query .= " AND csm.field_office LIKE '%" . $this->expansion_db->escape_like_str($field_office) . "%'";
	    }

	    if (!empty($year)) {
	        $year = (int)$year; // force numeric for safety
	        $query .= " AND (
	                        YEAR(csm.community_service_start) = $year
	                        OR YEAR(csm.community_service_end) = $year
	                        OR (
	                            YEAR(csm.community_service_start) <= $year
	                            AND YEAR(csm.community_service_end) >= $year
	                        )
	                    )";
	    }

	    $query .= " ORDER BY csm.id DESC) temp";

	    $primaryKey = 'id';

	    $columns = array(
	        array('db' => 'id', 'dt' => 0),
	        array('db' => 'docket_number', 'dt' => 1),
	        array('db' => 'full_name', 'dt' => 2),
	        array('db' => 'criminal_case_number', 'dt' => 3),
	        array('db' => 'court_of_origin', 'dt' => 4),
	        array('db' => 'assigned_officer', 'dt' => 5),
	        array('db' => 'date_received_by_ppo', 'dt' => 6),
	        array('db' => 'year_range', 'dt' => 7),
	        array('db' => 'community_service_start', 'dt' => 8),
	        array('db' => 'community_service_end', 'dt' => 9),
	        array('db' => 'field_office', 'dt' => 10),
	    );

	    $sql_details = array(
	        'user' => $this->expansion_db->username,
	        'pass' => $this->expansion_db->password,
	        'db'   => $this->expansion_db->database,
	        'host' => $this->expansion_db->hostname,
	    );

	    echo json_encode(
	        SSP::simple($_GET, $sql_details, $query, $primaryKey, $columns)
	    );
	}

	public function community_json($payload = null)
	{
	    $this->expansion_db = $this->load->database('expansion', TRUE);

	    $this->expansion_db->select("
	        community_service_masterlist.id, 
	        community_service_masterlist.docket_number, 
	        CONCAT(community_service_masterlist.first_name, ' ', IFNULL(community_service_masterlist.middle_name, ''), ' ', community_service_masterlist.last_name, ' ', IFNULL(community_service_masterlist.suffix, '')) AS full_name, 
	        community_service_masterlist.criminal_case_number, 
        	community_service_masterlist.court_of_origin,
	        community_service_masterlist.assigned_officer, 
	        community_service_masterlist.date_received_by_ppo,
	        community_service_masterlist.community_service_start, 
	        community_service_masterlist.community_service_end,
	        community_service_masterlist.field_office,
	        CASE
	            WHEN community_service_masterlist.community_service_start IS NULL AND community_service_masterlist.community_service_end IS NULL THEN ''
	            WHEN community_service_masterlist.community_service_start IS NULL THEN CONCAT(YEAR(community_service_masterlist.community_service_end))
	            WHEN community_service_masterlist.community_service_end IS NULL THEN CONCAT(YEAR(community_service_masterlist.community_service_start))
	            ELSE CONCAT(YEAR(community_service_masterlist.community_service_start), '-', YEAR(community_service_masterlist.community_service_end))
	        END AS year_range
	    ");
	    
	    $this->expansion_db->from('community_service_masterlist');
	    $this->expansion_db->where('community_service_masterlist.status', 1);

	    // Prefer payload already read by the controller (php://input is often one-shot)
	    if (is_array($payload)) {
	        $input = $payload;
	    } elseif (is_object($payload)) {
	        $input = (array) $payload;
	    } else {
	        $json = file_get_contents('php://input');
	        $input = json_decode($json, true);
	    }
	    if (!is_array($input)) {
	        $input = array();
	    }

	    // Same plain LIKE filters as communitySSP (table search) — no COLLATE/CONVERT
	    if (!empty($input['docket_number'])) {
	        $this->expansion_db->like('community_service_masterlist.docket_number', $input['docket_number']);
	    }
	    if (!empty($input['first_name'])) {
	        $this->expansion_db->like('community_service_masterlist.first_name', $input['first_name']);
	    }
	    if (!empty($input['middle_name'])) {
	        $this->expansion_db->like('community_service_masterlist.middle_name', $input['middle_name']);
	    }
	    if (!empty($input['last_name'])) {
	        $this->expansion_db->like('community_service_masterlist.last_name', $input['last_name']);
	    }
	    if (!empty($input['cc_number'])) {
	        $this->expansion_db->like('community_service_masterlist.criminal_case_number', $input['cc_number']);
	    }
	    if (!empty($input['court_of_origin'])) {
	        $this->expansion_db->like('community_service_masterlist.court_of_origin', $input['court_of_origin']);
	    }
	    if (!empty($input['assigned_officer'])) {
	        $this->expansion_db->like('community_service_masterlist.assigned_officer', $input['assigned_officer']);
	    }
	    if (!empty($input['start_date'])) {
	        $this->expansion_db->where('community_service_masterlist.community_service_start >=', $input['start_date']);
	    }
	    if (!empty($input['end_date'])) {
	        $this->expansion_db->where('community_service_masterlist.community_service_end <=', $input['end_date']);
	    }
	    if (!empty($input['field_office']) && strtoupper($input['field_office']) !== 'ALL') {
	        $this->expansion_db->like('community_service_masterlist.field_office', $input['field_office']);
	    }
	    if (!empty($input['year'])) {
	        $year = (int) $input['year'];
	        $this->expansion_db->where(
	            "(
	                YEAR(community_service_masterlist.community_service_start) = {$year}
	                OR YEAR(community_service_masterlist.community_service_end) = {$year}
	                OR (
	                    YEAR(community_service_masterlist.community_service_start) <= {$year}
	                    AND YEAR(community_service_masterlist.community_service_end) >= {$year}
	                )
	            )",
	            NULL,
	            FALSE
	        );
	    }

	    $limit = isset($input['limit']) ? (int) $input['limit'] : 100;
	    $offset = isset($input['offset']) ? (int) $input['offset'] : 0;
	    $this->expansion_db->limit($limit, $offset);
	    $this->expansion_db->order_by('community_service_masterlist.id', 'DESC');

	    $query = $this->expansion_db->get();

	    header('Content-Type: application/json; charset=utf-8');

	    if ($query === false) {
	        echo json_encode(array(), JSON_UNESCAPED_UNICODE);
	        return;
	    }

	    echo json_encode($query->result_array(), JSON_UNESCAPED_UNICODE);
	}

}


?>