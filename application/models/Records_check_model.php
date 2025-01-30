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
	public function get_data($db_name, $table_name, $docket_no = null, $name = null) {
        // Load the database dynamically
        $db = $this->load->database($db_name, TRUE);

        // Column mapping for petitioner and probationer based on table
        $column_map = [
            'f5t1' => 'petitioner',
            'f5t2_acted' => 'petitioner_name',
            'f5t2_notacted' => 'petitioner_name',
            'f5t2_rcv' => 'petitioner_name',
            'f5t3' => 'petitioner',
            'f5t4' => 'petitioner',
            'f5t5' => 'petitioner',
            'f5t6_cmpltd' => 'petitioner',
            'f5t6_rcv' => 'petitioner',
            'f5t7' => 'probationer',
            'f5t8' => 'probationer',
            'f5t9' => 'probationer',
            'f5t10' => 'probationer',
            'f5t11' => 'probationer',
            'f5t12' => 'probationer',
            'f5t13_rcv' => 'probationer',
            'f5t13_term' => 'probationer',
		    // f21 table mappings
		    'f21t1' => 'petitioner',
		    'f21t2_rcv' => 'petitioner_name',
		    'f21t2_acted' => 'petitioner_name',
		    'f21t4' => 'petitioner',
		    'f21t5' => 'petitioner',
		    'f21t6_rcv' => 'petitioner',
		    'f21t6_cmpltd' => 'petitioner',
		    'f21t7_pardon' => 'probationer',
		    'f21t7_parol' => 'probationer',
		    'f21t8_pardon' => 'probationer',
		    'f21t8_parol' => 'probationer',
		    'f21t9_pardon' => 'probationer',
		    'f21t9_parol' => 'probationer',
		    'f21t10_pardon' => 'probationer',
		    'f21t10_parol' => 'probationer',
		    'f21t11_pardon' => 'probationer',
		    'f21t11_parol' => 'probationer',
		    'f21t12_pardon' => 'probationer',
		    'f21t12_parol' => 'probationer',
		    'f21t13_pardon' => 'probationer',
		    'f21t13_parol' => 'probationer',
		    'f21t14_pardon' => 'probationer',
		    'f21t14_parol' => 'probationer',
		    'f21t15_rcv_pardon' => 'probationer',
		    'f21t15_rcv_parol' => 'probationer',
		    'f21t15_term_pardon' => 'probationer',
		    'f21t15_term_parol' => 'probationer',
		];

        // Check if the table is in the column map
        if (!isset($column_map[$table_name])) {
            return ['error' => 'Invalid table name or table not mapped'];
        }

        // Get the column name based on the table
        $name_column = $column_map[$table_name];

	    $docket_no = $this->security->xss_clean($docket_no);
	    $name = $this->security->xss_clean($name);
        // Build the query
        $db->from($table_name);
        $db->where('status', 1);
        if ($docket_no) {
            $db->where('docket_no', $docket_no); // Assuming 'docket_no' is a common column
        }
        if ($name) {
    		$db->like($name_column, trim($name));
        }

        // Execute the query
        $query = $db->get();
    	$result = $query->result_array();

	    // Check for errors in query execution
	    if ($query === FALSE) {
	        log_message('error', 'Database query failed: ' . $db->last_query());
	        return ['error' => 'Failed to fetch data from the database'];
	    }
	    foreach ($result as &$row) {
	        if (isset($row[$name_column])) {
	            $row['name'] = $row[$name_column];  // Assign the name column to 'name'
	            unset($row[$name_column]);  // Optionally remove the original column
	        }
	    }
        return $result;
    }
    public function get_data_expansion($db_name, $table_name, $docket_no = null, $name = null) {
	    // Load the database dynamically
	    $db = $this->load->database($db_name, TRUE);

	    // Sanitize inputs
	    $docket_no = $this->security->xss_clean($docket_no);
	    $name = $this->security->xss_clean($name);

	    // Start building the query
	    $db->select("$table_name.*, CONCAT(client_profile.first_name, ' ', client_profile.middle_name, ' ', client_profile.last_name, ' ', client_profile.suffix) AS name");
	    $db->from($table_name);
	    $db->join('client_profile', "$table_name.profile_id = client_profile.id", 'left'); // Join client_profile based on profile_id

	    // Apply filters if provided
	    if ($docket_no) {
	        $db->where("$table_name.docket_number", $docket_no);
	    }
	    if ($name) {
	        $db->like("CONCAT(client_profile.first_name, ' ', client_profile.middle_name, ' ', client_profile.last_name, ' ', client_profile.suffix)", trim($name));
	    }

	    // Ensure active records are fetched
	    $db->where("$table_name.status", 1);
	    $db->where('client_profile.status', 1);
	    
	    // Execute the query
	    $query = $db->get();

	    // If query was successful, fetch the result
	    $result = $query->result_array();

	    // Initialize response array
	    $response = [];

	    if (!empty($result)) {
	        // Customize the response to match your frontend's table structure
	        foreach ($result as $item) {
	            $response[] = [
	                'docket_no'    => $item['docket_number'],
	                'name'         => $item['name'],
	                'field_office' => $item['field_office'],
	                'Y_M'          => $item['y_m'],
	                'created_date' => $item['created_date']
	            ];
	        }
	    } else {
	        // If no results, return a custom message or empty array
	        $response = ['error' => 'No data found'];
	    }

	    return $response;
	}

}
?>