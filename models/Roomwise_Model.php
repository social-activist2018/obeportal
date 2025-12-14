<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Class Roomwise_Model extends CI_Model{
	
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
	* Function : getRoomdetailsbydept
	*/	
	public function getRoomdetailsbydept($school_id, $department_id) 
	{
		$dbreport = $this->load->database('dbreport', TRUE);
		$resutls = array();
		if($school_id>0 && $department_id>0){
			$where = '';
			
			if($semester_id){
				//$where = ' AND ds.semester_id='.$semester_id;
			}
			$sql = "select ds.*,rs.room_sharing, rs.room_number, rs.title,rs.id as room_id from tbl_deroomassignment ds JOIN tbl_block_room_master rs ON ds.room_id=rs.id  WHERE rs.status='1' AND ds.`department_id` ='".$department_id."' AND ds.`academic_year_id` ='".ACADEMIC_ID."' $where order by rs.room_number DESC";
			$query = $dbreport->query($sql);
			$resutls = $query->result_array();
		}
		return $resutls;
	}
	/*
	* Function : getSingleRecord
	*/
	public function getSingleRecord($tbl_name, $col = ' * ', $condition=null , $type='', $order_by = array('id'=>'desc'))
	{
        $time = time();
		$dbreport = $this->load->database('dbreport', TRUE);
        $dbreport->select($col);
       // $this->db->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$dbreport->where($key, $val);
			}
			
		}
				
		$query = $dbreport->get($tbl_name);
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$dbreport->order_by($key, $val);
			}
		}
		
		//echo $dbreport->last_query(); die;
		if($type){
			return $query->row();
		} else {
			return $query->row_array();
		}
    }
	
	/*
	* Function : SqlgetSingleRecord
	* DB Connection : db2
	*/
	public function SqlgetCommonIdArray($tbl_name='tbl_school_master', $col = ' * ', $condition=null)
    {
		$otherdb = $this->load->database('dbreport', TRUE);
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
	* Function : getDeptRoomWiseSlotdetails  
	*/	
	public function getDeptRoomWiseSlotdetails($department_id, $academic_year_id='3') 
	{
		$resutls = array();
		$dbreport = $this->load->database('dbreport', TRUE);
		$sql = "select ds.*, rs.room_number, rs.slot_id ,rs.class_number,ts.version_name from tbl_department_course_slot_assignment ds JOIN tbl_timetable_management ts ON ts.id=ds.tt_version_id JOIN tbl_assign_room_slot_section rs ON ds.id=rs.dept_course_id  WHERE rs.status='1' AND ts.status='1' AND ds.status='1' AND rs.is_deleted='0' AND ds.`department_id` ='".$department_id."' AND ds.`academic_year_id` ='".$academic_year_id."' order by rs.room_number DESC"; 
		$query = $dbreport->query($sql);
		$resutls = $query->result_array();
		
		return $resutls;
	}
	
	
	/*
	* Function : getRoomWiseSlotdetails
	*/	
	public function getRoomWiseSlotdetails($room_id, $academic_year_id='3') 
	{
		$resutls = array();
		$dbreport = $this->load->database('dbreport', TRUE);
		if($room_id>0){  
			
			$sql = "select ds.*, rs.room_number,rs.assign_slot_type, rs.slot_id ,rs.class_number,ts.version_name from tbl_department_course_slot_assignment ds JOIN tbl_timetable_management ts ON ts.id=ds.tt_version_id JOIN tbl_assign_room_slot_section rs ON ds.id=rs.dept_course_id  WHERE rs.status='1' AND ts.status='1' AND ds.status='1' AND rs.is_deleted='0' AND rs.`room_number` ='".$room_id."' AND ds.`academic_year_id` ='".$academic_year_id."' order by rs.room_number DESC"; 
			$query = $dbreport->query($sql);
			$resutls = $query->result_array();
		}
		return $resutls;
	}
	
	/*
	* Function : getAllotedRoomDetails
	*/	
	public function getAllotedRoomDetails($recordsArray) 
	{
		$resutls = array();
		$dbreport = $this->load->database('dbreport', TRUE);
		if($recordsArray['department_id']>0){
			$where = '';
			$department_id = $recordsArray['department_id'];
			$semester_id = $recordsArray['semester_id'];
			$academic_year_id = $recordsArray['academic_year_id'];
			if($semester_id){
				//$where = ' AND ds.semester_id='.$semester_id;
			}
			$sql = "select ds.*, rs.room_number, rs.title,rs.id as room_id from tbl_deroomassignment ds JOIN tbl_block_room_master rs ON ds.room_id=rs.id  WHERE rs.status='1' AND ds.`department_id` ='".$department_id."' $where AND academic_year_id='".$academic_year_id."' order by rs.room_number DESC";
			$query = $dbreport->query($sql);
			$resutls = $query->result_array();
		}
		return $resutls;
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
	

}