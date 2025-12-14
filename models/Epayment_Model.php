<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Class Epayment_Model extends CI_Model{
	
	/*
	* Function : getColumnArray
	* DB Connection : db6
	*
	*/
	public function getColumnArray($tbl_name)
	{
		$otherdb = $this->load->database('db6', TRUE);
        $query = $otherdb->query("SHOW `columns` FROM $tbl_name");
        return $query->result_array();
    }
	
	/*
	* Function : getSingleSQLRecord
	* DB Connection : db6
	*
	*/
	public function getSingleSQLRecord($tbl_name, $col = ' * ', $likeSearch=null)
	{
		$otherdb = $this->load->database('db6', TRUE);
        $time = time();
        $otherdb->select($col);
        $otherdb->where('is_deleted', '0');
	 
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
			$otherdb->where($likewhere);
		}
		
		$query = $otherdb->get($tbl_name);
		return $query->result_array();
    }
	
	/*
	* Function : getSQLAllRecords
	* DB Connection : db2
	*
	*
	*/
	public function getSQLAllRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL, $or_condition = NULL)
    {
		$otherdb = $this->load->database('db6', TRUE);
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
		return $query->result_array();
    }
	
}