<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Class Obematerial_Model extends CI_Model{
	
	
	public function get_material_details($id) {
        $this->db->select('
            tt.title AS chapter,
            om.title AS sub_topic,
            ob.title AS material_title,
            ob.full_description
        ');
        $this->db->from('tbl_obe_material AS ob');
        $this->db->join('tbl_title_obe_material AS tt', 'ob.title_id = tt.id');
        $this->db->join('tbl_subtitle_obe_material AS om', 'ob.sub_title_id = om.id');
        $this->db->where('ob.id', $id);
        $this->db->where('ob.status', '1');
        $this->db->where('tt.status', '1');
        $this->db->where('om.status', '1');
        $query = $this->db->get();
        return $query->row_array();
    }
	
	public function getChapterSubtopicData()
	{
		$this->db->select('tt.title as chapter, om.title as sub_topic, ob.id, ob.title, ob.short_description, ob.status, ob.is_deleted, ob.display_order');
		$this->db->from('tbl_obe_material AS ob');
		$this->db->join('tbl_title_obe_material AS tt', 'ob.title_id = tt.id');
		$this->db->join('tbl_subtitle_obe_material AS om', 'ob.sub_title_id = om.id');
		$this->db->where('ob.status', '1');
		$this->db->where('tt.status', '1');
		$this->db->where('om.status', '1');
		$this->db->order_by('tt.title', 'ASC');
		$this->db->order_by('om.title', 'ASC');

		$query = $this->db->get();
		$result = $query->result_array();

		$data = [];
		foreach ($result as $row) {
			$chapter = $row['chapter'];
			if (!isset($data[$chapter])) {
				$data[$chapter] = [];
			}
			$data[$chapter][] = [
				'sub_topic' => $row['sub_topic'],
				'id' => $row['id'],
				'title' => $row['title'],
				'short_description' => $row['short_description'],
				'status' => $row['status'],
				'display_order' => $row['display_order']
			];
		}

		return $data; // Returns array grouped by chapter
	}

public function getfullChapterSubtopicData()
	{
		$this->db->select('tt.title as chapter, om.title as sub_topic, ob.id, ob.title, ob.short_description, ob.status, ob.is_deleted');
		$this->db->from('tbl_obe_material AS ob');
		$this->db->join('tbl_title_obe_material AS tt', 'ob.title_id = tt.id');
		$this->db->join('tbl_subtitle_obe_material AS om', 'ob.sub_title_id = om.id');
		$this->db->where('ob.status', '1');
		$this->db->where('tt.status', '1');
		$this->db->where('om.status', '1');
		$this->db->order_by('tt.title', 'ASC');
		$this->db->order_by('om.title', 'ASC');

		$query = $this->db->get();
		$result = $query->result_array();

		$data = [];
		foreach ($result as $row) {
			$data[] = $row;
		}

		return $data; // Returns array grouped by chapter
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
	* Function : getAllDistinctRecords
	*/
	public function getAllDistinctRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL, $likeSearch=NULL, $where_in = NULL)
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
		$dbreport->distinct();
		if ($limit !== null && $start !== null) {
           $query = $dbreport->get($tbl_name,$limit, $start);
        } else {
			$query = $dbreport->get($tbl_name);
		}
		//echo $dbreport->last_query();// die;
		return $query->result_array();
    }
	/*
	* Function : getAllSemesterRecords
	*/
	public function getAllSemesterRecords($condition='')
	{
		//$this->db->order_by('tbl_create_counselling.createdon', 'asc');
		$db2 = $this->load->database('db2', TRUE);
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
		}
		
		$db2->where('tbl_credits.status', '1');
		$query = $db2->join('tbl_semester', 'tbl_semester.id = tbl_credits.semester_id');
		$db2->distinct();
		$query = $db2->select("tbl_credits.*,`tbl_semester`.`title`")->get('tbl_credits');
		//echo $db2->last_query();die;
		$records = $query->result_array();
		$response = array();
	    foreach($records as $row) {
			$response[$row['id']] = $row;
		}	
		return $response;
	}
	
	
	/*
	* Function : getAllSemesterArray
	* Description : getAllSemesterArray
	*/
	public function getAllSemesterArray($condition='')
	{
		$db2 = $this->load->database('db2', TRUE);
		$db2->select('tbl_semester.title, tbl_semester.psoft_name, tbl_credits.id,tbl_credits.academic_id,tbl_credits.program_type, tbl_credits.program_id,tbl_credits.status ');
		$db2->join('tbl_semester', 'tbl_semester.id = tbl_credits.semester_id', 'left');
		if($id>0){
			$db2->where('tbl_credits.id', $id);
			$db2->limit(1);
		}
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
			
		}
		$query = $db2->get('tbl_credits');
		//echo $db2->last_query();die;
		return $query->result_array();
		//"SELECT * FROM `tbl_semester` as sem JOIN tbl_credits as crd ON sem.id=crd.semester_id where crd.status='1' AND crd.academic_id='3' AND program_id="" AND program_type="";"
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
		$db2 = $this->load->database('db2', TRUE);
		$db2->select('id,btl_id,title');
		$db2->where('is_deleted', '0');
		$db2->where('status', '1');
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
			
			$db2->where($or_like);
		}
		
		//$db2->where('btl_id', $btl_id);
		$query = $db2->get('tbl_bloomstaxonomy ');
		//echo $db2->last_query(); die;
		$results = array();
		$results = $query->num_rows();
		return $query->row_array();
	}
	
	/*
	* Function : getAllSemesterWCourseRecords
	*/	
	public function getAllSemesterWCourseRecords($semester_id='',$academic_year_id='') 
	{
		$resutls = array();
		$db2 = $this->load->database('db2', TRUE);
		if($semester_id>0){
			$where = '';
			$where .= ' AND qs.semester_id='.$semester_id;
			if($academic_year_id){
				$where .= ' AND qs.academic_year_id='.$academic_year_id;
			}
			$sql = "select qs.*, catalog_nbr, course_title from tbl_teaching_scheme qs JOIN tbl_course cs ON qs.course_id=cs.id  WHERE qs.status='1' $where order by id DESC";
			$query = $db2->query($sql);
			//echo $db2->last_query(); die;
			$resutls = $query->result_array();
		}
		return $resutls;
	}
	
	/*
	* Function : getAllTransferCourseRecords
	*/	
	public function getAllTransferCourseRecords($department_id='',$semester_id='',$academic_year_id='') 
	{
		$resutls = array();
		$db2 = $this->load->database('db2', TRUE);
		if($department_id>0){
			$where = '';
			if($semester_id){
				$where .= ' AND qs.semester_id='.$semester_id;
			}
			if($academic_year_id){
				$where .= ' AND qs.academic_year_id='.$academic_year_id;
			}
			$sql = "select qs.* from tbl_teaching_scheme qs JOIN tbl_course cs ON qs.course_id=cs.id  WHERE is_transfer='1' AND cs.`subject_area` ='".$department_id."' $where order by id DESC";
			$query = $db2->query($sql);
			//echo $this->db->last_query(); die;
			$resutls = $query->result_array();
		}
		return $resutls;
	}
	/*
	* Function : questionBankReport
	*/	
	public function questionBankReport($academicYear='2') 
	{
		$db2 = $this->load->database('db2', TRUE);
		$sql = "select count(*) as total, school_name,school_id from tbl_questionbank qs JOIN tbl_teaching_scheme cs ON qs.course_id=cs.course_id LEFT JOIN tbl_school_master sm ON sm.id=cs.school_id WHERE cs.academic_year_id='".$academicYear."' AND  qs.unit_id IS NULL group by school_id order by total DESC";
				
		$query = $db2->query($sql);
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
		$db2 = $this->load->database('db2', TRUE);
		$sql = "select count(*) as total, school_name,school_id from tbl_questionbank qs JOIN tbl_teaching_scheme cs ON qs.course_id=cs.course_id LEFT JOIN tbl_school_master sm ON sm.id=cs.school_id WHERE cs.academic_year_id='".$academicYear."' AND qs.unit_id IS NOT NULL group by school_id order by total DESC";
		$query = $db2->query($sql);
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
		$db2 = $this->load->database('db2', TRUE);
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
		$db2->group_by('school_id');
		if ($limit !== null && $start !== null) {
           $query = $db2->get($tbl_name,$limit, $start);
        } else {
			$query = $db2->get($tbl_name);
		}
		
		//echo $db2->last_query(); die;
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
		$db2 = $this->load->database('db2', TRUE);
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
		$db2->group_by($group_by_column);
		$query = $db2->get($tbl_name); 
		//echo $db2->last_query(); die;
		$resutls = array();
		foreach($query->result_array() as $row){
			$resutls[$row[$group_by_column]] = $row['total'];
		}
        return $resutls;
    }
	/*
	* Function : getAllRecords
	*/
	public function getAllRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL, $likeSearch=NULL, $where_in = NULL, $betweenDate=NULL)
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
	    if(!empty($betweenDate))
		{
			$from_date = $betweenDate['from_date'];
			$to_date = $betweenDate['to_date'];
			$dbreport->where("DATE_FORMAT(createdon,'%m/%d/%Y') >='$from_date'");
			$dbreport->where("DATE_FORMAT(createdon,'%m/%d/%Y') <='$to_date'");
		}
		
		if ($limit !== null && $start !== null) {
           $query = $dbreport->get($tbl_name,$limit, $start);
        } else {
			$query = $dbreport->get($tbl_name);
		}
		//echo $this->db->last_query(); die;
		return $query->result_array();
    }

	/*
	* Function : getAllRecords
	*/
	public function getAllTransferedCoursenRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL, $likeSearch=NULL, $where_in = NULL)
    {
        $time = time();
		$db2 = $this->load->database('db2', TRUE);
        $db2->select($col);
        $db2->where('is_deleted', '0');
        $db2->where('paper_setter=0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
		}
		if(!empty($where_in)){
			$db2->where_in('program_id', $where_in);
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
			$db2->where($likewhere);
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
		//echo $db2->last_query(); die;
		return $query->result_array();
    }
	
	/*
	* Function : getAllRecordsWhereIn
	*/
	public function getAllRecordsWhereIn($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL, $likeSearch=NULL, $where_in = NULL)
    {
        $time = time();
		$db2 = $this->load->database('db2', TRUE);
        $db2->select($col);
        $db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
		}
		if(!empty($where_in)){
			$db2->where_in('id', $where_in);
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
			$db2->where($likewhere);
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
		//echo $db2->last_query(); die;
		return $query->result_array();
    }
	
	
	/*
	* Function : getAllMyRecords
	*/
	public function getAllMyRecords($tbl_name, $col = ' * ', $condition=null)
    {
        $time = time();
		$db2 = $this->load->database('db2', TRUE);
        $db2->select($col);
     	if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$db2->like($key, $val);
			}
		}
		$db2->order_by('id', 'desc');
		$query = $db2->get($tbl_name,'1000', '0');
		//echo $db2->last_query(); //die;
		return $query->result_array();
    }
	
	
	/*
	* Function : getAllGrievanceHistoryRecords
	*/
	public function getAllGrievanceHistoryRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL)
    {
        $time = time();
		$db2 = $this->load->database('db2', TRUE);
        $db2->select($col);
        $db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
		}
		$dateCond = 'now()-interval 3 month';
		$db2->where('lastUpdationDate >=', $dateCond, FALSE);
		
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
		//echo $db2->last_query(); die;
		return $query->result_array();
    }
	
	/*
	* Function : getAllAcademicprogrammeRecords
	*/
	public function getAllAcademicprogrammeRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL)
    {
        $time = time();
		$db2 = $this->load->database('db2', TRUE);
        $db2->select($col);
        $db2->select($col);
        $db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
		}
		
		$db2->where('MONTH(regDate)', date('m'));
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$db2->order_by($key, $val);
			}
		}
		$query = $db2->get($tbl_name);
        return $query->result_array();
    }
	/*
	* Function : getAllRecords
	*/
	public function getAllMonthlyRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL)
    {
        $time = time();
		$db2 = $this->load->database('db2', TRUE);
        $db2->select($col);
        $db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
		}
		
		$db2->where('MONTH(regDate)', date('m'));
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$db2->order_by($key, $val);
			}
		}
		$query = $db2->get($tbl_name);
        return $query->result_array();
    }
	
	/*
	* Function : getAllModuleList
	*/
	public function getAllModuleList($tbl_name, $col = ' * ', $condition=null, $where_in=NULL, $where_key = 'id')
    {
        $time = time();
		$db2 = $this->load->database('db2', TRUE);
        $db2->select($col);
        $db2->where('is_deleted', '0');
        $db2->where_in($where_key, $where_in);
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
			
		}
		//$db2->order_by('display_order', 'asc');
        $query = $db2->get($tbl_name);
		//echo $db2->last_query(); die;
        return $query->result_array();
    }
	
	/*
	* Function : getCommonPMArray
	*/
	public function getCommonPMArray($tbl_name='tbl_course_papersetter_moderator', $cond = '')
	{
		$db2 = $this->load->database('db2', TRUE);
		if(!empty($cond)) {
			foreach($cond as $key=>$val) {
				$db2->where($key, $val);
			}
		}
		$query = $db2->select("*")->get($tbl_name);
		//echo $db2->last_query(); die;
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
	public function getSingleRecord($tbl_name, $col = ' * ', $condition=null , $type='',$order_by = NULL,$condition_like = NULL)
	{
        $time = time();
		$db2 = $this->load->database('db2', TRUE);
        $db2->select($col);
       // $db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
			
		}
		
		// Like condition_like
		if(!empty($condition_like))
		{   $k=1;
			foreach($condition_like as $key=>$val) {
				$db2->like($key, $val);
				if($k>1) {
					$db2->or_like($key, $val);
				}
				$k++;
			}
			
		}
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$db2->order_by($key, $val);
			}
		}	
			
		$query = $db2->get($tbl_name);
		//echo $db2->last_query(); die;
		if($type){
			return $query->row();
		} else {
			return $query->row_array();
		}
    }
	
	
	/*
	* Function : getCourseWiseCPMByCourseArray
	*/
	public function getCourseWiseCPMByCourseArray($tbl_name='tbl_course_papersetter_moderator', $cond = '')
	{
		$db2 = $this->load->database('db2', TRUE);
		$db2->select('*');
		if(!empty($cond)) {
			foreach($cond as $key=>$val) {
				$db2->where($key, $val);
			}
		}
		$query = $db2->get($tbl_name);
		//echo $db2->last_query(); die;
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
	public function getCourseWiseCPMArray($tbl_name='tbl_course_papersetter_moderator', $cond = '')
	{
		$db2 = $this->load->database('db2', TRUE);
		$db2->select('*');
		if(!empty($cond)) {
			foreach($cond as $key=>$val) {
				$db2->where($key, $val);
			}
		}
		$query = $db2->get($tbl_name);
		//echo $db2->last_query(); die;
		$resp = $query->result_array();
		$results = array();
		foreach( $resp as $val){
			$results[$val['id']] = $val;
		}
		return $results;
		
    }
	
	
	/*
	* Function : getCourseWiseCPMArray
	*/
	public function getCourseWiseCPMArray_nold($tbl_name='tbl_course_papersetter_moderator', $cond = '')
	{
		$db2 = $this->load->database('db2', TRUE);
		if(!empty($cond)) {
			foreach($cond as $key=>$val) {
				$db2->where($key, $val);
			}
		}
		$query = $db2->select("*")->get($tbl_name);
		//echo $db2->last_query(); die;
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
		$db2 = $this->load->database('db2', TRUE);
		if(!empty($cond)) {
			foreach($cond as $key=>$val) {
				$db2->where($key, $val);
			}
		}
		$query = $db2->select("*")->get($tbl_name);
		//echo $db2->last_query(); die;
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
		$db2 = $this->load->database('db2', TRUE);
		if(!empty($cond)) {
			foreach($cond as $key=>$val) {
				$db2->where($key, $val);
			}
		}
		if(!empty($betweenDate))
		{
			$from_date = $betweenDate['from_date'];
			$to_date = $betweenDate['to_date'];
			$db2->where("DATE_FORMAT(tbl_create_counselling.createdon,'%m/%d/%Y') >='$from_date'");
			$db2->where("DATE_FORMAT(tbl_create_counselling.createdon,'%m/%d/%Y') <='$to_date'");
		}
		
		$db2->group_by('mentor_id');
		$query = $db2->select("mentor_id, count(*)as total")->get('tbl_create_counselling');
		//echo $db2->last_query(); die;
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
		$db2 = $this->load->database('db2', TRUE);
		//$db2->order_by('tbl_create_counselling.createdon', 'asc');
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
		}
		
		$db2->where('tbl_teaching_scheme.transfer_status', '1');
		$db2->where('tbl_teaching_scheme.cpm_id', '');
		$db2->where('tbl_teaching_scheme.total_ques', '0');
		$db2->where('tbl_teaching_scheme.status', '1');
		$db2->where('tbl_teaching_scheme.academic_id', $academic_id);
		if(!empty($where_in)){
			$db2->where_in('tbl_teaching_scheme.program_id', $where_in);
		}
		//$db2->group_by('`tbl_teaching_scheme`.`course_id`');
		$query = $db2->join('tbl_course', 'tbl_course.id = tbl_teaching_scheme.course_id');
		$query = $db2->join('tbl_department_master', 'tbl_department_master.id = tbl_course.subject_area');
		$query = $db2->join('tbl_school_master', 'tbl_school_master.id = tbl_course.acad_group');
		//$query = $db2->join('tbl_programme_master', 'tbl_programme_master.id = tbl_teaching_scheme.program_id');
		$db2->distinct();
		$query = $db2->select("tbl_teaching_scheme.id,tbl_teaching_scheme.course_id,tbl_teaching_scheme`.`program_id`,  `school_name`, `school_code`,  `tbl_course`.`catalog_nbr`, `tbl_course`.`course_title`, `tbl_course`.`su_paper_id`,`tbl_course`.`catalog_nbr`, `tbl_course`.`acad_group`, tbl_department_master.name, tbl_department_master.department_code")->get('tbl_teaching_scheme');
		//echo $db2->last_query();die;
		return $query->result_array();
		
	}
	
	/*
	* Function : getTransferCourseList_myold
	*/
	public function getTransferCourseList_myold($academic_id = '1', $condition='')
	{
		//$this->db->order_by('tbl_create_counselling.createdon', 'asc');
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db->where($key, $val);
			}
		}
		
		$this->db->where('tbl_teaching_scheme.transfer_status', '1');
		$this->db->where('tbl_teaching_scheme.academic_id', $academic_id);
		
		//$this->db->group_by('`tbl_teaching_scheme`.`course_id`');
		$query = $this->db->join('tbl_course', 'tbl_course.id = tbl_teaching_scheme.course_id');
		$query = $this->db->join('tbl_school_master', 'tbl_school_master.id = tbl_course.acad_group');
		//$query = $this->db->join('tbl_department_master', 'tbl_department_master.id = tbl_course.acad_org','left');
		$query = $this->db->join('tbl_department_master', 'tbl_department_master.id = tbl_course.subject_area');
		//$query = $this->db->join('tbl_programme_master', 'tbl_programme_master.id = tbl_teaching_scheme.program_id');
		$this->db->distinct();
		$query = $this->db->select("tbl_teaching_scheme.course_id,tbl_teaching_scheme`.`program_id`,  `school_name`, `school_code`,  `tbl_course`.`catalog_nbr`, `tbl_course`.`course_title`, `tbl_course`.`su_paper_id`,`tbl_course`.`catalog_nbr`,`tbl_course`.`acad_group`, tbl_department_master.name, tbl_department_master.department_code")->get('tbl_teaching_scheme');
		//echo $this->db->last_query();die;
		return $query->result_array();
		
	}
	
	/*
	* Function : getMenteeStats
	*/
	public function getMenteeStats($cond = '')
	{
		$db2 = $this->load->database('db2', TRUE);
		if(!empty($cond)) {
			foreach($cond as $key=>$val) {
				$db2->where($key, $val);
			}
		}
		$db2->group_by('mentor_id');
		$query = $db2->select("mentor_id, count(*)as total")->get('tbl_mentee');
		//echo $db2->last_query(); die;
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
		$db2 = $this->load->database('db2', TRUE);
        $db2->where('tbl_library_master.is_deleted', '0');
        if(!empty($createdon)){
		  $db2->where("DATE_FORMAT(createdon, '%Y-%m-%d') <= '$createdon'");
		}
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				if($key=='from_date' || $key=='to_date') {
					$db2->where("DATE_FORMAT(tbl_library_master.createdon,'%m/%d/%Y') >='$val'");
				}  else {
					$db2->where($key, $val);
				}
			}
			
		}
		if(!empty($betweenDate))
		{
			$from_date = $betweenDate['from_date'];
			$to_date = $betweenDate['to_date'];
			$db2->where("DATE_FORMAT(tbl_library_master.createdon,'%m/%d/%Y') >='$from_date'");
			$db2->where("DATE_FORMAT(tbl_library_master.createdon,'%m/%d/%Y') <='$to_date'");
		}
		$db2->order_by('tbl_library_master.createdon', 'asc');
		$query = $db2->join('tbl_campus', 'tbl_campus.id = tbl_library_master.campus_id');
		$query = $db2->join('tbl_admin', 'tbl_admin.id = tbl_library_master.author_id');
		$query = $db2->select("tbl_library_master.*, , tbl_campus.campus_name, tbl_admin.first_name,tbl_admin.last_name")->get('tbl_library_master');
		//echo $db2->last_query();die;
		return $query->result_array();
    }
	
	/*
	* Function : getAllMentorInteraction
	*/
	public function getAllMentorInteraction($condition=null, $createdon='', $betweenDate = '')
	{
		$db2 = $this->load->database('db2', TRUE);
        $db2->where('tbl_create_counselling.is_deleted', '0');
        if(!empty($createdon)){
		  $db2->where("DATE_FORMAT(createdon, '%Y-%m-%d') <= '$createdon'");
		}
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
			$db2->where("DATE_FORMAT(tbl_create_counselling.createdon,'%m/%d/%Y') >='$from_date'");
			$db2->where("DATE_FORMAT(tbl_create_counselling.createdon,'%m/%d/%Y') <='$to_date'");
		}
		$db2->order_by('tbl_create_counselling.createdon', 'asc');
		$query = $db2->join('tbl_mentee', 'tbl_mentee.id = tbl_create_counselling.mente_id');
		$query = $db2->join('tbl_admin', 'tbl_admin.id = tbl_create_counselling.mentor_id');
		$query = $db2->select("tbl_create_counselling.id,tbl_create_counselling.issue_type, tbl_create_counselling.point_covered, tbl_create_counselling.critically_level, tbl_create_counselling.next_appointment, tbl_create_counselling.school_id,tbl_create_counselling.department_id,tbl_create_counselling.createdon, tbl_create_counselling.status, tbl_mentee.full_name, tbl_admin.first_name,tbl_admin.last_name")->get('tbl_create_counselling');
		//echo $db2->last_query();die;
		return $query->result_array();
    }
	
	/*
	* Function : validatelogin
	*/
	public function validatelogin($tbl_name, $col = ' * ', $condition= array())
    {
        $time = time();
		$db2 = $this->load->database('db2', TRUE);
        $db2->select($col);
        $db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				if($key=='password'){
					$db2->where($key, md5($val));
				} else {
					
					$db2->where($key, $val);
				}
			}
			
		}
		$query = $db2->get($tbl_name);
		//echo $db2->last_query(); die;
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
	* Function : questionsTotalCount
	*/
	
	public function questionsTotalCount($tbl_name = 'patient_registration', $col='*', $condition= array('tbl_questionbank.is_deleted'=>'0'), $academic_year_id='2')
    {
		$db2 = $this->load->database('db2', TRUE);
		$db2->select('count(tbl_questionbank.id) as total');
		$db2->join('tbl_teaching_scheme ts', "ts.course_id = $tbl_name.course_id", 'left');
		//$this->db->where('academic_year_id', $academic_year_id);
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {  
				$db2->where($key, $val);
			}			
		}
		$query = $db2->get($tbl_name);
		//echo $db2->last_query();die;
		$results = $query->row_array();
		
        return $results['total'];
	}
	/*
	* Function : countrylist
	*/
	
	public function countrylist($tbl_name = 'su_country', $col = ' * ')
    {
        $time = time();
		$db2 = $this->load->database('db2', TRUE);
        $db2->select($col);
        $db2->where('is_deleted', '0');
		$db2->order_by('country_name', 'asc');
        $query = $db2->get($tbl_name);
        return $query->result_array();
    }
	/*
	* Function : occupationlist
	*/
	public function occupationlist($tbl_name = 'sh_occupation', $col = ' * ')
    {
        $time = time();
		$db2 = $this->load->database('db2', TRUE);
        $db2->select($col);
        $db2->where('is_deleted', '0');
		$db2->order_by('title', 'asc');
        $query = $db2->get($tbl_name);
        return $query->result_array();
    }
	
	
	
	/*
	* Function : getCommon2Query
	*/
	
	public function getCommon2Query($tbl_name = 'tbl_schools', $col = ' * ', $condition='')
    {
        $db2 = $this->load->database('db2', TRUE);
        $db2->select($col);
		$db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
			
		}
        $query = $db2->get($tbl_name);
		#echo $db2->last_query(); die;
        return $query->result_array();
    }
	/*
	* Function : getCommonQuery
	*/
	
	public function getCommonQuery($tbl_name = 'tbl_school_master', $col = ' * ', $condition='',$order_by='',$condition_like='')
    {
        $db2 = $this->load->database('db2', TRUE);
        $db2->select($col);
		$db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
			
		}
		// Like condition_like
		if(!empty($condition_like))
		{   $k=1;
			foreach($condition_like as $key=>$val) {
				$db2->like($key, $val);
				if($k>1) {
					$db2->or_like($key, $val);
				}
				$k++;
			}
			
		}
		
		if(!empty($order_by))
		{ 
			foreach($order_by as $key=>$val) {
				$db2->order_by($key, $val);
			}		
		}
        $query = $db2->get($tbl_name);
		//echo $db2->last_query(); //die;
        return $query->result_array();
    }
	
		
	/*
	* Function : checkApplicationDetails
	*/
	
	public function checkApplicationDetails($tbl_name = 'grievance_users', $col = ' * ', $cond)
    {
        $time = time();
		$db2 = $this->load->database('db2', TRUE);
        $db2->select($col);
		
		foreach($cond as $key=>$val) {
			if($val!='' && $key!=''){
				$db2->where($key, $val);
			}
		}
        $db2->where('is_deleted', '0');
		$query = $db2->get($tbl_name);
		//echo $db2->last_query(); die;
        return $query->row_array();
    }
	
	
	/*
	* Function : scheduleAppointmentDetails
	*/
	
	public function scheduleAppointmentDetails($tbl_name = 'schedule_appointment', $col = ' * ', $cond)
    {
		$db2 = $this->load->database('db2', TRUE);
        $db2->select($col);
		foreach($cond as $key=>$val) {
			if($val!='' && $key!=''){
				$db2->where($key, $val);
			}
		}
        $db2->where('appointment_date>=', date('Y-m-d'));
        $db2->where('is_deleted', '0');
        $db2->where('slots_available', '1');
        $db2->where('status', '1');
		$query = $db2->get($tbl_name);
        return $query->row_array();
    }
	
	
	public function saveinfo($tbl_name='', $post)
    {
		if($post['user-content-title']!=''){
			$post['title'] = $post['user-content-title'];
			unset($post['user-content-title']);
		}
		$this->db->insert($tbl_name, $post);
		//echo $this->db->last_query(); die;
		return $this->db->insert_id();
    }
	
	public function updateinfo($tbl_name='', $post, $field, $value)
    {
		if($post['user-content-title']!=''){
			$post['title'] = $post['user-content-title'];
			unset($post['user-content-title']);
		}
		$this->db->where($field, $value);
        if (!$this->db->update($tbl_name, $post)) {
            log_message('error', print_r($this->db->error(), true));
        }
		//echo $this->db->last_query(); die;
	}
	
	public function updateinfoCOs($tbl_name='', $post, $field, $value)
    {
		$this->db->where($field, $value);
        if (!$this->db->update($tbl_name, $post)) {
            log_message('error', print_r($this->db->error(), true));
        }
		//echo $this->db->last_query(); die;
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
	
	public function getSemesterArray($condition, $hodProgramList='')
	{
		$db2 = $this->load->database('db2', TRUE);
		$db2->select('tbl_credits.id,tbl_semester.title,tbl_semester.description');
		$db2->join('tbl_semester', 'tbl_semester.id = tbl_credits.semester_id', 'left');
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
		}
		if(!empty($hodProgramList)){
			    //$db2->where('a.id=(select max(id) from tbl_comments b where a.indent_id=b.indent_id)');			
			    $db2->where_in('tbl_credits.program_id',$hodProgramList);
			}
			
		//$db2->limit(1);
		$query = $db2->get('tbl_credits');
		//echo $db2->last_query();die;
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
		$db2 = $this->load->database('db2', TRUE);
        $db2->select($col);
        $db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
			
		}
		//$db2->order_by('id', 'asc');
        $query = $db2->get($tbl_name);
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
		$db2 = $this->load->database('db2', TRUE);
        $db2->select($col);
        $db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
			
		}
		//$db2->order_by('id', 'asc');
        $query = $db2->get($tbl_name);
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
		$db2 = $this->load->database('db2', TRUE);
        $db2->select($col);
        $db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
			
		}
		//$db2->order_by('id', 'asc');
        $query = $db2->get($tbl_name);
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
		$db2 = $this->load->database('db2', TRUE);
        $db2->select($col);
        $db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
			
		}
		if(!empty($order_by))
		{ 
			foreach($order_by as $key=>$val) {
				$db2->order_by($key, $val);
			}
			
		}
		//$db2->order_by('id', 'asc');
        $query = $db2->get($tbl_name);
		$results = array();
		//echo $db2->last_query(); die;
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
		$db2 = $this->load->database('db2', TRUE);
        $db2->select($col);
        $db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
			
		}
		if(!empty($order_by))
		{ 
			foreach($order_by as $key=>$val) {
				$db2->order_by($key, $val);
			}
			
		}
		//$db2->order_by('id', 'asc');
        $query = $db2->get($tbl_name);
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
		$db2 = $this->load->database('db2', TRUE);
        $db2->select($col);
        $db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
			
		}
		if(!empty($order_by))
		{ 
			foreach($order_by as $key=>$val) {
				$db2->order_by($key, $val);
			}
			
		}
		//$db2->like('( is_hod="1" OR is_pc="1" )');
		//$db2->order_by('id', 'asc');
        $query = $db2->get($tbl_name);
		//echo $db2->last_query(); //die;
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
		$db2 = $this->load->database('db2', TRUE);
        $db2->select($col);
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
		#echo $this->db->last_query(); die;
		#echo $this->db->last_query(); die;
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
		$db2 = $this->load->database('db2', TRUE);
		$db2->select('id,school_name, school_code');
		$db2->where('is_deleted', '0');
		$db2->where('status', '1');
		$db2->where('school_name', $name_value);
		$query = $db2->get('tbl_schools');
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
		$db2 = $this->load->database('db2', TRUE);
		$db2->select('id,department_name');
		$db2->where('is_deleted', '0');
		$db2->where('status', '1');
		$db2->where('department_name', $name_value);
		$query = $db2->get('tbl_departments');
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
		$db2 = $this->load->database('db2', TRUE);
        $db2->select($col);
        $db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
			
		}
		//$db2->order_by('id', 'asc');
        $query = $db2->get($tbl_name);
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
        $db2 = $this->load->database('db2', TRUE);
        $db2->select($col);
		$db2->where('is_deleted', '0');
		if(!empty($where_role)){
		    $db2->where($where_role);
		}
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
			
		}
		if(!empty($order_by))
		{ 
			foreach($order_by as $key=>$val) {
				$db2->order_by($key, $val);
			}		
		}
        $query = $db2->get($tbl_name);
		//echo $db2->last_query();die;
        return $query->result_array();
    }

	public function getCommonJoinRecords($tbl_name,$col='*',$where_role='',$indentLevel='',$condition='',$order_by='')
	{		
			$db2 = $this->load->database('db2', TRUE);
			$db2->select('tbl_indents.*,a.indentLevel,a.id as commentId,a.actionUserId,a.actionRoleId');
			//$db2->select('tbl_indents.*,a.indentLevel');
			$db2->join('tbl_comments a', 'tbl_indents.id = a.indent_id', 'left');
			$db2->where('a.id=(select max(id) from tbl_comments b where a.indent_id=b.indent_id)');			
			if(!empty($where_role)){
			    $db2->where($where_role);
			}
			if(!empty($indentLevel)){
			    //$db2->where('a.id=(select max(id) from tbl_comments b where a.indent_id=b.indent_id)');			
			    $db2->where_in('a.indentLevel',$indentLevel);
			}
			$db2->where('tbl_indents.is_deleted', '0');
			if(!empty($condition))
		    { 
			    foreach($condition as $key=>$val) {
				    $db2->where($key, $val);
			    }			
		    }
			if(!empty($order_by))
		    { 
			    foreach($order_by as $key=>$val) {
				    $db2->order_by($key, $val);
			    }		
		    }
            $query = $db2->get($tbl_name);
			//echo $db2->last_query();die;
            return $query->result_array();
	}

	public function getMaxActionId($col='*',$indent_id='',$condition='')
	{		
			$db2 = $this->load->database('db2', TRUE);
			$db2->select($col);
			$db2->from('tbl_comments a');
			$db2->where('a.indent_id', $indent_id);
			$db2->where('a.id=(select max(id) from tbl_comments b where a.indent_id=b.indent_id)');			
			$db2->where('a.is_deleted', '0');
			if(!empty($condition))
		    { 
			    foreach($condition as $key=>$val) {
				    $db2->where($key, $val);
			    }			
		    }
            $query = $db2->get();
			//echo $db2->last_query();die;
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
		//$otherdb = $this->load->database('db2', TRUE);
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
		//$otherdb = $this->load->database('db2', TRUE);
		$this->db->where($field, $value);
        if (!$this->db->update($tbl_name, $post)) {
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
		$db2 = $this->load->database('db2', TRUE);
		$time = time();
        $db2->select($col);
        $db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
			
		}
		//$otherdb->order_by('id', 'asc');
        $query = $db2->get($tbl_name);
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
		$otherdb = $this->load->database('db2', TRUE);
		if(!empty($course_area)){
			$sql = "SELECT * FROM `tbl_managebucket_ref` WHERE FIND_IN_SET('".$prog_code."',`program_code`) AND `program_cond`='".$prog_cond."' AND `course_reference`='".$course_reference."' AND `course_area`='".$course_area."' AND is_deleted='0' AND status='1'";
		}
		else{
			$sql = "SELECT * FROM `tbl_managebucket_ref` WHERE FIND_IN_SET('".$prog_code."',`program_code`) AND `program_cond`='".$prog_cond."' AND `course_reference`='".$course_reference."'  AND is_deleted='0' AND status='1'";
		}//die;
		//$sql = "SELECT DISTINCT sd.system_id, sd.rollno,sd.name,sd.email,sg.current_term ,sg.sgpa,sg.cgpa FROM `student_details` sd JOIN student_grade sg ON sd.system_id=sg.system_id where sd.school_code='".$school_code."' AND `department`='".$department."' AND sd.prog_name='".$programm."' ";

		$query = $otherdb->query($sql);
		//echo $db2->last_query(); die;
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
		$db2 = $this->load->database('db2', TRUE);
		$db2->select('tbl_academic_block_master.*, tbl_academic_master.academic_name as campus');
		$db2->join('tbl_academic_master', 'tbl_academic_master.id = tbl_academic_block_master.campus_id', 'left');
		if($id>0){
			$db2->where('tbl_academic_block_master.id', $id);
			$db2->limit(1);
		}
		$query = $db2->get('tbl_academic_block_master');
		//echo $db2->last_query();die;
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
		$db2 = $this->load->database('db2', TRUE);
		$db2->select('tbl_block_room_master.*, tbl_academic_master.academic_name as campus, tbl_academic_block_master.display_name as block');
		$db2->join('tbl_academic_master', 'tbl_academic_master.id = tbl_block_room_master.campus_id', 'left');
		$db2->join('tbl_academic_block_master', 'tbl_academic_block_master.id = tbl_block_room_master.block_id', 'left');
		if($id>0){
			$db2->where('tbl_block_room_master.id', $id);
			$db2->limit(1);
		}
		$query = $db2->get('tbl_block_room_master');
		//echo $db2->last_query();die;
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
		$db2 = $this->load->database('db2', TRUE);
		$db2->select('tbl_academic_season_master.*, tbl_academic_master.academic_name as campus,tbl_academicyear.academic_year as year, tbl_school_master.school_name');
		$db2->join('tbl_academic_master', 'tbl_academic_master.id = tbl_academic_season_master.campus_id', 'left');
		$db2->join('tbl_academicyear', 'tbl_academicyear.id = tbl_academic_season_master.year_id', 'left');
		$db2->join('tbl_school_master', 'tbl_school_master.id = tbl_academic_season_master.school_id', 'left');
		if($id>0){
			$db2->where('tbl_academic_season_master.id', $id);
			$db2->limit(1);
		}
		$query = $db2->get('tbl_academic_season_master');
		//echo $db2->last_query();die;
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
		$db2 = $this->load->database('db2', TRUE);
		$db2->select('sum(tbl_course.units_acad_prog) as total,tbl_teaching_scheme.semester_id,tbl_teaching_scheme.course_id,tbl_teaching_scheme.program_id, credits');
		$db2->join('tbl_course', 'tbl_course.id = tbl_teaching_scheme.course_id');
		$db2->join('tbl_credits', 'tbl_credits.id = tbl_teaching_scheme.semester_id');
		if(!empty($condArray)){
			foreach($condArray as $key=>$val) {
				$db2->where($key, $val);
			}
			
		}
		$db2->where('tbl_course.grading_basis', 'GRD');
		
		$query = $db2->get('tbl_teaching_scheme');
		//echo $db2->last_query(); die;
		$results = array();
		$resp = $query->result_array();
		foreach($resp as $row){
			$results[$row['program_id']][$row['semester_id']] = $row;
		}
		return $results;
		
	}
	public function getCommonIdProgram($tbl_name='tbl_schools', $col = ' * ', $condition=null, $prog_id=null, $order_by=null)
    {
		$db2 = $this->load->database('db2', TRUE);
        $time = time();
        $db2->select($col);
        $db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
			
		}
		if(!empty($prog_id) && $prog_id!=''){			
		    $db2->where_in('id',$prog_id);
		}
		if(!empty($order_by))
		{ 
			foreach($order_by as $key=>$val) {
				$db2->order_by($key, $val);
			}
			
		}
		//$db2->order_by('id', 'asc');
        $query = $db2->get($tbl_name);
		$results = array();
		//echo $db2->last_query(); die;
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
		//echo $db2->last_query();die;
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
		//echo $db2->last_query();die;
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
		$db2 = $this->load->database('db2', TRUE);
		$db2->select('tbl_obesemestercredits.id,tbl_semester.title,tbl_semester.description');
		$db2->join('tbl_semester', 'tbl_semester.id = tbl_obesemestercredits.semester_id', 'left');
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
		}
		if(!empty($hodProgramList)){
			    //$db2->where('a.id=(select max(id) from tbl_comments b where a.indent_id=b.indent_id)');			
			    $db2->where_in('tbl_obesemestercredits.program_id',$hodProgramList);
			}
			
		//$db2->limit(1);
		$query = $db2->get('tbl_obesemestercredits');
		//echo $db2->last_query();die;
		return $query->result_array();
		
	}
	
	/*
	* Function : getAllOBETechCourseRecords
	*/
	public function getAllOBETechCourseRecords($condition='')
	{
		//$db2->order_by('tbl_create_counselling.createdon', 'asc');
		$db2 = $this->load->database('db2', TRUE);
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
		}
		
		$db2->where(' tbl_obe_teaching_scheme.status', '1');
		$db2->where(' tbl_obe_teaching_scheme.is_deleted', '0');
		
		if(!empty($where_in)){
			$db2->where_in('tbl_obe_teaching_scheme.id', $where_in);
		}
		$query = $db2->join('tbl_course', 'tbl_course.id =  tbl_obe_teaching_scheme.course_id');
		$db2->distinct();
		$query = $db2->select(" tbl_obe_teaching_scheme.id,tbl_obe_teaching_scheme.school_id,tbl_obe_teaching_scheme.department_id,tbl_obe_teaching_scheme.semester_id, tbl_obe_teaching_scheme.course_id, tbl_obe_teaching_scheme`.`academic_year_id`, tbl_obe_teaching_scheme`.`program_id`,tbl_obe_teaching_scheme`.`program_id`, `tbl_course`.`catalog_nbr`, `tbl_course`.`course_title`, `tbl_course`.`su_paper_id`,`tbl_course`.`catalog_nbr`, `tbl_course`.`acad_group`, `tbl_course`.`lecture`, `tbl_course`.`tutorial`, `tbl_course`.`practical`, `tbl_course`.`units_maximum`")->get('tbl_obe_teaching_scheme');
		//echo $db2->last_query();die;
		return $query->result_array();
		
	}
	
	public function getCourseSectionAltArray($tbl_name, $where_id='')
	{
		$db2 = $this->load->database('db2', TRUE);
		$db2->select('*');
		if(!empty($where_id)){
			$db2->where_in("$tbl_name.course_id", $where_id);
		}
		$query = $db2->get($tbl_name);
		//echo $db2->last_query();die;
		return $query->result_array();
		
	}
	
	/*
	* Function : getProgramWiseCourseList
	*/
	public function getProgramWiseCourseList($condition='')
	{
		$db2 = $this->load->database('db2', TRUE);
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
		}
		$query = $db2->join('tbl_course', 'tbl_course.id = tbl_teaching_scheme.course_id');
		$db2->distinct();
		$query = $db2->select(" tbl_teaching_scheme.course_id, tbl_teaching_scheme`.`program_id`, `tbl_course`.`course_title`, `tbl_course`.`su_paper_id`,`tbl_course`.`catalog_nbr`,`tbl_course`.`lecture`,`tbl_course`.`tutorial`,`tbl_course`.`practical` ")->get('tbl_teaching_scheme');
		//echo $db2->last_query();die;
		return $query->result_array();
		
	}
	
	/* Function : getCommonDBFourArray
	* DB Connection : dbfour
	*/
	public function getCommonDBFourArray($tbl_name='stu_enrollment',$condition='',  $col="*", $order_by = NULL, $limit=NULL, $start=NULL)
	{
	  $otherndb = $this->load->database('db4', TRUE);
	
	  $time = time();
        $otherndb->select($col);
        $otherndb->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$otherndb->where($key, $val);
			}
		}
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$otherndb->order_by($key, $val);
			}
		}
		
        $query = $otherndb->get($tbl_name,'4', '0');
       
		//echo $otherndb->last_query(); die;
		return $query->result_array();
	
    }
	
	public function getAttendanceApprovalCases($system_id, $term, $from_date, $to_date) {
		$db2 = $this->load->database('db2', TRUE);
		$db2->select('*');
		$db2->from('tbl_attendanceapprovalcases');
		$db2->where('status', '1');
		$db2->where('is_deleted', '0');
		$db2->where('system_id', $system_id);
		$db2->where('term', $term);
		$db2->group_start();
		$db2->where('from_date >=', $from_date);
		$db2->where('from_date <=', $to_date);
		$db2->or_where('to_date >=', $from_date);
		$db2->where('to_date <=', $to_date);
		$db2->group_end();

		$query = $db2->get();
		//echo $db2->last_query();die;
		return $query->result();
	}
	
	public function getAllEmployeeIds($employees_code) 
	{
		$db2 = $this->load->database('db2', TRUE);
		$db2->select('id');
		$db2->from('tbl_employee_master');
		$db2->where_in('employee_id', $employees_code);
		$db2->where('status', '1');
		$query = $db2->get();
		$result = $query->result_array();
		//echo $db2->last_query();die;
		return array_column($result, 'id');
	}
	
	
	public function getCommonRecordsArray($table, $select, $where_in)
    {
		$db2 = $this->load->database('db2', TRUE);
        $db2->select($select);
		if(!empty($where_in['id'])){
			if (isset($where_in['id']) && is_array($where_in['id'])) {
				$db2->where_in('id', $where_in['id']);
			}
		}
        if (isset($where_in['status'])) {
            $db2->where('status', $where_in['status']);
        }
        $query = $db2->get($table);
        return $query->result_array();
    }

}