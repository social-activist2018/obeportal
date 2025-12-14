<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Class Helpsupport_Model extends CI_Model{
	
	/*
	* Function : getAllRecords
	*/
	public function getAllRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL, $likeSearch=NULL, $where_in = NULL, $betweenDate=NULL)
    {
	    $time = time();
		$otherdb = $this->load->database('dbreport', TRUE);
        $otherdb->select($col);
        $otherdb->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$otherdb->where($key, $val);
			}
		}
		if(!empty($where_in)){
			$otherdb->where_in('program_id', $where_in);
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
			$otherdb->where($likewhere);
		}
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$otherdb->order_by($key, $val);
			}
		}
	    if(!empty($betweenDate))
		{
			$from_date = $betweenDate['from_date'];
			$to_date = $betweenDate['to_date'];
			$otherdb->where("DATE_FORMAT(createdon,'%m/%d/%Y') >='$from_date'");
			$otherdb->where("DATE_FORMAT(createdon,'%m/%d/%Y') <='$to_date'");
		}
		
		if ($limit !== null && $start !== null) {
           $query = $otherdb->get($tbl_name,$limit, $start);
        } else {
			$query = $otherdb->get($tbl_name);
		}
		
		//echo $otherdb->last_query(); die;
		return $query->result_array();
    }
	
	/*
	* Function: getCommonIdArray
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getCommonIdArray($tbl_name='tbl_schools', $col = ' * ', $condition=null, $order_by=null)
    {
        $time = time();
		$otherdb = $this->load->database('dbreport', TRUE);
        $otherdb->select($col);
        $otherdb->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$otherdb->where($key, $val);
			}
			
		}
		if(!empty($order_by))
		{ 
			foreach($order_by as $key=>$val) {
				$otherdb->order_by($key, $val);
			}
			
		}
		//$otherdb->order_by('id', 'asc');
		
        $query = $otherdb->get($tbl_name);
		
		$results = array();
		//echo $otherdb->last_query(); die;
		foreach($query->result_array() as $row) {
			$results[$row['id']] = $row;
		}
        return $results;
	}
	
	/*
	* Function : getSingleRecord
	*/
	public function getSingleRecord($tbl_name, $col = ' * ', $condition=null , $type='',$order_by = NULL,$condition_like = NULL)
	{
        $time = time();
		$otherdb = $this->load->database('dbreport', TRUE);
        $otherdb->select($col);
       // $otherdb->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$otherdb->where($key, $val);
			}
			
		}
		
		// Like condition_like
		if(!empty($condition_like))
		{   $k=1;
			foreach($condition_like as $key=>$val) {
				$otherdb->like($key, $val);
				if($k>1) {
					$otherdb->or_like($key, $val);
				}
				$k++;
			}
			
		}
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$otherdb->order_by($key, $val);
			}
		}	
			
		$query = $otherdb->get($tbl_name);
		
		//echo $otherdb->last_query(); die;
		if($type){
			return $query->row();
		} else {
			return $query->row_array();
		}
    }
	
	
	public function saveinfo($tbl_name='', $post)
    {
		if($post['user-content-title']!=''){
			$post['title'] = $post['user-content-title'];
			unset($post['user-content-title']);
		}
		$this->db->insert($tbl_name, $post);
		//echo $this->db->last_query(); die;
		return $this->db->insert_id();
    }
	
	public function updateinfo($tbl_name='', $post, $field, $value)
    {
		if($post['user-content-title']!=''){
			$post['title'] = $post['user-content-title'];
			unset($post['user-content-title']);
		}
		$this->db->where($field, $value);
        if (!$this->db->update($tbl_name, $post)) {
            log_message('error', print_r($this->db->error(), true));
        }
		//echo $this->db->last_query(); die;
	}
	
	/*
	* Function : getCommonQuery
	*/
	
	public function getCommonQuery($tbl_name = 'tbl_school_master', $col = ' * ', $condition='',$order_by='',$condition_like='')
    {
        $otherdb = $this->load->database('dbreport', TRUE);
        $otherdb->select($col);
		$otherdb->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$otherdb->where($key, $val);
			}
			
		}
		// Like condition_like
		if(!empty($condition_like))
		{   $k=1;
			foreach($condition_like as $key=>$val) {
				$otherdb->like($key, $val);
				if($k>1) {
					$otherdb->or_like($key, $val);
				}
				$k++;
			}
			
		}
		
		if(!empty($order_by))
		{ 
			foreach($order_by as $key=>$val) {
				$otherdb->order_by($key, $val);
			}		
		}
			
		$query = $otherdb->get($tbl_name);
		
		
		//echo $otherdb->last_query(); //die;
        return $query->result_array();
    }
	
	public function SqlgetCommonQuery($tbl_name = 'su_schools', $col = ' * ', $condition='',$order_by='')
    {
		$otherdb = $this->load->database('db2', TRUE);
        $otherdb->select($col);
		$otherdb->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$otherdb->where($key, $val);
			}
			
		}
		if(!empty($order_by))
		{ 
			foreach($order_by as $key=>$val) {
				$otherdb->order_by($key, $val);
			}		
		}
       
			
		$query = $otherdb->get($tbl_name);
		
        return $query->result_array();
    }
	
	
	
	/*
	* Function : softDeleteRecords
	* Description : Delete Record
	*/
	public function softDeleteRecords($id, $field_name= 'id', $tbl_name)
    {
        $this->db->where($field_name, $id);
		$data = array();
		$data = array('is_deleted'=> '1','status'=>'0', 'modifiedon'=>date('Y-m-d h:i:s'));
        if (!$this->db->update($tbl_name, $data)) {
		    log_message('error', print_r($this->db->error(), true));
            show_error(lang('database_error'));
        }
		
    }
	
	/*
	* Function : getAllRecords
	*/
	public function customJoinQueryToGetAll($tbl_name, $col = '*', $condition=null, $order_by = NULL, $betweenDate=NULL, $limit=NULL, $start=NULL, $likeSearch=NULL, $where_in = NULL)
    {
	    $time = time();
		$otherdb = $this->load->database('dbreport', TRUE);
        $otherdb->select($col);
       # $otherdb->where('is_deleted', '0');
		
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$otherdb->where($key, $val);
			}
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
			$otherdb->where($likewhere);
		}
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$otherdb->order_by($key, $val);
			}
		}
	    if(!empty($betweenDate))
		{
			
			$from_date = $betweenDate['from_date'];
			$to_date = $betweenDate['to_date'];
			$otherdb->where("tbl_helpnsupport.createdon >='$from_date 00:00:00'");
			$otherdb->where("tbl_helpnsupport.createdon <='$to_date 23:59:59'");
		}
		$query = $otherdb->join('tbl_helpnsupport_comments', 'tbl_helpnsupport_comments.ticket_id = tbl_helpnsupport.id', 'LEFT');
		if ($limit !== null && $start !== null) {
           $query = $otherdb->get($tbl_name,$limit, $start);
        } else {
			$query = $otherdb->get($tbl_name);
		}
		
		//echo $otherdb->last_query(); die;
		return $query->result_array();
		
    }
	
	
}