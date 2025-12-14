<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Class Courseenrollment_Model extends CI_Model
{
	private $db2;
    public function __construct()
    {
        parent::__construct();
		$this->db2 = $this->load->database('db2', TRUE);
    }

	/*
	* Function : deleteRecords
	* Description : Delete Record
	*/
	public function deleteRecords($id, $field_name= 'id', $tbl_name)
    {
        $this->db->where($field_name, $id);
		$data = array();
		$data = array('is_deleted'=> '1','status'=>'0', 'modifiedon'=>date('Y-m-d h:i:s'));
        if (!$this->db->update($tbl_name, $data)) {
		    log_message('error', print_r($this->db->error(), true));
            show_error(lang('database_error'));
        }
		
    }
	
	
	function getCurrentTems( $tbl_name = 'tbl_student_details') 
	{
		$sql = "select distinct current_term from $tbl_name order by current_term";
		$queryResult = $this->db2->query($sql);
		return $queryResult->result_array();
		//echo $this->db->last_query();die;
	}
	
	
	
	function getMentordetail() 
	{
		
		$otherpdb = $this->load->database('db5', TRUE);
		$sql = "SELECT tbl_mentee.system_id,  tbl_admin.employee_id, tbl_admin.first_name, tbl_admin.email_id, tbl_admin.contact_number from tbl_mentee JOIN tbl_admin ON tbl_mentee.mentor_id = tbl_admin.id  WHERE tbl_mentee.status = '1' AND tbl_admin.status='1'";
		$queryResult = $otherpdb->query($sql);
		return $queryResult->result_array();
		
		//echo $this->db->last_query();die;
	}
	
	
	
	
	function getRecordsDuplicate( $system_id = NULL, $catalog_nbr = NULL) 
	{
		$sql = 'UPDATE tbl_internships SET status = "0",is_deleted = "1" WHERE system_id="'.$system_id.'" AND catalog_nbr="'.$catalog_nbr.'" AND report_upload is null';
				
		$this->db->query($sql);
		//echo $this->db->last_query();die;
	}
	
	function truncatedata() 
	{
		$sql = 'TRUNCATE TABLE tbl_students_enrolled_master';
		$this->db->query($sql);
	}
	
	/*
	* Function : updateRecords
	* Description : Update Record
	*/
	public function updateRecords($tbl_name='su_consultancy', $id, $field_name= 'id')
    {
        $this->db->where($field_name, $id);
		$data = array();
		$data = array('approval'=> '1', 'status'=> '1','is_deleted'=> '0','approved_by'=>'Dean','modifiedon'=>date('Y-m-d h:i:s'));
        if (!$this->db->update($tbl_name, $data)) {
		    log_message('error', print_r($this->db->error(), true));
            show_error(lang('database_error'));
        }
		
    }
	
	/*
	* Function : getCommonRecords
	* Description : Get all table records
	*/

	function getCommonRecords( $id = NULL, $tbl_name='su_videos', $limit=0, $order_by='') 
	{
		$this->db2->select('*');
       
		$this->db2->where('is_deleted', '0');
		
		if($id>0){
		 $this->db2->where('id', $id);	
		}
		if($order_by!='') {
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}
		}
		
		
		if($limit>0){
		   $this->db2->limit($limit);
		}
        $queryResult = $this->db2->get($tbl_name);
		
		if ($id >0) {
            return $queryResult->row_array();
        } else {
            return $queryResult->result_array();
        }
	}
	
	
	/*
	* Function : getcommonrecorddetail
	* Description : Get all records
	*/

	function getcommonrecorddetail($tbl_name = 'tbl_student_details',$where = array(), $limit = NULL, $page = NULL, $order = NULL, $col = '*')  
	{
		if(!empty($where))
		{
			foreach($where as $key=>$val)
			{
				$this->db2->where($key, $val);
			}
		}
		if(!empty($order))
		{
			foreach($order as $key=>$val)
			{
				$this->db2->order_by($key, $val);
			}
		}
		//$this->db->where('status', '1');
		//$this->db->where('is_deleted', '0');
		 $this->db2->limit('200000');
		$queryResult = $this->db2->select($col)->get($tbl_name, $limit, $page);
		//echo $this->db->last_query();die;
		return $queryResult->result_array();
    }
	
	/*
	* Function : getSearchRecords
	* Description : Get all records
	*/

	function getSearchRecords($tbl_name = 'su_website_search_tags',$limit = NULL, $page = NULL) 
	{
		$this->db2->order_by('su_website_search.title', 'asc');
		$this->db2->where('su_website_search_tags.is_deleted', '0');
		$this->db2->join('su_website_search', 'su_website_search.re_id = su_website_search_tags.search_id');
		$queryResult = $this->db2->select('su_website_search.title, su_website_search_tags.id, su_website_search_tags.tags')->get('su_website_search_tags', $limit, $page);
		return $queryResult->result_array();
    }
	
	
	/*
	* Function : setRecords
	* Description : Insert/Update the records based on id
	*/
	function setRecords($post, $tbl_name = 'NULL') 
	{
		if ($post['edit'] > 0) {
			$this->db->where('id', $post['id']);
            unset($post['edit']);
            unset($post['id']);
			$post['modifiedon']=date('Y-m-d h:i:s');
            if (!$this->db->update($tbl_name, $post)) {
				print_r($this->db->error());
                log_message('error', print_r($this->db->error(), true));
                show_error(lang('database_error'));
            }
			return $id = $post['edit'];
        } else {
            unset($post['edit']);
            unset($post['id']);
	        if (!$this->db->insert($tbl_name, $post)) {
				print_r($this->db->error());
                log_message('error', print_r($this->db->error(), true));
                show_error(lang('database_error'));
            }
			//
//echo $this->db->last_query();die;
			return $id = $this->db->insert_id();
	    }	
	}
	
	/*
	* Function : getRecords
	* Description : Get all table records
	*/
	
	function getRecords( $id = NULL, $tbl_name='su_videos', $order_by = NULL, $search_filter = array(),$limit=NULL,$from=NULL) 
	{
		$this->db->select('*');
		if($order_by!=''){
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}
			
		} else {
	        $this->db2->order_by('display_order', 'asc');
			$this->db2->order_by('id', 'asc');
        }
		$this->db2->where('is_deleted', '0');
		
		if($id>0){
		 $this->db2->where('id', $id);	
		}
		
		if(!empty($search_filter))
		{
			foreach($search_filter as $key=>$value){
			$this->db2->where($key, $value);
			}
		}
	    //$this->db->limit($limit);
        $queryResult = $this->db2->get($tbl_name, $limit, $from);
		//echo $this->db->last_query();die;
		if ($id >0) {
            return $queryResult->row_array();
        } else {
            return $queryResult->result_array();
        }
	}
	
	
	/*
	* Function : getAllRecords
	* Description : Get all table records
	*/
	
	function getAllRecords($tbl_name='su_videos',$col=null, $search_filter=null,$order_by=null, $key=null, $value=null,$groupBy=null) 
	{
		$this->db2->select($col);
		if($order_by!=''){
			$this->db2->order_by($order_by);
		}
		$this->db2->where('is_deleted', '0');
		
		if($key!='' && $value>0){
		 $this->db2->where($key, $value);	
		}
		
		if(!empty($search_filter))
		{
			foreach($search_filter as $key=>$value){
			$this->db2->where($key, $value);
			}
		}
		if(!empty($groupBy))
		{ 
			foreach($groupBy as $val) {
				$this->db2->group_by($val);
			}
			
		}
	    //$this->db2->limit($limit);
        $queryResult = $this->db2->get($tbl_name);
		//echo $this->db2->last_query();die;
		if ($id >0) {
            return $queryResult->row_array();
        } else {
            return $queryResult->result_array();
        }
	}
	
	
	/*
	* Function : getCourseRecords
	* Description : Get all records
	*/
	
	function getCourseRecords( $course_id = NULL) {
		
		$this->db2->select('id, course_name,school_id,status');
        $this->db2->order_by('course_name', 'asc');
        $this->db2->where('is_deleted', '0');
		if($course_id>0){
		 $this->db2->where('id', $course_id);	
		}
		
        //$this->db2->limit($limit);
        $queryResult = $this->db2->get('su_courses');
		
		if ($course_id >0) {
            return $queryResult->row_array();
        } else {
			$resp = $queryResult->result_array();
			$results = array();
			foreach($resp as $val) { 
				$results[$val['id']] = $val;
			}
            return $results;
        }
	
	}
	
	
	/*
	* Function : getSingleRecord
	*/
	public function getSingleRecord($tbl_name, $col = ' * ', $condition=null)
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
		$query = $this->db2->get($tbl_name);
		//echo $this->db->last_query(); die;
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
        }
		//echo $this->db->last_query(); die;
	}

	
	/*
	* Function : getAllMyRecords
	* DB Connection : db3
	*/
	public function getAllMentorRecords($tbl_name, $where=null)
	{
		$otherdbf = $this->load->database('db5', TRUE);
        if(!empty($where))
		{
			foreach($where as $key=>$val)
			{
				$otherdbf->where($key, $val);
			}
		}
		
		$otherdbf->order_by('tbl_mentee.id', 'desc');
		$otherdbf->join('tbl_admin', 'tbl_admin.id = tbl_mentee.mentor_id');
		$queryResult = $otherdbf->select('system_id,mentor_id,employee_id')->get($tbl_name, '20000', '0');
		//echo $otherdbf->last_query(); die('test');
		$responseArray = array();
		foreach($queryResult->result_array() as $row){
			if($row['system_id']){
			$responseArray[$row['system_id']] = $row['employee_id'];
			}
		}

		
		return $responseArray;
    }
	
	/*
	* Function : getAllMyRecords
	* DB Connection : db3
	*/
	public function getAllMyRecords($tbl_name, $where=null)
	{
		$otherdb = $this->load->database('db3', TRUE);
        if(!empty($where))
		{
			foreach($where as $key=>$val)
			{
				$otherdb->where($key, $val);
			}
		}
		$otherdb->order_by('id', 'desc');
		$queryResult = $otherdb->select('*')->get($tbl_name, '100', '0');
		//echo $this->db->last_query();die;
		return $queryResult->result_array();
    }
	
	/*
	* Function : getcommonUniqueEzoneRecorddetail
	* DB Connection : db2
	*/
	public function getcommonUniqueEzoneRecorddetail($tbl_name,  $where=null)
	{
		$otherdb = $this->load->database('db3', TRUE);
        if(!empty($where))
		{
			foreach($where as $key=>$val)
			{
				$otherdb->where($key, $val);
			}
		}
		$otherdb->where('status', '1');
		$otherdb->where('is_deleted', '0');
		// $this->db->limit(10);
		$otherdb->distinct();
		$queryResult = $otherdb->select('system_id')->get($tbl_name);
		//echo $this->db->last_query();die;
		return $queryResult->result_array();
    }
	
	/*
	* Function : SqlgetSingleRecord
	* DB Connection : db2
	*/
	public function getcommonEzoneRecorddetail($tbl_name,  $where=null,  $where_in=null)
	{
		$otherdb = $this->load->database('db3', TRUE);
        if(!empty($where))
		{
			foreach($where as $key=>$val)
			{
				$otherdb->where($key, $val);
			}
		}
		if(!empty($where_in)){
			$otherdb->where_in('system_id', $where_in);
		}
		$otherdb->where('status', '1');
		$otherdb->where('is_deleted', '0');
		// $this->db->limit(10);
		$queryResult = $otherdb->select('*')->get($tbl_name);
		//echo $this->db->last_query();die;
		return $queryResult->result_array();
    }
	
	/*
	* Function : SqlgetSingleRecord
	* DB Connection : db2
	*/
	public function SqlgetSingleRecord($tbl_name, $col = ' * ', $condition=null)
	{
		$otherdb = $this->load->database('db3', TRUE);
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
	* Function : getValidateEnrollment
	*/
	function getValidateEnrollment($system_id, $course)
	{
		$tbl_name  = 'PS_S_PRD_PE_STG';
		if ($system_id>0 && $course!='') {
			$post = [
				'username' => 'ATTEST',
				'password' => 'TFsgt^I8',
				'num_rows' => '1',		 
				'table' => $tbl_name,
		         'conditions' => serialize(array('EMPLID' => $system_id,'CATALOG_NBR'=>$course))
			];
			$responseArray = '';
			$fullArray = json_decode($this->getStudentAPIResponse($post));
			//print_r($fullArray); die('TEST');
			$responseArray = $fullArray[0]->EMPLID;
			if(empty($responseArray)){
				$responseArray = true;
			}
		} else {
			$responseArray = false;
		}
		return $responseArray;
	}
	
	function getValidateStudent($system_id, $strm)
	{
		$tbl_name  = 'PS_S_PRD_FEE_VW';
		if ($system_id>0 && $strm>0) {
			$post = [
				'username' => 'ATTEST',
				'password' => 'TFsgt^I8',
				'num_rows' => '100',		 
				'table' => $tbl_name,
		         'conditions' => serialize(array('EMPLID' => $system_id,'STRM'=>$strm))
			];
			//print_r($post); 
			$resultsArray = '';
			$fullArray = json_decode($this->getStudentAPIResponse($post));
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
	
	function getPeoplesoftCourseAdmitSections($system_id)
	{
		$tbl_name  = 'PS_S_ATTEND_STG';
		if ($system_id>0) {
			$post = [
				'username' => 'ATTEST',
				'password' => 'TFsgt^I8',
				'num_rows' => '100',		 
				'table' => $tbl_name,
		         'conditions' => serialize(array('EMPLID' => $system_id))
			];
			//print_r($post); 
			$resultsArray = '';
			$fullArray = json_decode($this->getStudentAPIResponse($post));
			$resultsArray = $fullArray;
			//print_r($resultsArray);die;
		} else {
			$resultsArray = 'Invalid Request';
		}
		return $resultsArray;
	}
	function getValidateStudentPsoft($system_id, $strm)
	{
		$tbl_name  = 'PS_S_PRD_PE_STG';
		if ($system_id>0 && $strm>0) {
			$post = [
				'username' => 'ATTEST',
				'password' => 'TFsgt^I8',
				'num_rows' => '100',		 
				'table' => $tbl_name,
		         'conditions' => serialize(array('EMPLID' => $system_id))
			];
			//print_r($post); 
			$resultsArray = '';
			$fullArray = json_decode($this->getStudentAPIResponse($post));
			$resultsArray = $fullArray;
			//print_r($resultsArray);die;
		} else {
			$resultsArray = 'Invalid Request';
		}
		return $resultsArray;
	}
	
	function getprevoiusEnrolledCourses($system_id)
	{
		//$tbl_name  = 'PS_S_PRD_FEE_VW';
		$tbl_name  = 'PS_S_PRD_PENRL_VW';
		if ($system_id>0) {
			$post = [
				'username' => 'ATTEST',
				'password' => 'TFsgt^I8',
				'num_rows' => '100',		 
				'table' => $tbl_name,
		        'conditions' => serialize(array('EMPLID' => $system_id))
			];
			//print_r($post); 
			$resultsArray = array();
			$fullArray = json_decode($this->getStudentAPIResponse($post));
			foreach($fullArray as $value){ $resultsArray[trim($value->STRM)][] = trim($value->CATALOG_NBR);}
			//echo '<pre>'; print_r($resultsArray);die;
			//$fullArray[0]->SU_FINAL_FLAG;
		} else {
			$resultsArray = 'Invalid Request';
		}
		return $resultsArray;
	}
	
	function getppsoftValidateStudentPsoft($system_id)
	{
		$url = 'https://slotbooking.sharda.ac.in/mentorapi/getCommonDetails';
		if ($system_id>0) {
			$post = [
					'username' => 'ATTEST',
					'password' => 'TFsgt^I8',
					'conditions' => serialize(array('EMPLID' => $system_id)),
					'num_rows' => '10',		 
					'table' => 'PS_S_PRD_STU_TT_VW'
				];
			if (!empty($url) && !empty($post)) {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
				$response = curl_exec($ch);
			}
			$resultsArray = json_decode($response);
		//print_r($resultsArray);die;
		} else {
			$resultsArray = 'Invalid Request';
		}
		return $resultsArray;
	}
	
	function getPeoplesoftCourseSections($condArray)
	{
		$tbl_name  = 'PS_S_PRD_TT_PI_VW';
		$resultsArray = '';
		
		if (!empty($condArray)) {
			$post = [
				'username' => 'ATTEST',
				'password' => 'TFsgt^I8',
				'num_rows' => '200',		 
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
		//echo $this->db->last_query();die;
		return $query->result_array();
		
	}

}
