<?php
class Studentattendance_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		$this->db2 = $this->load->database('db2', TRUE);
	}

    public function calculate_attendance_percentage($attended_classes, $total_classes) {
        if ($total_classes == 0) {
            return 0; // Avoid division by zero
        }
        return ($attended_classes / $total_classes) * 100;
    }

    public function print_course_master_mse($system_id) {
		$this->db2->where('system_id', $system_id);
        $query = $this->db2->get('tbl_coursewise_attendance_master_mse');
		return $query->result_array();
    }
	
	public function print_course_master($system_id) {
		$this->db2->where('system_id', $system_id);
        $query = $this->db2->get('tbl_coursewise_attendance_master');
		#echo  $this->db2->last_query(); die;
		return $query->result_array();
    }

    public function calculate_total_class_mte($academic_year_id, $course_id, $class_number, $start_date = null, $noc_dates = null) {
        $this->db2->select('COUNT(DISTINCT attendance_date, slot_name) as total');
        $this->db2->from('tbl_attendance_master_2401_1');
        $this->db2->where('academic_year_id', $academic_year_id);
        $this->db2->where('course_id', $course_id);
        $this->db2->where('class_number', $class_number);
        $this->db2->where('is_deleted', '0');
        
        if ($start_date) {
            $this->db2->where('attendance_date >=', $start_date);
        }
        if (strtotime($noc_dates[0])>0) {
			
           # $this->db->where("'attendance_date' 'NOT' 'BETWEEN' '".$noc_dates[0]."' AND '".$noc_dates[1]."'");
			$this->db2->where("NOT (attendance_date BETWEEN '".$noc_dates[0]."' AND '".$noc_dates[1]."')", NULL, FALSE);
        }
        
        $query = $this->db2->get();
		#echo  $this->db->last_query(); die;
        $result = $query->row_array();
        return $result ? $result['total'] : 0;
    }

    public function calculate_total_class($academic_year_id, $course_id, $class_number, $start_date = null, $noc_dates = null, $section_last_att_date = null) {
        $this->db2->select('COUNT(DISTINCT attendance_date, slot_name) as total');
        $this->db2->from('tbl_attendance_master');
        $this->db2->where('academic_year_id', $academic_year_id);
        $this->db2->where('course_id', $course_id);
        $this->db2->where('class_number', $class_number);
        $this->db2->where('is_deleted', '0');
        if ($start_date) {
            $this->db2->where('attendance_date >=', $start_date);
        }
		if ($section_last_att_date) {
            $this->db2->where('attendance_date <=', $section_last_att_date);
        }
        if (strtotime($noc_dates[0])>0) {
			$this->db2->where("NOT (attendance_date BETWEEN '".$noc_dates[0]."' AND '".$noc_dates[1]."')", NULL, FALSE);
            #$this->db->where("'attendance_date' 'NOT' 'BETWEEN' '".$noc_dates[0]."' AND '".$noc_dates[1]."'");
        }
        
        $query = $this->db2->get();
		#echo  $this->db2->last_query(); die;
		$result = $query->row_array();
        return $result ? $result['total'] : 0;
    }

    public function calculate_students_class_mte($academic_year_id, $course_id, $class_number, $system_id, $start_date = null,$section_status='0') {
        $this->db2->select('COUNT(DISTINCT attendance_date, slot_name) as total');
        $this->db2->from('tbl_attendance_master_2401_1');
        $this->db2->where('academic_year_id', $academic_year_id);
        $this->db2->where('course_id', $course_id);
		$this->db2->where('is_deleted', '0');
		if($section_status=='0'){
        $this->db2->where('class_number', $class_number);
		}
        $this->db2->where('system_id', $system_id);
        $this->db2->where_in('attendance_val', ['1', '2', '3']);
        
        if ($start_date) {
            $this->db2->where('attendance_date >=', $start_date);
        }
        
        $query = $this->db2->get();
        $result = $query->row_array();
        return $result ? $result['total'] : 0;
    }

    public function calculate_students_class($academic_year_id, $course_id, $class_number, $system_id, $start_date = null,$section_status='0' , $section_last_att_date = null) {
        $this->db2->select('COUNT(DISTINCT attendance_date, slot_name) as total');
        $this->db2->from('tbl_attendance_master');
        $this->db2->where('academic_year_id', $academic_year_id);
        $this->db2->where('course_id', $course_id);
		$this->db2->where('is_deleted', '0');
		#if($section_status=='0'){
        $this->db2->where('class_number', $class_number);
		#}
        $this->db2->where('system_id', $system_id);
        $this->db2->where_in('attendance_val', ['1', '2', '3']);
        
        if ($start_date) {
            $this->db2->where('attendance_date >=', $start_date);
        }
        if ($section_last_att_date) {
            $this->db2->where('attendance_date <=', $section_last_att_date);
        }
        
        $query = $this->db2->get();
		if($class_number=='2095'){
			#echo  $this->db2->last_query(); die;
		}
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

	public function get_courses_by_system_id($system_id) {
        $query = $this->db->distinct()
            ->select('system_id, course_id, class_number, academic_year_id, admit_term, class_section_name, catalog_nbr, employee_id')
            ->from('tbl_attendance_master')
            ->where('status', '1')
            ->where('system_id', $system_id)
            ->where('admit_term', ACTIVE_ADMIT_TERM)
            ->get();

        return $query->result_array();
    }
	
    public function get_start_date_from_table_one($system_id) {
        $this->db2->select('attendance_date');
        $this->db2->from('tbl_attendance_assignment');
        $this->db2->where('system_id', $system_id);
        $this->db2->where('status', '1');
        $this->db2->where('academic_year_id', CURRENT_ACTIVE_ACADEMIC_YEAR);
        
        $query = $this->db2->get();
        $result = $query->row_array();
        return $result ? $result['attendance_date'] : null;
    }

    public function get_section_drop_date($system_id, $class_number, $catalog_nbr) {
        $this->db2->select('drop_date,catalog_nbr,enroll_date');
        $this->db2->from('tbl_coursewise_attendance_report');
        $this->db2->where('system_id', $system_id);
        $this->db2->where('class_number', $class_number);
        $this->db2->where('catalog_nbr', $catalog_nbr);
        $this->db2->where('status', '1');
        $this->db2->where('academic_year_id', CURRENT_ACTIVE_ACADEMIC_YEAR);
        
        $query = $this->db2->get();
        $result = $query->row_array();
        return $result;
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
        $this->db2->select('from_date, to_date');
        $this->db2->from('tbl_attendanceapprovalcases');
        $this->db2->where('system_id', $system_id);
        $this->db2->where('applied_flag', '1');
        $this->db2->where('term', '2401');
        
        $query = $this->db2->get();
        $result = $query->row_array();
        return $result ? [$result['from_date'], $result['to_date']] : null;
    }

    public function get_prog_ef_date($system_id, $academic_year_id=CURRENT_ACTIVE_ACADEMIC_YEAR, $current_term='2401') {
		
		$pattern = '/^2024/';
		if (preg_match($pattern, $system_id)) {
			$this->db2->select('added_date');
			$this->db2->from('tbl_student_courses');
			$this->db2->where('system_id', $system_id);
			$this->db2->where('current_term', $current_term);        
			$this->db2->where('academic_year_id',  $academic_year_id);        
			$query = $this->db2->get();
			#echo  $this->db->last_query(); die;
			$result = $query->row_array();
			return $result ? $result['added_date'] : null;
		} else {
			 return null;
		}
    }
	
	/*
	* Function : SqlgetSingleRecord
	* DB Connection : db2
	*/
	public function SqlgetSingleRecord($tbl_name, $col = ' * ', $condition=null)
	{
		$otherdb = $this->load->database('db2', TRUE);
        $time = time();
        $otherdb->select($col);
       // $this->db->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$otherdb->where($key, $val);
			}
			
		}
		$query = $otherdb->get($tbl_name);
		//echo $this->db->last_query(); die;
        return $query->row_array();
    }
	
	public function Sqlsaveinfo($tbl_name='', $post)
	{
		//$otherdb = $this->load->database('db2', TRUE);
		$this->db->insert($tbl_name, $post);
		//echo $this->db->last_query(); die;
		return $this->db->insert_id();
    }
	
	/*
	* Function : Sqlupdateinfo
	*
	* DB Connection : db2
	*
	*/
	public function Sqlupdateinfo($tbl_name='', $post, $field, $value)
    {
		//$otherdb = $this->load->database('db2', TRUE);
		$this->db->where($field, $value);
        if (!$this->db->update($tbl_name, $post)) {
            log_message('error', print_r($this->db->error(), true));
        }
		//echo $this->db->last_query(); die;
	}
	
	/*
	* Function : getAllDistinctRecords
	*/
	public function getAllDistinctRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL)
    {
        $time = time();
		$dbreport = $this->load->database('dbreport', TRUE);
        $dbreport->select($col);
        $dbreport->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$dbreport->where($key, $val);
			}
		}
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$dbreport->order_by($key, $val);
			}
		}
		$dbreport->distinct();
		$query = $dbreport->get($tbl_name);
		//echo $dbreport->last_query();// die;
		return $query->result_array();
    }
	
	public function calculate_overall_attendance($system_id,$catalog_nbr ) {
		$resutls = array();
		$sql = "SELECT 
				system_id, 
				catalog_nbr, 
				SUM(total_classes_conducted) AS total_classes_conducted, 
				SUM(total_classes_attended) AS total_classes_attended, 
				CEIL((SUM(total_classes_attended) * 100.0) / NULLIF(SUM(total_classes_conducted), 0)) AS overall_percentage
			FROM tbl_coursewise_attendance_report
			WHERE system_id = '".$system_id."' 
			AND catalog_nbr = '".$catalog_nbr."'
			GROUP BY system_id, catalog_nbr";
		$query = $this->db2->query($sql);
		$resutls = $query->row_array();
		return $resutls;
	}
}
?>
