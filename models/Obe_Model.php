<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Class Obe_Model extends CI_Model{
	private $db2;
	public function __construct()
	{
		parent::__construct();
		$this->db2 = $this->load->database('db2', TRUE);
	}
	
	public function get_course_evaluation_by_id($id, $user_id, $course_id)
	{
		$this->db->select('tbl_obe_course_evaluation.*');
		$this->db->from('tbl_obe_course_evaluation');
		$this->db->join(
			'obe_evaluationmaster',
			'obe_evaluationmaster.id = tbl_obe_course_evaluation.course_evaluation_type',
			'inner'
		);
		$this->db->where('obe_evaluationmaster.id', $id);
		$this->db->where('tbl_obe_course_evaluation.user_id', $user_id);
		$this->db->where('tbl_obe_course_evaluation.course_id', $course_id);
		$query = $this->db->get();
		#echo $this->db->last_query();die;
		return $query->row_array(); // Return as array
	}


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
	* Function : getAllSemesterArray
	* Description : getAllSemesterArray
	*/
	public function getAllSemesterArray($condition='')
	{
		
		$this->db2->select('tbl_semester.title, tbl_semester.psoft_name, tbl_obesemestercredits.id,tbl_obesemestercredits.academic_id, tbl_obesemestercredits.program_type, tbl_obesemestercredits.program_id, tbl_obesemestercredits.status ');
		$this->db2->join('tbl_semester', 'tbl_semester.id = tbl_obesemestercredits.semester_id', 'left');
		if($id>0){
			$this->db2->where('tbl_obesemestercredits.id', $id);
			$this->db2->limit(1);
		}
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
		$query = $this->db2->get('tbl_obesemestercredits');
		//echo $this->db2->last_query();die;
		return $query->result_array();
		//"SELECT * FROM `tbl_semester` as sem JOIN tbl_obesemestercredits as crd ON sem.id=crd.semester_id where crd.status='1' AND crd.academic_id='3' AND program_id="" AND program_type="";"
	}
	
	

	/*
	* Function : getBloomsTaxonomy
	*/
	public function getBloomsTaxonomy($condArray)
	{
		$btl_id = $condArray['btl_id'];
		$course_id = $condArray['course_id'];
		$cos_id = $condArray['cos_id'];
		$qTitle = $condArray['cos_title'];
		
		$this->db2->select('id,btl_id,title');
		$this->db2->where('is_deleted', '0');
		$this->db2->where('status', '1');
		$qTitle = str_replace(array("'s",'?','.','`','@','http',':','#','','"','\'', '"',',' , ';', '<', '>', '.', '*'),' ', $qTitle);
		$qTitleArray = explode(' ', $qTitle);
		if(!empty($qTitleArray)){
			$orArray = array();
			foreach($qTitleArray as $rvalue){
				$frvalue = '';
				$frvalue = str_replace(array("'s",'?','.','`','@','http',':','#','','"','\'', '"',',' , ';', '<', '>'),'', $rvalue);
				if(preg_match('/^[-a-zA-Z0-9_]+$/i', $frvalue)) {
					//$orArray[] = ' title  like "%'.$frvalue.'%" ';
					if(ltrim($frvalue)){
						$orArray[] = ' title  = "'.ltrim($frvalue).'" ';
					}
				}
			}
			
			if(!empty($orArray)){
				$or_like = implode(' OR ', $orArray);
				$or_like = '( '.$or_like.' )';
			}
			
			$this->db2->where($or_like);
		}
		
		//$this->db2->where('btl_id', $btl_id);
		$query = $this->db2->get('tbl_bloomstaxonomy ');
		//echo $this->db2->last_query(); die;
		$results = array();
		$results = $query->num_rows();
		return $query->row_array();
	}
	
	/*
	* Function : getAllTransferCourseRecords
	*/	
	public function getAllTransferCourseRecords($department_id='',$semester_id='') 
	{
		$resutls = array();
		if($department_id>0){
			$where = '';
			if($semester_id){
				$where = ' AND qs.semester_id='.$semester_id;
			}
			$sql = "select qs.* from tbl_teaching_scheme qs JOIN tbl_course cs ON qs.course_id=cs.id  WHERE is_transfer='1' AND cs.`subject_area` ='".$department_id."' $where order by id DESC";
			$query = $this->db2->query($sql);
			$resutls = $query->result_array();
		}
		return $resutls;
	}
	/*
	* Function : questionBankReport
	*/	
	public function questionBankReport($academicYear='2') 
	{
		
		$sql = "select count(*) as total, school_name,school_id from tbl_questionbank qs JOIN tbl_teaching_scheme cs ON qs.course_id=cs.course_id LEFT JOIN tbl_school_master sm ON sm.id=cs.school_id WHERE cs.academic_year_id='".$academicYear."' AND  qs.unit_id IS NULL group by school_id order by total DESC";
				
		$query = $this->db2->query($sql);
		$resutls = array();
		foreach($query->result_array() as $row){
			$resutls[$row['school_id']] = $row;
		}
		
		return $resutls;
	}
	/*
	* Function : questionBankActiveReport
	*/	
	public function questionBankActiveReport($academicYear='2') 
	{
		$sql = "select count(*) as total, school_name,school_id from tbl_questionbank qs JOIN tbl_teaching_scheme cs ON qs.course_id=cs.course_id LEFT JOIN tbl_school_master sm ON sm.id=cs.school_id WHERE cs.academic_year_id='".$academicYear."' AND qs.unit_id IS NOT NULL group by school_id order by total DESC";
		$query = $this->db2->query($sql);
		$resutls = array();
		foreach($query->result_array() as $row){
			$resutls[$row['school_id']] = $row;
		}
		
		return $resutls;
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
		//echo $this->db2->last_query(); die;
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
	
	
	/*
	* Function : getAllMyRecords
	*/
	public function getAllMyRecords($tbl_name, $col = ' * ', $condition=null)
    {
        $time = time();
        $this->db2->select($col);
     	if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->like($key, $val);
			}
		}
		$this->db2->order_by('id', 'desc');
		$query = $this->db2->get($tbl_name,'500', '0');
		//echo $this->db2->last_query(); //die;
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
	* Function : getAllAcademicprogrammeRecords
	*/
	public function getAllAcademicprogrammeRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL)
    {
        $time = time();
        //$this->db2->select($col);
        $this->db2->select($col);
        $this->db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		
		//$this->db2->where('MONTH(regDate)', date('m'));
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}
		}
		$query = $this->db2->get($tbl_name);
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
        $this->db2->select($col);
       // $this->db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
				
		$query = $this->db2->get($tbl_name);
		//echo $this->db2->last_query(); die;
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
				$this->db2->where($key, $val);
			}
		}
		$query = $this->db2->select("*")->get($tbl_name);
		//echo $this->db2->last_query(); die;
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
	/*/*
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
	* Function : getTransferCourseList
	*/
	public function getTransferCourseList($academic_id = '1', $condition='', $where_in = '')
	{
		//$this->db2->order_by('tbl_create_counselling.createdon', 'asc');
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		
		$this->db2->where('tbl_teaching_scheme.transfer_status', '1');
		$this->db2->where('tbl_teaching_scheme.status', '1');
		$this->db2->where('tbl_teaching_scheme.academic_id', $academic_id);
		if(!empty($where_in)){
			$this->db2->where_in('tbl_teaching_scheme.program_id', $where_in);
		}
		//$this->db2->group_by('`tbl_teaching_scheme`.`course_id`');
		$query = $this->db2->join('tbl_course', 'tbl_course.id = tbl_teaching_scheme.course_id');
		$query = $this->db2->join('tbl_department_master', 'tbl_department_master.id = tbl_course.subject_area');
		$query = $this->db2->join('tbl_school_master', 'tbl_school_master.id = tbl_course.acad_group');
		//$query = $this->db2->join('tbl_programme_master', 'tbl_programme_master.id = tbl_teaching_scheme.program_id');
		$this->db2->distinct();
		$query = $this->db2->select("tbl_teaching_scheme.id,tbl_teaching_scheme.course_id,tbl_teaching_scheme`.`program_id`,  `school_name`, `school_code`,  `tbl_course`.`catalog_nbr`, `tbl_course`.`course_title`, `tbl_course`.`su_paper_id`,`tbl_course`.`catalog_nbr`, `tbl_course`.`acad_group`, tbl_department_master.name, tbl_department_master.department_code")->get('tbl_teaching_scheme');
		//echo $this->db2->last_query();die;
		return $query->result_array();
		
	}
	
	/*
	* Function : getTransferCourseList
	*/
	public function getTransferCourseList_myold($academic_id = '1', $condition='')
	{
		//$this->db2->order_by('tbl_create_counselling.createdon', 'asc');
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		
		$this->db2->where('tbl_teaching_scheme.transfer_status', '1');
		$this->db2->where('tbl_teaching_scheme.academic_id', $academic_id);
		
		//$this->db2->group_by('`tbl_teaching_scheme`.`course_id`');
		$query = $this->db2->join('tbl_course', 'tbl_course.id = tbl_teaching_scheme.course_id');
		$query = $this->db2->join('tbl_school_master', 'tbl_school_master.id = tbl_course.acad_group');
		//$query = $this->db2->join('tbl_department_master', 'tbl_department_master.id = tbl_course.acad_org','left');
		$query = $this->db2->join('tbl_department_master', 'tbl_department_master.id = tbl_course.subject_area');
		//$query = $this->db2->join('tbl_programme_master', 'tbl_programme_master.id = tbl_teaching_scheme.program_id');
		$this->db2->distinct();
		$query = $this->db2->select("tbl_teaching_scheme.course_id,tbl_teaching_scheme`.`program_id`,  `school_name`, `school_code`,  `tbl_course`.`catalog_nbr`, `tbl_course`.`course_title`, `tbl_course`.`su_paper_id`,`tbl_course`.`catalog_nbr`,`tbl_course`.`acad_group`, tbl_department_master.name, tbl_department_master.department_code")->get('tbl_teaching_scheme');
		//echo $this->db2->last_query();die;
		return $query->result_array();
		
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
		$query = $this->db2->join('tbl_campus', 'tbl_campus.id = tbl_library_master.campus_id');
		$query = $this->db2->join('tbl_admin', 'tbl_admin.id = tbl_library_master.author_id');
		$query = $this->db2->select("tbl_library_master.*, , tbl_campus.campus_name, tbl_admin.first_name,tbl_admin.last_name")->get('tbl_library_master');
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
	
	public function registrationCount($tbl_name = 'patient_registration', $col='*', $cond= array('is_deleted'=>'0'))
    {
		 $this->db2->select($col);
		 if(count($cond)){
			 foreach($cond as $key=>$val){
				$this->db2->where($key, $val);
			}
		}
		$query = $this->db2->get($tbl_name);
        return $query->num_rows();
	}
	/*
	* Function : questionsTotalCount
	*/
	
	public function questionsTotalCount($tbl_name = 'patient_registration', $col='*', $condition= array('tbl_questionbank.is_deleted'=>'0'), $academic_year_id='2')
    {
			$this->db2->select('count(tbl_questionbank.id) as total');
			$this->db2->join('tbl_teaching_scheme ts', "ts.course_id = $tbl_name.course_id", 'left');
			$this->db2->where('academic_year_id', $academic_year_id);
    		if(!empty($condition))
		    { 
			    foreach($condition as $key=>$val) {  
				    $this->db2->where($key, $val);
			    }			
		    }
            $query = $this->db2->get($tbl_name);
			//echo $this->db2->last_query();die;
            $results = $query->row_array();
		
        return $results['total'];
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
		//echo $this->db2->last_query(); die;
        return $query->result_array();
    }
	/*
	* Function : getCommonQuery
	*/
	
	public function getCommonQuery($tbl_name = 'tbl_school_master', $col = ' * ', $condition='',$order_by='',$condition_like='')
    {
        
        $this->db2->select($col);
		$this->db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
		// Like condition_like
		if(!empty($condition_like))
		{   $k=1;
			foreach($condition_like as $key=>$val) {
				$this->db2->like($key, $val);
				if($k>1) {
					$this->db2->or_like($key, $val);
				}
				$k++;
			}
			
		}
		
		if(!empty($order_by))
		{ 
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}		
		}
        $query = $this->db2->get($tbl_name);
		//echo $this->db2->last_query(); //die;
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
		//echo '<pre>'; print_r($post); die();
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
	public function updateCommonInfo($tbl_name='', $post, $condArray='')
    {
		if(!empty($condArray))
		{
			foreach($condArray as $field=>$value){
				$this->db->where($field, $value);
			}
			if (!$this->db->update($tbl_name, $post)) {
				log_message('error', print_r($this->db->error(), true));
			}
			//echo $this->db2->last_query(); die;
		}
	}
	public function updatePCinfo($tbl_name='', $post, $cond)
    {
		if(!empty($cond)){
			foreach($cond as $field=>$value){
				$this->db->where($field, $value);
			}
		}
        if (!$this->db->update($tbl_name, $post)) {
            log_message('error', print_r($this->db2->error(), true));
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
	* Createdb2y:
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
	* Createdb2y:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	
	public function getSemesterArray($condition, $hodProgramList='')
	{
		$this->db2->select('tbl_obesemestercredits.id,tbl_semester.title,tbl_semester.description');
		$this->db2->join('tbl_semester', 'tbl_semester.id = tbl_obesemestercredits.semester_id', 'left');
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		if(!empty($hodProgramList)){
			    //$this->db2->where('a.id=(select max(id) from tbl_comments b where a.indent_id=b.indent_id)');			
			    $this->db2->where_in('tbl_obesemestercredits.program_id',$hodProgramList);
			}
			
		//$this->db2->limit(1);
		$query = $this->db2->get('tbl_obesemestercredits');
		//echo $this->db2->last_query();die;
		return $query->result_array();
		
	}	
	
	/*
	* Function: getOBESemesterArray
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* Createdb2y:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	
	public function getOBESemesterArray($condition, $hodProgramList='')
	{
		$this->db2->select('tbl_obesemestercredits.id,tbl_semester.title,tbl_semester.description');
		$this->db2->join('tbl_semester', 'tbl_semester.id = tbl_obesemestercredits.semester_id', 'left');
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		if(!empty($hodProgramList)){
			    //$this->db2->where('a.id=(select max(id) from tbl_comments b where a.indent_id=b.indent_id)');			
			    $this->db2->where_in('tbl_obesemestercredits.program_id',$hodProgramList);
			}
			
		//$this->db2->limit(1);
		$query = $this->db2->get('tbl_obesemestercredits');
		//echo $this->db2->last_query();die;
		return $query->result_array();
		
	}
	/*
	* Function: encryptpassword
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* Createdb2y:
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
	* Createdb2y:
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
	* Createdb2y:
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
	* Createdb2y:
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
	* Createdb2y:
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
	* Createdb2y:
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
		//$this->db2->order_by('id', 'asc');
        $query = $this->db2->get($tbl_name);
		$results = array();
		//echo $this->db2->last_query(); die;
		foreach($query->result_array() as $row) {
			$results[$row['id']] = $row;
		}
        return $results;
	}
	/*
	* Function: getCommonIdListArray
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* Createdb2y:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getCommonIdListArray($tbl_name='tbl_schools', $col = ' * ', $condition=null, $order_by=null)
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
		//$this->db2->order_by('id', 'asc');
        $query = $this->db2->get($tbl_name);
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['programme_id']] = $row;
		}
        return $results;
	}
	/*
	* Function: getCommonEmpListArray
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* Createdb2y:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getCommonEmpListArray($tbl_name='tbl_employee_master', $col = ' * ', $condition=null, $order_by = NULL)
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
		//$this->db2->like('( is_hod="1" OR is_pc="1" )');
		//$this->db2->order_by('id', 'asc');
        $query = $this->db2->get($tbl_name);
		//echo $this->db2->last_query(); //die;
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
	* Createdb2y:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getCommonEmployeeArray($tbl_name='tbl_employee_master', $col = ' * ', $condition=null, $order_by = NULL)
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
		$this->db2->like('( is_hod="1" OR is_pc="1" )');
		//$this->db2->order_by('id', 'asc');
        $query = $this->db2->get($tbl_name);
		//echo $this->db2->last_query(); //die;
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
	* Createdb2y:
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
	* Createdb2y:
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

        /* Function :saverecords
        * Description :Used to save internship registration  data to Database 
        * Date: 2 june 2020
        * Created By: Divyansh Dixit
        */

    function saverecords($tbl_name='registration', $formArray)
	{
		$this->db->insert($tbl_name,$formArray);
		//echo $this->db2->last_query();die;
		return true;
	}

	public function getIndentRecordsForDean($tbl_name = 'tbl_indents', $col = ' * ', $where_role= '' ,$condition='',$order_by='')
    {
        
        $this->db2->select($col);
		$this->db2->where('is_deleted', '0');
		if(!empty($where_role)){
		    $this->db2->where($where_role);
		}
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
		//echo $this->db2->last_query();die;
        return $query->result_array();
    }

	public function getCommonJoinRecords($tbl_name,$col='*',$where_role='',$indentLevel='',$condition='',$order_by='')
	{		
			$this->db2->select('tbl_indents.*,a.indentLevel,a.id as commentId,a.actionUserId,a.actionRoleId');
			//$this->db2->select('tbl_indents.*,a.indentLevel');
			$this->db2->join('tbl_comments a', 'tbl_indents.id = a.indent_id', 'left');
			$this->db2->where('a.id=(select max(id) from tbl_comments b where a.indent_id=b.indent_id)');			
			if(!empty($where_role)){
			    $this->db2->where($where_role);
			}
			if(!empty($indentLevel)){
			    //$this->db2->where('a.id=(select max(id) from tbl_comments b where a.indent_id=b.indent_id)');			
			    $this->db2->where_in('a.indentLevel',$indentLevel);
			}
			$this->db2->where('tbl_indents.is_deleted', '0');
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
			//echo $this->db2->last_query();die;
            return $query->result_array();
	}

	public function getMaxActionId($col='*',$indent_id='',$condition='')
	{		
			$this->db2->select($col);
			$this->db2->from('tbl_comments a');
			$this->db2->where('a.indent_id', $indent_id);
			$this->db2->where('a.id=(select max(id) from tbl_comments b where a.indent_id=b.indent_id)');			
			$this->db2->where('a.is_deleted', '0');
			if(!empty($condition))
		    { 
			    foreach($condition as $key=>$val) {
				    $this->db2->where($key, $val);
			    }			
		    }
            $query = $this->db2->get();
			//echo $this->db2->last_query();die;
            return $query->row_array();
	}
	
	/*
	* Function : getSingleSQLRecord
	* db2 Connection : db22
	*
	*/
	public function getSingleSQLRecord($tbl_name, $col = ' * ', $condition=null, $order_by = NULL, $where_like=NULL, $where_like_key = 'id', $or_condition = NULL)
	{
		$otherdb2 = $this->load->database('db22', TRUE);
        $time = time();
        $otherdb2->select($col);
        $otherdb2->where('is_deleted', '0');
		if(!empty($where_like)) {
		$otherdb2->like($where_like_key, $where_like);
		}
		 
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$otherdb2->where($key, $val);
			}
			
		} 
		if(!empty($or_condition))
		{ 
			foreach($or_condition as $key=>$val) {
				$otherdb2->or_where($key, $val);
			}
			
		}
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$otherdb2->order_by($key, $val);
			}
		}
		$query = $otherdb2->get($tbl_name);
		//echo $otherdb2->last_query(); 
        return $query->row();
    }
	
	/*
	* Function : getSQLAllRecords
	* db2 Connection : db22
	*
	*
	*/
	public function getSQLAllRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL, $or_condition = NULL)
    {
		$otherdb2 = $this->load->database('db22', TRUE);
        $time = time();
        $otherdb2->select($col);
        $otherdb2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$otherdb2->where($key, $val);
			}
		}
		if(!empty($or_condition))
		{ 
			foreach($or_condition as $key=>$val) {
				$otherdb2->or_where($key, $val);
			}
		}
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$otherdb2->order_by($key, $val);
			}
		}
		if ($limit !== null && $start !== null) {
           $query = $otherdb2->get($tbl_name,$limit, $start);
        } else {
			$query = $otherdb2->get($tbl_name);
		}
		//echo $this->db2->last_query(); die;
		return $query->result();
    }
	
	/*
	* Function : Sqlsaveinfo
	*
	* db2 Connection : db22
	*
	*
	*/
	public function Sqlsaveinfo($tbl_name='', $post)
	{
		$otherdb = $this->load->database('db2', TRUE);
		$otherdb->insert($tbl_name, $post);
		//echo $this->db2->last_query(); die;
		return $otherdb->insert_id();
    }
	/*
	* Function : Sqlupdateinfo
	*
	* db2 Connection : db22
	*
	*
	*/
	public function Sqlupdateinfo($tbl_name='', $post, $field, $value)
    {
		$otherdb2 = $this->load->database('db2', TRUE);
		$otherdb2->where($field, $value);
        if (!$otherdb2->update($tbl_name, $post)) {
            log_message('error', print_r($this->db2->error(), true));
        }
		//echo $this->db2->last_query(); die;
	}

	/*
	* Function : SqlgetSingleRecord
	* db2 Connection : db22
	*/
	public function SqlgetSingleRecord($tbl_name, $col = ' * ', $condition=null)
	{
		$otherdb2 = $this->load->database('db22', TRUE);
        $time = time();
        $otherdb2->select($col);
       // $this->db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$otherdb2->where($key, $val);
			}
			
		}
		$query = $otherdb2->get($tbl_name);
		//echo $this->db2->last_query(); die;
        return $query->row_array();
    }

	/*
	* Function : getKeyValueRecordsArray
	* db2 Connection : db2
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
		//$otherdb2->order_by('id', 'asc');
        $query = $this->db2->get($tbl_name);
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['id']] = $row;
		}
        return $results;
	}
	/*
	* Function : SqlgetSingleRecord
	* db2 Connection : db22
	*/
	public function SqlgetCommonIdArray($tbl_name='tbl_school_master', $col = ' * ', $condition=null)
    {
		$otherdb2 = $this->load->database('db22', TRUE);
        $time = time();
        $otherdb2->select($col);
        $otherdb2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$otherdb2->where($key, $val);
			}
			
		}
		//$otherdb2->order_by('id', 'asc');
        $query = $otherdb2->get($tbl_name);
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['id']] = $row;
		}
        return $results;
	}

	/*
	* Function : SqlgetCommonQuery
	* db2 Connection : db22
	*/
	
	public function SqlgetCommonQuery($tbl_name = 'su_schools', $col = ' * ', $condition='',$order_by='')
    {
		$otherdb2 = $this->load->database('db22', TRUE);
        $otherdb2->select($col);
		$otherdb2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$otherdb2->where($key, $val);
			}
			
		}
		if(!empty($order_by))
		{ 
			foreach($order_by as $key=>$val) {
				$otherdb2->order_by($key, $val);
			}		
		}
        $query = $otherdb2->get($tbl_name);
        return $query->result_array();
    }
	
		/*
	* Function : getSQLBucketRecords   
	*/
	public function getSQLBucketRecords($course_reference = NULL, $prog_code=null, $prog_cond=null, $course_area=NULL)
    {
		
		if(!empty($course_area)){
			$sql = "SELECT * FROM `tbl_obe_managebucket_ref` WHERE FIND_IN_SET('".$prog_code."',`program_code`) AND `program_cond`='".$prog_cond."' AND `course_reference`='".$course_reference."' AND `course_area`='".$course_area."' AND is_deleted='0' AND status='1'";
		}
		else{
			$sql = "SELECT * FROM `tbl_obe_managebucket_ref` WHERE FIND_IN_SET('".$prog_code."',`program_code`) AND `program_cond`='".$prog_cond."' AND `course_reference`='".$course_reference."'  AND is_deleted='0' AND status='1'";
		}
		//$sql = "SELECT DISTINCT sd.system_id, sd.rollno,sd.name,sd.email,sg.current_term ,sg.sgpa,sg.cgpa FROM `student_details` sd JOIN student_grade sg ON sd.system_id=sg.system_id where sd.school_code='".$school_code."' AND `department`='".$department."' AND sd.prog_name='".$programm."' ";

		$query = $this->db2->query($sql);
		//echo $this->db2->last_query(); die;
		return $query->result_array();   
    }
	
	/*
	* Function: getBlockArray
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* Createdb2y:
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
	

	
	public function getRoomArray($id='')
	{
		
		$this->db2->select('tbl_block_room_master.*, tbl_academic_master.academic_name as campus, tbl_academic_block_master.display_name as block');
		$this->db2->join('tbl_academic_master', 'tbl_academic_master.id = tbl_block_room_master.campus_id', 'left');
		$this->db2->join('tbl_academic_block_master', 'tbl_academic_block_master.id = tbl_block_room_master.block_id', 'left');
		if($id>0){
			$this->db2->where('tbl_block_room_master.id', $id);
			$this->db2->limit(1);
		}
		$query = $this->db2->get('tbl_block_room_master');
		//echo $this->db2->last_query();die;
		return $query->result_array();
		
	}
	

	
	public function getProgramBySem($semester_id='')
	{
		
		$this->db2->select('tbl_obe_academicprogramme_master.programme_duration, tbl_obe_academicprogramme_master.maximum_credits, tbl_obe_academicprogramme_master.academic_terms');
		$this->db2->join('tbl_obe_academicprogramme_master', 'tbl_obe_academicprogramme_master.programme_id = tbl_obesemestercredits.program_id', 'left');
		$this->db2->where('tbl_obesemestercredits.id', $semester_id);
		$this->db2->limit(1);
		$query = $this->db2->get('tbl_obesemestercredits ');
		//echo $this->db2->last_query();die;
		return $query->row_array();
		
	}

	
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

	
	public function getSemesterWiseCredits($condArray= '' )
	{
		
		$this->db2->select('sum(tbl_course.units_acad_prog) as total,tbl_teaching_scheme.semester_id,tbl_teaching_scheme.course_id,tbl_teaching_scheme.program_id, credits');
		$this->db2->join('tbl_course', 'tbl_course.id = tbl_teaching_scheme.course_id');
		$this->db2->join('tbl_credits', 'tbl_credits.id = tbl_teaching_scheme.semester_id');
		if(!empty($condArray)){
			foreach($condArray as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
		$this->db2->where('tbl_course.grading_basis', 'GRD');
		
		$query = $this->db2->get('tbl_teaching_scheme');
		//echo $this->db2->last_query(); die;
		$results = array();
		$resp = $query->result_array();
		foreach($resp as $row){
			$results[$row['program_id']][$row['semester_id']] = $row;
		}
		return $results;
		
	}
	public function getCommonIdProgram($tbl_name='tbl_schools', $col = ' * ', $condition=null, $prog_id=null, $order_by=null)
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
		if(!empty($prog_id) && $prog_id!=''){			
		    $this->db2->where_in('id',$prog_id);
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
		foreach($query->result_array() as $row) {
			$results[$row['id']] = $row;
		}
        return $results;
	}
	
	
	/*
	* Function : getAllOBETechCourseRecords
	*/
	public function getAllOBETechCourseRecords($condition='')
	{
		
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		
		$this->db2->where(' tbl_obe_teaching_scheme.status', '1');
		$this->db2->where(' tbl_obe_teaching_scheme.is_deleted', '0');
		
		if(!empty($where_in)){
			$this->db2->where_in('tbl_obe_teaching_scheme.id', $where_in);
		}
		$this->db2->order_by('tbl_obe_teaching_scheme.evaluation_id', 'asc');
		$this->db2->order_by('tbl_obe_teaching_scheme.id', 'asc');
		
		$query = $this->db2->join('tbl_course', 'tbl_course.id =  tbl_obe_teaching_scheme.course_id');
		//$this->db2->distinct();
		$query = $this->db2->select(" tbl_obe_teaching_scheme.id,tbl_obe_teaching_scheme.school_id,tbl_obe_teaching_scheme.department_id,tbl_obe_teaching_scheme.semester_id, tbl_obe_teaching_scheme.course_id, tbl_obe_teaching_scheme`.`academic_year_id`, tbl_obe_teaching_scheme`.`program_id`,tbl_obe_teaching_scheme`.`program_id`, `tbl_course`.`catalog_nbr`, `tbl_course`.`course_title`, `tbl_course`.`su_paper_id`,`tbl_course`.`catalog_nbr`, `tbl_course`.`acad_group`, `tbl_course`.`lecture`, `tbl_course`.`tutorial`, `tbl_course`.`practical`, `tbl_course`.`units_maximum`")->get('tbl_obe_teaching_scheme');
		//echo $this->db2->last_query();die;
		return $query->result_array();
		
	}

	/**
	 * Get Last records 
	*/
	public function getLastRecords($tbl_name, $col = ' * ', $condition=null , $type='')
	{
		$time = time();
		$this->db2->select($col);
   		// $this->db2->where('is_deleted', '0');
   		$this->db2->order_by('tbl_obe_teaching_scheme.id', 'DESC');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
		$query = $this->db2->get($tbl_name);
		//echo $this->db2->last_query(); die;
		if($type){
			return $query->row();
		} else {
			return $query->row_array();
		}
	}

	public function get_ques_mse_lessthan_three() {
		$credit = 'FOR 1 OR 2 CREDIT COURSES';
		$this->db->select('id, q_name,qus_max_marks');
		$this->db->from('tbl_mse_max_marks');
		$this->db->where('details', $credit);
		$query = $this->db->get(); 
		return $query->result_array();
	}
	
	public function get_ques_mse_wq_grt_three() {
		$credit = 'FOR ≥ 3 CREDIT COURSES';
		$this->db->select('id, q_name,qus_max_marks	');
		$this->db->from('tbl_mse_max_marks');
		$this->db->where('details', $credit);
		$query = $this->db->get(); 
		return $query->result_array();
	}
	
	public function student_list($catalog_nbr, $su_paper_id) {
		$this->db->select('id');
		$this->db->from('tbl_course');
		$this->db->where('catalog_nbr', $catalog_nbr);
		$this->db->where('su_paper_id', $su_paper_id);
		$query = $this->db->get(); 
		$crse_id = $query->result_array();
		$course_id = $crse_id['0']['id']; 
		$this->db->select('programme_id');
		$this->db->from('tbl_department_course_slot_assignment');
		$this->db->where('course_id', $course_id);
		$query = $this->db->get(); 
		$program_id = $query->result_array();
		$program_id= $program_id['0']['programme_id']; 
		$this->db->select('program_code');
		$this->db->from('tbl_programme_master');
		$this->db->where('id', $program_id);
		$query = $this->db->get(); 
		$program_code = $query->result_array();

		$program_code = $program_code['0']['program_code']; 
	//	echo $program_code; die();
   // $program_code = 'SET0501';

    // Build the query
	$this->db->select('system_id, name');
	$this->db->from('tbl_student_details');
	$this->db->where('program_code', $program_code);
	$this->db->where_in('admit_term', array(2401, 2402)); // Filtering for admit_term 2401 or 2402
	$this->db->where('status', 1);
	$query = $this->db->get();
	
	return $query->result_array();
	

	}
	public function insert_student_mse_marks($insertData) {
		if (!empty($insertData)) {
			$this->db->insert_batch('mse_max_marks', $insertData);
			return $this->db->affected_rows() > 0;
		}
		return false;
	}
	
	public function get_mse_max_marks($course_code,$term,$classnumber) {
	
		$this->db->select('*');  // Adjust the selected column if needed
		$this->db->from('mse_max_marks');  // Correct table name
		$this->db->where('catalog_nbr', $course_code);
		$this->db->where('CLASS_NBR', $classnumber);
		$this->db->where('ADMIT_TERM', $term);
		$this->db->where('is_deleted', '0');
	
		$query = $this->db->get();
		
		return $query->result_array();  
	}
	
	public function course_id($catalog_nbr) {
		$this->db->select('id');  // Adjust the selected column if needed
		$this->db->from('tbl_course');  // Correct table name
		$this->db->where('catalog_nbr', $catalog_nbr);
	
		$query = $this->db->get();
		
		return $query->row_array();  // Return a single row as an associative array
	}
	public function student_array($id, $course_id,$semester_id) {
		//echo $semester_id ; die(); 
		$this->db->select('section, class_number');
		$this->db->from('tbl_assign_room_slot_section');
		$this->db->where('course_pi', $id);  // Filter by employee ID (course_pi)
		$this->db->where('course_id', $course_id);  // Filter by course ID
		$this->db->where('semester_id', $semester_id);
		$query = $this->db->get();
		
		return $query->result_array();  // Return the result as an array
	}

	public function get_student_marks($course_code,$classnumber,$term) {

		$this->db->select('*');
		$this->db->from('mse_max_marks');
		//$this->db->where('INSTRUCTOR_ID', $employee_id);  // Filter by employee ID
		$this->db->where('catalog_nbr', $course_code);  // Filter by employee ID
		$this-> db->where('CLASS_NBR',$classnumber);
		$this-> db->where('ADMIT_TERM',$term);
		$this->db->where('is_deleted', '0');
		$query = $this->db->get();
	
		return $query->result_array();
	}

	public function get_course_name($course_id) {

		$this->db->select('course_title,catalog_nbr ');  // Adjust the selected column if needed
		$this->db->from('tbl_course');  // Correct table name
		$this->db->where('id', $course_id);
	
		$query = $this->db->get();
		
		return $query->row_array();  // Return a single row as an associative array
	}

	public function department_name($department_id) {

		$this->db->select('name');  // Adjust the selected column if needed
		$this->db->from('tbl_department_master');  // Correct table name
		$this->db->where('id', $department_id);
	
		$query = $this->db->get();
		
		return $query->row_array();  // Return a single row as an associative array
	}
	public function acadmic_year($academic_year_id) {

		$this->db->select('academic_term,academic_year');  // Adjust the selected column if needed
		$this->db->from('tbl_academicyear');  // Correct table name
		$this->db->where('id', $academic_year_id);
	
		$query = $this->db->get();
		
		return $query->row_array();  // Return a single row as an associative array
	}

	public function updateinfo_mse($table, $data, $where_column, $where_value) {
		$this->db->where($where_column, $where_value);
		$updated = $this->db->update($table, $data);
		log_message('debug', 'Update Query: ' . $this->db->last_query());
		log_message('debug', 'Update Success: ' . ($this->db->affected_rows() > 0 ? 'Yes' : 'No'));
		return $this->db->affected_rows() > 0;
	}

	public function get_update_count($table, $column, $value) {
		$this->db->select('update_count');
		$this->db->from($table);
		$this->db->where($column, $value);
		$query = $this->db->get();
	
		if ($query->num_rows() > 0) {
			$row = $query->row();
			return (int) $row->update_count;
		}
	
		return 0;
	}
	public function getAllRecords_ques(){
		
		$this->db->select('*');
		$this->db->from('tbl_qus');
		$this->db->where('status', '1');  
		$query = $this->db->get();
		
		return $query->result_array();
	}

	public function evaluation_marks_table($batchInsertData)
{
    // Perform batch insert
    return $this->db->insert_batch('evaluation_marks_table', $batchInsertData);
}
public function insertBatch($batchData) {
    if (!empty($batchData)) {
        $this->db->insert_batch('evaluation_marks_table', $batchData);
    }
}
public function updateBatch($batchData) {
    if (!empty($batchData)) {
        $this->db->update_batch('evaluation_marks_table', $batchData, 'id');
    }
}
public function get_all_marks()
{
    return $this->db->get('evaluation_marks_table')->result_array();
}

public function update_marks_in_db($marks_data) {
	//echo '<pre>'; print_r($marks_data); die();
    foreach ($marks_data as $mark) {
        $this->db->where('student_id', $mark['student_id']);
        $this->db->where('evaluation_type', $mark['evaluation_type']);
        $this->db->where('evaluation_id', $mark['evaluation_id']);
        $this->db->where('question_id', $mark['question_id']);
        $this->db->where('co_id', $mark['co_id']);
		$this->db->where('is_deleted', '0');
        $this->db->update('evaluation_marks_table', ['marks' => $mark['marks']]);
		//echo $this->db->last_query(); die;


    }

  
}

public function facalty_name($id){
	$this->db->select('full_name');  // Adjust the selected column if needed
	$this->db->from('tbl_employee_master');  // Correct table name
	$this->db->where('employee_id', $id);

	$query = $this->db->get();
	
	return $query->row_array(); 
}
	
public function course_name($course_id){
	$this->db->select('course_title');  // Adjust the selected column if needed
	$this->db->from('tbl_course');  // Correct table name
	$this->db->where('id', $course_id);

	$query = $this->db->get();
	
	return $query->row_array(); 
}


public function get_student_quiz($course_code,$classnumber,$term, $eval_id, $assessment_type){

	    $loginDetails= $this->session->userdata('qb_adminloggedin'); 
	    $empid = $loginDetails->employee_id;
	    $this->db2->select('evaluation_marks_table.student_id, evaluation_marks_table.co_id, SUM(evaluation_marks_table.marks) AS total_marks,evaluation_marks_table.co_marks');
		$this->db2->from('evaluation_marks_table');
		$this->db2->join('tbl_obe_course_evaluation', 'tbl_obe_course_evaluation.id = evaluation_marks_table.evaluation_id', 'left');
		if($eval_id){
		$this->db2->where('evaluation_marks_table.evaluation_type', $eval_id);
		}
		$this->db2->where('tbl_obe_course_evaluation.assessment_type', $assessment_type);
		$this->db2->where('evaluation_marks_table.course_code', $course_code); 
		$this->db2->where('evaluation_marks_table.classnumber', $classnumber);
		$this->db2->where('evaluation_marks_table.term', $term);
		$this->db2->where('evaluation_marks_table.is_deleted', '0');
		$this->db2->where('tbl_obe_course_evaluation.classnumber', $classnumber);
		$this->db2->where('tbl_obe_course_evaluation.teacher_id', $empid);
		$this->db2->group_by('evaluation_marks_table.student_id, evaluation_marks_table.co_id');
		$this->db2->order_by('evaluation_marks_table.co_id', 'ASC');
		$this->db2->order_by('evaluation_marks_table.student_id', 'ASC');

	    $query = $this->db2->get(); 
	    return $query->result(); 


}

public function get_student_assiment($course_code,$classnumber,$term){
	
	$loginDetails= $this->session->userdata('qb_adminloggedin'); 
	$empid = $loginDetails->employee_id;
	$this->db2->select('evaluation_marks_table.student_id, evaluation_marks_table.co_id, SUM(evaluation_marks_table.marks) AS total_marks,evaluation_marks_table.co_marks');
	$this->db2->from('evaluation_marks_table');
	$this->db2->join('tbl_obe_course_evaluation', 'tbl_obe_course_evaluation.id = evaluation_marks_table.evaluation_id', 'left');
	$this->db2->where('evaluation_marks_table.evaluation_type', 2);
	$this->db2->where('evaluation_marks_table.course_code', $course_code); 
	$this->db2->where('evaluation_marks_table.classnumber', $classnumber);
	$this->db2->where('evaluation_marks_table.term', $term);
	$this->db2->where('evaluation_marks_table.is_deleted', '0');//
	$this->db2->where('tbl_obe_course_evaluation.classnumber', $classnumber);
	$this->db2->where('tbl_obe_course_evaluation.teacher_id', $empid);
	$this->db2->group_by('evaluation_marks_table.student_id, evaluation_marks_table.co_id');
	$this->db2->order_by('evaluation_marks_table.co_id', 'ASC');
	$this->db2->order_by('evaluation_marks_table.student_id', 'ASC');
	$query = $this->db2->get(); // Execute the query
	
	return $query->result(); // Return the result as an array of objects

}

public function get_student_project($course_code,$classnumber,$term){
	$loginDetails= $this->session->userdata('qb_adminloggedin'); 
	$empid = $loginDetails->employee_id;
	$this->db2->select('evaluation_marks_table.student_id, evaluation_marks_table.co_id, SUM(evaluation_marks_table.marks) AS total_marks,evaluation_marks_table.co_marks');
	$this->db2->from('evaluation_marks_table');
	$this->db2->join('tbl_obe_course_evaluation', 'tbl_obe_course_evaluation.id = evaluation_marks_table.evaluation_id', 'left');

	$this->db2->where('evaluation_marks_table.evaluation_type', 3);
	$this->db2->where('evaluation_marks_table.course_code', $course_code); 
	$this->db2->where('evaluation_marks_table.classnumber', $classnumber);
	$this->db2->where('evaluation_marks_table.term', $term);
	$this->db2->where('evaluation_marks_table.is_deleted', '0');
	$this->db2->where('tbl_obe_course_evaluation.classnumber', $classnumber);
	$this->db2->where('tbl_obe_course_evaluation.teacher_id', $empid);
	$this->db2->group_by('evaluation_marks_table.student_id, evaluation_marks_table.co_id');
	$this->db2->order_by('evaluation_marks_table.co_id', 'ASC');
	$this->db2->order_by('evaluation_marks_table.student_id', 'ASC');

	$query = $this->db2->get(); // Execute the query
	return $query->result(); // Return the result as an array of objects

}
public function insert_co_marks($co_ids, $marks, $questions, $teaching_scheme_type) {

        foreach ($co_ids as $index => $co_id) {
            $data = [
                'co_id'                => $co_id,
                'marks'               => isset($marks[$index]) ? $marks[$index] : 0,
                'question'            => isset($questions[$index]) ? $questions[$index] : '',
                'teaching_scheme_type' => $teaching_scheme_type,
                'created_at'          => date('Y-m-d H:i:s')
            ];
            $this->db->insert('tbl_co_marks', $data);
        }

}

public function get_student_countinous($course_code,$classnumber,$term){
	$loginDetails= $this->session->userdata('qb_adminloggedin'); 
	$empid = $loginDetails->employee_id;
	$this->db2->select('evaluation_marks_table.student_id, evaluation_marks_table.co_id, SUM(evaluation_marks_table.marks) AS total_marks,SUM(evaluation_marks_table.co_marks) AS co_max_marks');
	$this->db2->from('evaluation_marks_table');
	$this->db2->join('tbl_obe_course_evaluation', 'tbl_obe_course_evaluation.id = evaluation_marks_table.evaluation_id', 'left');
	$this->db2->where_in('evaluation_type', [1, 2, 3]);
	$this->db2->where('evaluation_marks_table.course_code', $course_code); 
	$this->db2->where('evaluation_marks_table.classnumber', $classnumber);
	$this->db2->where('evaluation_marks_table.term', $term);
	$this->db2->where('tbl_obe_course_evaluation.classnumber', $classnumber);
	$this->db2->where('tbl_obe_course_evaluation.teacher_id', $empid);
	$this->db2->where('evaluation_marks_table.is_deleted', '0');
	$this->db2->group_by('evaluation_marks_table.student_id, co_id');
	$this->db2->order_by('evaluation_marks_table.co_id', 'ASC');
	$this->db2->order_by('evaluation_marks_table.student_id', 'ASC');
	
	$query = $this->db2->get();
	return $query->result();

}
public function check_existing_record($student_id, $evaluation_type, $evaluation_id, $questionId, $co_id,$term,$coursecode,$classnumber) {

	$this->db2->select('id');
    $this->db2->where('student_id', $student_id);
    $this->db2->where('evaluation_type', $evaluation_type);
    $this->db2->where('evaluation_id', $evaluation_id);
    $this->db2->where('question_id', $questionId);
	if($co_id){
    $this->db2->where('co_id', $co_id);
	}
	$this->db2->where('term', $term);
	$this->db2->where('classnumber', $classnumber);
	$this->db2->where('course_code', $coursecode);
	$this->db2->where('is_deleted', '0');
    $query = $this->db2->get('evaluation_marks_table','1', '0'); // Replace 'your_marks_table' with the actual table name
	#echo $this->db2->last_query();  die;
    return $query->row_array(); // Returns the existing record or null
}
public function get_tsc_id($course_id){
    $this->db2->select('id');
    $this->db2->from('tbl_teaching_scheme');
    $this->db2->where_in('course_id', $course_id);
    $query = $this->db2->get();
    return $query->result();
}

public function get_corse_type($course_id){

    $this->db2->select('practical');
    $this->db2->from('tbl_course');
    $this->db2->where_in('id', $course_id);
    $query = $this->db2->get();
    return $query->result();
}



public function get_student_quiz_practical($course_code,$classnumber,$term){
	$loginDetails= $this->session->userdata('qb_adminloggedin'); 
	$empid = $loginDetails->employee_id;
	$this->db2->select('evaluation_marks_table.student_id, evaluation_marks_table.co_id, SUM(evaluation_marks_table.marks) AS total_marks,evaluation_marks_table.co_marks');
	$this->db2->from('evaluation_marks_table');
	$this->db2->join('tbl_obe_course_evaluation', 'tbl_obe_course_evaluation.id = evaluation_marks_table.evaluation_id', 'left');

	$this->db2->where('evaluation_marks_table.evaluation_type', 4);
	$this->db2->where('evaluation_marks_table.course_code', $course_code); 
	$this->db2->where('evaluation_marks_table.classnumber', $classnumber);
	$this->db2->where('evaluation_marks_table.term', $term);
	$this->db2->where('evaluation_marks_table.is_deleted', '0');
	$this->db2->where('tbl_obe_course_evaluation.classnumber', $classnumber);
	$this->db2->where('tbl_obe_course_evaluation.teacher_id', $empid);
	$this->db2->group_by('evaluation_marks_table.student_id, evaluation_marks_table.co_id');
	$this->db2->order_by('evaluation_marks_table.co_id', 'ASC');
	$this->db2->order_by('evaluation_marks_table.student_id', 'ASC');

	$query = $this->db2->get(); // Execute the query
	return $query->result(); // Return the result as an array of objects

}

public function get_student_assiment_practical($course_code,$classnumber,$term){
	$loginDetails= $this->session->userdata('qb_adminloggedin'); 
	$empid = $loginDetails->employee_id;
	$this->db2->select('evaluation_marks_table.student_id, evaluation_marks_table.co_id, SUM(evaluation_marks_table.marks) AS total_marks,evaluation_marks_table.co_marks');
	$this->db2->from('evaluation_marks_table');
	$this->db2->join('tbl_obe_course_evaluation', 'tbl_obe_course_evaluation.id = evaluation_marks_table.evaluation_id', 'left');

	$this->db2->where('evaluation_marks_table.evaluation_type', 5);
	$this->db2->where('evaluation_marks_table.course_code', $course_code); 
	$this->db2->where('evaluation_marks_table.classnumber', $classnumber);
	$this->db2->where('evaluation_marks_table.term', $term);
	$this->db2->where('evaluation_marks_table.is_deleted', '0');
	$this->db2->where('tbl_obe_course_evaluation.classnumber', $classnumber);
	$this->db2->where('tbl_obe_course_evaluation.teacher_id', $empid);
	$this->db2->group_by('evaluation_marks_table.student_id, evaluation_marks_table.co_id');
	$this->db2->order_by('evaluation_marks_table.co_id', 'ASC');
	$this->db2->order_by('evaluation_marks_table.student_id', 'ASC');

	$query = $this->db2->get(); // Execute the query
	return $query->result(); // Return the result as an array of objects

}

public function get_student_project_practical($course_code,$classnumber,$term){

	$loginDetails= $this->session->userdata('qb_adminloggedin'); 
	$empid = $loginDetails->employee_id;
	$this->db2->select('evaluation_marks_table.student_id, evaluation_marks_table.co_id, SUM(evaluation_marks_table.marks) AS total_marks,evaluation_marks_table.co_marks');
	$this->db2->from('evaluation_marks_table');
	$this->db2->join('tbl_obe_course_evaluation', 'tbl_obe_course_evaluation.id = evaluation_marks_table.evaluation_id', 'left');

	$this->db2->where('evaluation_marks_table.evaluation_type', 6);
	$this->db2->where('evaluation_marks_table.course_code', $course_code); 
	$this->db2->where('evaluation_marks_table.classnumber', $classnumber);
	$this->db2->where('evaluation_marks_table.term', $term);
	$this->db2->where('evaluation_marks_table.is_deleted', '0');
	$this->db2->where('tbl_obe_course_evaluation.classnumber', $classnumber);
	$this->db2->where('tbl_obe_course_evaluation.teacher_id', $empid);
	$this->db2->group_by('evaluation_marks_table.student_id, evaluation_marks_table.co_id');
	$this->db2->order_by('evaluation_marks_table.co_id', 'ASC');
	$this->db2->order_by('evaluation_marks_table.student_id', 'ASC');

	$query = $this->db2->get(); // Execute the query
	return $query->result(); // Return the result as an array of objects

}

public function get_student_countinous_practical($course_code,$classnumber,$term){
	$loginDetails= $this->session->userdata('qb_adminloggedin'); 
	$empid = $loginDetails->employee_id;
	$this->db2->select('evaluation_marks_table.student_id, evaluation_marks_table.co_id, SUM(evaluation_marks_table.marks) AS total_marks,SUM(evaluation_marks_table.co_marks) AS co_max_marks');
	$this->db2->from('evaluation_marks_table');
	$this->db2->join('tbl_obe_course_evaluation', 'tbl_obe_course_evaluation.id = evaluation_marks_table.evaluation_id', 'left');
	$this->db2->where_in('evaluation_type', [4, 5, 6]);
	$this->db2->where('evaluation_marks_table.course_code', $course_code); 
	$this->db2->where('evaluation_marks_table.classnumber', $classnumber);
	$this->db2->where('evaluation_marks_table.term', $term);
	$this->db2->where('tbl_obe_course_evaluation.classnumber', $classnumber);
	$this->db2->where('tbl_obe_course_evaluation.teacher_id', $empid);
	$this->db2->where('evaluation_marks_table.is_deleted', '0');
	$this->db2->group_by('evaluation_marks_table.student_id, co_id');
	$this->db2->order_by('evaluation_marks_table.co_id', 'ASC');
	$this->db2->order_by('evaluation_marks_table.student_id', 'ASC');
	
	$query = $this->db2->get();
	return $query->result();
}

public function getAllcostitle() {
    $this->db2->select('id, co_title');
    $this->db2->from('co_table');
    $query = $this->db2->get();
    return $query->result(); // Fetch results as an array of objects
}

public function getAllcostitlepracticle() {
    $this->db2->select('id, co_title');
    $this->db2->from('cotitlepractical');
    $query = $this->db2->get();
    return $query->result(); // Fetch results as an array of objects
}


public function insert_student_marks($data) {
    // Check if the data is not empty
    if (!empty($data)) {
        // Use insert_batch for bulk inserts
        $this->db->insert_batch('evaluation_marks_table', $data);
        // Return the number of affected rows
        return $this->db->affected_rows();
    }
    return 0;
}

public function program_id($course_id){
$this->db2->select('program_id');  
$this->db2->from('tbl_teaching_scheme');  
$this->db2->where('course_id', $course_id);

$query = $this->db2->get();

return $query->row_array(); 
}

public function posmappingdata($program_id) {
	
    $this->db2->select('*');  
    $this->db2->from('tbl_popsoobestatement');  
    $this->db2->where('programme_id', $program_id);
    
    $query = $this->db2->get();
    
    return $query->result_array(); 
}
public function Coursetheorytopractical() {
    $this->db2->select('coursecode');
    $this->db2->from('tbl_coursetheorytopractical');
    $this->db2->where('is_deleted', '0');
    $this->db2->where('status', 1);
    $query = $this->db2->get();
    
    $result = $query->result_array();

    // Extract only the coursecode values
    return array_column($result, 'coursecode');
}

public function get_classnumber($nuid, $employee_id, $course_id) {

    $query = $this->db->select('classnumber')
                      ->from('tbl_obe_course_evaluation')
                      ->where('tsc_id', $nuid)
                      ->where('teacher_id', $employee_id) 
                      ->where('course_id', $course_id)
                      ->get();

    return $query->result_array();
}
public function insert_classnumber($nuid, $employee_id, $course_id, $datainsert) {
    $where = [
        'tsc_id' => $nuid,
        'teacher_id' => $employee_id, 
        'course_id' => $course_id,
		'classnumber' => ''
    ];

    $this->db->where($where)
             ->update('tbl_obe_course_evaluation', $datainsert);

    return $this->db->affected_rows();
}

public function get_term_data($nuid, $employee_id, $course_id) {
	
	$query = $this->db->select('term')
					  ->from('tbl_obe_course_evaluation')
					  ->where('tsc_id', $nuid)
					  ->where('teacher_id', $employee_id)
					  ->where('course_id', $course_id)
					  ->get();

	return $query->result_array(); 
}
public function update_term($nuid, $employee_id, $course_id, $term) {

	$where = [
		'tsc_id' => $nuid,
		'teacher_id' => $employee_id,
		'course_id' => $course_id,
		'term' => 0 
	];

	$data = [
		'term' => $term
	];
	
	$this->db->where($where)
			 ->update('tbl_obe_course_evaluation', $data);

	return $this->db->affected_rows(); 
}

public function check_mse_marks_indb($insertData) {
    if (empty($insertData)) {
        return false;
    }

    // Extract unique values for where_in query
    $systemIds = array_column($insertData, 'system_id');
    $catalogNbrs = array_column($insertData, 'catalog_nbr');
    $suPaperIds = array_column($insertData, 'su_paper_id');
    $crseIds = array_column($insertData, 'CRSE_ID');
    $admitTerms = array_column($insertData, 'ADMIT_TERM');
    $classNbrs = array_column($insertData, 'CLASS_NBR');
    $instructorIds = array_column($insertData, 'INSTRUCTOR_ID');

    $this->db->where_in('system_id', $systemIds);
    $this->db->where_in('catalog_nbr', $catalogNbrs);
    $this->db->where_in('su_paper_id', $suPaperIds);
    $this->db->where_in('CRSE_ID', $crseIds);
    $this->db->where_in('ADMIT_TERM', $admitTerms);
    $this->db->where_in('CLASS_NBR', $classNbrs);
    $this->db->where_in('INSTRUCTOR_ID', $instructorIds);
	$this->db->where_in('is_deleted', '0');

    $query = $this->db->get('mse_max_marks');
    return ($query->num_rows() > 0) ? $query->result_array() : [];
}


public function get_student_marks_correctco1($course_code,$classnumber,$term) {
	$this->db->select('*');
	$this->db->from('mse_max_marks');
	$this->db->where('catalog_nbr', $course_code);
	$this->db->where('CLASS_NBR', $classnumber);
	$this->db->where('ADMIT_TERM', $term);
	$this->db->where('is_deleted', '0');
	$query = $this->db->get();
	
	$data = $query->result_array();
	if(!empty($unit)){
	      $unit = $data['0']['units_maximum'];
	// echo '<pre>' ; print_r($data);die;
	if ($unit >= 3) {
		$batchUpdateData = [];  // Store batch update data to minimize queries
	
		foreach ($data as $marks) {
			$q_obtain_marks = $marks['Q_obtain_marks'];  // Fetch marks
	
			// Convert string to array and remove spaces
			$marks_array = explode(',', str_replace(' ', '', $q_obtain_marks));

			$selected_indices_co1 = [0, 1, 4, 6, 7];
			$sum_co1 = 0;
			$count_co1 = 0;
	
			foreach ($selected_indices_co1 as $index) {
				if (isset($marks_array[$index]) && strtoupper($marks_array[$index]) !== 'NA') {
					$sum_co1 += (float)$marks_array[$index];  // Convert to float
					$count_co1++;
				}
			}
	
			$co1_value = ($count_co1 > 0) ? $sum_co1 / 2 : 0;

			$selected_indices_co2 = [2, 3, 5, 8, 9];
			$sum_co2 = 0;
			$count_co2 = 0;
	
			foreach ($selected_indices_co2 as $index) {
				if (isset($marks_array[$index]) && strtoupper($marks_array[$index]) !== 'NA') {
					$sum_co2 += (float)$marks_array[$index];  // Convert to float
					$count_co2++;
				}
			}
	
			$co2_value = ($count_co2 > 0) ? $sum_co2 / 2 : 0;
	
			$batchUpdateData[] = [
				'id'   => $marks['id'],
				'CO1'  => $co1_value,
				'CO2'  => $co2_value
			];
		}
	
		if (!empty($batchUpdateData)) {
			$this->db->update_batch('mse_max_marks', $batchUpdateData, 'id');
		}
	}else {
		$batchUpdateData = [];  // Store batch update data to minimize queries
	
		foreach ($data as &$marks) {
			$q_obtain_marks = $marks['Q_obtain_marks'];  // Fetch marks
	
			// Convert string to an array and remove spaces
			$marks_array = explode(',', str_replace(' ', '', $q_obtain_marks));

			$selected_indices_co1 = [0, 2, 4, 5];
			$sum_co1 = 0;
			$count_co1 = 0;
	
			foreach ($selected_indices_co1 as $index) {
				if (isset($marks_array[$index]) && strtoupper($marks_array[$index]) !== 'NA') {
					$sum_co1 += (float)$marks_array[$index];  // Convert to float
					$count_co1++;
				}
			}
	
			$co1_value = ($count_co1 > 0) ? $sum_co1 / 2 : 0;

			$selected_indices_co2 = [1, 3, 6, 7];
			$sum_co2 = 0;
			$count_co2 = 0;
	
			foreach ($selected_indices_co2 as $index) {
				if (isset($marks_array[$index]) && strtoupper($marks_array[$index]) !== 'NA') {
					$sum_co2 += (float)$marks_array[$index];  // Convert to float
					$count_co2++;
				}
			}
	
			$co2_value = ($count_co2 > 0) ? $sum_co2 / 2 : 0;
	
	
			$batchUpdateData[] = [
				'id'   => $marks['id'],
				'CO1'  => $co1_value,
				'CO2'  => $co2_value
			];
		}
	
	
		if (!empty($batchUpdateData)) {
			$this->db->update_batch('mse_max_marks', $batchUpdateData, 'id');
		}
	}
}
	
}




public function check_marks($emplid,$nkey,$nkrow_id,$q,$evalCo,$classnumber,$coursecode,$term){
	
	$this->db2->select('marks');
	$this->db2->from('evaluation_marks_table');
	$this->db2->where('student_id', $emplid);  // WHERE condition
	$this->db2->where('question_id', $q);
	$this->db2->where('co_id', $evalCo);
	$this->db2->where('classnumber', $classnumber);
	$this->db2->where('course_code', $coursecode);
	$this->db2->where('evaluation_id',$nkrow_id);
	$this->db2->where('term', $term);
	$this->db2->where('is_deleted', '0');
	$query = $this->db2->get();
	$result = $query->result();
	return $result; 

}

public function update_marks($emplid,$nkey,$nkrow_id,$q,$evalCo,$classnumber,$coursecode,$term,$obtain_marks){
	$this->db->set('marks', $obtain_marks);
	$this->db->set('modifiedon', date('Y-m-d H:i:s'));
	$this->db->where('student_id', $emplid);  // WHERE condition
	$this->db->where('question_id', $q);
	$this->db->where('co_id', $evalCo);
	$this->db->where('classnumber', $classnumber);
	$this->db->where('course_code', $coursecode);
	$this->db->where('evaluation_id',$nkrow_id);
	$this->db->where('term', $term);
	$this->db->update('evaluation_marks_table');
	#echo $this->db->last_query();die;
	return $this->db->affected_rows();
}

public function insert_marks($emplid,$nkey,$nkrow_id,$q,$evalCo,$classnumber,$coursecode,$term,$obtain_marks,$maxMarks,$empid){
	$data = [
		'student_id'    => $emplid,
		'question_id'   => $q,
		'co_id'         => $evalCo,
		'classnumber'   => $classnumber,
		'course_code'   => $coursecode,
		'evaluation_id' => $nkrow_id,
		'term'          => $term,
		'marks'         => $obtain_marks,
		'co_marks'      => $maxMarks,
		'evaluation_type' => $nkey,
		'created_at' => date('Y-m-d H:i:s'),
		'teacher_id'      => $empid
	];
	
	$this->db->insert('evaluation_marks_table', $data);
	if ($this->db->affected_rows() > 0) {
		echo json_encode(["status" => "success", "message" => "Record inserted successfully!"]);
	} else {
		echo json_encode(["status" => "error", "message" => "Failed to insert record."]);
	}
} 


// public function get_student_ese_marks($course_code, $classnumber, $term)
// {
//     $this->db->select([
//         'end_semester_marks.system_id',
//         'SUM(end_semester_marks.obtained_marks) AS obtainmarks',
//         'SUM(end_semester_marks.max_marks) AS co_max_marks',
//         'endsemester_fix_ques.co_id'
//     ]);

//     $this->db->from('end_semester_marks');

//     $this->db->join(
//         'endsemester_fix_ques',
//         'end_semester_marks.question = endsemester_fix_ques.q_name 
//          AND endsemester_fix_ques.qus_max_marks = end_semester_marks.max_marks',
//         'inner' // Explicitly stating the join type
//     );

//     $this->db->where('end_semester_marks.catalog_nbr', $course_code);
//     $this->db->where('end_semester_marks.class', $classnumber);
//     $this->db->where('end_semester_marks.term', $term);
//     $this->db->where('end_semester_marks.is_deleted', '0');

//     $this->db->group_by(['end_semester_marks.system_id', 'endsemester_fix_ques.co_id']);
    
//     $this->db->order_by('endsemester_fix_ques.co_id', 'ASC');
//     $this->db->order_by('end_semester_marks.system_id', 'ASC');

//     $query = $this->db->get();
// 	return $query->result();	
// }

public function get_student_ese_marks($course_code, $classnumber, $term)
{
    $question_list = [
        'Q6(a)', 'Q6(b)', 'Q7(a)', 'Q7(b)', 'Q8(a)', 'Q8(b)', 
        'Q9(a)', 'Q9(b)', 'Q10(a)', 'Q10(b)', 'Q11(a)', 'Q11(b)', 
        'Q12(a)', 'Q12(b)', 'Q13(a)', 'Q13(b)', 'Q14(a)', 'Q14(b)', 
        'Q15(a)', 'Q15(b)', 'Q16(a)', 'Q16(b)', 'Q17(a)', 'Q17(b)'
    ];

    $this->db->select([
        'end_semester_marks.system_id',
        'SUM(end_semester_marks.obtained_marks) AS obtainmarks',
        'SUM(
            CASE 
                WHEN end_semester_marks.question IN ("' . implode('","', $question_list) . '") 
                THEN FLOOR(end_semester_marks.max_marks / 2) 
                ELSE end_semester_marks.max_marks 
            END
        ) AS co_max_marks', // FLOOR() ensures integer value
        'endsemester_fix_ques.co_id'
    ]);

    $this->db->from('end_semester_marks');

    $this->db->join(
        'endsemester_fix_ques',
        'end_semester_marks.question = endsemester_fix_ques.q_name 
         AND endsemester_fix_ques.qus_max_marks = end_semester_marks.max_marks',
        'inner'
    );

    $this->db->where('end_semester_marks.catalog_nbr', $course_code);
    $this->db->where('end_semester_marks.class', $classnumber);
    $this->db->where('end_semester_marks.term', $term);
    $this->db->where('end_semester_marks.is_deleted', '0');

    $this->db->group_by(['end_semester_marks.system_id', 'endsemester_fix_ques.co_id']);
    
    $this->db->order_by('endsemester_fix_ques.co_id', 'ASC');
    $this->db->order_by('end_semester_marks.system_id', 'ASC');

    $query = $this->db->get();
    return $query->result();    
}

}

