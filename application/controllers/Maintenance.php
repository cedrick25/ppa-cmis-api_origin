<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Maintenance extends CI_Controller {

    public function update_offices() {
        // Load both databases
        $db_default = $this->load->database('default', TRUE);
        $db_expansion = $this->load->database('expansion', TRUE);
        
        $tables_default = [
            'f5t1', 'f5t2_acted', 'f5t2_notacted', 'f5t2_rcv', 'f5t3', 
            'f5t4', 'f5t5', 'f5t6_cmpltd', 'f5t6_rcv', 'f5t7', 
            'f5t8', 'f5t9', 'f5t10', 'f5t11', 'f5t12', 'f5t13_rcv', 'f5t13_term',
            'f21t1', 'f21t2_acted', 'f21t2_rcv', 'f21t3', 'f21t4', 'f21t5', 
            'f21t6_cmpltd', 'f21t6_rcv', 'f21t7_pardon', 'f21t7_parol', 
            'f21t8_pardon', 'f21t8_parol', 'f21t9_pardon', 'f21t9_parol', 
            'f21t10_pardon', 'f21t10_parol', 'f21t11_pardon', 'f21t11_parol', 
            'f21t12_pardon', 'f21t12_parol', 'f21t13_pardon', 'f21t13_parol', 
            'f21t14_pardon', 'f21t14_parol', 'f21t15_rcv_pardon', 'f21t15_rcv_parol', 
            'f21t15_term_pardon', 'f21t15_term_parol'
        ];

        $tables_expansion = [
            'f44t1', 'f44t2', 'f44t2_acted', 'f44t3', 'f44t4', 'f44t5', 'f44t6', 
            'f44t6_car', 'f44t7', 'f44t8', 'f44t9', 'f44t10', 'f44t11', 'f44t12', 
            'f44t13', 'f44t13_crt', 'f45t1', 'f45t2', 'f45t2_acted', 'f45t3', 
            'f45t4', 'f45t5', 'f45t6', 'f45t6_car', 'f45t7', 'f45t8', 'f45t9', 
            'f45t10', 'f45t11', 'f45t12', 'f45t13', 'f45t13_crt', 'f50t1', 
            'f50t2', 'f51t1', 'f51t2', 'f51t3', 'f51t4', 'f53t1', 'f53t2', 
            'f53t3', 'f53t4', 'f53t5', 'f53t6', 'f53t7', 'f53t8', 'f53t9', 
            'f53t10', 'f53t11'
        ];

        $total_rows_updated = 0;
        $total_tables_processed = count($tables_default) + count($tables_expansion);

        // 1. Process Default DB
        $db_default->trans_start();
        foreach ($tables_default as $table) {
            $total_rows_updated += $this->_apply_update($db_default, $table);
        }
        $db_default->trans_complete();

        // 2. Process Expansion DB
        $db_expansion->trans_start();
        foreach ($tables_expansion as $table) {
            $total_rows_updated += $this->_apply_update($db_expansion, $table);
        }
        $db_expansion->trans_complete();

        // Output simple message
        if ($db_default->trans_status() === FALSE || $db_expansion->trans_status() === FALSE) {
            echo "Error: Update failed in one of the databases.";
        } else {
            echo "Success: All office names updated across " . $total_tables_processed . " tables with " . $total_rows_updated . " total records modified.";
        }
    }

    private function _apply_update($db_obj, $table) {
        $rows = 0;
        if ($db_obj->field_exists('field_office', $table)) {
            // Update Masbate
            $db_obj->where('field_office', 'Masbate City Parole And Probation Office');
            $db_obj->update($table, ['field_office' => 'Masbate Province/City Parole And Probation Office']);
            $rows += $db_obj->affected_rows();

            // Update Sorsogon
            $db_obj->where('field_office', 'Sorsogon City Parole And Probation Office');
            $db_obj->update($table, ['field_office' => 'Sorsogon City/Province Parole And Probation Office']);
            $rows += $db_obj->affected_rows();
        }
        return $rows;
    }
}