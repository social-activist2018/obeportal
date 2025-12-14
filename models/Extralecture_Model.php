<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Class Extralecture_Model extends CI_Model{
	
	private $db2;
	public function __construct()
	{
		parent::__construct();
		$this->db2 = $this->load->database('db2', TRUE);
	}
	
	/*
	* Function : getAllAvailableSlotArray
	* DESC:
	* Createdby: Amit Verma
	*/
	public function getAllAvailableSlotArray($lecture_type='', $roomIds=null, $classnNbr=null, $slot_id='', $semester_id='',$is_medical='0', $academic_year_id='3')
	{
		
		$results = array();
		if($lecture_type>0 && $roomIds>0) {
			$sharedRoomArray = '';
			if($academic_year_id>0) {
				$sharedRoomArray = $this->getSharedRoomDetails($academic_year_id);
			}
				
			if(!empty($slot_id)) {
				$sql = "select id, display_name,assigned_periods FROM tbl_slot_master where id IN( SELECT slot_id FROM tbl_assign_room_slot_section WHERE lecture_type='".$lecture_type."' AND room_number = '".$roomIds."' AND slot_id NOT IN($slot_id) AND semester_id!='".$semester_id."' AND status='1') AND lecture_type='".$lecture_type."' AND is_medical='".$is_medical."' AND tbl_slot_master.is_deleted='0' AND tbl_slot_master.academic_year_id='".$academic_year_id."'";
			} else {
				$where_in = " ";
				if(!in_array($roomIds, $sharedRoomArray)) {
					$where_in = " id NOT IN( SELECT slot_id FROM tbl_assign_room_slot_section WHERE lecture_type='".$lecture_type."' AND room_number = '".$roomIds."' AND semester_id!='".$semester_id."' AND tbl_assign_room_slot_section.is_deleted='0' AND tbl_assign_room_slot_section.status='1' ) AND ";
				}
				$sql = "select id, display_name,assigned_periods FROM tbl_slot_master where $where_in lecture_type='".$lecture_type."' AND is_medical='".$is_medical."' AND tbl_slot_master.is_deleted='0' AND tbl_slot_master.academic_year_id='".$academic_year_id."'";
			}
			
		} else {
			$sharedRoomArray = '';
			if($academic_year_id>0) {
				$sharedRoomArray = $this->getSharedRoomDetails($academic_year_id);
			}
			if(!empty($slot_id)) {
				$sql = "select id, display_name,assigned_periods FROM tbl_slot_master where id IN( SELECT slot_id FROM tbl_assign_room_slot_section WHERE  room_number = '".$roomIds."' AND slot_id NOT IN($slot_id) AND semester_id!='".$semester_id."' AND status='1') AND is_medical='".$is_medical."' AND tbl_slot_master.is_deleted='0' AND tbl_slot_master.academic_year_id='".$academic_year_id."'";
			} else {
				$where_in = " ";
				if(!in_array($roomIds, $sharedRoomArray)) {
					$where_in = " id NOT IN( SELECT slot_id FROM tbl_assign_room_slot_section WHERE room_number = '".$roomIds."' AND semester_id!='".$semester_id."' AND tbl_assign_room_slot_section.is_deleted='0' AND tbl_assign_room_slot_section.status='1' ) AND ";
				}
				$sql = "select id, display_name,assigned_periods FROM tbl_slot_master where $where_in  is_medical='".$is_medical."' AND tbl_slot_master.is_deleted='0' AND tbl_slot_master.academic_year_id='".$academic_year_id."'";
			}
		}
			$query = $this->db2->query($sql);
			//echo $this->db2->last_query();die;   
			$results =  $query->result_array();
			
		
		
		return $results;
	}
	
	/*
	* Function : getAllAvailableRoomSlotArray
	* DESC:
	* Createdby: Amit Verma
	*/
	
	public function getAllAvailableRoomSlotArray($roomIds=null, $slot_id=null,$ACADEMIC_ID = ACADEMIC_ID)
	{
		$results = array();
		
		if($roomIds>0 && $slot_id>0) {
			$sql = "SELECT slot_id FROM tbl_assign_room_slot_section ss JOIN tbl_department_course_slot_assignment ds ON ds.id=ss.dept_course_id WHERE ss.room_number = '".$roomIds."' AND ss.slot_id = '".$slot_id."' AND ds.academic_year_id='".$ACADEMIC_ID."' AND ss.status='1'  AND ss.is_deleted='0' AND ds.status='1'";
			$query = $this->db2->query($sql);
			//echo $this->db->last_query();die;   
			$results =  $query->row_array();
		}
		
		return $results;
	}
	
	public function getAllExtraAvailablePIRoomVersionSlotArray($roomIds=null, $slot_id=null, $course_pi=null, $class_nbr=null,$ACADEMIC_ID =ACADEMIC_ID)
	{
		$results = array();
		
		if($roomIds>0 && $slot_id>0) {
			
			$sharedRoomArray = $this->getSharedRoomDetails($ACADEMIC_ID);
			$where_in = '';
			if(!in_array($roomIds,$sharedRoomArray)) {
				$where_in = "ss.room_number = '".$roomIds."' AND ";
			} 
			if($course_pi) {
				$where_inc = "AND ss.course_pi='".$course_pi."'";
			}
			$sql = "SELECT slot_id FROM tbl_assign_room_slot_section ss JOIN tbl_department_course_slot_assignment ds ON ds.id=ss.dept_course_id JOIN tbl_timetable_management tm ON tm.id=ds.tt_version_id WHERE ss.room_number = '".$roomIds."' AND ss.slot_id = '".$slot_id."' AND ds.academic_year_id='".$ACADEMIC_ID."' $where_inc AND tm.status='1'  AND ss.status='1'  AND ss.is_deleted='0'  AND ds.status='1' ";
			$query = $this->db2->query($sql);
			//echo $this->db2->last_query(); //die;   
			$results =  $query->row_array();
		}
		
		return $results;
	}
	
	/* Function : getAllAvailableSlotByPIArray
	* DESC:
	* Createdby: Amit Verma
	*/
	public function getAllAvailableSlotByPIArray($lecture_type='', $roomIds=null, $classnNbr=null, $slot_id='', $semester_id='', $course_id='', $course_pi='',$is_medical='0',$academic_year_id='4')
	{
			$results = array();

			if($course_id>0 && $roomIds>0 && $course_pi>0) {
				$sql = "select id, display_name,assigned_periods FROM tbl_slot_master where id IN( SELECT slot_id FROM tbl_assign_room_slot_section WHERE lecture_type='".$lecture_type."' AND room_number = '".$roomIds."'  AND course_id='".$course_id."' AND course_pi='".$course_pi."' AND tbl_assign_room_slot_section.status='1' ) AND lecture_type='".$lecture_type."' AND is_medical='".$is_medical."' AND tbl_slot_master.is_deleted='0' AND tbl_slot_master.is_deleted='0' AND tbl_slot_master.academic_year_id='".$academic_year_id."'";
				$query = $this->db2->query($sql);
				//echo $this->db->last_query();die;   
				$results =  $query->result_array();
			}
			
		return $results;
	}
	
	
	/*
	* Function : getSharedRoomDetails
	*/	
	public function getSharedRoomDetails($academic_year_id) 
	{
		$resutls = array();
		if($academic_year_id>0){
			
			$sql = "select id, title,room_number,room_sharing from tbl_block_room_master WHERE room_sharing='1' AND racademic_year_id='".$academic_year_id."'";
			$query = $this->db2->query($sql);
			$resutl = $query->result_array();
			foreach($resutl as $row){
				$resutls[$row['id']] = $row['id'];
			}
		}
		return $resutls;
	}
	
	/*
	* Function : softDeleteRecords
	* Description : Delete Record
	* Not Used
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
	* Function : getAllRecords
	*/
	public function getAllRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL, $likeSearch=NULL, $where_in = NULL)
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
		if(!empty($where_in)){
			$dbreport->where_in('program_id', $where_in);
		}
		// Like condition_like
		if(!empty($likeSearch))
		{   $k=1;
			$multiLike = array();
			foreach($likeSearch as $key=>$val) {
				$str = "";
				$str = str_replace(" ","|", $val);
				$multiLike[] = " $key rlike '".$str."' ";
			}
			$lwhere = '';
			$lwhere = implode(' OR ', $multiLike);
			$likewhere = '( '.$lwhere.' )';
			$dbreport->where($likewhere);
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
	* Function : getAllRecordsWhereIn
	*/
	public function getAllRecordsWhereIn($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL, $likeSearch=NULL, $where_in = NULL)
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
		if(!empty($where_in)){
			$this->db2->where_in('id', $where_in);
		}
		// Like condition_like
		if(!empty($likeSearch))
		{   $k=1;
			$multiLike = array();
			foreach($likeSearch as $key=>$val) {
				$str = "";
				$str = str_replace(" ","|", $val);
				$multiLike[] = " $key rlike '".$str."' ";
			}
			$lwhere = '';
			$lwhere = implode(' OR ', $multiLike);
			$likewhere = '( '.$lwhere.' )';
			$this->db2->where($likewhere);
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
	
	// Function for deletion
	public function deleterecords($tbl_name, $id){
		if($tbl_name!='' && $id>0){
			$sql_query=$this->db->where('id', $id)->delete($tbl_name);
		}
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
		//echo $this->db2->last_query(); die;
        return $query->result_array();
    }
	
	/*
	* Function : getCommonPMArray
	*/
	public function getCommonPMArray($tbl_name='tbl_course_papersetter_moderator', $cond = '')
	{
		if(!empty($cond)) {
			foreach($cond as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		$query = $this->db2->select("*")->get($tbl_name);
		//echo $this->db2->last_query(); die;
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
		$dbreport = $this->load->database('dbreport', TRUE);
        $dbreport->select($col);
       // $this->db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$dbreport->where($key, $val);
			}
			
		}
				
		$query = $dbreport->get($tbl_name);
		//echo $dbreport->last_query(); die;
		if($type){
			return $query->row();
		} else {
			return $query->row_array();
		}
    }
	/*
	* Function : getAllFullAssignmentWithVSLotsRecords
	*/
	public function getAllFullAssignmentWithVSLotsRecords($condition='', $where_in = '')
	{
		//$this->db2->order_by('tbl_create_counselling.createdon', 'asc');
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		
		if(!empty($where_in)){
			$this->db2->where_in('tbl_course.id', $where_in);
		}
		$this->db2->where('tbl_department_course_slot_assignment.status', '1');
		$this->db2->where('tbl_assign_room_slot_section.status', '1');
		$query = $this->db2->join('tbl_assign_room_slot_section', 'tbl_assign_room_slot_section.dept_course_id = tbl_department_course_slot_assignment.id');
		$query = $this->db2->join('tbl_timetable_management', 'tbl_timetable_management.id = tbl_department_course_slot_assignment.tt_version_id');
		$query = $this->db2->join('tbl_course', 'tbl_course.id = tbl_department_course_slot_assignment.course_id');
		$this->db2->distinct();
		$query = $this->db2->select("tbl_department_course_slot_assignment.*,`tbl_course`.`catalog_nbr`, `tbl_course`.`course_title`, `tbl_course`.`catalog_nbr`,tbl_course.cos_count,tbl_course.subject_area,tbl_course.lecture,tbl_course.tutorial,tbl_course.practical,tbl_assign_room_slot_section.course_pi, tbl_assign_room_slot_section.id as allotment_id")->get('tbl_department_course_slot_assignment');
		//echo $this->db2->last_query();die;
		return $query->result_array();
		
	}
	/*
	* Function : getAllFullAssignmentSLotsRecords
	*/
	public function getAllFullAssignmentSLotsRecords($condition='', $where_in = '')
	{
		//$this->db2->order_by('tbl_create_counselling.createdon', 'asc');
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		
		if(!empty($where_in)){
			$this->db2->where_in('tbl_course.id', $where_in);
		}
		$this->db2->where('tbl_department_course_slot_assignment.status', '1');
		$query = $this->db2->join('tbl_assign_room_slot_section', 'tbl_assign_room_slot_section.dept_course_id = tbl_department_course_slot_assignment.id');
		$query = $this->db2->join('tbl_course', 'tbl_course.id = tbl_department_course_slot_assignment.course_id');
		$this->db2->distinct();
		$query = $this->db2->select("tbl_department_course_slot_assignment.*,`tbl_course`.`catalog_nbr`, `tbl_course`.`course_title`, `tbl_course`.`catalog_nbr`,tbl_course.cos_count,tbl_course.subject_area,tbl_course.lecture,tbl_course.tutorial,tbl_course.practical,tbl_assign_room_slot_section.course_pi")->get('tbl_department_course_slot_assignment');
		//echo $this->db2->last_query();die;
		return $query->result_array();
		
	}
	/*
	* Function : getAllActivePISlotsRecords
	*/
	public function getAllActivePISlotsRecords($condition='')
	{
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		$this->db2->where('tbl_assign_room_slot_section.status', '1');
		$this->db2->where('tbl_slot_master.status', '1');
		//$this->db2->where('tbl_block_room_master.status', '1');
		$query = $this->db2->join('tbl_slot_master', 'tbl_slot_master.id = tbl_assign_room_slot_section.slot_id');
		//$query = $this->db2->join('tbl_block_room_master', 'tbl_block_room_master.id = tbl_assign_room_slot_section.room_number');
		$this->db2->distinct();
		$query = $this->db2->select("tbl_assign_room_slot_section.slot_id,tbl_assign_room_slot_section.course_id,tbl_assign_room_slot_section.course_pi,tbl_assign_room_slot_section.room_number,tbl_slot_master.slot_name,tbl_slot_master.assigned_periods")->get('tbl_assign_room_slot_section');
		#echo $this->db2->last_query();die;       
		return $query->result_array();   
		
	}
	
	/*
	* Function : getAllActivePIReportSlotsRecords
	*/
	public function getAllActivePIReportSlotsRecords($condition='')
	{
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		$this->db2->where('tbl_assign_room_slot_section.status', '1');
		$this->db2->where('tbl_slot_master.status', '1');
		$this->db2->where('tbl_timetable_management.status', '1');
		$query = $this->db2->join('tbl_department_course_slot_assignment', 'tbl_department_course_slot_assignment.id = tbl_assign_room_slot_section.dept_course_id');
		$query = $this->db2->join('tbl_timetable_management', 'tbl_timetable_management.id = tbl_department_course_slot_assignment.tt_version_id');
		$query = $this->db2->join('tbl_slot_master', 'tbl_slot_master.id = tbl_assign_room_slot_section.slot_id');
		//$query = $this->db2->join('tbl_block_room_master', 'tbl_block_room_master.id = tbl_assign_room_slot_section.room_number');
		$query = $this->db2->select("tbl_assign_room_slot_section.slot_id,tbl_assign_room_slot_section.section,tbl_assign_room_slot_section.class_number,tbl_assign_room_slot_section.course_id,tbl_assign_room_slot_section.course_pi,tbl_assign_room_slot_section.room_number,tbl_slot_master.slot_name,tbl_slot_master.assigned_periods")->get('tbl_assign_room_slot_section');
		//echo $this->db2->last_query();die;       
		return $query->result_array();   
		
	}
	
	/*
	* Function : getAllActiveClassNumberRecords
	*/
	public function getAllActiveClassNumberRecords($condition='')
	{
		//$this->db2->order_by('tbl_create_counselling.createdon', 'asc');
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		$this->db2->where('tbl_department_course_slot_assignment.status', '1');
		$query = $this->db2->join('tbl_assign_room_slot_section', 'tbl_assign_room_slot_section.dept_course_id = tbl_department_course_slot_assignment.id', 'INNER');
		$this->db2->distinct();
		$query = $this->db2->select("tbl_assign_room_slot_section.class_number,tbl_assign_room_slot_section.section,tbl_department_course_slot_assignment.*")->get('tbl_department_course_slot_assignment');
		//echo $this->db2->last_query();die;       
		return $query->result_array();
		
	}
	/*
	* Function : getAssignedSlotsRecords
	*/
	public function getAssignedSlotsRecords($condition='')
	{
		//$this->db2->order_by('tbl_create_counselling.createdon', 'asc');
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		
		$this->db2->where('tbl_assign_room_slot_section.status', '1');
		$this->db2->where('tbl_assign_room_slot_section.is_deleted', '0');
		$this->db2->where('tbl_slot_master.status', '1');
		$this->db2->where('tbl_block_room_master.status', '1');
		$query = $this->db2->join('tbl_slot_master', 'tbl_slot_master.id = tbl_assign_room_slot_section.slot_id');
		$query = $this->db2->join('tbl_block_room_master', 'tbl_block_room_master.id = tbl_assign_room_slot_section.room_number');
		//$query = $this->db2->join('tbl_academic_block_master', 'tbl_academic_block_master.id = tbl_block_room_master.block_id');
		$query = $this->db2->join('tbl_employee_master', 'tbl_employee_master.employee_id = tbl_assign_room_slot_section.course_pi');
		$this->db2->distinct();
		$query = $this->db2->select("tbl_employee_master.full_name, tbl_employee_master.phone, tbl_assign_room_slot_section.*, tbl_slot_master.display_name as slot_name,tbl_slot_master.assigned_periods, tbl_block_room_master.room_number as room_no, tbl_block_room_master.capacity, tbl_block_room_master.title, tbl_block_room_master.block_id")->get('tbl_assign_room_slot_section');
		//echo $this->db2->last_query();die;
		$records = $query->result_array();
		$response = array();
	    foreach($records as $row) {
			$response[$row['dept_course_id']][] = $row;
		}	
		return $response;
	}
	/*
	* Function : getAllSemesterRecords
	*/
	public function getAllSemesterRecords($condition='')
	{
		$dbreport = $this->load->database('dbreport', TRUE);
		//$this->db2->order_by('tbl_create_counselling.createdon', 'asc');
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$dbreport->where($key, $val);
			}
		}
		
		$dbreport->where('tbl_credits.status', '1');
		$query = $dbreport->join('tbl_semester', 'tbl_semester.id = tbl_credits.semester_id');
		$dbreport->distinct();
		$query = $dbreport->select("tbl_credits.*,`tbl_semester`.`title`")->get('tbl_credits');
		//echo $dbreport->last_query();die;
		$records = $query->result_array();
		$response = array();
	    foreach($records as $row) {
			$response[$row['id']] = $row;
		}	
		return $response;
	}
	
	/*
	* Function : saveinfo
	*/
	
	public function saveinfo($tbl_name='', $post)
    {
		$this->db->insert($tbl_name, $post);
		//echo $this->db->last_query(); die;
		return $this->db->insert_id();
    }
	
	/*
	* Function: getSemesterArray
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	
	public function getSemesterArray($condition, $hodProgramList='')
	{
		$this->db2->select('tbl_credits.id,tbl_semester.title,tbl_semester.description');
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
	public function getFullDepartmentList($tbl_name='tbl_departments', $col = ' * ', $condition=null)
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
		//$dbreport->order_by('id', 'asc');
        $query = $dbreport->get($tbl_name);
		$results = array();
		//echo $dbreport->last_query(); die;
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
	* Function : updateinfo
	*
	* DB Connection : db2
	*
	*
	*/
	public function updateinfo($tbl_name='', $post, $field, $value)
    {
		$this->db->where($field, $value);
        if (!$this->db->update($tbl_name, $post)) {
            log_message('error', print_r($this->db->error(), true));
        }
		//echo $this->db2->last_query(); die;
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
       // $this->db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$otherdb->where($key, $val);
			}
			
		}
		$query = $otherdb->get($tbl_name);
		//echo $this->db2->last_query(); die;
        return $query->row_array();
    }

	/*
	* Function : getKeyValueRecordsArray
	* DB Connection : db
	*/
	public function getKeyValueRecordsArray($tbl_name='tbl_schools', $col = ' * ', $condition=null)
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
		//$otherdb->order_by('id', 'asc');
        $query = $this->db2->get($tbl_name);
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
	public function SqlgetCommonIdArray($tbl_name='tbl_school_master', $col = ' * ', $condition=null, $where_like=null)
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
		
		if(!empty($where_like)) {
			foreach($where_like as $key=>$val) {
				$otherdb->like($key, $val);
			}
		}
		
		//$otherdb->order_by('id', 'asc');
        $query = $otherdb->get($tbl_name);
	#	echo $otherdb->last_query(); //die;
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

	
	function getPeoplesoftCourseSections($condArray)
	{
		$tbl_name  = 'PS_S_PRD_TT_PI_VW';
		$resultsArray = '';
		
		if (!empty($condArray)) {
			$post = [
				'username' => 'ATTEST',
				'password' => 'TFsgt^I8',
				'num_rows' => '300',		 
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
				'num_rows' => '300',		 
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
	* Function: alternativearrangement
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getextralectureAlterArray($tbl_name='tbl_alternativearrangement_master', $col = ' * ', $condition=null, $order_by=null)
    {
        $time = time();
        $this->db2->select($col);
        $this->db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				if($key=='old_course_pi'){
				  $this->db2->where("( old_course_pi = '".$val."' OR employee_id = '".$val."')");
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
		
		
		//$this->db2->order_by('id', 'asc');
        $query = $this->db2->get($tbl_name);
		$results = array();
		//echo $this->db2->last_query(); die;
		return $results = $query->result_array();
	}
	/**
	* Get All topic lists
	*/
	public function getCustomSlotsExtraAttotmentArray($conditions='', $betweenDate='') 
	{
		$resutls = array();

		if($conditions) {
			foreach($conditions as $key=>$val){
				$this->db2->where($key,$val);
			}
		}
		if(!empty($betweenDate))
		{
			$filter_from = date('Y-m-d',strtotime($betweenDate['from_date']));
			$fromOne = "'".$filter_from."' BETWEEN from_date AND to_date";
			$this->db2->where("$fromOne");
		}
		
		$query = $this->db2->join('tbl_department_course_slot_assignment', 'tbl_assign_room_slot_section.dept_course_id = tbl_department_course_slot_assignment.id');
		$query = $this->db2->join('tbl_course', 'tbl_course.id = tbl_department_course_slot_assignment.course_id');
		$query = $this->db2->join('tbl_timetable_management', 'tbl_timetable_management.id = tbl_department_course_slot_assignment.tt_version_id');
		$this->db2->distinct();
		$query = $this->db2->select("tbl_timetable_management.academic_year_id, tbl_timetable_management.school_id, tbl_course.course_title, tbl_course.catalog_nbr,tbl_course.lecture, tbl_course.ssr_component, tbl_assign_room_slot_section.room_number,course_pi, tbl_assign_room_slot_section.slot_id, tbl_assign_room_slot_section.class_number, tbl_assign_room_slot_section.section, tbl_assign_room_slot_section.course_pi, tbl_assign_room_slot_section.course_id, tbl_assign_room_slot_section.id, tbl_assign_room_slot_section.semester_id, tbl_assign_room_slot_section.dept_course_id")->get('tbl_assign_room_slot_section');
		//echo $this->db2->last_query();die;
		$resutls = $query->result_array();
		return $resutls;
	}   
	/**
	* Get All topic lists
	*/
	public function getDeptAssignedRoomListArray($conditions='') 
	{
		$resutls = array();

		if($conditions) {
			foreach($conditions as $key=>$val){
				$this->db2->where($key,$val);
			}
		}
		
		$query = $this->db2->join('tbl_block_room_master', 'tbl_block_room_master.id = tbl_deroomassignment.room_id');
		$this->db2->distinct();
		$query = $this->db2->select("tbl_deroomassignment.academic_year_id,tbl_block_room_master.*")->get('tbl_deroomassignment');
		#echo $this->db2->last_query();die;
		foreach($query->result_array() as $row) {
			$resutls[$row['id']] = $row;
		}
		return $resutls;
	}   
		
}