<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Class Employersurvey_Model extends CI_Model{
	
	function arrayslice($associativeArray,$startKey, $endKey){
			
		// Extract keys from the associative array
		$keys = array_keys($associativeArray);

		// Find the indexes of start and end keys
		$startIndex = array_search($startKey, $keys);
		$endIndex = array_search($endKey, $keys);

		// Slice the keys array
		$slicedKeys = array_slice($keys, $startIndex, $endIndex - $startIndex + 1);

		// Extract values based on sliced keys
		$slicedArray = array_intersect_key($associativeArray, array_flip($slicedKeys));
		
		if(empty($slicedArray)){
			$slicedKeys = array_slice($keys, $startIndex);
			// Extract values based on sliced keys
			$slicedArray = array_intersect_key($associativeArray, array_flip($slicedKeys));
		}
		
		return $slicedArray;
	}
	/*
	*
	* Function : save_answer
	*
	*/
	
	function save_answer(){
		$date_created = date('Y-m-d H:i:s');
		$date = date('Y-m-d');
		extract($_POST);
			foreach($qid as $k => $v){
				$data = " survey_id=$survey_id ";
				$data .= ", survey_for='$survey_for' ";
				$data .= ", full_name='$full_name' ";
				$data .= ", system_id='$system_id' ";
				$data .= ", gender='$gender' ";
				$data .= ", designations='$designations' ";
				$data .= ", affiliation='$affiliation' ";
				$data .= ", department='$department' ";
				$data .= ", programme='$programme' ";
				$data .= ", batch_name='$batch_name' ";
				$data .= ", year='$year' ";
				$data .= ", date='$date' ";
				$data .= ", date_created='$date_created' ";
				$data .= ", question_id='$qid[$k]' ";
				$data .= ", user_id='0' ";
				if($_POST['ques_type_'.$qid[$k]] == 'check_opt'){
					$data .= ", answer='[".implode("],[",$answer[$k])."]' ";
				} else {
					
					if($_POST['ques_type_'.$qid[$k]]=='textfield_b'){
						$answer ='';
						if($_POST['ans_id_'.$qid[$k]]==$qid[$k]){
							$nqid =$qid[$k]+1;
							$resp = $this->arrayslice($_POST,'ques_type_'.$qid[$k], 'ques_type_'.$nqid);
							$answerValue = serialize($resp);
						}
						
						$data .= ", answer='$answerValue' ";	
						
					} else if($_POST['ques_type_'.$qid[$k]]=='textfield_s'){
						$quid =$qid[$k];
						$myQAnswer = $_POST['answer'][$quid];
						$data .= ", answer='$myQAnswer' ";
					} else { 		
						$quid =$qid[$k];
						if($quid>0){
							$myAnswer = $_POST['answer'][$quid];						
							$data .= ", answer='$myAnswer' ";
							
							if('other_ans_'.$quid!='') {
								$othRow = $_POST["other_ans_".$quid];
								$data .= ", answer_other='$othRow'";
							}
						}
					}
				}
				//if($quid=='5') {
					//print_r($_POST['answer']);
					//echo "INSERT INTO overall_survey_answers set $data"; die;
				//}
				//echo "INSERT INTO overall_survey_answers set $data"; die;
				$save[] = $this->db->query("INSERT INTO overall_survey_answers SET $data");
			}
		if(isset($save))
		return 1;
	}
	
	function save_question(){
	
		extract($_POST);
			unset($_POST['addrecord']);
			$nquestion = str_replace("'","\'",$question);
			$data = " survey_id=$sid ";
			$data .= ", category='$category' ";
			$data .= ", question='$nquestion' ";
			##$data .= ", hindi_question='$hindi_question' ";
			$data .= ", order_by='$order_by' ";
			$data .= ", status='$status' ";
			$data .= ", type='$type' ";
			if(!in_array($type,array('textfield_s','textfield_b'))){
				$arr = array();
				foreach ($label as $k => $v) {
					$i = 0 ;
					while($i == 0){
						$k = substr(str_shuffle(str_repeat($x='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(5/strlen($x)) )),1,5);
						if(!isset($arr[$k]))
							$i = 1;
					}
					if(!empty($v)){
					$arr[$k] = $v;
					}
				}
			   $data .= ", frm_option='".json_encode($arr,JSON_UNESCAPED_UNICODE)."' ";
			} else {
				if(trim($type)=='textfield_b') {
					$data .= ", frm_option='".base64_encode($_POST['frm_textarea_opt'])."'"; 
					#$data .= ", frm_hindi_option='".base64_encode($_POST['frm_hindi_option'])."'";
				} else {
					$data .= ", frm_option='' ";
					#$data .= ", frm_hindi_option='' ";
				}
			}
	
		if(empty($id)){
			$save = $this->db->query("INSERT INTO survey_questions set $data");
		}else{
			$save = $this->db->query("UPDATE survey_questions set $data where id = $id");
		}

		if($save)
			return 1;
	}
	
	/*
	* Function : save_survey
	*/	
	public function savesurvey(){
		extract($_POST);
		unset($_POST['addrecord']);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		
		if(empty($id)){
			$save = $this->db->query("INSERT INTO survey_set set $data");
		}else{
			$save = $this->db->query("UPDATE survey_set set $data where id = $id");
		}

		if($save)
			return 1;
	}
	
	public function customQuerySIngleRecords($id, $system_id){
		$response = array();
		if($id>0){
			$sql = "SELECT a.*,q.type from overall_survey_answers a inner join survey_questions q on q.id = a.question_id where a.survey_id ={$id} AND a.system_id ={$system_id}";
			$results = $this->db->query($sql);
			$response = $results->result_array();
		}
		return $response;
		
	}
	
	public function customQueryRecords($id){
		$response = array();
		if($id>0){
			$sql = "SELECT a.*,q.type from overall_survey_answers a inner join survey_questions q on q.id = a.question_id where a.survey_id ={$id}";
			$results = $this->db->query($sql);
			$response = $results->result_array();
		}
		return $response;
		
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
	public function getAllFeedbackNRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL,$betweenDate = NULL)
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
		
		if(!empty($betweenDate))
		{
			$from_date = $betweenDate['from_date'];
			$to_date = $betweenDate['to_date'];
			$this->db->where("DATE_FORMAT(tbl_feedback_ratingrecord.createdon,'%Y-%m-%d') >='$from_date'");
			$this->db->where("DATE_FORMAT(tbl_feedback_ratingrecord.createdon,'%Y-%m-%d') <='$to_date'");
		}
		
		if ($limit !== null && $start !== null) {
           $query = $this->db->get($tbl_name,$limit, $start);
        } else {
			$query = $this->db->get($tbl_name);
		}
		//echo $this->db->last_query(); die;
		return $query->result_array();
    }
	
	/* Function : getAllRecords
	*/
	public function getAllRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL)
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
	* Function : getSingleRecord
	*/
	public function getSingleRecord($tbl_name, $col = ' * ', $condition=null)
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
        return $query->row_array();
    }
	
	
	/*
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
	* Function : registrationCount
	*/
	
	public function registrationCount($tbl_name = 'patient_registration')
    {
        $result = $this->db->query("SELECT id FROM ".$tbl_name." where is_deleted='0'");
        return $result->num_rows();
        
    }
	public function customCountRow($survey_id)
    {
        $result = $this->db->query("SELECT distinct(system_id) FROM overall_survey_answers where is_deleted='0' AND survey_id = '".$survey_id."'");
        return $result->num_rows();
        
    }
	/*
	* Function : questionsCount
	*/
	
	public function questionsCount($tbl_name = 'tbl_feedback_ratingrecord', $question_id='1',$search_fromdate='',$search_todate='')
    {
		$db2 = $this->load->database('dbreport', TRUE);
		$time = time();
        $db2->select($col);
        $db2->where('is_deleted', '0');
        $db2->where('q_id', $question_id);
		if($search_fromdate){
			$db2->where('createdon >=', $search_fromdate .'00:00:00');
		}
		if($search_todate){
			$db2->where('createdon <=', $search_todate.'23:59:59');
		}
		$result = $db2->get($tbl_name);
		//echo $this->db->last_query(); die;
	    return $result->num_rows();
        
    }
	/*
	* Function : questionsRatingCount
	*/
	
	public function questionsRatingCount($tbl_name = 'tbl_feedback_ratingrecord', $question_id='1', $rating_id='1',$search_fromdate='',$search_todate='')
    {
		$db2 = $this->load->database('dbreport', TRUE);
		$sql .= "SELECT id FROM ".$tbl_name." where is_deleted='0' AND q_id=$question_id AND r_id=$rating_id";
		if($search_fromdate){
			$sql .=' AND createdon>="'.date('Y-m-d',strtotime($search_fromdate)).' 00:00:00"';
		}
		if($search_todate){
			$sql .=' AND createdon<="'.date('Y-m-d',strtotime($search_todate)).' 23:59:59"';
		}
		//echo $sql;
        $result = $db2->query($sql);
        return $result->num_rows();
        
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
	* Function : getCommonDistinctQuery
	*/
	
	public function getCommonDistinctQuery($tbl_name = 'tbl_schools', $col = ' * ', $condition='', $order_by='', $where_in='',$where_key='survey_id')
    {
       $this->db->distinct();
       $this->db->select($col);
		$this->db->where('is_deleted', '0');
		if(!empty($where_in)){
		$this->db->where_in($where_key, $where_in);
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
			$this->db->where("DATE_FORMAT(".$tbl_name.".date_created,'%Y-%m-%d') >='$from_date'");
			$this->db->where("DATE_FORMAT(".$tbl_name.".date_created,'%Y-%m-%d') <='$to_date'");
		}
		
		if(!empty($order_by))
		{ 
			foreach($order_by as $key=>$val) {
				$this->db->order_by($key, $val);
			}
			
		}
        $query = $this->db->get($tbl_name);
        return $query->result_array();
    }
	
	/*
	* Function : getCommonQuery
	*/
	
	public function getCommonQuery($tbl_name = 'tbl_schools', $col = ' * ', $condition='', $order_by='', $betweenDate='', $where_id='')
    {
        $db2 = $this->load->database('dbreport', TRUE);
        $db2->select($col);
		$db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
			
		}
		if(!empty($where_id))
		{ 
			$db2->where_in('slag_id', $where_id);
		}
		
		if(!empty($betweenDate))
		{
			$from_date = $betweenDate['from_date'];
			$to_date = $betweenDate['to_date'];
			$db2->where("DATE_FORMAT(".$tbl_name.".createdon,'%Y-%m-%d') >='$from_date'");
			$db2->where("DATE_FORMAT(".$tbl_name.".createdon,'%Y-%m-%d') <='$to_date'");
		}
		
		if(!empty($order_by))
		{ 
			foreach($order_by as $key=>$val) {
				$db2->order_by($key, $val);
			}
			
		}
        $query = $db2->get($tbl_name);
        return $query->result_array();
    }
	
	/*
	* Function : getCommonQuery
	*/
	
	public function getCommonLikeQuery($tbl_name = 'tbl_schools', $col = ' * ', $condition='', $order_by='', $betweenDate=NULL)
    {
        
        $this->db->select($col);
		$this->db->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				//$this->db->where($key, $val);
				$this->db->like($key, $val, 'after');
			}
			
		}
		if(!empty($betweenDate))
		{
			$from_date = $betweenDate['from_date'];
			$to_date = $betweenDate['to_date'];
			$this->db->where("DATE_FORMAT($tbl_name.createdon,'%Y-%m-%d') >='$from_date'");
			$this->db->where("DATE_FORMAT($tbl_name.createdon,'%Y-%m-%d') <='$to_date'");
		}
		
		if(!empty($order_by))
		{ 
			foreach($order_by as $key=>$val) {
				$this->db->order_by($key, $val);
			}
			
		}
        $query = $this->db->get($tbl_name);
		//echo $this->db->last_query(); die;
        return $query->result_array();
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
	public function getSchoolList($tbl_name='su_schools', $col = ' * ', $condition=null)
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
	public function getDepartmentList($tbl_name='su_departments', $col = ' * ', $condition=null)
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
	* Function: getFullCommonListBYID
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getCommonSingleLikeRecord($tbl_name='su_departments', $col = ' * ', $condition=null)
    {
        $time = time();
        $this->db->select($col);
        $this->db->where('is_deleted', '0');
        $this->db->where('status', '1');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db->like($key, $val, 'before');
			}
			
		}
		$query = $this->db->get($tbl_name);
		//echo $this->db->last_query(); die;
		$results = array();
		$results = $query->row_array();
        return $results;
    }
	
	/*
	* Function: getFullCommonListBYID
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getFullCommonListBYID($tbl_name='su_departments', $col = ' * ', $condition=null)
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
	* Function: getGrievanceList
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getCommonIdArray($tbl_name='tbl_schools', $col = ' * ', $condition=null)
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
		$db2 = $this->load->database('dbreport', TRUE);
        $db2->select($col);
        $db2->where('status', '1');
        $db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
			
		}
		$query = $db2->get($tbl_name);
		$results = array();
		$results = $query->row_array();
	    return $results;
	}
	

	public function getAllRecordscount($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL)
    {
        $time = time();
		$db2 = $this->load->database('dbreport', TRUE);
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
		if ($limit !== null && $start !== null) {
           $query = $db2->get($tbl_name,$limit, $start);
        } else {
			$query = $db2->get($tbl_name);
		}
		//echo $this->db->last_query(); die;
		return $query->num_rows();
    }
	
	// Function for deletion
	public function deleterecords($tbl_name, $id){
		if($tbl_name!='' && $id>0){
			 $data = array('is_deleted'=>'1','status'=>'0');
			$this->db->query("UPDATE $tbl_name set $data where id = $id");
		}
	}
	
	
	// Function for deletethisrecords
	public function deletethisrecords($tbl_name, $id){
		if($tbl_name!='' && $id>0){
			
			$this->db->query("DELETE FROM $tbl_name where id = $id");
		}
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
	
	/*
	* Function : getAllQuestionsMaster
	**
	*
	*/
	public function getAllQuestionsMaster($condition=null)
	{
        if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db->where($key, $val);
			}
		}
		$this->db->order_by('tbl_questions_master.display_order', 'asc');
		$query = $this->db->join('tbl_checklist_master', 'tbl_checklist_master.id = tbl_questions_master.category_id');
		$query = $this->db->select("tbl_questions_master.*, checklist")->get('tbl_questions_master');
		//echo $this->db->last_query();die;
		return $query->result_array();
    }
	
	/*
	* Function : getpdmsipCommonQuery
	**
	*
	*/
	public function getpdmsipCommonQuery($condition=null, $betweenDate=NULL)
	{
		$db2 = $this->load->database('dbreport', TRUE);
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
			$db2->where("DATE_FORMAT(tbl_feedback_otherrecords.createdon,'%Y-%m-%d') >='$from_date'");
			$db2->where("DATE_FORMAT(tbl_feedback_otherrecords.createdon,'%Y-%m-%d') <='$to_date'");
		}
		$db2->order_by('tbl_feedback_otherrecords.id', 'desc');
		$query = $db2->join('tbl_feedback_otherrecords', 'tbl_feedback_otherrecords.slag_id = tbl_pdms_ip_patient_info.slag_id');
		$query = $db2->select("tbl_pdms_ip_patient_info.*, suggestion,reason_if_no,recommedation, tbl_feedback_otherrecords.createdon as othcreatedon")->get('tbl_pdms_ip_patient_info');
		//echo $this->db->last_query();die;
		return $query->result_array();
    }
	
	/*
	* Function : getpdmsopCommonQuery
	**
	*
	*/
	public function getpdmsopCommonQuery($condition=null, $betweenDate =NULL)
	{
		$db2 = $this->load->database('dbreport', TRUE);
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
			$db2->where("DATE_FORMAT(tbl_feedback_otherrecords.createdon,'%Y-%m-%d') >='$from_date'");
			$db2->where("DATE_FORMAT(tbl_feedback_otherrecords.createdon,'%Y-%m-%d') <='$to_date'");
		}
		
		$db2->order_by('tbl_pdms_op_patient_info.createdon', 'desc');
		$query = $db2->join('tbl_feedback_otherrecords', 'tbl_feedback_otherrecords.slag_id = tbl_pdms_op_patient_info.slag_id');
		$query = $db2->select("tbl_pdms_op_patient_info.*, suggestion,reason_if_no,recommedation, tbl_feedback_otherrecords.createdon as othcreatedon")->get('tbl_pdms_op_patient_info');
		//echo $this->db->last_query();die;
		return $query->result_array();
    }
	
	/*
	* Function : getpdmsipPatientCommonQuery
	**
	*
	*/
	public function getpdmsipPatientCommonQuery($condition=null)
	{
        if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db->where($key, $val);
			}
		}
		$this->db->order_by('tbl_pdms_ip_patient_info.createdon', 'desc');
		$query = $this->db->join('tbl_feedback_ratingrecord', 'tbl_feedback_ratingrecord.slag_id = tbl_pdms_ip_patient_info.slag_id');
		$query = $this->db->select("tbl_pdms_ip_patient_info.*, tbl_feedback_ratingrecord.createdon")->get('tbl_pdms_ip_patient_info');
		//echo $this->db->last_query();die;
		return $query->result_array();
    }
	
	/*
	* Function : getpdmsopPatientCommonQuery
	**
	*
	*/
	public function getpdmsopPatientCommonQuery($condition=null)
	{
        if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db->where($key, $val);
			}
		}
		$this->db->order_by('tbl_pdms_op_patient_info.createdon', 'desc');
		$query = $this->db->join('tbl_feedback_ratingrecord', 'tbl_feedback_ratingrecord.slag_id = tbl_pdms_op_patient_info.slag_id');
		$query = $this->db->select("tbl_pdms_op_patient_info.*,tbl_feedback_ratingrecord.createdon")->get('tbl_pdms_op_patient_info');
		//echo $this->db->last_query();die;
		return $query->result_array();
    }
	/*
	* Function : getFeedbackCollectionCommonQuery
	**
	*
	*/
	public function getFeedbackCollectionCommonQuery($condition=null, $betweenDate='')
	{
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
			$this->db->where("DATE_FORMAT(tbl_feedbackcollection.createdon,'%Y-%m-%d') >='$from_date'");
			$this->db->where("DATE_FORMAT(tbl_feedbackcollection.createdon,'%Y-%m-%d') <='$to_date'");
		}
			
		$this->db->order_by('tbl_feedbackcollection.createdon', 'desc');
		$query = $this->db->join('tbl_feedback_source', 'tbl_feedback_source.id = tbl_feedbackcollection.feedback_source');
		//$query = $this->db->join('tbl_departments', 'tbl_departments.id = tbl_feedbackcollection.department_id','left');
		$query = $this->db->select("tbl_feedbackcollection.*,tbl_feedback_source.title")->get('tbl_feedbackcollection');
		//echo $this->db->last_query();die;
		return $query->result_array();
    }

}