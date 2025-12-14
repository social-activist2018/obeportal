<?php

class Markssubmission_model extends CI_Model
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
	
	
	function getRecordsDuplicate( $system_id = NULL, $catalog_nbr = NULL) 
	{
		$sql = 'UPDATE tbl_internships SET status = "0",is_deleted = "1" WHERE system_id="'.$system_id.'" AND catalog_nbr="'.$catalog_nbr.'" AND report_upload is null';
				
		$this->db->query($sql);
		//echo $this->db2->last_query();die;
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
	* Function: setCourseDetails
	* Description : set banner details
	*/
	
	
	function setBanners($post) 
	{
		$post['banner_url'] = str_replace(' ','-',$post['banner_url']); // Replace space with -
	
		if ($post['edit'] > 0) {
			$this->db->where('id', $post['edit']);
            unset($post['edit']);
            unset($post['id']);
  
            if (!$this->db->update('su_banners', $post)) {
				print_r($this->db->error());
                log_message('error', print_r($this->db->error(), true));
                show_error(lang('database_error'));
            }
			
        } else {
            unset($post['edit']);
            unset($post['id']);
	        if (!$this->db->insert('su_banners', $post)) {
				print_r($this->db->error());
                log_message('error', print_r($this->db->error(), true));
                show_error(lang('database_error'));
            }
	    }
	}
	
	/*
	* Function : getNaacsubcriteriaRecords
	* Description : Get all details
	*/
	
	function getNaacsubcriteriaRecords() {
		
		$this->db2->select('stz_naacsubcriterias.id, stz_naacsubcriterias.title,stz_naacsubcriterias.criteria_id,stz_naacsubcriterias.sub_criteria');
       	$this->db2->from('stz_naacsubcriterias');
		//$this->db2->join('stz_naaccriterias', 'stz_naaccriterias.id = stz_naacsubcriterias.criteria_id');
		$this->db2->order_by('stz_naacsubcriterias.sub_criteria', 'asc');
        $this->db2->where('stz_naacsubcriterias.is_deleted', '0');
				
		$queryResult = $this->db2->get();
		//echo $this->db2->last_query(); die;
     	return $queryResult->result_array();
	  }
	
	/*
	* Function : getBanners
	* Description : Get all banners
	*/
	
	function getBanners($banner_id = NULL, $tbl_name = 'su_banners') 
	{
		$this->db2->select('*');
        $this->db2->order_by('display_order', 'asc');
        $this->db2->where('is_deleted', '0');
		if($banner_id>0){
		 $this->db2->where('id', $banner_id);	
		}
	    //$this->db2->limit($limit);
        $queryResult = $this->db2->get($tbl_name);
		
		if ($banner_id >0) {
            return $queryResult->row_array();
        } else {
            return $queryResult->result_array();
        }
	}
	
	/*
	* Function : getcommonrecorddetail
	* Description : Get all records
	*/

	function getcommonrecorddetail($tbl_name = 'stz_products',$where = array(), $limit = NULL, $page = NULL) 
	{
		if(!empty($where))
		{
			foreach($where as $key=>$val)
			{
				$this->db2->where($key, $val);
			}
		}
		$this->db2->where('status', '1');
		$this->db2->where('is_deleted', '0');
		$queryResult = $this->db2->select('*')->get($tbl_name, $limit, $page);
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
	* Function :  getDeanDetailsArray
	*/
	public function getDeanDetailsArray($tbl_name = 'su_faculties', $school_id)
	{
		$this->db2->select('id, name, school_id, email_id, contact_no, status');
        $this->db2->order_by('display_order', 'asc');
        $this->db2->where('is_deleted', '0');
		
		if($school_id>0){
		 $this->db2->where('school_id', $school_id);	
		}
		
	    $queryResult = $this->db2->get($tbl_name);
		
		if ($school_id >0) {
            return $queryResult->row_array();
        } else {
            return $queryResult->result_array();
        }
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
			return $id = $this->db->insert_id();
	    }	
	}
	
	/*
	* Function : getRecords
	* Description : Get all table records
	*/
	
	function getRecords( $id = NULL, $tbl_name='su_videos', $order_by = NULL, $search_filter = array(),$limit=NULL,$from=NULL) 
	{
		$this->db2->select('*');
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
	    //$this->db2->limit($limit);
        $queryResult = $this->db2->get($tbl_name, $limit, $from);
		//echo $this->db2->last_query();die;
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
	
	function getAllRecords($tbl_name='su_videos', $order_by, $search_filter, $key, $value) 
	{
		$this->db2->select('*');
		if($order_by!=''){
			$this->db2->order_by($order_by);
		} else {
	        $this->db2->order_by('display_order', 'asc');
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
	    //$this->db2->limit($limit);
        $queryResult = $this->db2->get($tbl_name);
		
		if ($id >0) {
            return $queryResult->row_array();
        } else {
            return $queryResult->result_array();
        }
	}
	
	/*
	* Function : getCustomFiles
	* Description : Get all table records
	*/
	
	function getCustomFiles( $id = NULL, $tbl_name='su_customfiles') 
	{
		$this->db2->select('*');
      	$this->db2->where('is_deleted', '0');	
		if($id>0){
		 $this->db2->where('id', $id);	
		}
		
		$queryResult = $this->db2->get($tbl_name);
		
		if ($id >0) {
            return $queryResult->row_array();
        } else {
            return $queryResult->result_array();
        }
	}
	/*
	* Function : getDepartments
	* Description : Get all Departments details
	*/
	
	function getDepartments( $school_id = NULL) {
		
		$this->db2->select('id,name,status');
        $this->db2->order_by('name', 'asc');
        $this->db2->where('is_deleted', '0');
        $this->db2->where('status', '1');
		if($school_id>0){
		 $this->db2->where('school_id', $school_id);	
		}
		
        //$this->db2->limit($limit);
        $queryResult = $this->db2->get('su_departments');
		return $queryResult->result_array();
        	
	}
	
	
	/*
	* Function : getRTDCDepartments
	* Description : Get all RTDC Departments details
	*/
	
	function getRTDCDepartments( $school_id = NULL) {
		
		$this->db2->select('id,name,status');
        $this->db2->order_by('name', 'asc');
        $this->db2->where('is_deleted', '0');
        $this->db2->where('status', '1');
		if($school_id>0){
		 $this->db2->where('school_id', $school_id);	
		}
		
        //$this->db2->limit($limit);
        $queryResult = $this->db2->get('su_rtdc');
		return $queryResult->result_array();
        	
	}
	
	/*
	* Function : getSubProducts
	* Description : Get all getSubProducts details
	*/
	
	function getSubProducts( $product_id = NULL) {
		
		$this->db2->select('id,subproduct_name,status');
        $this->db2->order_by('id', 'asc');
        $this->db2->where('is_deleted', '0');
        $this->db2->where('status', '1');
		if($product_id>0){
		 $this->db2->where('product_id', $product_id);	
		}
		
        //$this->db2->limit($limit);
        $queryResult = $this->db2->get('stz_subproducts');
		return $queryResult->result_array();
        	
	}
	
	
/*
	* Function : getSchools
	* Description : Get all schools details
	*/
	
	function getSchools( $school_id = NULL) {
		
		$this->db2->select('id, school_name, school_url, school_code, display_order, status');
        $this->db2->order_by('display_order', 'asc');
        $this->db2->order_by('school_name', 'asc');
        $this->db2->where('is_deleted', '0');
		if($school_id>0){
		 $this->db2->where('id', $school_id);	
		}
		
        //$this->db2->limit($limit);
        $queryResult = $this->db2->get('su_schools');
		
		if ($school_id >0) {
            return $queryResult->row_array();
        } else {
            return $queryResult->result_array();
        }
	
	}
	
	/*
	* Function : getDisciplineRecords
	* Description : Get all records
	*/
	
	function getDisciplineRecords( $discipline_id = NULL) {
		
		$this->db2->select('id, name,status');
        $this->db2->order_by('name', 'asc');
        $this->db2->where('is_deleted', '0');
		if($discipline_id>0){
		 $this->db2->where('id', $discipline_id);	
		}
		
        //$this->db2->limit($limit);
        $queryResult = $this->db2->get('su_discipline');
		
		if ($discipline_id >0) {
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
	* Function : getProgrammeRecords
	* Description : Get all records
	*/
	
	function getProgrammeRecords( $programme_id = NULL, $menu_id='1') {
		
		$this->db2->select('id, programme_name, status');
        $this->db2->order_by('programme_name', 'asc');
        $this->db2->where('is_deleted', '0');
        $this->db2->where('menu_id', $menu_id);
		if($programme_id>0){
		 $this->db2->where('id', $programme_id);	
		}
		
        //$this->db2->limit($limit);
        $queryResult = $this->db2->get('su_menu_programme');
		
		if ($programme_id >0) {
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
	* Function : getSections
	* Description : Get all sections details
	*/
	
	function getSections($tbl_name='su_happenings') 
	{
		$this->db2->select('*');
		$this->db2->order_by('id', 'asc');
		$this->db2->order_by('display_order', 'asc');
		$this->db2->where('status', '1');
		$this->db2->where('section', '7');
		$this->db2->where('is_deleted', '0');
		
		if($sections_id>0){
			$this->db2->where('id', $sections_id);	
		}
		
		if ($limit != NULL) {
			$this->db2->limit($limit);
		}
        $queryResult = $this->db2->get($tbl_name);
		
		if ($sections_id >0) {
            return $queryResult->row_array();
        } else {
            return $queryResult->result_array();
        }
	
	}
	/*
	* Function : getEvents
	* Description : Get all sections details
	*/

	function getEvents($tbl_name='su_eventdetails') 
	{
		$this->db2->select('*');
		$this->db2->order_by('id', 'asc');
		$this->db2->order_by('display_order', 'asc');
		$this->db2->where('status', '1');
		$this->db2->where('is_deleted', '0');
		
		if($sections_id>0){
			$this->db2->where('id', $sections_id);	
		}
		
		if ($limit != NULL) {
			$this->db2->limit($limit);
		}
        $queryResult = $this->db2->get($tbl_name);
		
		if ($sections_id >0) {
            return $queryResult->row_array();
        } else {
            return $queryResult->result_array();
        }
	
	}
	/*
	* Function : getRegistrationsRecords
	* Description : Get all table records
	*/
	
	function getRegistrationsRecords( $id = NULL, $tbl_name='registrations', $search_array, $limit, $page, $search_like) 
	{
		$this->db2->select('*');
		$this->db2->order_by('id', 'desc');	
		if($id>0){
		 $this->db2->where('id', $id);	
		}
		if(!empty($search_array)) {
		 foreach($search_array as $key=>$val)
		 {
			$this->db2->where($key, $val);	 
		 }			 
		}
		// like search
		if ($search_like != null) {
            $search_like = trim($this->db2->escape_like_str($search_like));
            $this->db2->where("(created LIKE '%$search_like%')");
        }
		
		$this->db2->where('is_deleted', '0');	
	    //$this->db2->limit($limit);
        $queryResult = $this->db2->get($tbl_name,$limit, $page);
		
		if ($id >0) {
            return $queryResult->row_array();
        } else {
            return $queryResult->result_array();
        }
	}
	
	public function productsCount($table = null, $condition)
    {
        if($condition!=''){
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
		$this->db2->where('is_deleted', '0');
        return $this->db2->count_all_results($table);
    }
	
	/*
	* Function : getExportreport
	* Description : Get all table records
	*/
	
	function getExportreport( $id = NULL, $tbl_name='su_videos', $order_by = NULL, $column) 
	{
		
		if($column!=''){
			$this->db2->select($column);
		} else {
			$this->db2->select('*');
		}
		if($order_by!=''){
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}
			
		} else {
			$this->db2->order_by('id', 'asc');
        }
		$this->db2->where('is_deleted', '0');
		
		if($id>0){
		 $this->db2->where('id', $id);	
		}
        $queryResult = $this->db2->get($tbl_name);
		//echo $this->db2->last_query();die;
		if ($id >0) {
            return $queryResult->row_array();
        } else {
            return $queryResult->result_array();
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
		//echo $this->db2->last_query(); die;
        return $query->row_array();
	}
	
	public function saveinfo($tbl_name='', $post)
    {
		
        $this->db->insert($tbl_name, $post);
        return $this->db->insert_id();
    }
	
	public function updateinfo($tbl_name='', $post, $field, $value)
    {
		$this->db->where($field, $value);
        if (!$this->db->update($tbl_name, $post)) {
            log_message('error', print_r($this->db->error(), true));
        }
	}

	function getallpendingregistrations( $id = NULL, $tbl_name='tbl_register', $search_filter = array(),$limit=NULL,$from=NULL) 
	{
		$this->db2->select('tbl_register.*,su_schools.school_name,su_departments.name as department_name,assign_role.role_name');
		$this->db2->from('tbl_register');
		$this->db2->join('su_schools', 'su_schools.id = tbl_register.school_id', 'left');
		$this->db2->join('su_departments', 'su_departments.id = tbl_register.department_id', 'left');
		$this->db2->join('assign_role', 'assign_role.role_id = tbl_register.role_id', 'left');
		$this->db2->where('tbl_register.is_deleted', '0');
		$this->db2->where('tbl_register.verify_status', '1');
		
		if($id>0){
		 $this->db2->where('tbl_register.id', $id);	
		}
		
		if(!empty($search_filter))
		{
			foreach($search_filter as $key=>$value){
			$this->db2->where($key, $value);
			}
		}
	    //$this->db2->limit($limit);
        $queryResult = $this->db2->get($limit, $from);
		//echo $this->db2->last_query();die;
		if ($id >0) {
            return $queryResult->row_array();
        } else {
            return $queryResult->result_array();
        }
	}

	function getallpendingregistrationsofhod( $id = NULL, $tbl_name='tbl_register', $search_filter = array(),$limit=NULL,$from=NULL) 
	{
		$this->db2->select('tbl_register.*,su_schools.school_name,su_departments.name as department_name,assign_role.role_name');
		$this->db2->from('tbl_register');
		$this->db2->join('su_schools', 'su_schools.id = tbl_register.school_id', 'left');
		$this->db2->join('su_departments', 'su_departments.id = tbl_register.department_id', 'left');
		$this->db2->join('assign_role', 'assign_role.role_id = tbl_register.role_id', 'left');
		$this->db2->where('tbl_register.is_deleted', '0');
		$this->db2->where('tbl_register.role_id!=', '14');
		$this->db2->where('tbl_register.verify_status', '1');
		
		if($id>0){
		 $this->db2->where('tbl_register.id', $id);	
		}
		
		if(!empty($search_filter))
		{
			foreach($search_filter as $key=>$value){
			$this->db2->where($key, $value);
			}
		}
	    //$this->db2->limit($limit);
        $queryResult = $this->db2->get($limit, $from);
		//echo $this->db2->last_query();die;
		if ($id >0) {
            return $queryResult->row_array();
        } else {
            return $queryResult->result_array();
        }
	}

	function getRecordsforHOD($id = NULL , $tbl_name , $email ,$department_id , $order_by = NULL ,$search_filter = array() ,$limit=NULL,$from=NULL){
		$this->db2->select('*');
		$this->db2->from($tbl_name);
		//$this->db2->where('uploaded_by = "'.$email.'" or uploaded_by IN (select email from tbl_register where department_id = "'.$department_id.'" and (role_id = 15 or role_id = 16) )');
		$this->db2->where('uploaded_by = "'.$email.'" or department_id = "'.$department_id.'"');
		
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
	    //$this->db2->limit($limit);
        $queryResult = $this->db2->get('',$limit,$from);
		//echo $this->db2->last_query();die;
		if ($id >0) {
            return $queryResult->row_array();
        } else {
            return $queryResult->result_array();
        }
	}

	function getRecordsforcriteriacordinator($id = NULL , $tbl_name , $email ,$role_id , $order_by = NULL ,$search_filter = array(),$limit=NULL,$from=NULL){
		$this->db2->select('*');
		$this->db2->from($tbl_name);
		$this->db2->where('(uploaded_by = "'.$email.'" or uploaded_by IN (select email from tbl_register where role_id = "'.$role_id.'"))');
		
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
	    //$this->db2->limit($limit);
        $queryResult = $this->db2->get('',$limit,$from);
		//echo $this->db2->last_query();die;
		if ($id >0) {
            return $queryResult->row_array();
        } else {
            return $queryResult->result_array();
        }
	}

	function getRecordsForFaculty($id = NULL , $tbl_name , $email , $order_by = NULL ,$search_filter = array(),$limit=NULL,$from=NULL){
		$this->db2->select('*');
		$this->db2->from($tbl_name);
		$this->db2->where('uploaded_by = "'.$email.'" ');
		
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
	    //$this->db2->limit($limit);
        $queryResult = $this->db2->get('',$limit,$from);
		//echo $this->db2->last_query();die;
		if ($id >0) {
            return $queryResult->row_array();
        } else {
            return $queryResult->result_array();
        }
	}

	function getfieldrecorddetail($tbl_name = 'stz_products',$fields = '*' ,$where = array(), $limit = NULL, $page = NULL) 
	{
		if(!empty($where))
		{
			foreach($where as $key=>$val)
			{
				$this->db2->where($key, $val);
			}
		}
		$this->db2->where('status', '1');
		$this->db2->where('is_deleted', '0');
		$queryResult = $this->db2->select($fields)->get($tbl_name, $limit, $page);
		return $queryResult->result_array();
    }

	/*
	* Function : setRecords
	* Description : Insert/Update the records based on id
	*/
	function setRecordsForIQAC($post, $tbl_name = 'NULL') 
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
			$post['uploaded_by'] = $this->session->userdata('ezone2022_email');
			$post['created_on'] = date('Y-m-d h:m:s');
	        if (!$this->db->insert($tbl_name, $post)) {
				print_r($this->db->error());
                log_message('error', print_r($this->db->error(), true));
                show_error(lang('database_error'));
            }
			return $id = $this->db->insert_id();
	    }	
	}

	function getallinternshipcertificatesrecords( $id = NULL, $tbl_name='tbl_student_internship_certificates_form', $search_filter = array(),$limit=NULL,$from=NULL) 
	{
		$this->db2->select($tbl_name.'.*,su_schools.school_name,su_departments.name as department_name');
		$this->db2->from($tbl_name);
		$this->db2->join('su_schools', 'su_schools.id ='. $tbl_name.'.school_id', 'left');
		$this->db2->join('su_departments', 'su_departments.id ='.$tbl_name.'.department_id', 'left');
		$this->db2->where($tbl_name.'.is_deleted', '0');
		//$this->db2->where($tbl_name.'.status', '1');
		
		if($id>0){
		 $this->db2->where($tbl_name.'.id', $id);	
		}
		
		if(!empty($search_filter))
		{
			foreach($search_filter as $key=>$value){
			$this->db2->where($key, $value);
			}
		}
	    //$this->db2->limit($limit);
        $queryResult = $this->db2->get($limit, $from);
		//echo $this->db2->last_query();die;
		if ($id >0) {
            return $queryResult->row_array();
        } else {
            return $queryResult->result_array();
        }
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
		$this->db2->group_by('department_id');
		if ($limit !== null && $start !== null) {
           $query = $this->db2->get($tbl_name,$limit, $start);
        } else {
			$query = $this->db2->get($tbl_name);
		}
		
		//echo $this->db2->last_query(); die;
		$resutls = array();
		foreach($query->result_array() as $row){
			$resutls[$row['department_id']] = $row['total'];
		}
        return $resutls;
    }

	public function getCommonIdArray($tbl_name='tbl_schools', $col = ' * ', $condition=null)
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
	* Function : setRecords
	* Description : Insert/Update the records based on id
	*/
	function setRecordsForDAR($post, $tbl_name = 'NULL',$uploadedb2y = NULL,$createdOn = Null,$modifiedOn = NULL) 
	{
		if ($post['edit'] > 0) {
			$this->db->where('id', $post['id']);
            unset($post['edit']);
            unset($post['id']);
			if($modifiedOn){ $post['modifiedon']=date('Y-m-d h:i:s'); }
            if (!$this->db->update($tbl_name, $post)) {
				print_r($this->db->error());
                log_message('error', print_r($this->db->error(), true));
                show_error(lang('database_error'));
            }
			return $id = $post['edit'];
        } else {
            unset($post['edit']);
			unset($post['id']);
			if($uploadedby){ $post['uploaded_by'] = $this->session->userdata('ezone2022_email'); }
			if($createdOn){ $post['created_on'] = date('Y-m-d h:m:s'); }
	        if (!$this->db->insert($tbl_name, $post)) {
				print_r($this->db->error());
                log_message('error', print_r($this->db->error(), true));
                show_error(lang('database_error'));
            }
			return $id = $this->db->insert_id();
	    }	
	}

	public function copyData($copyFrom,$copyTo,$id,$cols)
    {
        $query      = $this->db->query("INSERT INTO $copyTo (created_on,modifiedon,year,school_id,department_id,ac_ordinal_no,program_code,program_name,$cols) SELECT NOW(),NOW(),year,school_id,department_id,ac_ordinal_no,program_code,program_name,$cols FROM $copyFrom WHERE id = '$id'");
		if (!$query) {
			print_r($this->db->error());
			log_message('error', print_r($this->db->error(), true));
			show_error(lang('database_error'));
		}
        return $id = $this->db->insert_id();
    }

	function getRecordSchDeptWise($id = NULL , $tbl_name , $where , $order_by = NULL ,$search_filter = array() ,$limit=NULL,$from=NULL){
		$this->db2->select('*');
		$this->db2->from($tbl_name);
		$this->db2->where($where);
		
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
	    //$this->db2->limit($limit);
        $queryResult = $this->db2->get('',$limit,$from);
		//echo $this->db2->last_query();die;
		if ($id >0) {
            return $queryResult->row_array();
        } else {
            return $queryResult->result_array();
        }
	}

	function getAllInternshipRecords( $id = NULL, $tbl_name='tbl_internships',$order_by = NULL, $search_filter = array(),$limit=NULL,$from=NULL) 
	{
		$this->db2->select($tbl_name.'.*,ppsoft_employee_master.employee_code,ppsoft_employee_master.fullname as faculty_name');
		$this->db2->from($tbl_name);
		$this->db2->join('ppsoft_employee_master', 'ppsoft_employee_master.employee_code ='. $tbl_name.'.faculty_id', 'left');
		$this->db2->where($tbl_name.'.is_deleted', '0');
		//$this->db2->where($tbl_name.'.status', '1');
	    $this->db2->where('ppsoft_employee_master.status','1');
		if($order_by!=''){
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}
			
		} else {
	        $this->db2->order_by('display_order', 'asc');
			$this->db2->order_by('id', 'asc');
        }
		if($id>0){
		 $this->db2->where($tbl_name.'.id', $id);	
		}
		
		if(!empty($search_filter))
		{
			foreach($search_filter as $key=>$value){
			$this->db2->where($key, $value);
			}
		}
	    //$this->db2->limit($limit);
        $queryResult = $this->db2->get($limit, $from);
		//echo $this->db2->last_query();die;
		if ($id >0) {
            return $queryResult->row_array();
        } else {
            return $queryResult->result_array();
        }
	}
	
	/*
    * Function : getMarksSubRecords
    */
    public function getMarksSubRecords($tbl_name, $condition1 = NULL,$condition2=NULL, $order_by = NULL, $limit = NULL, $start = NULL)
    {
        $time = time();
        $this->db2->select('`catalog_nbr`,`catalog_desc`,`class_section`,`class_nbr`,`faculty_id`,`faculty_name`,COUNT(DISTINCT(system_id)) as total_students');
        $this->db2->where('is_deleted', '0');
        $this->db2->where('class_section !=', 'FR');
        $this->db2->where('class_section !=', 'FI');
        $this->db2->where('class_section !=', 'F');
        if (!empty($condition1)) {
            foreach ($condition1 as $key => $val) {
                $this->db2->where($key, $val);
            }
        }
        if (!empty($condition2)) {
                $this->db2->where($condition2);
        }
        if (!empty($order_by)) {
            foreach ($order_by as $key => $val) {
                $this->db2->order_by($key, $val);
            }
        }
        $this->db2->group_by('`catalog_nbr`,`catalog_desc`,`class_section`,`class_nbr`,`faculty_id`,`faculty_name`');
        if ($limit !== null && $start !== null) {
            $query = $this->db2->get($tbl_name, $limit, $start);
        } else {
            $query = $this->db2->get($tbl_name);
        }
        //echo $this->db2->last_query(); die;
        return $query->result_array();
    }

	/*
    * Function : getMarksSubRecords
    */
    public function getMarksRecords($tbl_name, $condition1 = NULL,$condition2=NULL, $order_by = NULL, $limit = NULL, $start = NULL)
    {
        $time = time();
        $this->db2->select('`catalog_nbr`,`catalog_desc`,`class_section`,`class_nbr`,`faculty_id`,`faculty_name`,COUNT(DISTINCT(`system_id`)) as total_students');
        $this->db2->where('is_deleted', '0');
        $this->db2->where('class_section !=', 'FR');
        $this->db2->where('class_section !=', 'FI');
        $this->db2->where('class_section !=', 'F');
        if (!empty($condition1)) {
            foreach ($condition1 as $key => $val) {
                $this->db2->where($key, $val);
            }
        }
        if (!empty($condition2)) {
                $this->db2->where($condition2);
        }
        if (!empty($order_by)) {
            foreach ($order_by as $key => $val) {
                $this->db2->order_by($key, $val);
            }
        }
        $this->db2->group_by('`catalog_nbr`,`catalog_desc`,`class_section`,`class_nbr`,`faculty_id`,`faculty_name`');
        if ($limit !== null && $start !== null) {
            $query = $this->db2->get($tbl_name, $limit, $start);
        } else {
            $query = $this->db2->get($tbl_name);
        }
       //echo $this->db2->last_query(); die;
        return $query->result_array();
    }

	/*
	* Function : getColRecords
	* Description : Get all records
	*/

	function getColRecords($tbl_name = 'tbl_marks_submission',$col='*',$where = NULL,$order_by=NULL, $limit = NULL, $page = NULL) 
	{
		if (!empty($where)) {
			$this->db2->where($where);
	    }
		if (!empty($order_by)) {
            foreach ($order_by as $key => $val) {
                $this->db2->order_by($key, $val);
            }
        }
		$this->db2->where('status', '1');
		$this->db2->where('is_deleted', '0');
        $this->db2->where('class_section !=', 'FR');
        $this->db2->where('class_section !=', 'FI');
        $this->db2->where('class_section !=', 'F');
		$this->db2->group_by('`system_id`, `student_name`');
		$queryResult = $this->db2->select($col)->get($tbl_name, $limit, $page);
        //echo $this->db2->last_query(); die();

		return $queryResult->result_array();
    }
	
	
	
	/*
	* Function : getAllfilledStudent  :SUM( CASE WHEN sg.grade= 'DEB' THEN 1 ELSE 0 END) AS debarred, 
	*/
	public function getAllfilledStudent($catalog_nbr,$class_nbr,$class_section,$faculty_id,$type)
    {
		if($type=="MTE/MSE/MST"){
		 $sql = "SELECT COUNT(*) as total_filled FROM ( SELECT DISTINCT catalog_nbr, class_nbr, class_section, faculty_id, system_id  FROM tbl_marks_submission where catalog_nbr = '".$catalog_nbr."'  AND class_nbr = '".$class_nbr."' AND class_section = '".$class_section."' AND faculty_id = '".$faculty_id."'AND (`lam_type` = 'MTE' OR `lam_type` = 'MSE' OR `lam_type` = 'MST')) AS p";
		 $query = $this->db2->query($sql);
		 echo $this->db2->last_query();
		 echo "<pre>";
		 print_r($query->result_array()); die;
		return $query->result_array();   
		}/*
		else if($type=="CA/IA"){
		 $sql = "SELECT COUNT(*) as total_filled FROM ( SELECT DISTINCT catalog_nbr, class_nbr, class_section, faculty_id, system_id  FROM tbl_marks_submission where catalog_nbr = '".$catalog_nbr."'  AND class_nbr = '".$class_nbr."' AND class_section = '".$class_section."' AND faculty_id = '".$faculty_id."'AND (`lam_type` = 'CA' OR `lam_type` = 'IA' OR `lam_type` = 'INTERNAL')) AS p";
		}
		else if($type=="CE/Viva"){
		 $sql = "SELECT COUNT(*) as total_filled FROM ( SELECT DISTINCT catalog_nbr, class_nbr, class_section, faculty_id, system_id  FROM tbl_marks_submission where catalog_nbr = '".$catalog_nbr."'  AND class_nbr = '".$class_nbr."' AND class_section = '".$class_section."' AND faculty_id = '".$faculty_id."'AND (`lam_type` = 'CE(VIVA)')) AS p";
		}
		else if($type=="ETE/ESE/External"){ 
		 $sql = "SELECT COUNT(*) as total_filled FROM ( SELECT DISTINCT catalog_nbr, class_nbr, class_section, faculty_id, system_id  FROM tbl_marks_submission where catalog_nbr = '".$catalog_nbr."'  AND class_nbr = '".$class_nbr."' AND class_section = '".$class_section."' AND faculty_id = '".$faculty_id."'AND (`lam_type` = 'ETE' OR `lam_type` = 'ESE' OR lam_type = 'EXTERNAL')) AS p";
		}*/
		// $sql = "SELECT COUNT(*) as total FROM ( SELECT DISTINCT student_id, term, course_code, grade  FROM stu_course where student_id IN(".$list.")  AND term = '".$term."' AND semester = '".$semester."' AND grade = '".$grade."') AS p";
		// $sql = "SELECT COUNT(*) as total FROM stu_course where student_id IN(".$list.")  AND term = '".$term."' AND semester = '".$semester."' AND grade = '".$grade."'";
    }

	/*
    * Function : getMarksSubRecords
    */
    // public function getMarksSubRecords($tbl_name, $condition1 = NULL,$condition2=NULL, $order_by = NULL, $limit = NULL, $start = NULL)
    // {
	// 	$otherdb2 = $this->load->database('db22', TRUE);
    //     $time = time();
    //     $otherdb2->select('`catalog_nbr`,`catalog_desc`,`class_section`,`class_nbr`,`faculty_id`,`faculty_name`,COUNT(*) as total_students');
    //     $otherdb2->where('is_deleted', '0');
    //     if (!empty($condition1)) {
    //         foreach ($condition1 as $key => $val) {
    //             $otherdb2->where($key, $val);
    //         }
    //     }
    //     if (!empty($condition2)) {
    //             $otherdb2->where($condition2);
    //     }
    //     if (!empty($order_by)) {
    //         foreach ($order_by as $key => $val) {
    //             $otherdb2->order_by($key, $val);
    //         }
    //     }
    //     $otherdb2->group_by('`catalog_nbr`,`catalog_desc`,`class_section`,`class_nbr`,`faculty_id`,`faculty_name`');
    //     if ($limit !== null && $start !== null) {
    //         $query = $otherdb2->get($tbl_name, $limit, $start);
    //     } else {
    //         $query = $otherdb2->get($tbl_name);
    //     }
    //     //echo $this->db2->last_query(); die;
    //     return $query->result_array();
    // }

	/*
	* Function : getColRecords
	* Description : Get all records
	*/

	// function getColRecords($tbl_name = 'tbl_marks_submission',$col='*',$where = NULL,$order_by=NULL, $limit = NULL, $page = NULL) 
	// {
	// 	$otherdb2 = $this->load->database('db22', TRUE);
	// 	if (!empty($where)) {
	// 		$otherdb2->where($where);
	//     }
	// 	if (!empty($order_by)) {
    //         foreach ($order_by as $key => $val) {
    //             $otherdb2->order_by($key, $val);
    //         }
    //     }
	// 	$otherdb2->where('status', '1');
	// 	$otherdb2->where('is_deleted', '0');
	// 	$queryResult = $otherdb2->select($col)->get($tbl_name, $limit, $page);
    //     //echo $this->db2->last_query(); die;

	// 	return $queryResult->result_array();
    // }
	
}
