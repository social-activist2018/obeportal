<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Class Timetable_Model extends CI_Model{
	
	private $db2;
	public function __construct()
	{
		parent::__construct();
		$this->db2 = $this->load->database('db2', TRUE);
	}
	
	public function getfullccnrecords($condArray = array()) 
	{
		$this->db2->select('vw.id,vw.programme_id,vw.class_nbr,vw.academic_year_id, vw.status,vw.lecture_id, vw.emp_id,vw.strm,vw.createdon,vw.course_id, cs.catalog_nbr, cs.course_title, em.full_name, em.phone, em.email_id');
		$this->db2->from('PS_S_PRD_CLS_PI_VW vw');
		$this->db2->join('tbl_employee_master em', 'em.employee_id = vw.emp_id');
		$this->db2->join('tbl_course cs', 'cs.id = vw.course_id');
		$this->db2->where('vw.instr_role','1'); // WHERE 1
		$this->db2->where('vw.is_deleted','0'); // WHERE 1
		if(!empty($condArray)){
			foreach($condArray as $key=>$value) {
				$this->db2->where($key, $value); // WHERE 1
			}
		}
		$query = $this->db2->get();
		$result = $query->result_array();
		return $result;

	}
	/*
	* Function : getAllFullNewsAssignmentRecords
	*/
	public function getAllFullNewsAssignmentRecords($condition='', $where_in = '')
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
		//$this->db2->where('PS_S_PRD_CLS_PI_VW.status', '1');
		$this->db2->where('tbl_course.status', '1');
		$query = $this->db2->join('tbl_course', 'tbl_course.id = PS_S_PRD_CLS_PI_VW.course_id');
		$this->db2->distinct();
		$query = $this->db2->select("PS_S_PRD_CLS_PI_VW.*,`tbl_course`.`catalog_nbr`, `tbl_course`.`course_title`,  `tbl_course`.`su_paper_id`, `tbl_course`.`catalog_nbr`,tbl_course.cos_count,tbl_course.subject_area,tbl_course.lecture,tbl_course.tutorial,tbl_course.practical")->get('PS_S_PRD_CLS_PI_VW');
		//echo $this->db2->last_query();die;
		return $query->result_array();
		
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
	* Function : getRoomdetailsbydept
	*/	
	public function getRoomdetailsbydept($school_id, $department_id) 
	{
		$resutls = array();
		if($school_id>0 && $department_id>0){
			$where = '';
			
			if($semester_id){
				//$where = ' AND ds.semester_id='.$semester_id;
			}
			$sql = "select ds.*,rs.room_sharing, rs.room_number, rs.title,rs.id as room_id from tbl_deroomassignment ds JOIN tbl_block_room_master rs ON ds.room_id=rs.id  WHERE rs.status='1' AND ds.`department_id` ='".$department_id."' AND ds.`academic_year_id` ='".ACADEMIC_ID."' $where order by rs.room_number DESC";
			$query = $this->db2->query($sql);
			$resutls = $query->result_array();
		}
		return $resutls;
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
			
			$sql = "select ds.*, rs.room_number, rs.slot_id ,rs.class_number,ts.version_name from tbl_department_course_slot_assignment ds JOIN tbl_timetable_management ts ON ts.id=ds.tt_version_id JOIN tbl_assign_room_slot_section rs ON ds.id=rs.dept_course_id  WHERE rs.status='1' AND ts.status='1' AND ds.status='1' AND rs.is_deleted='0' AND rs.`room_number` ='".$room_id."' AND ds.`academic_year_id` ='".$academic_year_id."' order by rs.room_number DESC"; 
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
			if($academic_year_id){
				$where_in = ' AND academic_year_id='.$academic_year_id;
			}
			$sql = "select ds.*, rs.room_number, rs.title,rs.id as room_id from tbl_deroomassignment ds JOIN tbl_block_room_master rs ON ds.room_id=rs.id  WHERE rs.status='1' AND ds.`department_id` ='".$department_id."' $where $where_in order by rs.room_number DESC";
			$query = $dbreport->query($sql);
			$resutls = $query->result_array();
		}
		return $resutls;
	}
	
	/*
	* Function : questionBankReport
	*/	
	public function questionBankReport($academicYear='2') 
	{
		$dbreport = $this->load->database('dbreport', TRUE);
		$sql = "select count(*) as total, school_name,school_id from tbl_questionbank qs JOIN tbl_teaching_scheme cs ON qs.course_id=cs.course_id LEFT JOIN tbl_school_master sm ON sm.id=cs.school_id WHERE cs.academic_year_id='".$academicYear."' AND  qs.unit_id IS NULL group by school_id order by total DESC";
				
		$query = $dbreport->query($sql);
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
		$dbreport = $this->load->database('dbreport', TRUE);
		$sql = "select count(*) as total, school_name,school_id from tbl_questionbank qs JOIN tbl_teaching_scheme cs ON qs.course_id=cs.course_id LEFT JOIN tbl_school_master sm ON sm.id=cs.school_id WHERE cs.academic_year_id='".$academicYear."' AND qs.unit_id IS NOT NULL group by school_id order by total DESC";
		$query = $dbreport->query($sql);
		$resutls = array();
		foreach($query->result_array() as $row){
			$resutls[$row['school_id']] = $row;
		}
		
		return $resutls;
	}
	
	/* 
	* Function : getAllRecordsGroupBy
	*/
	public function getAllRecordsGroupBy($tbl_name, $col = '*', $condition=null, $order_by = NULL, $limit=NULL, $start=NULL,$group_by = 'school_id' )
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
			$resutls[$row["$group_by"]] = $row['total'];
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
		##echo $dbreport->last_query(); die;
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
        $this->db2->select($col);
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
	
	// Function for deletion
	public function deleterecords($tbl_name, $id){
		if($tbl_name!='' && $id>0){
			$sql_query=$this->db->where('id', $id)->delete($tbl_name);
		}
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
	* Function : getAllFullTTAssignmentRecords
	*/
	public function getAllFullTTAssignmentRecords($condition='', $where_in = '')
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
		$this->db2->where('tbl_timetable_management.status', '1');
		$this->db2->where('tbl_course.status', '1');
		$query = $this->db2->join('tbl_course', 'tbl_course.id = tbl_department_course_slot_assignment.course_id');
		$query = $this->db2->join('tbl_timetable_management', 'tbl_timetable_management.id = tbl_department_course_slot_assignment.tt_version_id');
		$this->db2->distinct();
		$query = $this->db2->select("tbl_department_course_slot_assignment.*,`tbl_course`.`catalog_nbr`, `tbl_course`.`course_title`,  `tbl_course`.`su_paper_id`, `tbl_course`.`catalog_nbr`,tbl_course.cos_count,tbl_course.subject_area,tbl_course.lecture,tbl_course.tutorial,tbl_course.practical,tbl_timetable_management.from_date,tbl_timetable_management.to_date")->get('tbl_department_course_slot_assignment');
		//echo $this->db2->last_query();die;
		return $query->result_array();
		
	}
	
	/*
	* Function : getAllFullAssignmentRecords
	*/
	public function getAllFullAssignmentRecords($condition='', $where_in = '')
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
		//$this->db2->where('tbl_department_course_slot_assignment.status', '1');
		$this->db2->where('tbl_course.status', '1');
		$query = $this->db2->join('tbl_course', 'tbl_course.id = tbl_department_course_slot_assignment.course_id');
		$this->db2->distinct();
		$query = $this->db2->select("tbl_department_course_slot_assignment.*,`tbl_course`.`catalog_nbr`, `tbl_course`.`course_title`,  `tbl_course`.`su_paper_id`, `tbl_course`.`catalog_nbr`,tbl_course.cos_count,tbl_course.subject_area,tbl_course.lecture,tbl_course.tutorial,tbl_course.practical")->get('tbl_department_course_slot_assignment');
		//echo $this->db2->last_query();die;
		return $query->result_array();
		
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
		$query = $this->db2->select("tbl_assign_room_slot_section.slot_id,tbl_assign_room_slot_section.course_id,tbl_assign_room_slot_section.course_pi,tbl_assign_room_slot_section.room_number,tbl_slot_master.slot_name,tbl_slot_master.assigned_periods")->get('tbl_assign_room_slot_section');
		//echo $this->db2->last_query();die;       
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
		#echo $this->db2->last_query();die;
		$records = $query->result_array();
		$response = array();
	    foreach($records as $row) {
			$response[$row['dept_course_id']][] = $row;
		}	
		return $response;
	}
	
	
	/*
	* Function : getAssignedSlotsRecords
	*/
	public function getAssignedSlotsRecordsCommunityconnect($condition='')
	{
		//$this->db2->order_by('tbl_create_counselling.createdon', 'asc');
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		
		$this->db2->where('tbl_assign_room_slot_section.status', '1');
		$this->db2->where('tbl_assign_room_slot_section.is_deleted', '0');
		$this->db2->where('tbl_assign_room_slot_section.slot_id', '0');
		$this->db2->where('tbl_assign_room_slot_section.room_number', '0');
		$this->db2->distinct();
		$query = $this->db2->select("tbl_assign_room_slot_section.course_id")->get('tbl_assign_room_slot_section');
		//echo $this->db2->last_query();die;
		$records = $query->result_array();
		$response = array();
	    foreach($records as $row) {
			$response[$row['course_id']] = $row['course_id'];
		}	
		return $response;
	}
	/*
	* Function : getAssignedSlotsListRecords
	*/
	public function getAssignedSlotsListRecords($condition='')
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
		//$query = $this->db2->join('tbl_employee_master', 'tbl_employee_master.employee_id = tbl_assign_room_slot_section.course_pi');
		$this->db2->distinct();
		$query = $this->db2->select("tbl_assign_room_slot_section.*, tbl_slot_master.display_name as slot_name,tbl_slot_master.assigned_periods, tbl_block_room_master.room_number as room_no, tbl_block_room_master.capacity, tbl_block_room_master.title")->get('tbl_assign_room_slot_section');
		//echo $this->db2->last_query();die;
		$records = $query->result_array();
		$response = array();
	    foreach($records as $row) {
			$response[$row['dept_course_id']][] = $row;
		}	
		return $response;
	}
	/*
	* Function : getAssignedSlotsSectionClsRecords
	*/
	public function getAssignedSlotsSectionClsRecords($condition='')
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
		$query = $this->db2->join('tbl_academic_block_master', 'tbl_academic_block_master.id = tbl_block_room_master.block_id');
		$query = $this->db2->join('tbl_employee_master', 'tbl_employee_master.employee_id = tbl_assign_room_slot_section.course_pi');
		$this->db2->distinct();
		$query = $this->db2->select("tbl_employee_master.full_name, tbl_employee_master.phone, tbl_assign_room_slot_section.*, tbl_slot_master.display_name as slot_name,tbl_slot_master.assigned_periods, tbl_block_room_master.room_number as room_no, tbl_block_room_master.capacity, tbl_block_room_master.title,tbl_academic_block_master.title as block_name")->get('tbl_assign_room_slot_section');
		//echo $this->db2->last_query();die;
		$records = $query->result_array();
		return $records;
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
	* Function : getProgramWiseCourseList
	*/
	public function getProgramWiseCourseList($condition='')  
	{
		
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		$query = $this->db2->join('tbl_course', 'tbl_course.id = tbl_teaching_scheme.course_id');
		$this->db2->distinct();
		$query = $this->db2->select(" tbl_teaching_scheme.course_id, tbl_teaching_scheme`.`program_id`, `tbl_course`.`course_title`, `tbl_course`.`su_paper_id`,`tbl_course`.`catalog_nbr`,`tbl_course`.`lecture`,`tbl_course`.`tutorial`,`tbl_course`.`practical` ")->get('tbl_teaching_scheme');
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
		echo $this->db2->last_query(); die;
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
		$this->db->insert($tbl_name, $post);
		//echo $this->db->last_query(); die;
		return $this->db->insert_id();
    }
	
	public function updateinfo($tbl_name='', $post, $field, $value)
    {
		$this->db->where($field, $value);
        if (!$this->db->update($tbl_name, $post)) {
            log_message('error', print_r($this->db->error(), true));
			$resp = $this->db->error();
			return $resp['message'];
	    }
		//echo $this->db2->last_query(); die;
	}
	public function updatePCinfo($tbl_name='', $post, $cond)
    {
		if(!empty($cond)){
			foreach($cond as $field=>$value){
				$this->db->where($field, $value);
			}
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
			
			$this->db2->where($col_name, $user_id);
			$this->db2->delete($table_name); 
			
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
	* Function: alternativearrangement
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getAlternativeArrangementArray($tbl_name='tbl_alternativearrangement_master', $col = ' * ', $condition=null, $order_by=null)
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
	public function getAlternativeArrangementAlterArray($tbl_name='tbl_alternativearrangement_master', $col = ' * ', $condition=null, $order_by=null, $betweenDate = null)
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
		
		if(!empty($betweenDate))
		{
			$from_date = $betweenDate['from_date'];
			$to_date = $betweenDate['to_date'];
			$this->db2->where("DATE_FORMAT(tbl_alternativearrangement_master.date,'%m/%d/%Y') >='$from_date'");
			$this->db2->where("DATE_FORMAT(tbl_alternativearrangement_master.date,'%m/%d/%Y') <='$to_date'");
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
	* Function: getCommonIdListArray
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
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
	* CreatedBy:
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
	* CreatedBy:
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
		//echo $this->db2->last_query(); die;
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
		
		$this->db->insert($tbl_name, $post);
		//echo $this->db->last_query(); die;
		return $this->db->insert_id();
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
	
		/*
	* Function : getSQLBucketRecords   
	*/
	public function getSQLBucketRecords($course_reference = NULL, $prog_code=null, $prog_cond=null, $course_area=NULL)
    {
		
		if(!empty($course_area)){
			$sql = "SELECT * FROM `tbl_managebucket_ref` WHERE FIND_IN_SET('".$prog_code."',`program_code`) AND `program_cond`='".$prog_cond."' AND `course_reference`='".$course_reference."' AND `course_area`='".$course_area."' AND is_deleted='0' AND status='1'";
		}
		else{
			$sql = "SELECT * FROM `tbl_managebucket_ref` WHERE FIND_IN_SET('".$prog_code."',`program_code`) AND `program_cond`='".$prog_cond."' AND `course_reference`='".$course_reference."'  AND is_deleted='0' AND status='1'";
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
	
	public function getSemesterWiseCredits($condArray= '' )
	{
		return true;
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
	* Function : getEnrollmentCount
	* DB Connection : db2
	*/
	public function getTotalEnrollmentCount($tbl_name='stu_enrollment')
	{
		$otherdb = $this->load->database('db3', TRUE);
       	$otherdb->where('status', '1');
		$otherdb->where('is_deleted', '0');
		$queryResult = $otherdb->select('count(distinct system_id) as total')->get($tbl_name);
		//echo $this->db2->last_query();die;
		return $queryResult->row_array();
    }
	
	/*
	* Function : getTodayEnrollmentCount
	* DB Connection : db2
	*/
	public function getTodayEnrollmentCount($tbl_name='stu_enrollment')
	{
		$otherdb = $this->load->database('db3', TRUE);
       	$otherdb->like('createdon', date('Y-m-d'));
       	$otherdb->where('status', '1');
		$otherdb->where('is_deleted', '0');
		$queryResult = $otherdb->select('count(distinct system_id) as total')->get($tbl_name);
		//echo $this->db2->last_query();die;
		return $queryResult->row_array();
    }
	
	/*
	* Function: getOBESemesterArray
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
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
	
	/*
	* Function : getAllTechCourseRecords
	*/
	public function getAllTechCourseRecords($condition='')
	{
		//$this->db2->order_by('tbl_create_counselling.createdon', 'asc');
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
		$query = $this->db2->join('tbl_course', 'tbl_course.id =  tbl_teaching_scheme.course_id');
		$this->db2->distinct();
		$query = $this->db2->select(" tbl_teaching_scheme.id,tbl_teaching_scheme.school_id,tbl_teaching_scheme.department_id,tbl_teaching_scheme.semester_id, tbl_teaching_scheme.course_id, tbl_teaching_scheme`.`academic_year_id`, tbl_teaching_scheme`.`program_id`,tbl_teaching_scheme`.`program_id`, `tbl_course`.`catalog_nbr`, `tbl_course`.`course_title`, `tbl_course`.`su_paper_id`,`tbl_course`.`catalog_nbr`, `tbl_course`.`acad_group`, `tbl_course`.`lecture`, `tbl_course`.`tutorial`, `tbl_course`.`practical`, `tbl_course`.`units_maximum`")->get('tbl_teaching_scheme');
		//echo $this->db2->last_query();die;
		return $query->result_array();
		
	}
	
	/*
	* Function : getAvailableRoomDetails
	*/
	public function getAvailableRoomDetails($condition='', $bookedRooms='')
	{
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		
		$this->db2->where(' tbl_block_room_master.status', '1');
		$this->db2->where(' tbl_block_room_master.is_deleted', '0');
		if(!empty($bookedRooms)){
		$this->db2->where_not_in('id', $bookedRooms);
		}
		$this->db2->distinct();
		$query = $this->db2->select("*")->get('tbl_block_room_master');
		//echo $this->db2->last_query();die;
		return $query->result_array();
		
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
	* Function : getAllFullAssignmentWithInRecords
	*/
	public function getAllFullAssignmentWithInRecords($condition='', $where_in = '')
	{
		//$this->db2->order_by('tbl_create_counselling.createdon', 'asc');
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		
		if(!empty($where_in)){
			$this->db2->where_in('tt_version_id', $where_in);
		}
		$this->db2->where('tbl_department_course_slot_assignment.status', '1');
		$query = $this->db2->join('tbl_course', 'tbl_course.id = tbl_department_course_slot_assignment.course_id');
		$this->db2->distinct();
		$query = $this->db2->select("tbl_department_course_slot_assignment.*,`tbl_course`.`catalog_nbr`, `tbl_course`.`course_title`, `tbl_course`.`catalog_nbr`,tbl_course.cos_count")->get('tbl_department_course_slot_assignment');
		//echo $this->db2->last_query();die;
		return $query->result_array();
		
	}
	
	
		
	/**
	* Get All topic lists
	*/
	public function getCustomSlotAttotmentArray($conditions='', $betweenDate='') 
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
			$filter_to = date('Y-m-d',strtotime($betweenDate['to_date']));
			$fromOne = "'".$filter_from."' BETWEEN from_date AND to_date";
			$fromTwo = "'".$filter_from."' BETWEEN from_date AND to_date";
			$this->db2->where("( $fromOne OR $fromTwo )");
		}
		
		$query = $this->db2->join('tbl_department_course_slot_assignment', 'tbl_assign_room_slot_section.dept_course_id = tbl_department_course_slot_assignment.id');
		$query = $this->db2->join('tbl_timetable_management', 'tbl_timetable_management.id = tbl_department_course_slot_assignment.tt_version_id');
		$query = $this->db2->select("tbl_assign_room_slot_section.* , from_date, to_date")->get('tbl_assign_room_slot_section');
		#echo $this->db2->last_query();die;
		$resutls = $query->result_array();
		return $resutls;
	}
	
		/*
	* Function : getAllFullAssignmentSLotsWithVesionRecords
	*/
	public function getAllFullAssignmentSLotsWithVesionRecords($condition='', $where_in = '')
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
		$this->db2->where('tbl_timetable_management.status', '1');
		$query = $this->db2->join('tbl_assign_room_slot_section', 'tbl_assign_room_slot_section.dept_course_id = tbl_department_course_slot_assignment.id');
		$query = $this->db2->join('tbl_course', 'tbl_course.id = tbl_department_course_slot_assignment.course_id');
		$query = $this->db2->join('tbl_timetable_management', 'tbl_timetable_management.id = tbl_department_course_slot_assignment.tt_version_id');
		$this->db2->distinct();
		$query = $this->db2->select("tbl_department_course_slot_assignment.*,`tbl_course`.`catalog_nbr`, `tbl_course`.`course_title`, `tbl_course`.`catalog_nbr`,tbl_course.cos_count,tbl_course.subject_area,tbl_course.lecture,tbl_course.tutorial,tbl_course.practical,tbl_assign_room_slot_section.course_pi,tbl_assign_room_slot_section.class_number,tbl_assign_room_slot_section.section")->get('tbl_department_course_slot_assignment');
		//echo $this->db2->last_query();die;
		return $query->result_array();
		
	}
	
	/*
	* Function : getAllFullDeptCourseRecords
	*/
	public function getAllFullDeptCourseRecords($condition='')
	{
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		
		$this->db2->where('tbl_department_course_slot_assignment.status', '1');
		$this->db2->where('tbl_timetable_management.status', '1');
		$query = $this->db2->join('tbl_timetable_management', 'tbl_timetable_management.id = tbl_department_course_slot_assignment.tt_version_id');
		$this->db2->distinct();
		$query = $this->db2->select("tbl_department_course_slot_assignment.*,tbl_timetable_management.from_date,tbl_timetable_management.to_date")->get('tbl_department_course_slot_assignment');
		//echo $this->db2->last_query();die;
		return $query->result_array();
		
	}
	
	
	public function caselectionlist($academic_year_id,$course_id='', $classnumber='', $term='1')
	{
		$resutls = array();
		$dbreport = $this->load->database('dbreport', TRUE);
		$sql = "SELECT oe.*, oce.assessment_type
			FROM tbl_obe_course_evaluation oce
			JOIN obe_evaluationmaster oe ON oe.id = oce.course_evaluation_type
			WHERE 
			oe.is_deleted = '0' 
			AND oce.academic_year_id = '".$academic_year_id."'
			AND oce.course_id = '".$course_id."'
			AND oce.classnumber = '".$classnumber."'
			AND oce.term = '".$term."'
			AND oe.status = '1'";
			$query = $dbreport->query($sql);
			#echo $dbreport->last_query();die;
			$resutls = $query->result_array();
			
			return $resutls;
	
	}
	
	public function getallTransferedList($academic_year_id, $transfer_status='1')
	{
		$resutls = array();
		$dbreport = $this->load->database('dbreport', TRUE);
		$sql = "SELECT 
			dcs.id,dcs.status,subject_area,  -- Select only required columns to reduce data load
			dcs.academic_year_id,  -- Select only required columns to reduce data load
			dcs.course_transfer_status,  -- Select only required columns to reduce data load
			dcs.course_id,  -- Avoid selecting * unless you need all columns
			dcs.semester_id,  -- Avoid selecting * unless you need all columns
			c.catalog_nbr, 
			dmc.name AS department_name,
			smc.school_name,
			c.course_title, 
			c.su_paper_id, 
			c.cos_count, 
			c.subject_area, 
			c.lecture, 
			c.tutorial, 
			sm.class_number, 
			sm.section, 
			c.practical,
			sem.psoft_name,tmc.version_name , tmc.from_date, tmc.to_date,tmc.status as v_status,
			COALESCE(pm.program_name, spm.title) AS programme_name,
			IF(dcs.teaching_scheme_type = 1, 'General', 'Special') AS programme_type,
			GROUP_CONCAT(DISTINCT sms.slot_name ORDER BY sms.slot_name ASC SEPARATOR ', ') AS all_slots,
			GROUP_CONCAT(DISTINCT bm.room_number ORDER BY bm.room_number ASC SEPARATOR ', ') AS all_rooms,
			GROUP_CONCAT(DISTINCT sm.course_pi ORDER BY sm.course_pi ASC SEPARATOR ', ') AS all_course_pis
		FROM 
			tbl_department_course_slot_assignment dcs
		JOIN 
			tbl_course c ON c.id = dcs.course_id
		JOIN 
			tbl_credits cr ON cr.id = dcs.semester_id
		JOIN 
			tbl_semester sem ON sem.id = cr.semester_id
		JOIN 
			tbl_school_master smc ON smc.id = dcs.school_id	
		LEFT JOIN 
            tbl_timetable_management tmc ON dcs.tt_version_id = tmc.id
		JOIN 
			tbl_department_master dmc ON dmc.id = dcs.department_id
		JOIN 
			tbl_assign_room_slot_section sm ON sm.dept_course_id = dcs.id AND sm.status='1'
		JOIN 
			tbl_block_room_master bm ON bm.id = sm.room_number
		JOIN 
			tbl_slot_master sms ON sms.id = sm.slot_id
		LEFT JOIN 
			tbl_programme_master pm ON pm.id = dcs.programme_id AND dcs.teaching_scheme_type = 1
		LEFT JOIN 
			tbl_specializationprogramme spm ON spm.id = dcs.programme_id AND dcs.teaching_scheme_type != 1	
		WHERE 
			dcs.is_deleted = '0' 
			AND dcs.academic_year_id = '".$academic_year_id."'
			AND dcs.course_transfer_status = '".$transfer_status."'
			AND c.status = '1'
		GROUP BY 
			sm.dept_course_id
		ORDER BY 
			dcs.id;  -- Use ORDER BY if necessary; avoid random ordering

			";
			$query = $dbreport->query($sql);
			$resutls = $query->result_array();
			
			return $resutls;
	
	}
	
	/**
	* Get All topic lists
	*/
	public function getCustomSlotAttotmentLikeArray($conditions='', $betweenDate='', $where_like='') 
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
			$filter_to = date('Y-m-d',strtotime($betweenDate['to_date']));
			$fromOne = "'".$filter_from."' BETWEEN from_date AND to_date";
			$fromTwo = "'".$filter_from."' BETWEEN from_date AND to_date";
			$this->db2->where("( $fromOne OR $fromTwo )");
		}
		$this->db2->like('tbl_slot_master.assigned_periods',$where_like);
		
		$query = $this->db2->join('tbl_department_course_slot_assignment', 'tbl_assign_room_slot_section.dept_course_id = tbl_department_course_slot_assignment.id');
		$query = $this->db2->join('tbl_timetable_management', 'tbl_timetable_management.id = tbl_department_course_slot_assignment.tt_version_id');
		$query = $this->db2->join('tbl_slot_master', 'tbl_slot_master.id = tbl_assign_room_slot_section.slot_id');
		$query = $this->db2->select("tbl_assign_room_slot_section.* , from_date, to_date,assigned_periods")->get('tbl_assign_room_slot_section');
		//echo $this->db2->last_query();die;
		$resutls = $query->result_array();
		return $resutls;
	}
	
	// Get All courses

	public function getAllCourses($condition='', $where_in = '')
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
		//$this->db2->where('tbl_department_course_slot_assignment.status', '1');
		$this->db2->where('tbl_course.status', '1');
		$query = $this->db2->join('tbl_course', 'tbl_course.id = tbl_department_course_slot_assignment.course_id');
		$this->db2->distinct();
		$query = $this->db2->select("`tbl_course`.`id`,`tbl_course`.`catalog_nbr`, `tbl_course`.`course_title`")->get('tbl_department_course_slot_assignment');
		
		//echo $this->db2->last_query();die;
		return $query->result_array();
		
	}
	
}