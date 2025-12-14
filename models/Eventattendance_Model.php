<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Class Eventattendance_Model extends CI_Model{
		
	/*
	* Function : getSQLAllRecords
	* DB Connection : db2
	*
	*
	*/
	public function getStudentAttendance($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL, $or_condition = NULL, $groupBy = NULL, $where_in = NULL)
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
		if(!empty($or_condition))
		{ 
			foreach($or_condition as $key=>$val) {
				$dbreport->or_where($key, $val);
			}
		}
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$dbreport->order_by($key, $val);
			}
		}		
		if(!empty($groupBy)){
			$dbreport->group_by($groupBy);
		}
		if (!empty($where_in)) {
			foreach($where_in as $key=>$values){
				//print_r($values);die;
				$dbreport->where_in($key, $values);
			}
        }
		if ($limit !== null && $start !== null) {
           $query = $dbreport->get($tbl_name,$limit, $start);
        } else {
			$query = $dbreport->get($tbl_name);
		}
		//echo $dbreport->last_query(); //die;
		return $query->result_array();
    }
	
	
	
	
	/*
	* Function : getSQLAllRecords
	* DB Connection : db2
	*
	*
	*/
	public function getSQLAllRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL, $or_condition = NULL, $groupBy = NULL, $where_in = NULL, $find_in_set_val = null, $find_in_set_key = null, $join_table = NULL, $join_cond = NULL, $join_table_two = NULL, $join_cond_two = NULL,$betweenDate=NULL)
    {
		$otherdb = $this->load->database('dbreport', TRUE);
        $time = time();
        $otherdb->select($col);
		if(!empty($join_table) && !empty($join_cond))
		{ 
				$otherdb->join($join_table, $join_cond);
		}
		if(!empty($join_table_two) && !empty($join_cond_two))
		{ 
				$otherdb->join($join_table_two, $join_cond_two);
		}
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$otherdb->where($key, $val);
			}
		}
		if(!empty($or_condition))
		{ 
			foreach($or_condition as $key=>$val) {
				$otherdb->or_where($key, $val);
			}
		}
		if(!empty($betweenDate))
		{
			$from_date = $betweenDate['from_date'];
			$to_date = $betweenDate['to_date'];
			$otherdb->where("DATE_FORMAT(createdon,'%m/%d/%Y') >='$from_date'");
			$otherdb->where("DATE_FORMAT(createdon,'%m/%d/%Y') <='$to_date'");
		}
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$otherdb->order_by($key, $val);
			}
		}		
		if(!empty($groupBy)){
			$otherdb->group_by($groupBy);
		}
		if (!empty($where_in)) {
			foreach($where_in as $key=>$values){
				//print_r($values);die;
				$otherdb->where_in($key, $values);
			}
        }
		if($find_in_set_val!=null && $find_in_set_key!=null)
		{ 
			$otherdb->where("FIND_IN_SET('$find_in_set_val',$find_in_set_key) !=", 0);
			
		}
		if ($limit !== null && $start !== null) {
           $query = $otherdb->get($tbl_name,$limit, $start);
        } else {
			$query = $otherdb->get($tbl_name);
		}
		//echo $otherdb->last_query(); //die;
		return $query->result_array();
    }
	
	
	
	
	/*
	* Function : getAllSemesterRecords
	*/
	public function getAllSemesterRecords($condition='')
	{
		//$this->db->order_by('tbl_create_counselling.createdon', 'asc');
		$dbreport = $this->load->database('dbreport', TRUE);
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$dbreport->where($key, $val);
			}
		}
		
		$dbreport->where('tbl_credits.status', '1');
		$query = $dbreport->join('tbl_semester', 'tbl_semester.id = tbl_credits.semester_id');
		$dbreport->distinct();
		$query = $dbreport->select("tbl_credits.*,`tbl_semester`.`title`,`tbl_semester`.`psoft_name`")->get('tbl_credits');
		//echo $this->db->last_query();die;
		$records = $query->result_array();
		$response = array();
	    foreach($records as $row) {
			$response[$row['id']] = $row;
		}	
		return $response;
	}
	function getPeoplesoftCourseSections($condArray)
	{
		$tbl_name  = 'PS_S_PRD_TT_PI_VW';
		$resultsArray = '';
		
		if (!empty($condArray)) {
			$post = [
				'username' => 'ATTEST',
				'password' => 'TFsgt^I8',
				'num_rows' => '1000000',		 
				'table' => $tbl_name,
		        'conditions' => serialize($condArray)
			];
			$resultsArray = '';
			$fullArray = json_decode($this->getStudentAPIResponse($post));
			//$resultsArray = 'ALLOWED'; 
			$resultsArray = $fullArray;
		} else {
			$resultsArray = 'Invalid Request';
		}
		return $resultsArray;
	}
	function getStudentAPIResponse($post)
	{
		$url = 'https://slotbooking.sharda.ac.in/mentorapi/getCommonDetails'; // die;
		if (!empty($url) && !empty($post))
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
			$response = curl_exec($ch);
		}
		//print_r($response); die('TEST');
		return $response;
	} 
	/*
	* Function : Sqlsaveinfo
	*
	* DB Connection : db2
	*
	*
	*/
	public function Sqlsaveinfo($tbl_name='', $post)
	{	
		#$otherdb = $this->load->database('db2', TRUE);
		//print_r($otherdb);die;
		$this->db->insert($tbl_name, $post);
		//echo $$otherdb->last_query(); die;
		return $otherdb->insert_id();
    }/*
	* Function : Sqlupdateinfo
	*
	* DB Connection : db2
	*
	*
	*/
public function Sqlupdateinfo($tbl_name = '', $post, $field, $values)
{
    // Load the other database (db2)
    // Check if $values is an array
    if (is_array($values)) {
        // Use the `where_in` method to update multiple rows
        $this->db->where_in($field, $values);
    } else {
        // Update a single row if $values is not an array
        $this->db->where($field, $values);
    }

    // Perform the update operation
    if (!$this->db->update($tbl_name, $post)) {
        // Log the error if the update fails
        log_message('error', print_r($this->db->error(), true));
    }

    // Display the last executed query
    //echo $this->db->last_query(); die;
}

 public function calculate_attendance_percentage($attended_classes, $total_classes) {
        if ($total_classes == 0) {
            return 0; // Avoid division by zero
        }
        return ($attended_classes / $total_classes) * 100;
    }

    public function print_course_master() {
        $query = $this->db->get('tbl_coursewise_attendance_master_UAT');
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
        if ($noc_dates) {
           $this->db->where('attendance_date NOT BETWEEN "'.$noc_dates[0].'" AND "'.$noc_dates[1].'"', NULL, FALSE);

        }
        
        $query = $this->db->get();
		//echo $this->db->last_query(); ; die;
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
        $this->db->where_in('attendance_val', ['1']);
        
        if ($start_date) {
            $this->db->where('attendance_date >=', $start_date);
        }
        
        $query = $this->db->get();
        $result = $query->row_array();
        return $result ? $result['total'] : 0;
    }
    public function calculate_event_students_class($academic_year_id, $course_id, $class_number, $system_id, $start_date = null) {
        $this->db->select('COUNT(DISTINCT attendance_date, slot_name) as total');
        $this->db->from('tbl_attendance_master');
        $this->db->where('academic_year_id', $academic_year_id);
        $this->db->where('course_id', $course_id);
        $this->db->where('class_number', $class_number);
        $this->db->where('system_id', $system_id);
        $this->db->where_in('attendance_val', ['2', '3']);
        
        if ($start_date) {
            $this->db->where('attendance_date >=', $start_date);
        }
        
        $query = $this->db->get();
        $result = $query->row_array();
        return $result ? $result['total'] : 0;
    }

    public function calculate_absent_students_class($academic_year_id, $course_id, $class_number, $system_id, $start_date = null,$from_date = null,$to_date = null) {
        $this->db->select('COUNT(DISTINCT attendance_date, slot_name) as total');
        $this->db->from('tbl_attendance_master');
        $this->db->where('academic_year_id', $academic_year_id);
        $this->db->where('course_id', $course_id);
        $this->db->where('class_number', $class_number);
        $this->db->where('system_id', $system_id);
		$this->db->where('attendance_date BETWEEN "'.$from_date.'" AND "'.$to_date.'"', NULL, FALSE);
        $this->db->where_in('attendance_val', ['0']);
        
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
            //$this->db->update($table, $data);
        } else {
            // Insert record
            //$this->db->insert($table, $data);
        }
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
            //$this->db->update($table, $data);
        } else {
            // Insert record
            //$this->db->insert($table, $data);
        }
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


}