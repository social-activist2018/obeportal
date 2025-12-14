<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Class Surveyquestion_Model extends CI_Model{
		
	/*
	* Function : getAllRecordsGroupBy
	*/
	public function getAllRecordsGroupBy($tbl_name, $col = '*', $condition=null, $order_by = NULL, $limit=NULL, $start=NULL)
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
		$dbreport->group_by('school_id');
		if ($limit !== null && $start !== null) {
           $query = $dbreport->get($tbl_name,$limit, $start);
        } else {
			$query = $dbreport->get($tbl_name);
		}
		
		//echo $dbreport->last_query(); die;
		$resutls = array();
		foreach($query->result_array() as $row){
			$resutls[$row['school_id']] = $row['total'];
		}
        return $resutls;
    }
	
	/*
	* Function : getRecordsByGroup
	*/
	public function getRecordsByGroup($tbl_name='', $col = '*', $condition=null, $group_by_column='school_id')
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
		$dbreport->group_by($group_by_column);
		$query = $dbreport->get($tbl_name); 
		//echo $dbreport->last_query(); die;
		$resutls = array();
		foreach($query->result_array() as $row){
			$resutls[$row[$group_by_column]] = $row['total'];
		}
        return $resutls;
    }
	/*
	* Function : getAllRecords
	*/
	public function getAllFeedbackNRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL,$betweenDate = NULL)
    {
		$dbreport = $this->load->database('dbreport', TRUE);
        $time = time();
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
		
		if(!empty($betweenDate))
		{
			$from_date = $betweenDate['from_date'];
			$to_date = $betweenDate['to_date'];
			$dbreport->where("DATE_FORMAT(tbl_feedback_ratingrecord.createdon,'%Y-%m-%d') >='$from_date'");
			$dbreport->where("DATE_FORMAT(tbl_feedback_ratingrecord.createdon,'%Y-%m-%d') <='$to_date'");
		}
		
		if ($limit !== null && $start !== null) {
           $query = $dbreport->get($tbl_name,$limit, $start);
        } else {
			$query = $dbreport->get($tbl_name);
		}
		//echo $dbreport->last_query(); die;
		return $query->result_array();
    }
	
	/* Function : getAllRecords
	*/
	public function getAllRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL)
    {
		$dbreport = $this->load->database('dbreport', TRUE);
        $time = time();
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
		if ($limit !== null && $start !== null) {
           $query = $dbreport->get($tbl_name,$limit, $start);
        } else {
			$query = $dbreport->get($tbl_name);
		}
		//echo $dbreport->last_query(); die;
		return $query->result_array();
    }
	
	/*
	* Function : getAllGrievanceHistoryRecords
	*/
	public function getAllGrievanceHistoryRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL)
    {
		$dbreport = $this->load->database('dbreport', TRUE);
        $time = time();
        $dbreport->select($col);
        $dbreport->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$dbreport->where($key, $val);
			}
		}
		$dateCond = 'now()-interval 3 month';
		$dbreport->where('lastUpdationDate >=', $dateCond, FALSE);
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$dbreport->order_by($key, $val);
			}
		}
		if ($limit !== null && $start !== null) {
           $query = $dbreport->get($tbl_name,$limit, $start);
        } else {
			$query = $dbreport->get($tbl_name);
		}
		//echo $dbreport->last_query(); die;
		return $query->result_array();
    }
	
	/*
	* Function : getAllRecords
	*/
	public function getAllMonthlyRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL)
    {
		$dbreport = $this->load->database('dbreport', TRUE);
        $time = time();
        $dbreport->select($col);
        $dbreport->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$dbreport->where($key, $val);
			}
		}
		
		$dbreport->where('MONTH(regDate)', date('m'));
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$dbreport->order_by($key, $val);
			}
		}
		$query = $dbreport->get($tbl_name);
        return $query->result_array();
    }
	
	/*
	* Function : getAllModuleList
	*/
	public function getAllModuleList($tbl_name, $col = ' * ', $condition=null, $where_in=NULL, $where_key = 'id')
    {
		$dbreport = $this->load->database('dbreport', TRUE);
        $time = time();
        $dbreport->select($col);
        $dbreport->where('is_deleted', '0');
        $dbreport->where_in($where_key, $where_in);
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$dbreport->where($key, $val);
			}
			
		}
		//$dbreport->order_by('display_order', 'asc');
        $query = $dbreport->get($tbl_name);
		//echo $dbreport->last_query(); die;
        return $query->result_array();
    }
	
	/*
	* Function : getSingleRecord
	*/
	public function getSingleRecord($tbl_name, $col = ' * ', $condition=null)
	{
		$dbreport = $this->load->database('dbreport', TRUE);
        $time = time();
        $dbreport->select($col);
       // $dbreport->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$dbreport->where($key, $val);
			}
			
		}
		$query = $dbreport->get($tbl_name);
		//echo $this->db->last_query(); die;
        return $query->row_array();
    }
	
	
	/*
	* Function : getInteraction
	*/
	public function getInteraction($cond = '', $betweenDate = '')
	{
		if(!empty($cond)) {
			foreach($cond as $key=>$val) {
				$this->db->where($key, $val);
			}
		}
		if(!empty($betweenDate))
		{
			$from_date = $betweenDate['from_date'];
			$to_date = $betweenDate['to_date'];
			$this->db->where("DATE_FORMAT(tbl_create_counselling.createdon,'%m/%d/%Y') >='$from_date'");
			$this->db->where("DATE_FORMAT(tbl_create_counselling.createdon,'%m/%d/%Y') <='$to_date'");
		}
		
		$this->db->group_by('mentor_id');
		$query = $this->db->select("mentor_id, count(*)as total")->get('tbl_create_counselling');
		//echo $this->db->last_query(); die;
		$resp = $query->result_array();
		$results = array();
		foreach( $resp as $val){
			$results[$val['mentor_id']] = $val['total'];
		}
		return $results;
		
    }
	/*
	* Function : getMenteeStats
	*/
	public function getMenteeStats($cond = '')
	{
		if(!empty($cond)) {
			foreach($cond as $key=>$val) {
				$this->db->where($key, $val);
			}
		}
		$this->db->group_by('mentor_id');
		$query = $this->db->select("mentor_id, count(*)as total")->get('tbl_mentee');
		//echo $this->db->last_query(); die;
		$results = array();
		foreach($query->result_array() as $val) {
			$results[$val['mentor_id']] = $val['total'];
		}
		return $results;
		
    }	
	
	/*
	* Function : getMissInteraction
	*/
	public function getMissInteraction($umid='')
	{
		$this->db->where('status', '1');
		$this->db->where('is_deleted', '0');
		$today = date('Y-m-d h:i:s');
		$this->db->where("DATE_FORMAT(next_appointment, '%Y-%m-%d') <= NOW()");
		$this->db->where_not_in('counselling_status','3');
		$this->db->where('mentor_id', $umid);
		$this->db->order_by('createdon', 'asc');
		$query = $this->db->select("*")->get('tbl_create_counselling');
		//echo $this->db->last_query();die;
		return $query->result_array();
    }
	/*
	* Function : getUpInteraction
	*/
	public function getUpInteraction($umid='')
	{
        $this->db->where('status', '1');
        $this->db->where('is_deleted', '0');
        $this->db->where("DATE_FORMAT(next_appointment, '%Y-%m-%d') >= NOW()");
		$this->db->where('mentor_id', $umid);
		$this->db->order_by('next_appointment', 'asc');
		$query = $this->db->select("*")->get('tbl_create_counselling');
		//echo $this->db->last_query();die;
		return $query->result_array();
    }	
	
	/*
	* Function : getAllNonAcademicComplaintsRecords
	*/
	public function getAllInteraction($condition=null, $createdon='', $betweenDate = '')
	{
        $this->db->where('tbl_create_counselling.is_deleted', '0');
        if(!empty($createdon)){
		  $this->db->where("DATE_FORMAT(createdon, '%Y-%m-%d') <= '$createdon'");
		}
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				if($key=='from_date' || $key=='to_date') {
					$this->db->where("DATE_FORMAT(tbl_create_counselling.createdon,'%m/%d/%Y') >='$val'");
				}  else {
					$this->db->where($key, $val);
				}
			}
			
		}
		if(!empty($betweenDate))
		{
			$from_date = $betweenDate['from_date'];
			$to_date = $betweenDate['to_date'];
			$this->db->where("DATE_FORMAT(tbl_create_counselling.createdon,'%m/%d/%Y') >='$from_date'");
			$this->db->where("DATE_FORMAT(tbl_create_counselling.createdon,'%m/%d/%Y') <='$to_date'");
		}
		$this->db->order_by('tbl_create_counselling.createdon', 'asc');
		$query = $this->db->join('tbl_mentee', 'tbl_mentee.id = tbl_create_counselling.mente_id');
		$query = $this->db->join('tbl_admin', 'tbl_admin.id = tbl_create_counselling.mentor_id');
		$query = $this->db->select("tbl_create_counselling.id,tbl_create_counselling.issue_type, tbl_create_counselling.point_covered, tbl_create_counselling.critically_level, tbl_create_counselling.next_appointment, tbl_create_counselling.school_id,tbl_create_counselling.department_id,tbl_create_counselling.createdon,tbl_create_counselling.mentor_id, tbl_create_counselling.counselling_status , tbl_mentee.full_name, tbl_admin.first_name,tbl_admin.last_name")->get('tbl_create_counselling');
		//echo $this->db->last_query();die;
		return $query->result_array();
    }
	/*
	* Function : getAllVideo
	*/
	public function getAllVideo($condition=null, $createdon='', $betweenDate = '')
	{
        $this->db->where('tbl_library_master.is_deleted', '0');
        if(!empty($createdon)){
		  $this->db->where("DATE_FORMAT(createdon, '%Y-%m-%d') <= '$createdon'");
		}
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				if($key=='from_date' || $key=='to_date') {
					$this->db->where("DATE_FORMAT(tbl_library_master.createdon,'%m/%d/%Y') >='$val'");
				}  else {
					$this->db->where($key, $val);
				}
			}
			
		}
		if(!empty($betweenDate))
		{
			$from_date = $betweenDate['from_date'];
			$to_date = $betweenDate['to_date'];
			$this->db->where("DATE_FORMAT(tbl_library_master.createdon,'%m/%d/%Y') >='$from_date'");
			$this->db->where("DATE_FORMAT(tbl_library_master.createdon,'%m/%d/%Y') <='$to_date'");
		}
		$this->db->order_by('tbl_library_master.createdon', 'asc');
		$query = $this->db->join('tbl_campus', 'tbl_campus.id = tbl_library_master.campus_id');
		$query = $this->db->join('tbl_admin', 'tbl_admin.id = tbl_library_master.author_id');
		$query = $this->db->select("tbl_library_master.*, , tbl_campus.campus_name, tbl_admin.first_name,tbl_admin.last_name")->get('tbl_library_master');
		//echo $this->db->last_query();die;
		return $query->result_array();
    }
	
	/*
	* Function : getAllMentorInteraction
	*/
	public function getAllMentorInteraction($condition=null, $createdon='', $betweenDate = '')
	{
        $this->db->where('tbl_create_counselling.is_deleted', '0');
        if(!empty($createdon)){
		  $this->db->where("DATE_FORMAT(createdon, '%Y-%m-%d') <= '$createdon'");
		}
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db->where($key, $val);
			}
			
		}
		if(!empty($betweenDate))
		{
			$from_date = $betweenDate['from_date'];
			$to_date = $betweenDate['to_date'];
			$this->db->where("DATE_FORMAT(tbl_create_counselling.createdon,'%m/%d/%Y') >='$from_date'");
			$this->db->where("DATE_FORMAT(tbl_create_counselling.createdon,'%m/%d/%Y') <='$to_date'");
		}
		$this->db->order_by('tbl_create_counselling.createdon', 'asc');
		$query = $this->db->join('tbl_mentee', 'tbl_mentee.id = tbl_create_counselling.mente_id');
		$query = $this->db->join('tbl_admin', 'tbl_admin.id = tbl_create_counselling.mentor_id');
		$query = $this->db->select("tbl_create_counselling.id,tbl_create_counselling.issue_type, tbl_create_counselling.point_covered, tbl_create_counselling.critically_level, tbl_create_counselling.next_appointment, tbl_create_counselling.school_id,tbl_create_counselling.department_id,tbl_create_counselling.createdon, tbl_create_counselling.status, tbl_mentee.full_name, tbl_admin.first_name,tbl_admin.last_name")->get('tbl_create_counselling');
		//echo $this->db->last_query();die;
		return $query->result_array();
    }
	
	/*
	* Function : validatelogin
	*/
	public function validatelogin($tbl_name, $col = ' * ', $condition= array())
    {
        $time = time();
        $this->db->select($col);
        $this->db->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				if($key=='password'){
					$this->db->where($key, md5($val));
				} else {
					
					$this->db->where($key, $val);
				}
			}
			
		}
		$query = $this->db->get($tbl_name);
		//echo $this->db->last_query(); die;
        return $query->row_array();
    }
	
	/*
	* Function : registrationCount
	*/
	
	public function registrationCount($tbl_name = 'patient_registration')
    {
        $result = $this->db->query("SELECT id FROM ".$tbl_name." where is_deleted='0'");
        return $result->num_rows();
        
    }
	/*
	* Function : questionsCount
	*/
	
	public function questionsCount($tbl_name = 'tbl_feedback_ratingrecord', $question_id='1',$search_fromdate='',$search_todate='')
    {
		$db2 = $this->load->database('dbreport', TRUE);
		$time = time();
        $db2->select($col);
        $db2->where('is_deleted', '0');
        $db2->where('q_id', $question_id);
		if($search_fromdate){
			$db2->where('createdon >=', $search_fromdate .'00:00:00');
		}
		if($search_todate){
			$db2->where('createdon <=', $search_todate.'23:59:59');
		}
		$result = $db2->get($tbl_name);
		//echo $this->db->last_query(); die;
	    return $result->num_rows();
        
    }
	/*
	* Function : questionsRatingCount
	*/
	
	public function questionsRatingCount($tbl_name = 'tbl_feedback_ratingrecord', $question_id='1', $rating_id='1',$search_fromdate='',$search_todate='')
    {
		$db2 = $this->load->database('dbreport', TRUE);
		$sql .= "SELECT id FROM ".$tbl_name." where is_deleted='0' AND q_id=$question_id AND r_id=$rating_id";
		if($search_fromdate){
			$sql .=' AND createdon>="'.date('Y-m-d',strtotime($search_fromdate)).' 00:00:00"';
		}
		if($search_todate){
			$sql .=' AND createdon<="'.date('Y-m-d',strtotime($search_todate)).' 23:59:59"';
		}
		//echo $sql;
        $result = $db2->query($sql);
        return $result->num_rows();
        
    }
	/*
	* Function : countrylist
	*/
	
	public function countrylist($tbl_name = 'su_country', $col = ' * ')
    {
        $time = time();
        $this->db->select($col);
        $this->db->where('is_deleted', '0');
		$this->db->order_by('country_name', 'asc');
        $query = $this->db->get($tbl_name);
        return $query->result_array();
    }
	/*
	* Function : occupationlist
	*/
	public function occupationlist($tbl_name = 'sh_occupation', $col = ' * ')
    {
        $time = time();
        $this->db->select($col);
        $this->db->where('is_deleted', '0');
		$this->db->order_by('title', 'asc');
        $query = $this->db->get($tbl_name);
        return $query->result_array();
    }
	
	/*
	* Function : getAllDoctorsProfile
	*/
	
	public function getAllDoctorsProfile($tbl_name = 'doctors_master', $col = ' * ', $condition)
    {
        
        $this->db->select($col);
		$this->db->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db->where($key, $val);
			}
			
		}
       
		$this->db->order_by('dr_name', 'asc');
        $query = $this->db->get($tbl_name);
        return $query->result_array();
    }
	
	
	
	/*
	* Function : getCommon2Query
	*/
	
	public function getCommon2Query($tbl_name = 'tbl_schools', $col = ' * ', $condition='')
    {
        
        $this->db->select($col);
		$this->db->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db->where($key, $val);
			}
			
		}
        $query = $this->db->get($tbl_name);
		echo $this->db->last_query(); die;
        return $query->result_array();
    }
	/*
	* Function : getCommonQuery
	*/
	
	public function getCommonQuery($tbl_name = 'tbl_schools', $col = ' * ', $condition='', $order_by='', $betweenDate='', $where_id='')
    {
        $db2 = $this->load->database('dbreport', TRUE);
        $db2->select($col);
		$db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
			
		}
		if(!empty($where_id))
		{ 
			$db2->where_in('slag_id', $where_id);
		}
		
		if(!empty($betweenDate))
		{
			$from_date = $betweenDate['from_date'];
			$to_date = $betweenDate['to_date'];
			$db2->where("DATE_FORMAT(".$tbl_name.".createdon,'%Y-%m-%d') >='$from_date'");
			$db2->where("DATE_FORMAT(".$tbl_name.".createdon,'%Y-%m-%d') <='$to_date'");
		}
		
		if(!empty($order_by))
		{ 
			foreach($order_by as $key=>$val) {
				$db2->order_by($key, $val);
			}
			
		}
        $query = $db2->get($tbl_name);
        return $query->result_array();
    }
	
	/*
	* Function : getCommonQuery
	*/
	
	public function getCommonLikeQuery($tbl_name = 'tbl_schools', $col = ' * ', $condition='', $order_by='', $betweenDate=NULL)
    {
        
        $this->db->select($col);
		$this->db->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				//$this->db->where($key, $val);
				$this->db->like($key, $val, 'after');
			}
			
		}
		if(!empty($betweenDate))
		{
			$from_date = $betweenDate['from_date'];
			$to_date = $betweenDate['to_date'];
			$this->db->where("DATE_FORMAT($tbl_name.createdon,'%Y-%m-%d') >='$from_date'");
			$this->db->where("DATE_FORMAT($tbl_name.createdon,'%Y-%m-%d') <='$to_date'");
		}
		
		if(!empty($order_by))
		{ 
			foreach($order_by as $key=>$val) {
				$this->db->order_by($key, $val);
			}
			
		}
        $query = $this->db->get($tbl_name);
		//echo $this->db->last_query(); die;
        return $query->result_array();
    }
	
	
	/*
	* Function : getTotalNumRecords
	*/
	public function getTotalNumRecords($tbl_name='tbl_pdms_ip_patient_info')
	{
        
			$this->db->select('*');
			$this->db->where('is_deleted', '0');
			$this->db->where('status', '1');
			$query = $this->db->get($tbl_name);;
			return $query->num_rows();
		
    }
	
	/*
	* Function : getDoctorsAvailableSlots
	*/
	public function getDoctorsAvailableSlots($dcotor_id)
	{
        if($dcotor_id>0)
		{
			$this->db->select('*');
			$this->db->where('id', $dcotor_id);
			$this->db->where('is_deleted', '0');
			$this->db->where('status', '1');
			$query = $this->db->get('doctors_master');;
			return $query->row_array();
		}
    }
	
	
	/*
	* Function : opdSlotsMaster
	*/
	public function opdSlotsMaster()
	{
		$this->db->select('*');
		$this->db->where('status', '1');
		$this->db->order_by('is_deleted', '0');
		$query = $this->db->get('sh_opd_slots');
		$results = array();
		foreach($query->result_array() as $value) {
			$results[$value['id']] = $value['name'];
		}
		return $results;
	}
	
	
	/*
	* Function : checkApplicationDetails
	*/
	
	public function checkApplicationDetails($tbl_name = 'grievance_users', $col = ' * ', $cond)
    {
        $time = time();
        $this->db->select($col);
		
		foreach($cond as $key=>$val) {
			if($val!='' && $key!=''){
				$this->db->where($key, $val);
			}
		}
        $this->db->where('is_deleted', '0');
		$query = $this->db->get($tbl_name);
		//echo $this->db->last_query(); die;
        return $query->row_array();
    }
	
	
	/*
	* Function : scheduleAppointmentDetails
	*/
	
	public function scheduleAppointmentDetails($tbl_name = 'schedule_appointment', $col = ' * ', $cond)
    {
        $this->db->select($col);
		foreach($cond as $key=>$val) {
			if($val!='' && $key!=''){
				$this->db->where($key, $val);
			}
		}
        $this->db->where('appointment_date>=', date('Y-m-d'));
        $this->db->where('is_deleted', '0');
        $this->db->where('slots_available', '1');
        $this->db->where('status', '1');
		$query = $this->db->get($tbl_name);
        return $query->row_array();
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
	* Function: removeAllItems
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function removeAllItems($table_name="", $user_id, $col_name = 'user_id'){
		
		if($user_id>0){
			
			$this->db->where($col_name, $user_id);
			$this->db->delete($table_name); 
			
			return true;
		}
	}
	
	/*
	* Function: getRegisterationDetails
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	
	public function getRegisterationDetails($id)
	{
		if($id>0){
			$this->db->select('patient_registration.*,present_address_master.registration_id,present_address_master.house_no,present_address_master.area,present_address_master.country, present_address_master.state, present_address_master.district, present_address_master.taluk, present_address_master.pincode, permanent_address_master.registration_id,permanent_address_master.permanent_house_no,permanent_address_master.permanent_area,permanent_address_master.permanent_country, permanent_address_master.permanent_state, permanent_address_master.permanent_district, permanent_address_master.permanent_taluk, permanent_address_master.permanent_pincode,passport_information.passport_number, passport_information.passport_issue_date, passport_information.passport_expiry_date, passport_information.personal_phone_no,visa_number,visa_issue_date,visa_expiry_date,date_of_arrival,attendant_name,attendant_passport_number,attendant_passport_issue_date,attendant_passport_expiry_date,attendant_personal_phone_no,attendant_visa_number,attendant_visa_issue_date,attendant_visa_expiry_date,attendant_date_of_arrival');
			$this->db->join('permanent_address_master', 'permanent_address_master.registration_id = patient_registration.id', 'left');
			$this->db->join('present_address_master', 'present_address_master.registration_id = patient_registration.id', 'left');
			$this->db->join('passport_information', 'passport_information.registration_id = patient_registration.id', 'left');
			$this->db->join('visa_information', 'visa_information.registration_id = patient_registration.id', 'left');
			$this->db->join('attendant_passport_information', 'attendant_passport_information.registration_id = patient_registration.id', 'left');
			$this->db->join('attendant_visa_information', 'attendant_visa_information.registration_id = patient_registration.id', 'left');
			$this->db->where('patient_registration.id', $id);
			$this->db->limit(1);
			$query = $this->db->get('patient_registration');
			//echo $this->db->last_query();die;
			return $query->row_array();
		}
	}
	/*
	* Function: encryptpassword
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	
	function encryptpassword($plainText) 
	{ 
		$base64 = base64_encode($plainText);
		$base64url = strtr($base64, '+/=', '-_,');
		return $base64url;
	} 
	
	/*
	* Function: decryptpassword
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	function decryptpassword($plainText) 
	{ 
	
    $base64url = strtr($plainText, '-_,', '+/=');
    $base64 = base64_decode($base64url);
    return $base64;

	} 

	/*
	* Function: getSchoolList
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getSchoolList($tbl_name='su_schools', $col = ' * ', $condition=null)
    {
        $time = time();
        $this->db->select($col);
        $this->db->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db->where($key, $val);
			}
			
		}
		//$this->db->order_by('id', 'asc');
        $query = $this->db->get($tbl_name);
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['id']] = $row['school_name'];
		}
        return $results;
    }
	
	/*
	* Function: getDepartmentList
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getDepartmentList($tbl_name='su_departments', $col = ' * ', $condition=null)
    {
        $time = time();
        $this->db->select($col);
        $this->db->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db->where($key, $val);
			}
			
		}
		//$this->db->order_by('id', 'asc');
        $query = $this->db->get($tbl_name);
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['id']] = $row['name'];
		}
        return $results;
    }
	
	/*
	* Function: getFullCommonListBYID
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getCommonSingleLikeRecord($tbl_name='su_departments', $col = ' * ', $condition=null)
    {
        $time = time();
        $this->db->select($col);
        $this->db->where('is_deleted', '0');
        $this->db->where('status', '1');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db->like($key, $val, 'before');
			}
			
		}
		$query = $this->db->get($tbl_name);
		//echo $this->db->last_query(); die;
		$results = array();
		$results = $query->row_array();
        return $results;
    }
	
	/*
	* Function: getFullCommonListBYID
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getFullCommonListBYID($tbl_name='su_departments', $col = ' * ', $condition=null)
    {
        $time = time();
        $this->db->select($col);
        $this->db->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db->where($key, $val);
			}
			
		}
		//$this->db->order_by('id', 'asc');
        $query = $this->db->get($tbl_name);
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['id']] = $row;
		}
        return $results;
    }
	/*
	* Function: getFullDepartmentList
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getFullDepartmentList($tbl_name='su_departments', $col = ' * ', $condition=null)
    {
        $time = time();
        $this->db->select($col);
        $this->db->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db->where($key, $val);
			}
			
		}
		//$this->db->order_by('id', 'asc');
        $query = $this->db->get($tbl_name);
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['id']] = $row;
		}
        return $results;
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
	public function getCommonIdArray($tbl_name='tbl_schools', $col = ' * ', $condition=null)
    {
        $time = time();
        $this->db->select($col);
        $this->db->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db->where($key, $val);
			}
			
		}
		//$this->db->order_by('id', 'asc');
        $query = $this->db->get($tbl_name);
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
		$db2 = $this->load->database('dbreport', TRUE);
        $db2->select($col);
        $db2->where('status', '1');
        $db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
			
		}
		$query = $db2->get($tbl_name);
		$results = array();
		$results = $query->row_array();
	    return $results;
	}
	

	public function getAllRecordscount($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL)
    {
        $time = time();
		$db2 = $this->load->database('dbreport', TRUE);
        $db2->select($col);
        $db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
		}
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$db2->order_by($key, $val);
			}
		}
		if ($limit !== null && $start !== null) {
           $query = $db2->get($tbl_name,$limit, $start);
        } else {
			$query = $db2->get($tbl_name);
		}
		//echo $this->db->last_query(); die;
		return $query->num_rows();
    }
	
	
	// Function for deletion
	public function deleterecords($tbl_name, $id){
		if($tbl_name!='' && $id>0){
			$sql_query=$this->db->where('id', $id)->delete($tbl_name);
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
							$respe = $this->db->insert('tbl_reassign_mentor',$insertArray);
							
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
				$respe = $this->db->insert('tbl_admin',$insertArray);
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
		$dbreport = $this->load->database('dbreport', TRUE);
		$dbreport->select('id,system_id');
		$dbreport->where('is_deleted', '0');
		$dbreport->where('status', '1');
		$dbreport->where('system_id', $system_id);
		$query = $dbreport->get('tbl_mentee');
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
		$dbreport = $this->load->database('dbreport', TRUE);
		$employee_id = $this->getAllDigitsNumber($employee_id);
		$dbreport->select('id');
		$dbreport->where('is_deleted', '0');
		$dbreport->where('status', '1');
		$dbreport->like('employee_id', $employee_id, 'before');
		$query = $dbreport->get('tbl_admin');
		//echo $dbreport->last_query(); die;
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
		$dbreport = $this->load->database('dbreport', TRUE);
		$dbreport->select('id,school_name, school_code');
		$dbreport->where('is_deleted', '0');
		$dbreport->where('status', '1');
		$dbreport->where('school_name', $name_value);
		$query = $dbreport->get('tbl_schools');
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
		$dbreport = $this->load->database('dbreport', TRUE);
		$dbreport->select('id,department_name');
		$dbreport->where('is_deleted', '0');
		$dbreport->where('status', '1');
		$dbreport->where('department_name', $name_value);
		$query = $dbreport->get('tbl_departments');
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
		$dbreport = $this->load->database('dbreport', TRUE);
        $time = time();
        $dbreport->select($col);
        $dbreport->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$dbreport->where($key, $val);
			}
			
		}
		//$dbreport->order_by('id', 'asc');
        $query = $dbreport->get($tbl_name);
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
	
	/*
	* Function : getAllQuestionsMaster
	**
	*
	*/
	public function getAllQuestionsMaster($condition=null)
	{
		$dbreport = $this->load->database('dbreport', TRUE);
        if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$dbreport->where($key, $val);
			}
		}
		$dbreport->order_by('survey_questions_master.display_order', 'asc');
		$query = $dbreport->join('tbl_checklist_master', 'tbl_checklist_master.id = survey_questions_master.category_id');
		$query = $dbreport->select("survey_questions_master.*, checklist")->get('survey_questions_master');
		//echo $dbreport->last_query();die;
		return $query->result_array();
    }
	
	/*
	* Function : getpdmsipCommonQuery
	**
	*
	*/
	public function getpdmsipCommonQuery($condition=null, $betweenDate=NULL)
	{
		$db2 = $this->load->database('dbreport', TRUE);
        if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
		}
		if(!empty($betweenDate))
		{
			$from_date = $betweenDate['from_date'];
			$to_date = $betweenDate['to_date'];
			$db2->where("DATE_FORMAT(tbl_feedback_otherrecords.createdon,'%Y-%m-%d') >='$from_date'");
			$db2->where("DATE_FORMAT(tbl_feedback_otherrecords.createdon,'%Y-%m-%d') <='$to_date'");
		}
		$db2->order_by('tbl_feedback_otherrecords.id', 'desc');
		$query = $db2->join('tbl_feedback_otherrecords', 'tbl_feedback_otherrecords.slag_id = tbl_pdms_ip_patient_info.slag_id');
		$query = $db2->select("tbl_pdms_ip_patient_info.*, suggestion,reason_if_no,recommedation, tbl_feedback_otherrecords.createdon as othcreatedon")->get('tbl_pdms_ip_patient_info');
		//echo $dbreport->last_query();die;
		return $query->result_array();
    }
	
	/*
	* Function : getpdmsopCommonQuery
	**
	*
	*/
	public function getpdmsopCommonQuery($condition=null, $betweenDate =NULL)
	{
		$dbreport = $this->load->database('dbreport', TRUE);
        if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$dbreport->where($key, $val);
			}
		}
		if(!empty($betweenDate))
		{
			$from_date = $betweenDate['from_date'];
			$to_date = $betweenDate['to_date'];
			$dbreport->where("DATE_FORMAT(tbl_feedback_otherrecords.createdon,'%Y-%m-%d') >='$from_date'");
			$dbreport->where("DATE_FORMAT(tbl_feedback_otherrecords.createdon,'%Y-%m-%d') <='$to_date'");
		}
		
		$dbreport->order_by('tbl_pdms_op_patient_info.createdon', 'desc');
		$query = $dbreport->join('tbl_feedback_otherrecords', 'tbl_feedback_otherrecords.slag_id = tbl_pdms_op_patient_info.slag_id');
		$query = $dbreport->select("tbl_pdms_op_patient_info.*, suggestion,reason_if_no,recommedation, tbl_feedback_otherrecords.createdon as othcreatedon")->get('tbl_pdms_op_patient_info');
		//echo $dbreport->last_query();die;
		return $query->result_array();
    }
	
	/*
	* Function : getpdmsipPatientCommonQuery
	**
	*
	*/
	public function getpdmsipPatientCommonQuery($condition=null)
	{
		$dbreport = $this->load->database('dbreport', TRUE);
        if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$dbreport->where($key, $val);
			}
		}
		$dbreport->order_by('tbl_pdms_ip_patient_info.createdon', 'desc');
		$query = $dbreport->join('tbl_feedback_ratingrecord', 'tbl_feedback_ratingrecord.slag_id = tbl_pdms_ip_patient_info.slag_id');
		$query = $dbreport->select("tbl_pdms_ip_patient_info.*, tbl_feedback_ratingrecord.createdon")->get('tbl_pdms_ip_patient_info');
		//echo $dbreport->last_query();die;
		return $query->result_array();
    }
	
	/*
	* Function : getpdmsopPatientCommonQuery
	**
	*
	*/
	public function getpdmsopPatientCommonQuery($condition=null)
	{
		$dbreport = $this->load->database('dbreport', TRUE);
        if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$dbreport->where($key, $val);
			}
		}
		$dbreport->order_by('tbl_pdms_op_patient_info.createdon', 'desc');
		$query = $dbreport->join('tbl_feedback_ratingrecord', 'tbl_feedback_ratingrecord.slag_id = tbl_pdms_op_patient_info.slag_id');
		$query = $dbreport->select("tbl_pdms_op_patient_info.*,tbl_feedback_ratingrecord.createdon")->get('tbl_pdms_op_patient_info');
		//echo $dbreport->last_query();die;
		return $query->result_array();
    }
	/*
	* Function : getFeedbackCollectionCommonQuery
	**
	*
	*/
	public function getFeedbackCollectionCommonQuery($condition=null, $betweenDate='')
	{
		$dbreport = $this->load->database('dbreport', TRUE);
        if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$dbreport->where($key, $val);
			}
		}
		
		if(!empty($betweenDate))
		{
			$from_date = $betweenDate['from_date'];
			$to_date = $betweenDate['to_date'];
			$dbreport->where("DATE_FORMAT(tbl_feedbackcollection.createdon,'%Y-%m-%d') >='$from_date'");
			$dbreport->where("DATE_FORMAT(tbl_feedbackcollection.createdon,'%Y-%m-%d') <='$to_date'");
		}
			
		$dbreport->order_by('tbl_feedbackcollection.createdon', 'desc');
		$query = $dbreport->join('tbl_feedback_source', 'tbl_feedback_source.id = tbl_feedbackcollection.feedback_source');
		//$query = $dbreport->join('tbl_departments', 'tbl_departments.id = tbl_feedbackcollection.department_id','left');
		$query = $dbreport->select("tbl_feedbackcollection.*,tbl_feedback_source.title")->get('tbl_feedbackcollection');
		//echo $this->db->last_query();die;
		return $query->result_array();
    }

}