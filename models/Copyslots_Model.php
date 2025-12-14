<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Class Copyslots_Model extends CI_Model{
	private $db2;
	public function __construct()
	{
		parent::__construct();
		$this->db2 = $this->load->database('db2', TRUE);
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
		//echo $this->db->last_query(); die;
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['id']] = $row;
		}
        return $results;
	}
	
	public function getAllSemesterArray($condition='',$id='')
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
	* Function : getAllActiveClassNumberRecords
	*/
	public function getAllActiveClassNumberRecords($condition='')
	{
		//$this->db->order_by('tbl_create_counselling.createdon', 'asc');
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
		//echo $this->db->last_query();die;
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
		//echo $this->db->last_query();die;
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
		//echo $this->db->last_query(); die;
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
		//echo $this->db->last_query(); die;
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['id']] = $row;
		}
        return $results;
	}
	
	/*
	* Function : getSingleRecord
	*/
	public function getSingleRecord($tbl_name, $col = ' * ', $condition=null, $order_by = NULL, $where_like=NULL, $where_like_key = 'id')
	{
        $time = time();
		$dbreport = $this->load->database('dbreport', TRUE);
        $dbreport->select($col);
        $dbreport->where('is_deleted', '0');
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
	* Function : getAllActivePISlotsRecords
	*/
	public function getAllActivePISlotsRecords($condition='', $where_like='')
	{
		$dbreport = $this->load->database('dbreport', TRUE);
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$dbreport->where($key, $val);
			}
		}
		if(!empty($where_like)) {
			$dbreport->like('assigned_periods', $where_like);
		}
		$dbreport->where('tbl_assign_room_slot_section.status', '1');
		$dbreport->where('tbl_timetable_management.status', '1');
		$dbreport->where('tbl_department_course_slot_assignment.status', '1');
		$dbreport->where('tbl_slot_master.status', '1');
		$query = $dbreport->join('tbl_timetable_management', 'tbl_timetable_management.id = tbl_department_course_slot_assignment.tt_version_id');
		$query = $dbreport->join('tbl_assign_room_slot_section', 'tbl_assign_room_slot_section.dept_course_id = tbl_department_course_slot_assignment.id');
		$query = $dbreport->join('tbl_slot_master', 'tbl_slot_master.id = tbl_assign_room_slot_section.slot_id');
		
		$query = $dbreport->select("tbl_department_course_slot_assignment.academic_year_id,version_name,tbl_assign_room_slot_section.*,tbl_slot_master.assigned_periods,tbl_slot_master.slot_name,tbl_slot_master.lecture_type,tt_version_id")->get('tbl_department_course_slot_assignment');
		//echo $dbreport->last_query();die;       
		return $query->result_array();   
	}
	/*
	* Function: getCommonIdLikeArray
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getCommonIdLikeArray($tbl_name='tbl_schools', $col = ' * ', $condition=null, $order_by=null, $where_like=null)
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
		
		if(!empty($where_like)) {
			foreach($where_like as $key=>$val) {
			 $dbreport->like($key, $val);
			}
			
		}
		
        $query = $dbreport->get($tbl_name);
		//echo $this->db->last_query(); die;
		$results = array();
		foreach($query->result_array() as $row) {
			$results[] = $row;
		}
        return $results;
	}
	
	
}