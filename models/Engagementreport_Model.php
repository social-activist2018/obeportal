<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Class Engagementreport_Model extends CI_Model{
	
	private $db2;
	public function __construct()
	{
		parent::__construct();
		$this->db2 = $this->load->database('db2', TRUE);
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
	
	
	// Function for deletion
	public function deleterecords($tbl_name, $id){
		if($tbl_name!='' && $id>0){
			$sql_query=$this->db2->where('id', $id)->delete($tbl_name);
		}
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
	
	public function getAllSemesterArray($condition='')
	{
		
		$this->db2->select('tbl_semester.title, tbl_semester.psoft_name, tbl_credits.id,tbl_credits.academic_id,tbl_credits.program_type, tbl_credits.program_id,tbl_credits.status ');
		$this->db2->join('tbl_semester', 'tbl_semester.id = tbl_credits.semester_id', 'left');
		if($id>0){
			$this->db2->where('tbl_credits.id', $id);
			$this->db2->limit(1);
		}
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
		$query = $this->db2->get('tbl_credits');
		//echo $this->db2->last_query();die;
		return $query->result_array();
		//"SELECT * FROM `tbl_semester` as sem JOIN tbl_credits as crd ON sem.id=crd.semester_id where crd.status='1' AND crd.academic_id='3' AND program_id="" AND program_type="";"
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
	
	
	
	/**
	* Get All topic lists
	*/
	public function getCustomAllMyRecordsReports_prev($conditions='') 
	{
		$resutls = array();
		$where = '';
		if($conditions) {
			foreach($conditions as $key=>$val){
				$where .= ' AND '.$key.'= "'.$val.'"';
			}
		}
		$sql = 'SELECT 
			dcs.academic_year_id,
			dcs.school_id,
			dcs.department_id,
			em.full_name,
			GROUP_CONCAT(CONCAT(sm.slot_name, \'::\', sm.assigned_periods, \'\') ORDER BY sm.slot_name SEPARATOR \'; \') AS slot_details,
			ars.course_pi,
			ars.assign_slot_type,
			ars.semester_id
		FROM 
			tbl_assign_room_slot_section ars
		LEFT JOIN 
			tbl_department_course_slot_assignment dcs ON ars.dept_course_id = dcs.id
		LEFT JOIN 
			tbl_timetable_management tm ON tm.id = dcs.tt_version_id	
		JOIN 
			tbl_employee_master em ON em.employee_id = ars.course_pi
		JOIN 
			tbl_slot_master sm ON sm.id = ars.slot_id
		WHERE 
		  ars.status = "1"
		  AND tm.status = "1"
		  AND dcs.status = "1"
		   '.$where.'
		GROUP BY 
			em.employee_id
		';
		
		$query = $this->db2->query($sql);
		$resutls = $query->result_array();
		return $resutls;
	}
	/**
	* Get All topic lists
	*/
	public function getCustomAllMyRecordsReports($school_id='',$department_id='',$academic_year_id='') 
	{
		$resutls = array();
		$sql = 'SELECT 
			dcs.academic_year_id, 
			dcs.school_id, 
			dcs.department_id, 
			em.full_name, 
			GROUP_CONCAT(
				CONCAT(sm.slot_name, \'::\', sm.assigned_periods, \'\') 
				ORDER BY sm.slot_name SEPARATOR \'; \'
			) AS slot_details, 
			ars.course_pi, 
			ars.assign_slot_type, 
			ars.semester_id 
		FROM 
			tbl_assign_room_slot_section ars 
		LEFT JOIN 
			tbl_department_course_slot_assignment dcs ON ars.dept_course_id = dcs.id 
		LEFT JOIN 
			tbl_timetable_management tm ON tm.id = dcs.tt_version_id 
		JOIN 
			tbl_employee_master em ON em.employee_id = ars.course_pi 
		JOIN 
			tbl_slot_master sm ON sm.id = ars.slot_id 
		WHERE 
			ars.status = "1" 
			AND tm.status = "1" 
			AND dcs.status = "1" 
			AND ars.course_pi IN (
				SELECT DISTINCT ars_inner.course_pi
				FROM tbl_assign_room_slot_section ars_inner
				LEFT JOIN tbl_department_course_slot_assignment dcs_inner ON ars_inner.dept_course_id = dcs_inner.id
				WHERE dcs_inner.school_id = "'.$school_id.'" AND dcs_inner.department_id = "'.$department_id.'"
			) 
			AND dcs.academic_year_id = "'.$academic_year_id.'" 
		GROUP BY 
			em.employee_id;

		';
		
		$query = $this->db2->query($sql);
		$resutls = $query->result_array();
		return $resutls;
	}
	
}