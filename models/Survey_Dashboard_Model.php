<?php
defined('BASEPATH') OR exit('No direct script access allowed'); 
class Survey_Dashboard_Model extends CI_Model {

function __construct(){
parent::__construct();
if(! $this->session->userdata('adid'))
redirect('admin/login');
}

/*
	* Function : getCommonQuery
	*/
	
	public function getCommonNumrowsQuery($tbl_name = 'tbl_schools', $col = ' * ', $condition='')
    {
        
        $this->db->select($col);
		//$this->db->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				if($key=='discharge_status') {
					$this->db->where("discharge_date IS NULL");
				} else {
				$this->db->where($key, $val);
				}
			}
			
		}
        $query = $this->db->get($tbl_name);
		//echo $this->db->last_query(); die;
        return $query->num_rows();
    }
/*
	* Function : getCommonQuery
	*/
	
	public function getCommonQuery($tbl_name = 'tbl_schools', $col = ' * ', $condition='')
    {
        
        $this->db->select($col);
		//$this->db->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db->where($key, $val);
			}
			
		}
        $query = $this->db->get($tbl_name);
		//echo $this->db->last_query(); die;
        return $query->result_array();
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
	* Function : getSingleRecord
	*/
	public function getSingleObjRecord($tbl_name, $col = ' * ', $condition=null)
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
		$query = $this->db->get($tbl_name);
		//echo $this->db->last_query(); die;
        return $query->row();
    }
	
	/*
	* function : countlastsevendays
	*
	*/
	public function countlastsevendays(){
	$query2=$this->db->select('id')   
					 ->where('regDate >=  DATE(NOW()) - INTERVAL 10 DAY')
					 ->get('tblusers');
	return  $query2->num_rows();
	}

	/*
	* function : countthirtydays
	*
	*/
	public function countthirtydays(){
	$query3=$this->db->select('id')   
					 ->where('regDate >=  DATE(NOW()) - INTERVAL 30 DAY')
					 ->get('tblusers');
	return  $query3->num_rows();
	}

	/*
	* function : questionWiseSurveyCount
	*
	*/
	public function questionWiseSurveyCount($survey_id = '', $question_id = ''){
		
		$sql = "";
		$sql .= "SELECT count(distinct contact_no) as total,question_category FROM `survey_questions` join survey_answers ON survey_questions.id=survey_answers.question_id WHERE survey_questions.status='1'";
		if($survey_id>0) {
		$sql .= " AND survey_answers.survey_id=$survey_id";
		}
		if($question_id>0) {
		$sql .= " AND survey_answers.question_id=$question_id";
		}
		$sql .= " group by question_category";

		$query3=$this->db->query($sql);
		return $query3->result_array();
	}
	/*
	* function : questionWiseSurveyCount
	*
	*/
	public function questionTotalWiseSurveyCount($survey_id = '', $question_id = ''){
		
		$sql = "";
		$sql .= "SELECT survey_answers.* FROM survey_answers WHERE status='1'";
		if($survey_id>0) {
			$sql .= " AND survey_answers.survey_id=$survey_id";
		}
		if($question_id>0) {
			$sql .= " AND survey_answers.question_id=$question_id";
		}
		//$sql .= "group by question_category";
		$query3=$this->db->query($sql);
		return $query3->num_rows();
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
	* Function :getfeedbackPatientIPByGroup
	*
	*/
	
	
	public function getfeedbackPatientIPByGroup($cond='',$betweenDate=''){
		if(!empty($cond)) {
			foreach($cond as $key=>$val) {
				$this->db->where($key, $val);
			}
		}
		
		if(!empty($betweenDate))
		{
			$from_date = $betweenDate['from_date'];
			$to_date = $betweenDate['to_date'];
			$this->db->where("survey_answers.createdon >='$from_date'");
			$this->db->where("survey_answers.createdon <='$to_date'");
		}
		//$this->db->where('pat_type', '1');
		$this->db->group_by('r_id');
		$this->db->order_by('survey_answers.createdon', 'asc');
		$query = $this->db->join('tbl_pdms_ip_patient_info', 'tbl_pdms_ip_patient_info.slag_id = survey_answers.slag_id','LEFT');
		$query = $this->db->select("count(survey_answers.id) as total, r_id")->get('survey_answers');
		//echo $this->db->last_query(); die;
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['r_id']] = $row['total'];
		}
		return $results;
	}
	/*
	* Function :getfeedbackPatientOPByGroup
	*
	*/
	
	
	public function getfeedbackPatientOPByGroup($cond='', $betweenDate=''){
		if(!empty($cond)) {
			foreach($cond as $key=>$val) {
				$this->db->where($key, $val);
			}
		}
		if(!empty($betweenDate))
		{
			$from_date = $betweenDate['from_date'];
			$to_date = $betweenDate['to_date'];
			$this->db->where("survey_answers.date_created >='$from_date'");
			$this->db->where("survey_answers.date_created <='$to_date'");
		}
		
		$this->db->group_by('contact_no');
		$this->db->order_by('survey_answers.date_created', 'asc');
		$query = $this->db->select("count(survey_answers.id) as total, survey_id")->get('survey_answers');
		//echo $this->db->last_query(); die;
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['survey_id']] = $row['total'];
		}
		return $results;
	}

}
