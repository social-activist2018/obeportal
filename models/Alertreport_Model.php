<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Class Alertreport_Model extends CI_Model{
	
	private $db2;
	public function __construct()
	{
		parent::__construct();
		$this->db2 = $this->load->database('db2', TRUE);
	}	
	/*
	* Function : getAllRecordsGroupBy
	*/
	public function getAllRecordsGroupBy($tbl_name, $col = '*', $condition=null, $order_by = NULL, $limit=NULL, $start=NULL, $group_by='school_id')
    {
		$time = time();
        $this->db2->select($col);
        $this->db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}
		}
		$this->db2->group_by($group_by);
		if ($limit !== null && $start !== null) {
           $query = $this->db2->get($tbl_name,$limit, $start);
        } else {
			$query = $this->db2->get($tbl_name);
		}
		
		//echo $this->db2->last_query(); die;
		$resutls = array();
		foreach($query->result_array() as $row){
			$resutls[$row[$group_by]] = $row['total'];
		}
        return $resutls;
    }
	
	/*
	* Function : getDistinctRecords
	*/
	public function getDistinctRecords($tbl_name='', $col = '*', $condition=null)
	{
		$time = time();
        $this->db2->select($col);
        $this->db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}
		}
		$this->db2->distinct();
		$query = $this->db2->get($tbl_name); 
		//echo $this->db2->last_query(); die;
		$resutls = $query->result_array();
		return $resutls;
    }
	/*
	* Function : getAllRecordsByWhereIN
	*/
	public function getAllRecordsByWhereIN($tbl_name, $col = ' * ', $condition=null,$where_in = NULL)
    {
        $time = time();
        $this->db2->select($col);
        $this->db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}
		}
		if(!empty($where_in)){
			$this->db2->where_in('id',$where_in);
		}
		if ($limit !== null && $start !== null) {
           $query = $this->db2->get($tbl_name,$limit, $start);
        } else {
			$query = $this->db2->get($tbl_name);
		}
		//echo $this->db2->last_query(); die;
		return $query->result_array();
    }
	/*
	* Function : getAllRecords
	*/
	public function getAllRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL)
    {
        $time = time();
        $this->db2->select($col);
        $this->db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}
		}
		if ($limit !== null && $start !== null) {
           $query = $this->db2->get($tbl_name,$limit, $start);
        } else {
			$query = $this->db2->get($tbl_name);
		}
		//echo $this->db2->last_query(); die;
		return $query->result_array();
    }
	
	
	/*
	* Function : getAllModuleByIDList
	*/
	public function getAllModuleByIDList($tbl_name, $col = ' * ', $condition=null, $where_in=NULL, $where_key = 'id')
    {
        $time = time();
        $this->db2->select($col);
        $this->db2->where('is_deleted', '0');
		if($where_in) {
        $this->db2->where_in($where_key, $where_in);
		}
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
		//$this->db2->order_by('display_order', 'asc');
        $query = $this->db2->get($tbl_name);
		//echo $this->db2->last_query(); die;
		$results = array();
        foreach($query->result_array() as $row) {
			$results[$row['id']] = $row;
		}
		return $results;
    }
	/*
	* Function : getAllModuleList
	*/
	public function getAllModuleList($tbl_name, $col = ' * ', $condition=null, $where_in=NULL, $where_key = 'id')
    {
        $time = time();
        $this->db2->select($col);
        $this->db2->where('is_deleted', '0');
        $this->db2->where_in($where_key, $where_in);
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
		//$this->db2->order_by('display_order', 'asc');
        $query = $this->db2->get($tbl_name);
		//echo $this->db2->last_query(); //die;
        return $query->result_array();
    }
	
	/*
	* Function : getSingleRecord
	*/
	public function getSingleRecord($tbl_name, $col = ' * ', $condition=null, $order_by = NULL, $where_like=NULL, $where_like_key = 'id')
	{
        $time = time();
        $this->db2->select($col);
        $this->db2->where('is_deleted', '0');
		if(!empty($where_like)) {
		$this->db2->like($where_like_key, $where_like);
		}
		 
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}
		}
		$query = $this->db2->get($tbl_name);
		//echo $this->db2->last_query(); 
        return $query->row_array();
    }


	
	/*
	* Function : getCommon2Query
	*/
	
	public function getCommon2Query($tbl_name = 'tbl_schools', $col = ' * ', $condition='')
    {
        
        $this->db2->select($col);
		$this->db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
        $query = $this->db2->get($tbl_name);
		echo $this->db2->last_query(); die;
        return $query->result_array();
    }
	/*
	* Function : getCommonQuery
	*/
	
	public function getCommonQuery($tbl_name = 'tbl_schools', $col = ' * ', $condition=NULL, $orderBy = array())
    {
        
        $this->db2->select($col);
		$this->db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
		if(!empty($orderBy))
		{ 
			foreach($orderBy as $key=>$val) {
				$this->db2->order_by($key, $val);
			}
			
		}
		
        $query = $this->db2->get($tbl_name);
		//echo $this->db2->last_query(); die;
        return $query->result_array();
    }
	
	
	
	public function saveinfo($tbl_name='', $post)
    {
		$this->db->insert($tbl_name, $post);
		//echo $this->db->last_query(); die;
		return $this->db->insert_id();
    }
	
	public function updateinfo($tbl_name='', $post, $field, $value)
    {
		$this->db->where($field, $value);
        if (!$this->db->update($tbl_name, $post)) {
            log_message('error', print_r($this->db->error(), true));
        }
		//echo $this->db->last_query(); die;
	}
	
	
	public function updateProfile($post)
    {
        $array = array(
            'name' => $post['name'],
            'phone' => $post['phone'],
            'email' => $post['email']
        );
        if (trim($post['pass']) != '') {
            $array['password'] = md5($post['pass']);
        }
        $this->db->where('id', $post['id']);
        $this->db->update('users_public', $array);
    }
	
	/*
	* Function: getGrievanceList
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getCommonIdArray($tbl_name='tbl_schools', $col = ' * ', $condition=null, $order_by=null)
    {
        $time = time();
        $this->db2->select($col);
        $this->db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val); 
			}
			
		}
		if(!empty($order_by))
		{
			foreach($order_by as $key=>$val) {
			$this->db2->order_by($key, $val);
			}
		}
        $query = $this->db2->get($tbl_name);
		//echo $this->db2->last_query(); die;
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['id']] = $row;
		}
        return $results;
	}
	
	/*
	* Function: getCommonSingleRecord
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getCommonSingleRecord($tbl_name='tbl_schools', $col = ' * ', $condition=null)
	{
        $this->db2->select($col);
        $this->db2->where('status', '1');
        $this->db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
		$query = $this->db2->get($tbl_name);
		$results = array();
		$results = $query->row_array();
	    return $results;
	}
	

	public function getAllRecordscount($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL)
    {
        $time = time();
        $this->db2->select($col);
        $this->db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}
		}
		if ($limit !== null && $start !== null) {
           $query = $this->db2->get($tbl_name,$limit, $start);
        } else {
			$query = $this->db2->get($tbl_name);
		}
		//echo $this->db2->last_query(); die;
		return $query->num_rows();
    }
	
	
	// Function for deletion
	public function deleterecords($tbl_name, $id){
		if($tbl_name!='' && $id>0){
			$sql_query=$this->db2->where('id', $id)->delete($tbl_name);
		}
	}
	
	/*
	* Function : remove_mentee
	* Description: Upload xls data to remove mentor from mentee profile
	*/
	public function remove_mentee($menteeArray,$uid='')
	{
		$k=0;
		foreach($menteeArray as $key => $singlerow)
		{
			if($key != 0){
				$system_id= str_replace('"','&#34;',$singlerow['0']);
				$updateArray = array('mentor_id'=>'0','modifiedon'=>date('Y-m-d h:i:s'),'xls_remove_by'=>$uid->id);
				if(!empty($system_id))
				{
					$respe = $this->updateinfo($tbl_name='tbl_mentee', $updateArray, $field='system_id', $system_id);
					$k++; 
				}
			}
		}
		return $k;
	}
	
	/*
	* Function : assign_mentee
	* Description: Upload xls data to Assign mentor
	*/
	public function assign_mentee($menteeArray,$uid=''){
		
		$k=0;
		foreach($menteeArray as $key => $singlerow)
		{
			if($key != 0){
				$system_id= str_replace('"','&#34;',$singlerow['0']);
				$system_id= str_replace("`",'&#39;',$system_id);
				$system_id= str_replace("‘",'&#39;',$system_id);
				$system_id= str_replace("’",'&#39;',$system_id);
				$system_id= str_replace("â€œ",'&#34;',$system_id);
				$system_id= str_replace("â€˜",'&#39;',$system_id);
				$system_id= str_replace("â€™",'&#39;',$system_id);
				
				$employee_id= str_replace('"','&#34;',$singlerow['6']);
				$employee_id= str_replace("`",'&#39;',$employee_id);
				$employee_id= str_replace("‘",'&#39;',$employee_id);
				$employee_id= str_replace("’",'&#39;',$employee_id);
				$employee_id= str_replace("â€œ",'&#34;',$employee_id);
				$employee_id= str_replace("â€˜",'&#39;',$employee_id);
				$employee_id= str_replace("â€™",'&#39;',$employee_id);
			
				// Get Mentor Id from mentor table_name
				$mentor_id = $this->getMentorID($employee_id);
				$menteeDetails = $this->getSingleRecord($tbl_name='tbl_mentee', $col = ' * ', $condition=array('system_id'=>$system_id));
				//$system_id = $this->getMenteeID($system_id);
				$nsystem_id = $menteeDetails['system_id'];
				//print_r($menteeDetails); die;
				if($mentor_id>0 && $nsystem_id!=''){
					$updateArray = array('mentor_id'=>$mentor_id,'modifiedon'=>date('Y-m-d h:i:s'),'xls_update_by'=>$uid->id);
					if(!empty($nsystem_id))
					{
						$respe = $this->updateinfo($tbl_name='tbl_mentee', $updateArray, $field='system_id', $nsystem_id);
						
						// Send Mentor Assignment Email
						$school_id= $menteeDetails['school_id'];
						$department_id= $menteeDetails['department_id'];
						$mentee_id= $menteeDetails['id'];
						$reassignDetails = '';
						$reassignDetails = $this->getSingleRecord($tbl_name='tbl_reassign_mentor', $col = ' * ', $condition=array('school_id'=>$school_id,'department_id'=>$department_id,'mentee_id'=>$mentee_id,'mentor_id'=>$mentor_id));
						
						if(empty($reassignDetails))
						{
							$insertArray = array('mentor_id'=>$mentor_id, 'school_id'=>$school_id, 'department_id'=>$department_id, 'mentee_id'=>$mentee_id,'remarks'=>'Mentor Assign Successfully', 'createdon'=>date('Y-m-d h:i:s'));
							$respe = $this->db2->insert('tbl_reassign_mentor',$insertArray);
							
							$mentorDetails = $this->getSingleRecord($tbl_name='tbl_admin', $col = ' * ', $condition=array('id'=>$mentor_id));
							
							// Send Email
							$to_emails = 'aadil.hasan@shardatech.org'; // $menteeDetails['email_id'];
							$mentee_name = $menteeDetails['full_name'];
							$message = $this->load->view('admin/email_template/mentro_assignment_email', $params, true);
							$point_covered = 'We have successfully assigned you a Mentor.';
							$mentor_name = $mentorDetails['first_name'].' '.$mentorDetails['last_name'];
							$mentor_contact = $mentorDetails['contact_number'];
							$mentor_email = $mentorDetails['email_id'];
							$message = str_replace('##STUDENTS##',$mentee_name,$message);
							$message = str_replace('##MESSAGE##',$point_covered,$message);
							$message = str_replace('##MENTOR_NAME##',$mentor_name,$message);
							$message = str_replace('##MENTOR_EMAIL##',$mentor_email,$message);
							$message = str_replace('##MENTOR_CONTACT##',$mentor_contact,$message);
							$subject = 'Mentor Assignment Alert!!';
							$emailResp = send_email_pepipost($to_emails, $subject, $message);
						}
						$k++; 
					}
				}
			}
		}
		return $k;
	}
	
	/*
	* Function : assignMentortoMentee
	* Description: Upload xls data to Assign mentor
	*/
	public function assignMentortoMentee($mentorList,$uid=''){
		$k=1;
		foreach($mentorList as $key => $singlerow)
		{
			//echo "<pre>";print_r($singlerow); 
			// Student Details
			if($key != 0){
			$emp_id= str_replace('"','&#34;',$singlerow['0']);
			$emp_id= str_replace("`",'&#39;',$emp_id);
			$emp_id= str_replace("‘",'&#39;',$emp_id);
			$emp_id= str_replace("’",'&#39;',$emp_id);
			$emp_id= str_replace("â€œ",'&#34;',$emp_id);
			$emp_id= str_replace("â€˜",'&#39;',$emp_id);
			$emp_id= str_replace("â€™",'&#39;',$emp_id);
			$full_name= str_replace('"','&#34;',$singlerow['1']);
			$contact_number= str_replace('"','&#34;',$singlerow['2']);
			$email_id= str_replace('"','&#34;',$singlerow['3']);
			// School Details
			$school_name= str_replace('"','&#34;',$singlerow['4']);
			$school_id = $this->getschoolID($school_name);
			$dept_name= str_replace('"','&#34;',$singlerow['5']);
			$department_id = $this->getdepartmentID($dept_name);
			
			// Create insertArray
			$insertArray = array(
			'employee_id' => $emp_id,
			'userName' => $email_id,
			'password' => substr($contact_number,6,4),
			'first_name' => $full_name,
			'contact_number' =>$contact_number,
			'email_id' => $email_id,
			'school_id' => $school_id,
			'department_id' => $department_id,
			'role_id' => '4',
			'access_id' => '3',
			'added_by' => $uid->id,
			'status' => '1',
			'createdon' => date('Y-m-d H:i:s'),
			'is_deleted' => '0'
			);
			//echo "<pre>";
			//print_r($insertArray); die('DONE');
			if(!empty($insertArray)){
				$respe = $this->db2->insert('tbl_admin',$insertArray);
				$k++;
			}
			}
		}
		return $k;
	}
	
	/*
	* Function : getMenteeID
	*/
	public function getMenteeID($system_id)
	{
		$this->db2->select('id,system_id');
		$this->db2->where('is_deleted', '0');
		$this->db2->where('status', '1');
		$this->db2->where('system_id', $system_id);
		$query = $this->db2->get('tbl_mentee');
		$results = array();
		$row = $query->row_array();
		$results = $row['system_id'];
		return $results;
	}
	/*
	* Function : getMentorID
	*/
	public function getMentorID($employee_id)
	{
		$employee_id = $this->getAllDigitsNumber($employee_id);
		$this->db2->select('id');
		$this->db2->where('is_deleted', '0');
		$this->db2->where('status', '1');
		$this->db2->like('employee_id', $employee_id, 'before');
		$query = $this->db2->get('tbl_admin');
		//echo $this->db2->last_query(); die;
		$results = array();
		$row = $query->row_array();
		$results = $row['id'];
		return $results;
	}
	
	/*
	* Function : getAllDigitsNumber
	*/
	public function getAllDigitsNumber($number)
	{
		$no_of_digit = 7;
		$length = strlen((string)$number);
		for($i = $length;$i<$no_of_digit;$i++)
		{
			$number = '0'.$number;
		}
		return $number;
   }
	
	/*
	* Function : getschoolID
	*/
	public function getschoolID($name_value)
	{
		$this->db2->select('id,school_name, school_code');
		$this->db2->where('is_deleted', '0');
		$this->db2->where('status', '1');
		$this->db2->where('school_name', $name_value);
		$query = $this->db2->get('tbl_schools');
		$results = array();
		$row = $query->row_array();
		$results = $row['id'];
		return $results;
	}
	
	/*
	* Function : getdepartmentID
	*/
	public function getdepartmentID($name_value)
	{
		$this->db2->select('id,department_name');
		$this->db2->where('is_deleted', '0');
		$this->db2->where('status', '1');
		$this->db2->where('department_name', $name_value);
		$query = $this->db2->get('tbl_departments');
		$results = array();
		$row = $query->row_array();
		$results = $row['id'];
		return $results;
	}
	
		/*
	* Function: getCommonstatsArray 
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getCommonstatsArray($tbl_name='tbl_stats_master', $col = ' * ', $condition=null)
    {
        $time = time();
        $this->db2->select($col);
        $this->db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
		//$this->db2->order_by('id', 'asc');
        $query = $this->db2->get($tbl_name);
		$menteesArray = $query->result_array();
		$results = array();
		foreach($menteesArray as $val) {
			$results[$val['admin_id']] = $val;
		}	
        return $results;
	}
	
	/*
	* Function : getAPIResponse
	* Description :  send request and get response in JSON format
	* Date: 19 Oct 2020
	* Created By: Amit Verma
	*/

	function getAPIResponse($post)
	{
		$url = 'https://slotbooking.sharda.ac.in/mentorapi/getCommonDetails'; 
		if (!empty($url) && !empty($post)) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
			$response = curl_exec($ch);
		}
		//print_r($response); die;
	   return $response;
	}

	public function getSlotRoomRecord($tbl_name, $col = ' * ', $slot_id=NULL, $condition=null)
    {
        $time = time();
        $this->db2->select($col);
        $this->db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		$where = " FIND_IN_SET('".$slot_id."', slot_ids) ";
		$this->db2->where($where);
		$query = $this->db2->get($tbl_name);
		//echo $this->db2->last_query(); die;
		return $query->row_array();
    }

	// public function getallTimetableRecords($class_id='',$ac_year)
	// {
		
	// 	$this->db2->select('tbl_faculty_course_assignment.faculty_id,tbl_faculty_course_assignment.course_id,tbl_faculty_course_assignment.class_id,tbl_faculty_course_assignment.semester,tbl_faculty_course_assignment.division_id,tbl_faculty_course_assignment.allotment_type,tbl_faculty_course_assignment.practical_batch,tbl_faculty_course_assignment.load_detail,faculty_id,tbl_faculty_course_assignment.ac_year');
	// 	//if($id>0){
	// 		$this->db2->where('tbl_faculty_course_assignment.class_id', $class_id);
	// 		$this->db2->where('tbl_faculty_course_assignment.ac_year', $ac_year);
	// 		//$this->db2->limit(1);
	// 	//}
	// 	$query = $this->db2->get();
	// 	//echo $this->db2->last_query();die;
	// 	return $query->result_array();
		
	// }

	public function getAllfacultyslotrecords($tbl_name='tbl_faculty_slot_assignment', $col = ' * ', $condition=null, $where_in=NULL, $where_key = 'id')
    {
        $time = time();
        $this->db2->where('tbl_faculty_slot_assignment.is_deleted', '0');
		if(!empty($where_in)) {
        $this->db2->where($where_key.' IN('.$where_in.')');
		}
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
		$query = $this->db2->join('tbl_faculty_course_assignment', 'tbl_faculty_course_assignment.id = tbl_faculty_slot_assignment.faculty_course_id');
		$query = $this->db2->select($col)->get($tbl_name);
		//echo $this->db2->last_query(); die;
        return $query->result_array();
    }

	public function getRecordswhereIn($tbl_name='tbl_faculty_slot_assignment', $col = ' * ', $condition=null, $where_in=NULL, $where_key = 'id')
    {
        $time = time();
        $this->db2->where('tbl_faculty_slot_assignment.is_deleted', '0');
        $this->db2->where($where_key.' IN('.$where_in.')');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
		$query = $this->db2->select($col)->get($tbl_name);
		//echo $this->db2->last_query(); die;
        return $query->result_array();
    }
	
	function getPeoplesoftCourseSections($condArray)
	{
		$tbl_name  = 'PS_S_PRD_TT_PI_VW';
		$resultsArray = '';
		
		if (!empty($condArray)) {
			$post = [
				'username' => 'ATTEST',
				'password' => 'TFsgt^I8',
				'num_rows' => '100',		 
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
	
	
	function getActivePeoplesoftCourseSections($condArray)
	{
		$tbl_name  = 'PS_S_PRD_CLS_PI_VW';
		$resultsArray = '';
		
		if (!empty($condArray)) {
			$post = [
				'username' => 'ATTEST',
				'password' => 'TFsgt^I8',
				'num_rows' => '400',		 
				'table' => $tbl_name,
		         'conditions' => serialize($condArray)
			];
			
			$resultsArray = '';
			$fullArray = json_decode($this->getStudentAPIResponse($post));
			//$resultsArray = 'ALLOWED'; 
			$resultsArray = $fullArray;
			//print_r($resultsArray); die;
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
	* Function : getAllSemesterRecords
	*/
	public function getAllSemesterRecords($condition='')
	{
		//$this->db2->order_by('tbl_create_counselling.createdon', 'asc');
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		
		$this->db2->where('tbl_credits.status', '1');
		$query = $this->db2->join('tbl_semester', 'tbl_semester.id = tbl_credits.semester_id');
		$this->db2->distinct();
		$query = $this->db2->select("tbl_credits.*,`tbl_semester`.`title`,`tbl_semester`.`psoft_name`")->get('tbl_credits');
		//echo $this->db2->last_query();die;
		$records = $query->result_array();
		$response = array();
	    foreach($records as $row) {
			$response[$row['id']] = $row;
		}	
		return $response;
	}
	
	/*
	* Function : getSemesterIDByRow
	*/
	public function getSemesterIDByRow($condition='')
	{
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		$this->db2->where('tbl_credits.status', '1');
		$query = $this->db2->join('tbl_semester', 'tbl_semester.id = tbl_credits.semester_id');
		$query = $this->db2->select("tbl_credits.*,`tbl_semester`.`title`,`tbl_semester`.`psoft_name`")->get('tbl_credits');
		//echo $this->db2->last_query();die;
		$records = $query->row_array();
		
		return $records;
	}
	
	/*
	* Function : getEnrolledDistinctStudentsArray
	* DB Connection : db2
	*/
	public function getEnrolledDistinctStudentsArray_old($tbl_name='stu_enrollment', $condition='')
	{
		$otherdb = $this->load->database('db3', TRUE);
	
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$otherdb->where($key, $val);
			}
		}
		
       	$otherdb->where('status', '1');
		$otherdb->where('is_deleted', '0');
		$otherdb->distinct();
		$queryResult = $otherdb->select('system_id, program_id, semester, createdon')->get($tbl_name);
		//print_r($otherdb); die;
		//echo $this->db2->last_query(); die;
		return $queryResult->result_array();
    }
	
	/*
	* Function : getEnrolledFullStudentsArray
	* DB Connection : db2
	*/
	public function getEnrolledFullStudentsArray($tbl_name='stu_enrollment', $condition='')
	{
		$otherdb = $this->load->database('db3', TRUE);
	
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$otherdb->where($key, $val);
			}
		}
		
       	$otherdb->where('status', '1');
		$otherdb->where('is_deleted', '0');
		//$otherdb->distinct();
		$queryResult = $otherdb->select('*')->get($tbl_name);
		//print_r($otherdb); die;
		//echo $this->db2->last_query(); die;
		return $queryResult->result_array();
    }
	
	/*
	* Function: getCourseBycatalogArray
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getCourseBycatalogArray($tbl_name='tbl_course', $col = ' * ', $condition=null, $order_by=null)
    {
        $time = time();
        $this->db2->select($col);
        $this->db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val); 
			}
			
		}
		if(!empty($order_by))
		{
			foreach($order_by as $key=>$val) {
			$this->db2->order_by($key, $val);
			}
		}
        $query = $this->db2->get($tbl_name);
		//echo $this->db2->last_query(); die;
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['catalog_nbr']] = $row;
		}
        return $results;
	}
	
	public function getSemesterArray($condition, $hodProgramList='')
	{
		$this->db2->select('tbl_credits.id,tbl_semester.title,tbl_semester.description,tbl_semester.psoft_name');
		$this->db2->join('tbl_semester', 'tbl_semester.id = tbl_credits.semester_id', 'left');
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		if(!empty($hodProgramList)){
			    //$this->db2->where('a.id=(select max(id) from tbl_comments b where a.indent_id=b.indent_id)');			
			    $this->db2->where_in('tbl_credits.program_id',$hodProgramList);
			}
			
		//$this->db2->limit(1);
		$query = $this->db2->get('tbl_credits');
		//echo $this->db2->last_query();die;
		return $query->result_array();
		
	}
	
	/*
	* Function : getEnrollmentCount
	* DB Connection : db2
	*/
	public function getEnrolledStudentsArray($tbl_name='stu_enrollment', $condition='')
	{
		$otherdb = $this->load->database('db3', TRUE);
	
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$otherdb->where($key, $val);
			}
		}
		
       	$otherdb->where('status', '1');
		$otherdb->where('is_deleted', '0');
		$queryResult = $otherdb->select('*')->get($tbl_name);
		//print_r($otherdb); die;
		//echo $this->db2->last_query(); die;
		return $queryResult->result_array();
    }
	
	/*
	* Function : getEnrolledDistinctStudentsArray
	* DB Connection : db2
	*/
	public function getEnrolledDistinctStudentsArray($tbl_name='stu_enrollment', $condition='')
	{
		$otherdb = $this->load->database('db3', TRUE);
	
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$otherdb->where($key, $val);
			}
		}
		
       	$otherdb->where('status', '1');
		$otherdb->where('is_deleted', '0');
		$otherdb->distinct();
		$queryResult = $otherdb->select('system_id, program_id, semester, createdon')->get($tbl_name);
		//print_r($otherdb); die;
		//echo $this->db2->last_query(); die;
		return $queryResult->result_array();
    }
	
	public function getSemesterPPsoftArray($condition, $hodProgramList='')
	{
		$this->db2->select('tbl_credits.id,tbl_semester.title,tbl_semester.description,tbl_semester.psoft_name');
		$this->db2->join('tbl_semester', 'tbl_semester.id = tbl_credits.semester_id', 'left');
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		if(!empty($hodProgramList)){
			    //$this->db2->where('a.id=(select max(id) from tbl_comments b where a.indent_id=b.indent_id)');			
			    $this->db2->where_in('tbl_credits.program_id',$hodProgramList);
			}
			
		//$this->db2->limit(1);
		$query = $this->db2->get('tbl_credits');
		//echo $this->db2->last_query();die;
		return $query->result_array();
		
	}
	public function getAllAssignedCourseList($condition)
	{
		
		$this->db2->select('tbl_teaching_scheme.*,tbl_course.course_title, tbl_course.catalog_nbr, tbl_course.units_maximum');
		$this->db2->join('tbl_course', 'tbl_course.id = tbl_teaching_scheme.course_id');
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		$query = $this->db2->get('tbl_teaching_scheme');
		//echo $this->db2->last_query();die;
		return $query->result_array();
		
	}
	
	public function getAllPIInfoArray($condition)
	{
		
		$this->db2->select('tbl_timetable_management.version_name,tbl_timetable_management.from_date,tbl_timetable_management.to_date,,tbl_timetable_management.status as tt_status,tbl_assign_room_slot_section.*,tbl_course.course_title, tbl_course.catalog_nbr, tbl_course.units_maximum,tbl_slot_master.slot_name,tbl_slot_master.lecture_type,tbl_department_course_slot_assignment.teaching_load,tbl_department_course_slot_assignment.tt_version_id');
		$this->db2->join('tbl_course', 'tbl_course.id = tbl_assign_room_slot_section.course_id');
		$this->db2->join('tbl_slot_master', 'tbl_slot_master.id = tbl_assign_room_slot_section.slot_id');
		$this->db2->join('tbl_department_course_slot_assignment', 'tbl_department_course_slot_assignment.id = tbl_assign_room_slot_section.dept_course_id ');
		$this->db2->join('tbl_timetable_management', 'tbl_timetable_management.id = tbl_department_course_slot_assignment.tt_version_id ');
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		$query = $this->db2->get('tbl_assign_room_slot_section');
		//echo $this->db2->last_query();die;
		$records = array();
		foreach($query->result_array() as $row){
			$records[$row['catalog_nbr']][] = $row;
		}
		return $records;
		
	}
	public function getEmployeeArray($condition)
	{
		
		$this->db2->select('id,employee_id,full_name,email_id');
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		$query = $this->db2->get('tbl_employee_master');
		//echo $this->db2->last_query();die;
		$records = array();
		foreach($query->result_array() as $row){
			$records[$row['employee_id']]= $row;
		}
		return $records;
		
	}

	
	/**
	* Get All topic lists
	*/
	public function getCustomAllMyRecordsReports($conditions='') 
	{
		$resutls = array();
		$where = '';
		if($conditions) {
			foreach($conditions as $key=>$val){
				$where .= ' AND '.$key.'= "'.$val.'"';
			}
		}
		
		$sql = 'SELECT 
			srm.school_name as School,
			srm.department AS Department,
			srm.prog_name as Programme,
			IF(cr.program_type = 1, "General", "Special") AS programme_type,
			srm.semester,
			srm.system_id,
			srm.name AS studentName,
			IF(sfm.system_id IS NOT NULL, "Y", "N") AS Feedback_status,
			sfm.created_at
		FROM tbl_student_details srm
		LEFT JOIN tbl_course_feedback sfm ON sfm.system_id = srm.system_id
		LEFT JOIN tbl_credits cr ON cr.id = sfm.semester_id AND cr.academic_id = "4"
		WHERE srm.current_term = "2401" '.$where.'
		GROUP BY srm.system_id'; 
		#echo $sql; die;	
		$query = $this->db2->query($sql);
		$resutls = $query->result_array();
		return $resutls;
	}
	/**
	* Get All SMS  report
	*/
	public function getSMSreport() 
	{
		$resutls = array();
		$sql = ' SELECT 
                sd.school_name, 
                sd.department,               
                dm.system_id,
                sd.name,
                dm.contact_no,
                sd.semester,
                dm.message,
                dm.createdon
            FROM 
                tbl_parentsalert_sms dm 
            JOIN 
                tbl_student_details sd ON dm.system_id = sd.system_id 

            WHERE 
                dm.status = "2"

            ORDER BY dm.system_id;  '; 
		#echo $sql; die;	
		$query = $this->db2->query($sql);
		$resutls = $query->result_array();
		return $resutls;
	}
	/**
	* Get All Email  report
	*/
	public function getEMailReport() 
	{
		$resutls = array();
		$sql = 'SELECT 
                sd.school_name, 
                sd.department,               
                dm.system_id,
                sd.name,
                dm.email,
                sd.semester,
                dm.message,
                dm.createdon
            FROM 
                tbl_parentsalert_email dm 
            JOIN 
                tbl_student_details sd ON dm.system_id = sd.system_id 
            WHERE 
                dm.status = "2"
            ORDER BY dm.system_id;  '; 
		#echo $sql; die;	
		$query = $this->db2->query($sql);
		$resutls = $query->result_array();
		return $resutls;
	}
	
}