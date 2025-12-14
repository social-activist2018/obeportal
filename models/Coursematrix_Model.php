<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Class Coursematrix_Model extends CI_Model{
	private $db2;
	public function __construct()
	{
		parent::__construct();
		$this->db2 = $this->load->database('db2', TRUE);
	}
	/*
	* Function : getSingleRecord
	*/
	public function getSingleRecord($tbl_name='', $col = ' * ', $condition=null , $type='')
	{
        $time = time();
        $this->db->select($col);
       // $this->db->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
				
		$query = $this->db2->get($tbl_name);
		//echo $this->db->last_query(); die;
		if($type){
			return $query->row();
		} else {
			return $query->row_array();
		}
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
	* Function : getAllRecords
	*/
	public function getAllRecords($tbl_name='', $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL, $likeSearch=NULL, $where_in = NULL)
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
			$this->db2->where_in('program_id', $where_in);
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
		//echo $this->db->last_query(); die;
		return $query->result_array();
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
		//$this->db->order_by('id', 'asc');
        $query = $this->db2->get($tbl_name);
		$results = array();
		//echo $this->db->last_query(); die;
		foreach($query->result_array() as $row) {
			$results[$row['id']] = $row;
		}
        return $results;
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
	
	public function getSemesterArray($condition='', $hodProgramList='')
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
		//echo $this->db->last_query();die;
		return $query->result_array();
		
	}	
	
	
	/*
	* Function : getAllTechCourseRecords
	*/
	public function getAllTechCourseRecords($condition='')
	{
		
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		
		$this->db2->where(' tbl_teaching_scheme.status', '1');
		$this->db2->where(' tbl_teaching_scheme.is_deleted', '0');
		
		if(!empty($where_in)){
			$this->db2->where_in('tbl_teaching_scheme.id', $where_in);
		}
		$this->db2->order_by('tbl_teaching_scheme.evaluation_id', 'asc');
		$this->db2->order_by('tbl_teaching_scheme.id', 'asc');
		
		$query = $this->db2->join('tbl_course', 'tbl_course.id =  tbl_teaching_scheme.course_id');
		//$this->db2->distinct();
		$query = $this->db2->select(" tbl_teaching_scheme.id,tbl_teaching_scheme.school_id,tbl_teaching_scheme.department_id,tbl_teaching_scheme.semester_id, tbl_teaching_scheme.course_id, tbl_teaching_scheme`.`academic_year_id`, tbl_teaching_scheme`.`program_id`,tbl_teaching_scheme`.`program_id`, `tbl_course`.`catalog_nbr`, `tbl_course`.`course_title`, `tbl_course`.`su_paper_id`,`tbl_course`.`catalog_nbr`, `tbl_course`.`acad_group`, `tbl_course`.`lecture`, `tbl_course`.`tutorial`, `tbl_course`.`practical`, `tbl_course`.`units_maximum`")->get('tbl_teaching_scheme');
		//echo $this->db2->last_query();die;
		return $query->result_array();
		
	}
}