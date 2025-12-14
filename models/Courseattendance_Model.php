<?php
class Studentattendance_model extends CI_Model {

    public function __construct() {
        //$this->load->database();
    }

    public function calculate_attendance_percentage($attended_classes, $total_classes) {
        if ($total_classes == 0) {
            return 0; // Avoid division by zero
        }
        return ($attended_classes / $total_classes) * 100;
    }

    public function print_course_master($system_id) {
		$this->db->where('system_id', $system_id);
        $query = $this->db->get('tbl_coursewise_attendance_master');
		return $query->result_array();
    }

    public function calculate_total_class($academic_year_id, $course_id, $class_number, $start_date = null, $noc_dates = null) {
        $this->db->select('COUNT(DISTINCT attendance_date, slot_name) as total');
        $this->db->from('tbl_attendance_master');
        $this->db->where('academic_year_id', $academic_year_id);
        $this->db->where('course_id', $course_id);
        $this->db->where('class_number', $class_number);
        
        if ($start_date) {
            $this->db->where('attendance_date >=', $start_date);
        }
        if (strtotime($noc_dates[0])>0) {
			
            $this->db->where("'attendance_date' 'NOT' 'BETWEEN' '".$noc_dates[0]."' AND '".$noc_dates[1]."'");
        }
        
        $query = $this->db->get();
		#echo  $this->db->last_query(); die;
        $result = $query->row_array();
        return $result ? $result['total'] : 0;
    }

    public function calculate_students_class($academic_year_id, $course_id, $class_number, $system_id, $start_date = null) {
        $this->db->select('COUNT(DISTINCT attendance_date, slot_name) as total');
        $this->db->from('tbl_attendance_master');
        $this->db->where('academic_year_id', $academic_year_id);
        $this->db->where('course_id', $course_id);
        $this->db->where('class_number', $class_number);
        $this->db->where('system_id', $system_id);
        $this->db->where_in('attendance_val', ['1', '2', '3']);
        
        if ($start_date) {
            $this->db->where('attendance_date >=', $start_date);
        }
        
        $query = $this->db->get();
        $result = $query->row_array();
        return $result ? $result['total'] : 0;
    }

    public function round_to_nearest($number, $multiple) {
        return round($number / $multiple) * $multiple;
    }

    public function semester_record($course_id, $class_number) {
        $this->db->select('semester_id');
        $this->db->from('tbl_assign_room_slot_section');
        $this->db->where('course_id', $course_id);
        $this->db->where('class_number', $class_number);
        
        $query = $this->db->get();
        $result = $query->row_array();
        return $result ? $result['semester_id'] : null;
    }

    public function check_and_upsert_record($table, $data, $condition1, $condition2) {
        $this->db->where($condition1[0], $condition1[1]);
        $this->db->where($condition2[0], $condition2[1]);
        $query = $this->db->get($table);
        
        if ($query->num_rows() > 0) {
            // Update record
            $this->db->where($condition1[0], $condition1[1]);
            $this->db->where($condition2[0], $condition2[1]);
            $this->db->update($table, $data);
        } else {
            // Insert record
            $this->db->insert($table, $data);
        }
		#echo  $this->db->last_query(); die;
    }

    public function check_and_upsert_record_report($table, $data, $condition1, $condition2, $condition3) {
        $this->db->where($condition1[0], $condition1[1]);
        $this->db->where($condition2[0], $condition2[1]);
        $this->db->where($condition3[0], $condition3[1]);
        $query = $this->db->get($table);
        
        if ($query->num_rows() > 0) {
            // Update record
            $this->db->where($condition1[0], $condition1[1]);
            $this->db->where($condition2[0], $condition2[1]);
            $this->db->where($condition3[0], $condition3[1]);
            $this->db->update($table, $data);
        } else {
            // Insert record
            $this->db->insert($table, $data);
        }
		#echo  $this->db->last_query(); die;
    }

    public function get_start_date_from_table_one($system_id) {
        $this->db->select('attendance_date');
        $this->db->from('tbl_attendance_assignment');
        $this->db->where('system_id', $system_id);
        $this->db->where('status', '1');
        $this->db->where('academic_year_id', '4');
        
        $query = $this->db->get();
        $result = $query->row_array();
        return $result ? $result['attendance_date'] : null;
    }

    public function get_employee_data($employee_id) {
        $this->db->select('full_name');
        $this->db->from('tbl_employee_master');
        $this->db->where('employee_id', $employee_id);
        $this->db->where('status', '1');
        
        $query = $this->db->get();
        $result = $query->row_array();
        return $result ? $result['full_name'] : null;
    }

    public function get_noc_from_table_one($system_id) {
        $this->db->select('from_date, to_date');
        $this->db->from('tbl_attendanceapprovalcases');
        $this->db->where('system_id', $system_id);
        $this->db->where('applied_flag', '1');
        $this->db->where('term', '2401');
        
        $query = $this->db->get();
        $result = $query->row_array();
        return $result ? [$result['from_date'], $result['to_date']] : null;
    }

    public function get_prog_ef_date($system_id, $academic_year_id='4', $current_term='2401') {
		
		$pattern = '/^2024/';
		if (preg_match($pattern, $system_id)) {
			$this->db->select('added_date');
			$this->db->from('tbl_student_courses');
			$this->db->where('system_id', $system_id);
			$this->db->where('current_term', $current_term);        
			$this->db->where('academic_year_id',  $academic_year_id);        
			$query = $this->db->get();
			#echo  $this->db->last_query(); die;
			$result = $query->row_array();
			return $result ? $result['added_date'] : null;
		} else {
			 return null;
		}
    }
	
	
}
?>
