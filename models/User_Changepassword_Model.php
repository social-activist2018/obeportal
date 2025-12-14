<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Class User_Changepassword_Model extends CI_Model {
    private $db2;
	public function __construct()
	{
		parent::__construct();
		$this->db2 = $this->load->database('db2', TRUE);
	}
public function getcurrentpassword($userid){
$query=$this->db2->where(['id'=>$userid])
                    ->get('tblusers');
           if($query->num_rows() > 0)
           {
           	return $query->row();
           }
}

public function updatepassword($userid,$newpassword){
	$data=array('userPassword' =>$newpassword );
	return $this->db->where(['id'=>$userid])->update('tblusers',$data);
}

}
