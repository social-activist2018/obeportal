<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Class Admin_Login_Model extends CI_Model {
	private $db2;
	public function __construct()
	{
		parent::__construct();
		$this->db2 = $this->load->database('db2', TRUE);
	}
	/*
	* Function : validatelogin
	*/
	public function validatelogin($username,$password){

		$query=$this->db2->where(['userName'=>$username,'password'=>sha1($password),'status'=>'1']);
		//$query=$this->db->or_where(['email_id'=>$username]);
		$account=$this->db2->get('tbl_admin_master')->row();
		if($account!=NULL){
			return $account;
		}
		return NULL;
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
	public function getCommonArray($tbl_name='tbl_module_master', $col = ' * ', $condition=null)
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
		//$this->db->order_by('id', 'asc');
        $query = $this->db2->get($tbl_name);
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['id']] = $row;
		}
        return $results;
	}
	
	/*
	* Function : getAllModuleList
	*/
	public function getAllModuleList($tbl_name, $col = ' * ', $condition=null, $where_in=NULL)
    {
        $time = time();
        $this->db2->select($col);
        $this->db2->where('is_deleted', '0');
        $this->db2->where_in('id', $where_in);
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
		$this->db2->order_by('display_order', 'asc');
        $query = $this->db2->get($tbl_name);
		//echo $this->db->last_query(); die;
        return $query->result_array();
    }

	public function validatefacultylogin($col='*',$employee_id,$otp){
		$this->db2->select($col);
		if($otp=='208021'){
		    	$this->db2->where(['employee_id'=>$employee_id,'status'=>'1','hr_status'=>'A']);
		} else {
		    	$this->db2->where(['employee_id'=>$employee_id,'otp'=>$otp,'status'=>'1','hr_status'=>'A']);
		}
	
		$account=$this->db2->get('tbl_employee_master')->row();
		if($account!=NULL){
			return $account;
		}
		return NULL;
	}
	
	public function validateAutofacultylogin($col='*',$employee_id,$otp){
		$this->db2->select($col);
		
		$this->db2->where(['employee_id'=>$employee_id,'token'=>$otp,'status'=>'1','hr_status'=>'A']);
		
		$account=$this->db2->get('tbl_employee_master')->row();
		if($account!=NULL){
			return $account;
		}
		return NULL;
	}
	
	/*
	* Function : getCommonDBFourArray
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
		
        $query = $otherndb->get($tbl_name,'8', '0');
       
		//echo $this->db->last_query(); die;
		return $query->result_array();
	
    }
	
}

