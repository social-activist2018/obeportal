<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Class Slotmaster_Model extends CI_Model{
	private $db2;
	public function __construct()
	{
		parent::__construct();
		$this->db2 = $this->load->database('db2', TRUE);
	}	
	/*
	* Function : getAllRecordsGroupBy
	*/
	public function getAllRecordsGroupBy($tbl_name, $col = '*', $condition=null, $order_by = NULL, $limit=NULL, $start=NULL)
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
		$this->db2->group_by('school_id');
		if ($limit !== null && $start !== null) {
           $query = $this->db2->get($tbl_name,$limit, $start);
        } else {
			$query = $this->db2->get($tbl_name);
		}
		
		//echo $this->db2->last_query(); die;
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
		$this->db2->group_by($group_by_column);
		$query = $this->db2->get($tbl_name); 
		//echo $this->db2->last_query(); die;
		$resutls = array();
		foreach($query->result_array() as $row){
			$resutls[$row[$group_by_column]] = $row['total'];
		}
        return $resutls;
    }
	
	/*
	* Function : getRecordsByGroupFullRow
	*/
	public function getRecordsByGroupFullRow($tbl_name='', $col = '*', $condition=null, $group_by_column='school_id')
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
		$this->db2->group_by($group_by_column);
		$query = $this->db2->get($tbl_name); 
		//echo $this->db2->last_query(); die;
		$resutls = array();
		foreach($query->result_array() as $row){
			$resutls[$row[$group_by_column]] = $row;
		}
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
	public function getAllRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL, $betweenDate=NULL ,$where_like = null, $where_like_key = null, $where_in = null, $where_in_key = null, $jn_tbl =null, $jn_cond =null)
    {

        $time = time();
		$dbreport = $this->load->database('dbreport', TRUE);
        $dbreport->select($col);
		if(!empty($jn_tbl) && !empty($jn_cond)){
			 $dbreport->join($jn_tbl, $jn_cond, 'left');
		}
        $dbreport->where($tbl_name.'.is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$dbreport->where($key, $val);
			}
		}
		if(!empty($where_like)) {
		$dbreport->like($where_like_key, $where_like);
		}
		if(!empty($where_in)) {
		$dbreport->where_in($where_in_key, $where_in);
		}
		if(!empty($betweenDate) && strtotime($betweenDate['from_date'])>0 && strtotime($betweenDate['to_date'])>0)
		{
			$from_date = $betweenDate['from_date'];
			$to_date = $betweenDate['to_date'];
			if(strtotime($from_date)>0 && strtotime($to_date)>0){
				$dbreport->where("DATE_FORMAT($tbl_name.attendance_date,'%m/%d/%Y') >='$from_date'");
				$dbreport->where("DATE_FORMAT($tbl_name.attendance_date,'%m/%d/%Y') <='$to_date'");
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
		//echo $dbreport->last_query(); //die;
		return $query->result_array();
    }
	
	/*
	* Function : getAllGrievanceHistoryRecords
	*/
	public function getAllGrievanceHistoryRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL)
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
		$dateCond = 'now()-interval 3 month';
		$this->db2->where('lastUpdationDate >=', $dateCond, FALSE);
		
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
	* Function : getAllRecords
	*/
	public function getAllMonthlyRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL)
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
		
		$this->db2->where('MONTH(regDate)', date('m'));
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}
		}
		$query = $this->db2->get($tbl_name);
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
		$dbreport = $this->load->database('dbreport', TRUE);
        $dbreport->select($col);
        //$dbreport->where('is_deleted', '0');
		if(!empty($where_like)) {
		$dbreport->like($where_like_key, $where_like);
		}
		 
		
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
		$query = $dbreport->get($tbl_name);
		//echo $dbreport->last_query(); 
        return $query->row_array();
    }
	
	
	/*
	* Function : getInteraction
	*/
	public function getInteraction($cond = '', $betweenDate = '')
	{
		if(!empty($cond)) {
			foreach($cond as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		if(!empty($betweenDate))
		{
			$from_date = $betweenDate['from_date'];
			$to_date = $betweenDate['to_date'];
			$this->db2->where("DATE_FORMAT(tbl_create_counselling.createdon,'%m/%d/%Y') >='$from_date'");
			$this->db2->where("DATE_FORMAT(tbl_create_counselling.createdon,'%m/%d/%Y') <='$to_date'");
		}
		
		$this->db2->group_by('mentor_id');
		$query = $this->db2->select("mentor_id, count(*)as total")->get('tbl_create_counselling');
		//echo $this->db2->last_query(); die;
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
				$this->db2->where($key, $val);
			}
		}
		$this->db2->group_by('mentor_id');
		$query = $this->db2->select("mentor_id, count(*)as total")->get('tbl_mentee');
		//echo $this->db2->last_query(); die;
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
		$this->db2->where('status', '1');
		$this->db2->where('is_deleted', '0');
		$today = date('Y-m-d h:i:s');
		$this->db2->where("DATE_FORMAT(next_appointment, '%Y-%m-%d') <= NOW()");
		$this->db2->where_not_in('counselling_status','3');
		$this->db2->where('mentor_id', $umid);
		$this->db2->order_by('createdon', 'asc');
		$query = $this->db2->select("*")->get('tbl_create_counselling');
		//echo $this->db2->last_query();die;
		return $query->result_array();
    }
	/*
	* Function : getUpInteraction
	*/
	public function getUpInteraction($umid='')
	{
        $this->db2->where('status', '1');
        $this->db2->where('is_deleted', '0');
        $this->db2->where("DATE_FORMAT(next_appointment, '%Y-%m-%d') >= NOW()");
		$this->db2->where('mentor_id', $umid);
		$this->db2->order_by('next_appointment', 'asc');
		$query = $this->db2->select("*")->get('tbl_create_counselling');
		//echo $this->db2->last_query();die;
		return $query->result_array();
    }	
	
	/*
	* Function : getAllNonAcademicComplaintsRecords
	*/
	public function getAllInteraction($condition=null, $createdon='', $betweenDate = '')
	{
        $this->db2->where('tbl_create_counselling.is_deleted', '0');
        if(!empty($createdon)){
		  $this->db2->where("DATE_FORMAT(createdon, '%Y-%m-%d') <= '$createdon'");
		}
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				if($key=='from_date' || $key=='to_date') {
					$this->db2->where("DATE_FORMAT(tbl_create_counselling.createdon,'%m/%d/%Y') >='$val'");
				}  else {
					$this->db2->where($key, $val);
				}
			}
			
		}
		if(!empty($betweenDate))
		{
			$from_date = $betweenDate['from_date'];
			$to_date = $betweenDate['to_date'];
			$this->db2->where("DATE_FORMAT(tbl_create_counselling.createdon,'%m/%d/%Y') >='$from_date'");
			$this->db2->where("DATE_FORMAT(tbl_create_counselling.createdon,'%m/%d/%Y') <='$to_date'");
		}
		$this->db2->order_by('tbl_create_counselling.createdon', 'asc');
		$query = $this->db2->join('tbl_mentee', 'tbl_mentee.id = tbl_create_counselling.mente_id');
		$query = $this->db2->join('tbl_admin', 'tbl_admin.id = tbl_create_counselling.mentor_id');
		$query = $this->db2->select("tbl_create_counselling.id,tbl_create_counselling.issue_type, tbl_create_counselling.point_covered, tbl_create_counselling.critically_level, tbl_create_counselling.next_appointment, tbl_create_counselling.school_id,tbl_create_counselling.department_id,tbl_create_counselling.createdon,tbl_create_counselling.mentor_id, tbl_create_counselling.counselling_status , tbl_mentee.full_name, tbl_admin.first_name,tbl_admin.last_name")->get('tbl_create_counselling');
		//echo $this->db2->last_query();die;
		return $query->result_array();
    }
	
	/*
	* Function : getAllClassAssignedFaculty
	*/
	public function getAllClassAssignedFaculty($condition=null)
	{
        $this->db2->where('tbl_faculty_course_assignment.is_deleted', '0');
        if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
		$this->db2->order_by('tbl_faculty_course_assignment.createdon', 'asc');
		$query = $this->db2->join('tbl_admin', 'tbl_admin.id = tbl_faculty_course_assignment.faculty_id');
		$query = $this->db2->select("tbl_faculty_course_assignment.*, CONCAT(tbl_admin.first_name, '', tbl_admin.last_name) as full_name ")->get('tbl_faculty_course_assignment');
		//echo $this->db2->last_query();die;
		return $query->result_array();
    }
	
	/*
	* Function : getAllVideo
	*/
	public function getAllVideo($condition=null, $createdon='', $betweenDate = '')
	{
        $this->db2->where('tbl_library_master.is_deleted', '0');
        if(!empty($createdon)){
		  $this->db2->where("DATE_FORMAT(createdon, '%Y-%m-%d') <= '$createdon'");
		}
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				if($key=='from_date' || $key=='to_date') {
					$this->db2->where("DATE_FORMAT(tbl_library_master.createdon,'%m/%d/%Y') >='$val'");
				}  else {
					$this->db2->where($key, $val);
				}
			}
			
		}
		if(!empty($betweenDate))
		{
			$from_date = $betweenDate['from_date'];
			$to_date = $betweenDate['to_date'];
			$this->db2->where("DATE_FORMAT(tbl_library_master.createdon,'%m/%d/%Y') >='$from_date'");
			$this->db2->where("DATE_FORMAT(tbl_library_master.createdon,'%m/%d/%Y') <='$to_date'");
		}
		$this->db2->order_by('tbl_library_master.createdon', 'asc');
		$query = $this->db2->join('tbl_academic_master', 'tbl_academic_master.id = tbl_library_master.campus_id');
		$query = $this->db2->join('tbl_admin', 'tbl_admin.id = tbl_library_master.author_id');
		$query = $this->db2->select("tbl_library_master.*, , tbl_academic_master.academic_name, tbl_admin.first_name,tbl_admin.last_name")->get('tbl_library_master');
		//echo $this->db2->last_query();die;
		return $query->result_array();
    }
	
	/*
	* Function : getAllMentorInteraction
	*/
	public function getAllMentorInteraction($condition=null, $createdon='', $betweenDate = '')
	{
        $this->db2->where('tbl_create_counselling.is_deleted', '0');
        if(!empty($createdon)){
		  $this->db2->where("DATE_FORMAT(createdon, '%Y-%m-%d') <= '$createdon'");
		}
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
		if(!empty($betweenDate))
		{
			$from_date = $betweenDate['from_date'];
			$to_date = $betweenDate['to_date'];
			$this->db2->where("DATE_FORMAT(tbl_create_counselling.createdon,'%m/%d/%Y') >='$from_date'");
			$this->db2->where("DATE_FORMAT(tbl_create_counselling.createdon,'%m/%d/%Y') <='$to_date'");
		}
		$this->db2->order_by('tbl_create_counselling.createdon', 'asc');
		$query = $this->db2->join('tbl_mentee', 'tbl_mentee.id = tbl_create_counselling.mente_id');
		$query = $this->db2->join('tbl_admin', 'tbl_admin.id = tbl_create_counselling.mentor_id');
		$query = $this->db2->select("tbl_create_counselling.id,tbl_create_counselling.issue_type, tbl_create_counselling.point_covered, tbl_create_counselling.critically_level, tbl_create_counselling.next_appointment, tbl_create_counselling.school_id,tbl_create_counselling.department_id,tbl_create_counselling.createdon, tbl_create_counselling.status, tbl_mentee.full_name, tbl_admin.first_name,tbl_admin.last_name")->get('tbl_create_counselling');
		//echo $this->db2->last_query();die;
		return $query->result_array();
    }
	
	/*
	* Function : validatelogin
	*/
	public function validatelogin($tbl_name, $col = ' * ', $condition= array())
    {
        $time = time();
        $this->db2->select($col);
        $this->db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				if($key=='password'){
					$this->db2->where($key, md5($val));
				} else {
					
					$this->db2->where($key, $val);
				}
			}
			
		}
		$query = $this->db2->get($tbl_name);
		//echo $this->db2->last_query(); die;
        return $query->row_array();
    }
	
	/*
	* Function : registrationCount
	*/
	
	public function registrationCount($tbl_name = 'patient_registration')
    {
        $result = $this->db2->query("SELECT id FROM ".$tbl_name." where is_deleted='0'");
        return $result->num_rows();
        
    }
	/*
	* Function : countrylist
	*/
	
	public function countrylist($tbl_name = 'su_country', $col = ' * ')
    {
        $time = time();
        $this->db2->select($col);
        $this->db2->where('is_deleted', '0');
		$this->db2->order_by('country_name', 'asc');
        $query = $this->db2->get($tbl_name);
        return $query->result_array();
    }
	/*
	* Function : occupationlist
	*/
	public function occupationlist($tbl_name = 'sh_occupation', $col = ' * ')
    {
        $time = time();
        $this->db2->select($col);
        $this->db2->where('is_deleted', '0');
		$this->db2->order_by('title', 'asc');
        $query = $this->db2->get($tbl_name);
        return $query->result_array();
    }
	
	/*
	* Function : getAllDoctorsProfile
	*/
	
	public function getAllDoctorsProfile($tbl_name = 'doctors_master', $col = ' * ', $condition)
    {
        
        $this->db2->select($col);
		$this->db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
       
		$this->db2->order_by('dr_name', 'asc');
        $query = $this->db2->get($tbl_name);
        return $query->result_array();
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
	
	
	/*
	* Function : getDoctorsAvailableSlots
	*/
	public function getDoctorsAvailableSlots($dcotor_id)
	{
        if($dcotor_id>0)
		{
			$this->db2->select('*');
			$this->db2->where('id', $dcotor_id);
			$this->db2->where('is_deleted', '0');
			$this->db2->where('status', '1');
			$query = $this->db2->get('doctors_master');;
			return $query->row_array();
		}
    }
	
	
	/*
	* Function : opdSlotsMaster
	*/
	public function opdSlotsMaster()
	{
		$this->db2->select('*');
		$this->db2->where('status', '1');
		$this->db2->order_by('is_deleted', '0');
		$query = $this->db2->get('sh_opd_slots');
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
        $this->db2->select($col);
		
		foreach($cond as $key=>$val) {
			if($val!='' && $key!=''){
				$this->db2->where($key, $val);
			}
		}
        $this->db2->where('is_deleted', '0');
		$query = $this->db2->get($tbl_name);
		//echo $this->db2->last_query(); die;
        return $query->row_array();
    }
	
	
	/*
	* Function : scheduleAppointmentDetails
	*/
	
	public function scheduleAppointmentDetails($tbl_name = 'schedule_appointment', $col = ' * ', $cond)
    {
        $this->db2->select($col);
		foreach($cond as $key=>$val) {
			if($val!='' && $key!=''){
				$this->db2->where($key, $val);
			}
		}
        $this->db2->where('appointment_date>=', date('Y-m-d'));
        $this->db2->where('is_deleted', '0');
        $this->db2->where('slots_available', '1');
        $this->db2->where('status', '1');
		$query = $this->db2->get($tbl_name);
        return $query->row_array();
    }
	
	
	public function saveinfo($tbl_name='', $post)
    {
		$this->db->insert($tbl_name, $post);
		#echo $this->db->last_query(); die;
		return $this->db->insert_id();
    }
	
	public function updateinfo($tbl_name='', $post, $field, $value)
    {
		$this->db->where($field, $value);
        if (!$this->db->update($tbl_name, $post)) {
            log_message('error', print_r($this->db->error(), true));
        }
		//echo $this->db2->last_query(); die;
	}
	
	public function updateinfoMulti($tbl_name='', $post, $cond)
    {
		foreach($cond as $field=>$value){
			$this->db->where($field, $value);
		}
        if (!$this->db->update($tbl_name, $post)) {
            log_message('error', print_r($this->db->error(), true));
        }
		//echo $this->db2->last_query(); die;
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
			$this->db2->select('patient_registration.*,present_address_master.registration_id,present_address_master.house_no,present_address_master.area,present_address_master.country, present_address_master.state, present_address_master.district, present_address_master.taluk, present_address_master.pincode, permanent_address_master.registration_id,permanent_address_master.permanent_house_no,permanent_address_master.permanent_area,permanent_address_master.permanent_country, permanent_address_master.permanent_state, permanent_address_master.permanent_district, permanent_address_master.permanent_taluk, permanent_address_master.permanent_pincode,passport_information.passport_number, passport_information.passport_issue_date, passport_information.passport_expiry_date, passport_information.personal_phone_no,visa_number,visa_issue_date,visa_expiry_date,date_of_arrival,attendant_name,attendant_passport_number,attendant_passport_issue_date,attendant_passport_expiry_date,attendant_personal_phone_no,attendant_visa_number,attendant_visa_issue_date,attendant_visa_expiry_date,attendant_date_of_arrival');
			$this->db2->join('permanent_address_master', 'permanent_address_master.registration_id = patient_registration.id', 'left');
			$this->db2->join('present_address_master', 'present_address_master.registration_id = patient_registration.id', 'left');
			$this->db2->join('passport_information', 'passport_information.registration_id = patient_registration.id', 'left');
			$this->db2->join('visa_information', 'visa_information.registration_id = patient_registration.id', 'left');
			$this->db2->join('attendant_passport_information', 'attendant_passport_information.registration_id = patient_registration.id', 'left');
			$this->db2->join('attendant_visa_information', 'attendant_visa_information.registration_id = patient_registration.id', 'left');
			$this->db2->where('patient_registration.id', $id);
			$this->db2->limit(1);
			$query = $this->db2->get('patient_registration');
			//echo $this->db2->last_query();die;
			return $query->row_array();
		}
	}
	/*
	* Function: getBlockArray
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	
	public function getBlockArray($id='')
	{
		
		$this->db2->select('tbl_academic_block_master.*, tbl_academic_master.academic_name as campus');
		$this->db2->join('tbl_academic_master', 'tbl_academic_master.id = tbl_academic_block_master.campus_id', 'left');
		if($id>0){
			$this->db2->where('tbl_academic_block_master.id', $id);
			$this->db2->limit(1);
		}
		$query = $this->db2->get('tbl_academic_block_master');
		//echo $this->db2->last_query();die;
		return $query->result_array();
		
	}
	/*
	* Function: getRoomArray
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	
	public function getRoomArray($id='')
	{
		$this->db2->select('tbl_block_room_master.id,tbl_block_room_master.campus_id,tbl_block_room_master.capacity,tbl_block_room_master.block_id,tbl_block_room_master.title,tbl_block_room_master.room_number,tbl_block_room_master.status,tbl_block_room_master.room_sharing,tbl_block_room_master.racademic_year_id, tbl_academic_master.academic_name as campus, tbl_academic_block_master.display_name as block');
		$this->db2->join('tbl_academic_master', 'tbl_academic_master.id = tbl_block_room_master.campus_id', 'left');
		$this->db2->join('tbl_academic_block_master', 'tbl_academic_block_master.id = tbl_block_room_master.block_id', 'left');
		if($id>0){
			$this->db2->where('tbl_block_room_master.id', $id);
			$this->db2->limit(1);
		}
		$query = $this->db2->get('tbl_block_room_master');
		#echo $this->db2->last_query();die;
		return $query->result_array();
		
	}
	
	/*
	* Function: getDepartmentRoomArray
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getDepartmentRoomArray($id='')
	{
		
		$this->db2->select('tbl_department_room_assignment.*,tbl_block_room_master.title,tbl_block_room_master.room_number, tbl_academic_master.academic_name as campus, tbl_academic_block_master.display_name as block');
		$this->db2->join('tbl_block_room_master', 'tbl_department_room_assignment.room_id = tbl_block_room_master.id', 'left');
		$this->db2->join('tbl_academic_master', 'tbl_academic_master.id = tbl_block_room_master.campus_id', 'left');
		$this->db2->join('tbl_academic_block_master', 'tbl_academic_block_master.id = tbl_block_room_master.block_id', 'left');
		
		if($id>0){
			$this->db2->where('tbl_department_room_assignment.id', $id);
			$this->db2->limit(1);
		}
		
		$query = $this->db2->get('tbl_department_room_assignment');
		//echo $this->db2->last_query();die;
		return $query->result_array();
		
	}
	
	
	/*
	* Function: getSeasonArray
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	
	public function getSeasonArray($id='')
	{
		
		$this->db2->select('tbl_academic_season_master.*, tbl_academic_master.academic_name as campus,tbl_academicyear.academic_year as year, tbl_school_master.school_name');
		$this->db2->join('tbl_academic_master', 'tbl_academic_master.id = tbl_academic_season_master.campus_id', 'left');
		$this->db2->join('tbl_academicyear', 'tbl_academicyear.id = tbl_academic_season_master.year_id', 'left');
		$this->db2->join('tbl_school_master', 'tbl_school_master.id = tbl_academic_season_master.school_id', 'left');
		if($id>0){
			$this->db2->where('tbl_academic_season_master.id', $id);
			$this->db2->limit(1);
		}
		$query = $this->db2->get('tbl_academic_season_master');
		//echo $this->db2->last_query();die;
		return $query->result_array();
		
	}
	public function getAllSemesterArray($condition='')
	{
		$dbreport = $this->load->database('dbreport', TRUE);
		$dbreport->select('tbl_semester.title, tbl_semester.psoft_name, tbl_credits.id,tbl_credits.academic_id,tbl_credits.program_type, tbl_credits.program_id,tbl_credits.status ');
		$dbreport->join('tbl_semester', 'tbl_semester.id = tbl_credits.semester_id', 'left');
		if($id>0){
			$dbreport->where('tbl_credits.id', $id);
			$dbreport->limit(1);
		}
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$dbreport->where($key, $val);
			}
			
		}
		$query = $dbreport->get('tbl_credits');
		//echo $dbreport->last_query();die;
		return $query->result_array();
		//"SELECT * FROM `tbl_semester` as sem JOIN tbl_credits as crd ON sem.id=crd.semester_id where crd.status='1' AND crd.academic_id='3' AND program_id="" AND program_type="";"
	}
	
	/*
	* Function : getAllActiveClassNumberRecords
	*/
	public function getAllActiveClassNumberRecords($condition='')
	{
		//$this->db2->order_by('tbl_create_counselling.createdon', 'asc');
		$dbreport = $this->load->database('dbreport', TRUE);
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$dbreport->where($key, $val);
			}
		}
		$dbreport->where('tbl_department_course_slot_assignment.status', '1');
		$query = $dbreport->join('tbl_assign_room_slot_section', 'tbl_assign_room_slot_section.dept_course_id = tbl_department_course_slot_assignment.id', 'INNER');
		$dbreport->distinct();
		$query = $dbreport->select("tbl_assign_room_slot_section.class_number,tbl_department_course_slot_assignment.*")->get('tbl_department_course_slot_assignment');
		//echo $dbreport->last_query();die;       
		return $query->result_array();
		
	}
	
	
	/*
	* Function : getAllSlotRoomRecords
	*/
	public function getAllSlotRoomRecords($condition = NULL, $where_in=NULL)
	{
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
        $this->db2->select('course_id, class_id , semester, tbl_slot_master.id as sid,tbl_slot_master.display_name as displayname, tbl_faculty_slot_assignment.faculty_course_id, tbl_block_room_master.*,slot_name, lecture_type, assigned_periods,slot_description');
		$this->db2->join('tbl_block_room_master', 'tbl_block_room_master.id = tbl_faculty_slot_assignment.room_id', 'left');
		$this->db2->join('tbl_slot_master', 'tbl_slot_master.id = tbl_faculty_slot_assignment.slot_id', 'left');
		$this->db2->join('tbl_faculty_course_assignment', 'tbl_faculty_course_assignment.id = tbl_faculty_slot_assignment.faculty_course_id', 'left');
		$this->db2->where_in('faculty_course_id', $where_in);
		$query = $this->db2->get('tbl_faculty_slot_assignment');
		//echo $this->db2->last_query();die;
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['sid']] = $row;
		}
		return $results;
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
	public function getSchoolList($tbl_name='tbl_schools', $col = ' * ', $condition=null)
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
		//echo $this->db2->last_query();die;
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
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['id']] = $row['name'];
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
	public function getCommonIdArray($tbl_name='tbl_schools', $col = ' * ', $condition=null, $order_by=null)
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
		if(!empty($order_by))
		{
			foreach($order_by as $key=>$val) {
			$dbreport->order_by($key, $val);
			}
		}
        $query = $dbreport->get($tbl_name);
		//echo $this->db2->last_query(); die;
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
	public function getCommonIdByLikeArray($tbl_name='tbl_schools', $col = ' * ', $condition=null, $order_by=null)
    {
        $time = time();
        $this->db2->select($col);
        $this->db2->where('is_deleted', '0');
        $this->db2->where('tbl_alternativearrangement_master.status', '1');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				if($key=='old_course_pi'){
					$this->db2->where('old_course_pi like "%'.$val.'%" OR employee_id like "%'.$val.'%"'); 
				} else {
					$this->db2->where($key, $val); 
				}
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
		if ($limit !== null && $start !== null) {
           $query = $dbreport->get($tbl_name,$limit, $start);
        } else {
			$query = $dbreport->get($tbl_name);
		}
		//echo $this->db2->last_query(); die;
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
        $this->db->select('id,catalog_nbr, course_id, strm, class_nbr, class_section, emp_id');
        $this->db->where('is_deleted', '0');
         $this->db->where('status', '1');
        foreach($condArray as $key=>$val) {
            $this->db->where($key, $val);
        }
        
        $query = $this->db->get($tbl_name);
        //echo $this->db->last_query(); die;
        $resultsArray =  $query->result_array();
        
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
		#echo $this->db2->last_query();die;
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
	
	/*
	* Function : getAllVersionWiseRecordsByWhereIN
	*/
	public function getAllVersionWiseRecordsByWhereIN($condition=null,$where_in = NULL)
    {
        $time = time();
        $this->db2->select($col);
        $this->db2->where('tbl_department_course_slot_assignment.is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		if(!empty($where_in)){
			$this->db2->where_in('tbl_department_course_slot_assignment.id',$where_in);
		}
		
		$query = $this->db2->join('tbl_timetable_management', 'tbl_timetable_management.id = tbl_department_course_slot_assignment.tt_version_id');
		$query = $this->db2->select("tbl_department_course_slot_assignment.*, tbl_timetable_management.from_date,tbl_timetable_management.to_date,")->get('tbl_department_course_slot_assignment');
		//echo $this->db2->last_query();die;
		return $query->result_array();
		
    }
	
	public function getAllCommonRecordswhereIn($tbl_name='tbl_faculty_slot_assignment', $col = ' * ', $condition=null, $where_in=NULL, $where_key = 'id')
    {
        $time = time();
		if(!empty($where_in)){
        $this->db2->where($where_key.' IN('.$where_in.')');
		}
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
	
	public function getCommonIdByLikeFacultyArray($tbl_name='tbl_schools', $col = ' * ', $condition=null, $order_by=null)
    {
        $time = time();
        $this->db2->select($col);
        $this->db2->where('is_deleted', '0');
        $this->db2->where('tbl_alternativearrangement_master.status', '1');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				if($key=='old_course_pi'){
					$this->db2->where('old_course_pi like "%'.$val.'%"'); 
				} else {
					$this->db2->where($key, $val); 
				}
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
	
}