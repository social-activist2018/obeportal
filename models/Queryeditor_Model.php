<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Class Queryeditor_Model extends CI_Model{
	
	private $db2;
	public function __construct()
	{
		parent::__construct();
		$this->db2 = $this->load->database('db2', TRUE);
	}	
	/*
	* Function : myQuery
	*/
	
	public function myQuery($myquery) 
	{
		$resutls = array();	
		if($myquery){
			$pattern = '/limit/';
			if (!preg_match($pattern, $myquery)) {
				$sql = $myquery .' limit 500'; 
			} else {
				$sql = $myquery ;
			}
			
			$query = $this->db2->query($sql);
			$resutls = $query->result_array();
		}
		return $resutls;
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
		
}