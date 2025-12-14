<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Class User_Login_Model extends CI_Model {


	public function validatelogin($username,$password){

		$query=$this->db->where(['system_id'=>$username,'contact_number'=>$password,'status'=>'1']);
		if(!empty($username)){
		$query=$this->db->or_where(['email_id'=>$username]);
		}
		if(!empty($password)){
		$query=$this->db->or_where(['contact_number'=>$password]);
		}
		$account=$this->db->get('tbl_mentee')->row();
		if($account!=NULL){
			return $account;
		}
		return NULL;
	}


	public function getCommonArray($tbl_name='tbl_schools', $col = ' * ', $condition=null)
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
	* Function : getAllModuleList
	*/
	public function getAllModuleList($tbl_name, $col = ' * ', $condition=null, $where_in=NULL)
    {
        $time = time();
        $this->db->select($col);
        $this->db->where('is_deleted', '0');
        $this->db->where_in('id', $where_in);
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db->where($key, $val);
			}
			
		}
		$this->db->order_by('display_order', 'asc');
        $query = $this->db->get($tbl_name);
		//echo $this->db->last_query(); die;
        return $query->result_array();
    }
}

