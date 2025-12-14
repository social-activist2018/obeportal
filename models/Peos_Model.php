<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Class Peos_Model extends CI_Model{
	
	/*
	* Function : softDeleteRecords
	* Description : Delete Record
	*/
	public function softDeleteRecords($id, $field_name= 'id', $tbl_name)
    {
        $this->db->where($field_name, $id);
		$data = array();
		$data = array('is_deleted'=> '1','status'=>'0', 'modifiedon'=>date('Y-m-d h:i:s'));
        if (!$this->db->update($tbl_name, $data)) {
		    log_message('error', print_r($this->db->error(), true));
            show_error(lang('database_error'));
        }
		
    }
		
	/* 
	* Function : getAllRecordsGroupBy
	*/
	public function getAllRecordsGroupBy($tbl_name, $col = '*', $condition=null, $order_by = NULL, $limit=NULL, $start=NULL)
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
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$this->db->order_by($key, $val);
			}
		}
		$this->db->group_by('school_id');
		if ($limit !== null && $start !== null) {
           $query = $this->db->get($tbl_name,$limit, $start);
        } else {
			$query = $this->db->get($tbl_name);
		}
		
		//echo $this->db->last_query(); die;
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
        $this->db->select($col);
        $this->db->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db->where($key, $val);
			}
		}
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$this->db->order_by($key, $val);
			}
		}
		$this->db->group_by($group_by_column);
		$query = $this->db->get($tbl_name); 
		//echo $this->db->last_query(); die;
		$resutls = array();
		foreach($query->result_array() as $row){
			$resutls[$row[$group_by_column]] = $row['total'];
		}
        return $resutls;
    }
	/*
	* Function : getAllRecords
	*/
	public function getAllRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL, $likeSearch=NULL)
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
		// Like condition_like
		if(!empty($likeSearch))
		{   $k=1;
			foreach($likeSearch as $key=>$val) {
				$this->db->like($key, $val);
				if($k>1) {
					$this->db->or_like($key, $val);
				}
				$k++;
			}
			
		}
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$this->db->order_by($key, $val);
			}
		}
		if ($limit !== null && $start !== null) {
           $query = $this->db->get($tbl_name,$limit, $start);
        } else {
			$query = $this->db->get($tbl_name);
		}
		//echo $this->db->last_query(); //die;
		return $query->result_array();
    }
	
	/*
	* Function : getCustomCommonQuery
	*/
	public function getCustomCommonQuery($school_id)
    {
       
		$sql = "SELECT DISTINCT title FROM `tbl_department_mvcvalues` WHERE status='1' AND mvc_type='2' AND school_id='".$school_id."' order by title ASC"; 
	 	$query = $this->db->query($sql);
		//echo $this->db->last_query(); //die;
		return $query->result_array();
    }
	
	/*
	* Function : getCustomDeptCommonQuery
	*/
	public function getCustomDeptCommonQuery($condition = array())
    {
        $time = time();
        $this->db->select('tbl_department_mvcvalues.department_id,tbl_department_master.name');
        $this->db->distinct('department_id');
        $this->db->where('tbl_department_mvcvalues.is_deleted', '0');
        $this->db->where('tbl_department_mvcvalues.status', '1');
        $this->db->where('tbl_department_mvcvalues.mvc_type', '2');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db->where($key, $val);
			}
		}
		$query = $this->db->join('tbl_department_master', 'tbl_department_master.id = tbl_department_mvcvalues.department_id');
		
		$query = $this->db->get('tbl_department_mvcvalues ');
		
		//echo $this->db->last_query(); //die;
		return $query->result_array();
    }
	
	/*
	* Function : getAllGrievanceHistoryRecords
	*/
	public function getAllGrievanceHistoryRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL)
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
		$dateCond = 'now()-interval 3 month';
		$this->db->where('lastUpdationDate >=', $dateCond, FALSE);
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$this->db->order_by($key, $val);
			}
		}
		if ($limit !== null && $start !== null) {
           $query = $this->db->get($tbl_name,$limit, $start);
        } else {
			$query = $this->db->get($tbl_name);
		}
		//echo $this->db->last_query(); die;
		return $query->result_array();
    }
	
	/*
	* Function : getAllAcademicprogrammeRecords
	*/
	public function getAllAcademicprogrammeRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL)
    {
        $time = time();
        $this->db->select($col);
        $this->db->select($col);
        $this->db->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db->where($key, $val);
			}
		}
		
		$this->db->where('MONTH(regDate)', date('m'));
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$this->db->order_by($key, $val);
			}
		}
		$query = $this->db->get($tbl_name);
        return $query->result_array();
    }
	/*
	* Function : getAllRecords
	*/
	public function getAllMonthlyRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL)
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
		
		$this->db->where('MONTH(regDate)', date('m'));
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$this->db->order_by($key, $val);
			}
		}
		$query = $this->db->get($tbl_name);
        return $query->result_array();
    }
	
	/*
	* Function : getAllModuleList
	*/
	public function getAllModuleList($tbl_name, $col = ' * ', $condition=null, $where_in=NULL, $where_key = 'id')
    {
        $time = time();
        $this->db->select($col);
        $this->db->where('is_deleted', '0');
        $this->db->where_in($where_key, $where_in);
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db->where($key, $val);
			}
			
		}
		//$this->db->order_by('display_order', 'asc');
        $query = $this->db->get($tbl_name);
		//echo $this->db->last_query(); die;
        return $query->result_array();
    }
	
	/*
	* Function : getCommonPMArray
	*/
	public function getCommonPMArray($tbl_name='tbl_course_papersetter_moderator', $cond = '')
	{
		if(!empty($cond)) {
			foreach($cond as $key=>$val) {
				$this->db->where($key, $val);
			}
		}
		$query = $this->db->select("*")->get($tbl_name);
		//echo $this->db->last_query(); die;
		$resp = $query->result_array();
		$results = array();
		foreach( $resp as $val){
			$results[$val['id']] = $val;
		}
		return $results;
		
    }
	
	/*
	* Function : getSingleRecord
	*/
	public function getSingleRecord($tbl_name, $col = ' * ', $condition=null , $type='')
	{
        $time = time();
        $this->db->select($col);
       // $this->db->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db->where($key, $val);
			}
			
		}
		$query = $this->db->get($tbl_name);
		//echo $this->db->last_query(); die;
		if($type){
			return $query->row();
		} else {
			return $query->row_array();
		}
    }
	
	
	/*
	* Function : getCourseWiseCPMArray
	*/
	public function getCourseWiseCPMArray($tbl_name='tbl_course_papersetter_moderator', $cond = '')
	{
		if(!empty($cond)) {
			foreach($cond as $key=>$val) {
				$this->db->where($key, $val);
			}
		}
		$query = $this->db->select("*")->get($tbl_name);
		//echo $this->db->last_query(); die;
		$resp = $query->result_array();
		$results = array();
		foreach( $resp as $val){
			$results[$val['course_id']] = $val;
		}
		return $results;
		
    }
	
	/*
	* Function : getCommonPModeratorArray
	*/
	public function getCommonPModeratorArray($tbl_name='tbl_course_papersetter_moderator', $cond = '')
	{
		if(!empty($cond)) {
			foreach($cond as $key=>$val) {
				$this->db->where($key, $val);
			}
		}
		$query = $this->db->select("*")->get($tbl_name);
		//echo $this->db->last_query(); die;
		$resp = $query->result_array();
		$results = array();
		foreach( $resp as $val){
			$results[$val['id']] = $val;
		}
		return $results;
		
    }
	/*/*
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
	
	public function registrationCount($tbl_name = 'patient_registration', $col='*', $cond= array('is_deleted'=>'0'))
    {
		 $this->db->select($col);
		 if(count($cond)){
			 foreach($cond as $key=>$val){
				$this->db->where($key, $val);
			}
		}
		$query = $this->db->get($tbl_name);
        return $query->num_rows();
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
	
	public function getCommonQuery($tbl_name = 'tbl_school_master', $col = ' * ', $condition='',$order_by='',$condition_like='')
    {
        
        $this->db->select($col);
		$this->db->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db->where($key, $val);
			}
			
		}
		// Like condition_like
		if(!empty($condition_like))
		{   $k=1;
			foreach($condition_like as $key=>$val) {
				$this->db->like($key, $val);
				if($k>1) {
					$this->db->or_like($key, $val);
				}
				$k++;
			}
			
		}
		
		if(!empty($order_by))
		{ 
			foreach($order_by as $key=>$val) {
				$this->db->order_by($key, $val);
			}		
		}
        $query = $this->db->get($tbl_name);
		//echo $this->db->last_query(); //die;
        return $query->result_array();
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
		//echo $this->db->last_query(); //die;
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
	
	public function getSemesterArray($condition)
	{
		$this->db->select('tbl_credits.id,tbl_semester.title,tbl_semester.description');
		$this->db->join('tbl_semester', 'tbl_semester.id = tbl_credits.semester_id', 'left');
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db->where($key, $val);
			}
		}
		//$this->db->limit(1);
		$query = $this->db->get('tbl_credits');
		//echo $this->db->last_query();die;
		return $query->result_array();
		
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
	public function getDepartmentList($tbl_name='tbl_departments', $col = ' * ', $condition=null)
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
	* Function: getFullDepartmentList
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getFullDepartmentList($tbl_name='tbl_departments', $col = ' * ', $condition=null)
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
	* Function: getCommonIdArray
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
        $this->db->select($col);
        $this->db->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db->where($key, $val);
			}
			
		}
		if(!empty($order_by))
		{ 
			foreach($order_by as $key=>$val) {
				$this->db->order_by($key, $val);
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
	* Function: getCommonEmpListArray
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getCommonEmpListArray($tbl_name='tbl_employee_master', $col = ' * ', $condition=null, $order_by = NULL)
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
		if(!empty($order_by))
		{ 
			foreach($order_by as $key=>$val) {
				$this->db->order_by($key, $val);
			}
			
		}
		//$this->db->like('( is_hod="1" OR is_pc="1" )');
		//$this->db->order_by('id', 'asc');
        $query = $this->db->get($tbl_name);
		//echo $this->db->last_query(); //die;
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['id']] = $row;
		}
        return $results;
	}
	/*
	* Function: getCommonIdArray
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getCommonEmployeeArray($tbl_name='tbl_employee_master', $col = ' * ', $condition=null, $order_by = NULL)
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
		if(!empty($order_by))
		{ 
			foreach($order_by as $key=>$val) {
				$this->db->order_by($key, $val);
			}
			
		}
		$this->db->like('( is_hod="1" OR is_pc="1" )');
		//$this->db->order_by('id', 'asc');
        $query = $this->db->get($tbl_name);
		//echo $this->db->last_query(); //die;
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
        $this->db->select($col);
        $this->db->where('status', '1');
        $this->db->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db->where($key, $val);
			}
			
		}
		$query = $this->db->get($tbl_name);
		$results = array();
		$results = $query->row_array();
	    return $results;
	}
	

	public function getAllRecordscount($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL)
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
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$this->db->order_by($key, $val);
			}
		}
		if ($limit !== null && $start !== null) {
           $query = $this->db->get($tbl_name,$limit, $start);
        } else {
			$query = $this->db->get($tbl_name);
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
		$this->db->select('id,system_id');
		$this->db->where('is_deleted', '0');
		$this->db->where('status', '1');
		$this->db->where('system_id', $system_id);
		$query = $this->db->get('tbl_mentee');
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
		$this->db->select('id');
		$this->db->where('is_deleted', '0');
		$this->db->where('status', '1');
		$this->db->like('employee_id', $employee_id, 'before');
		$query = $this->db->get('tbl_admin');
		//echo $this->db->last_query(); die;
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
		$this->db->select('id,school_name, school_code');
		$this->db->where('is_deleted', '0');
		$this->db->where('status', '1');
		$this->db->where('school_name', $name_value);
		$query = $this->db->get('tbl_schools');
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
		$this->db->select('id,department_name');
		$this->db->where('is_deleted', '0');
		$this->db->where('status', '1');
		$this->db->where('department_name', $name_value);
		$query = $this->db->get('tbl_departments');
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

        /* Function :saverecords
        * Description :Used to save internship registration  data to Database 
        * Date: 2 june 2020
        * Created By: Divyansh Dixit
        */

    function saverecords($tbl_name='registration', $formArray)
	{
		$this->db->insert($tbl_name,$formArray);
		//echo $this->db->last_query();die;
		return true;
	}

	public function getIndentRecordsForDean($tbl_name = 'tbl_indents', $col = ' * ', $where_role= '' ,$condition='',$order_by='')
    {
        
        $this->db->select($col);
		$this->db->where('is_deleted', '0');
		if(!empty($where_role)){
		    $this->db->where($where_role);
		}
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db->where($key, $val);
			}
			
		}
		if(!empty($order_by))
		{ 
			foreach($order_by as $key=>$val) {
				$this->db->order_by($key, $val);
			}		
		}
        $query = $this->db->get($tbl_name);
		//echo $this->db->last_query();die;
        return $query->result_array();
    }

	public function getCommonJoinRecords($tbl_name,$col='*',$where_role='',$indentLevel='',$condition='',$order_by='')
	{		
			$this->db->select('tbl_indents.*,a.indentLevel,a.id as commentId,a.actionUserId,a.actionRoleId');
			//$this->db->select('tbl_indents.*,a.indentLevel');
			$this->db->join('tbl_comments a', 'tbl_indents.id = a.indent_id', 'left');
			$this->db->where('a.id=(select max(id) from tbl_comments b where a.indent_id=b.indent_id)');			
			if(!empty($where_role)){
			    $this->db->where($where_role);
			}
			if(!empty($indentLevel)){
			    //$this->db->where('a.id=(select max(id) from tbl_comments b where a.indent_id=b.indent_id)');			
			    $this->db->where_in('a.indentLevel',$indentLevel);
			}
			$this->db->where('tbl_indents.is_deleted', '0');
			if(!empty($condition))
		    { 
			    foreach($condition as $key=>$val) {
				    $this->db->where($key, $val);
			    }			
		    }
			if(!empty($order_by))
		    { 
			    foreach($order_by as $key=>$val) {
				    $this->db->order_by($key, $val);
			    }		
		    }
            $query = $this->db->get($tbl_name);
			//echo $this->db->last_query();die;
            return $query->result_array();
	}

	public function getMaxActionId($col='*',$indent_id='',$condition='')
	{		
			$this->db->select($col);
			$this->db->from('tbl_comments a');
			$this->db->where('a.indent_id', $indent_id);
			$this->db->where('a.id=(select max(id) from tbl_comments b where a.indent_id=b.indent_id)');			
			$this->db->where('a.is_deleted', '0');
			if(!empty($condition))
		    { 
			    foreach($condition as $key=>$val) {
				    $this->db->where($key, $val);
			    }			
		    }
            $query = $this->db->get();
			//echo $this->db->last_query();die;
            return $query->row_array();
	}
	
	/*
	* Function : getSingleSQLRecord
	* DB Connection : db2
	*
	*/
	public function getSingleSQLRecord($tbl_name, $col = ' * ', $condition=null, $order_by = NULL, $where_like=NULL, $where_like_key = 'id', $or_condition = NULL)
	{
		$otherdb = $this->load->database('db2', TRUE);
        $time = time();
        $otherdb->select($col);
        $otherdb->where('is_deleted', '0');
		if(!empty($where_like)) {
		$otherdb->like($where_like_key, $where_like);
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
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$otherdb->order_by($key, $val);
			}
		}
		$query = $otherdb->get($tbl_name);
		//echo $otherdb->last_query(); 
        return $query->row();
    }
	
	/*
	* Function : getSQLAllRecords
	* DB Connection : db2
	*
	*
	*/
	public function getSQLAllRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL, $or_condition = NULL)
    {
		$otherdb = $this->load->database('db2', TRUE);
        $time = time();
        $otherdb->select($col);
        $otherdb->where('is_deleted', '0');
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
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$otherdb->order_by($key, $val);
			}
		}
		if ($limit !== null && $start !== null) {
           $query = $otherdb->get($tbl_name,$limit, $start);
        } else {
			$query = $otherdb->get($tbl_name);
		}
		//echo $this->db->last_query(); die;
		return $query->result();
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
		$otherdb = $this->load->database('db2', TRUE);
		$otherdb->insert($tbl_name, $post);
		//echo $this->db->last_query(); die;
		return $otherdb->insert_id();
    }
	/*
	* Function : Sqlupdateinfo
	*
	* DB Connection : db2
	*
	*
	*/
	public function Sqlupdateinfo($tbl_name='', $post, $field, $value)
    {
		$otherdb = $this->load->database('db2', TRUE);
		$otherdb->where($field, $value);
        if (!$otherdb->update($tbl_name, $post)) {
            log_message('error', print_r($this->db->error(), true));
        }
		//echo $this->db->last_query(); die;
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

	/*
	* Function : getKeyValueRecordsArray
	* DB Connection : db
	*/
	public function getKeyValueRecordsArray($tbl_name='tbl_schools', $col = ' * ', $condition=null)
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
		//$otherdb->order_by('id', 'asc');
        $query = $this->db->get($tbl_name);
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['id']] = $row;
		}
        return $results;
	}
	/*
	* Function : SqlgetSingleRecord
	* DB Connection : db2
	*/
	public function SqlgetCommonIdArray($tbl_name='tbl_school_master', $col = ' * ', $condition=null)
    {
		$otherdb = $this->load->database('db2', TRUE);
        $time = time();
        $otherdb->select($col);
        $otherdb->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$otherdb->where($key, $val);
			}
			
		}
		//$otherdb->order_by('id', 'asc');
        $query = $otherdb->get($tbl_name);
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['id']] = $row;
		}
        return $results;
	}

	/*
	* Function : SqlgetCommonQuery
	* DB Connection : db2
	*/
	
	public function SqlgetCommonQuery($tbl_name = 'su_schools', $col = ' * ', $condition='',$order_by='')
    {
		$otherdb = $this->load->database('db2', TRUE);
        $otherdb->select($col);
		$otherdb->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$otherdb->where($key, $val);
			}
			
		}
		if(!empty($order_by))
		{ 
			foreach($order_by as $key=>$val) {
				$otherdb->order_by($key, $val);
			}		
		}
        $query = $otherdb->get($tbl_name);
        return $query->result_array();
    }
	

}