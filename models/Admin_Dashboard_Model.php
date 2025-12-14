<?php
defined('BASEPATH') OR exit('No direct script access allowed'); 
class Admin_Dashboard_Model extends CI_Model {

	private $db2;
	function __construct(){
		parent::__construct();
		$this->db2 = $this->load->database('db2', TRUE);
		if(! $this->session->userdata('elib_adminloggedin'))
		redirect('admin/login');
	}

/*
	* Function : getCommonQuery
	*/
	
	public function getCommonNumrowsQuery($tbl_name = 'tbl_schools', $col = ' * ', $condition='')
    {
        
        $this->db2->select($col);
		//$this->db->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
        $query = $this->db2->get($tbl_name);
		//echo $this->db->last_query(); die;
        return $query->num_rows();
    }
/*
	* Function : getCommonQuery
	*/
	
	public function getCommonQuery($tbl_name = 'tbl_schools', $col = ' * ', $condition='')
    {
        
        $this->db2->select($col);
		//$this->db->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
        $query = $this->db2->get($tbl_name);
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
* function : countlastsevendays
*
*/
public function countlastsevendays(){
$query2=$this->db2->select('id')   
                 ->where('regDate >=  DATE(NOW()) - INTERVAL 10 DAY')
                 ->get('tblusers');
return  $query2->num_rows();
}

/*
* function : countthirtydays
*
*/
public function countthirtydays(){
$query3=$this->db2->select('id')   
                 ->where('regDate >=  DATE(NOW()) - INTERVAL 30 DAY')
                 ->get('tblusers');
return  $query3->num_rows();
}



}
