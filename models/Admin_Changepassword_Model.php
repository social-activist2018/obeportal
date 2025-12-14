<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Class Admin_Changepassword_Model extends CI_Model {
	private $db2;
	public function __construct()
	{
		parent::__construct();
		$this->db2 = $this->load->database('db2', TRUE);
	}
	public function getcurrentpassword($adminid){
		$query=$this->db2->where(['id'=>$adminid])->get('tbl_admin_master');
		if($query->num_rows() > 0)
		{
			return $query->row();
		}
	}

	public function updatepassword($adminid,$newpassword){
		$data=array('password' =>sha1($newpassword) ,'last_modifiedon'=>date('Y-m-d H:i:s'));
		return $this->db->where(['id'=>$adminid])->update('tbl_admin_master',$data);
	}

}
