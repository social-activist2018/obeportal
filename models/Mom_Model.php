<?php
class Mom_Model extends CI_Model {

    // Insert new MOM
    public function insertMOM($data) {
        $this->db->insert('mom_records', $data);
    }

    // Approve MOM
    public function approveMOM($id, $status_column) {
        $this->db->where('id', $id);
        $this->db->update('tbl_mom_records', [$status_column => 'approved']);
    }

    // Get MOMs based on user role
    public function getMOMsForRole($role) {
        if ($role == 'HOD') {
            $this->db->where('hod_status', 'pending');
        } elseif ($role == 'Dean') {
            $this->db->where('hod_status', 'approved');
            $this->db->where('dean_status', 'pending');
        } elseif ($role == 'VC') {
            $this->db->where('dean_status', 'approved');
            $this->db->where('vc_status', 'pending');
        }
        return $this->db->get('mom_records')->result();
    }
	
	public function saveinfo($tbl_name='', $post)
    {
		$this->db->insert($tbl_name, $post);
		#echo $this->db->last_query(); die;
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
		$db2 = $this->load->database('db2', TRUE);
        $db2->select($col);
        $db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
			
		}
		if(!empty($order_by))
		{ 
			foreach($order_by as $key=>$val) {
				$db2->order_by($key, $val);
			}
			
		}
		//$db2->order_by('id', 'asc');
        $query = $db2->get($tbl_name);
		$results = array();
		//echo $db2->last_query(); die;
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
		$db2 = $this->load->database('db2', TRUE);
        $db2->select($col);
       // $db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$db2->where($key, $val);
			}
			
		}
		
		// Like condition_like
		if(!empty($condition_like))
		{   $k=1;
			foreach($condition_like as $key=>$val) {
				$db2->like($key, $val);
				if($k>1) {
					$db2->or_like($key, $val);
				}
				$k++;
			}
			
		}
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$db2->order_by($key, $val);
			}
		}	
			
		$query = $db2->get($tbl_name);
		//echo $db2->last_query(); die;
		if($type){
			return $query->row();
		} else {
			return $query->row_array();
		}
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
	
	public function getCommonMOMnRecords($condition='')
	{		
			$db2 = $this->load->database('db2', TRUE);
			$db2->select('tbl_mom_records.*, tbl_employee_master.full_name, tbl_employee_master.employee_id, school_name, name as department_name');
			#$db2->join('tbl_mom_titles', 'tbl_mom_records.mom_title_id = tbl_mom_titles.id', 'left');
			$db2->join('tbl_employee_master', 'tbl_employee_master.employee_id = tbl_mom_records.created_by', 'left');
			$db2->join('tbl_school_master', 'tbl_school_master.id = tbl_mom_records.school_id', 'left');
			$db2->join('tbl_department_master', 'tbl_department_master.id = tbl_mom_records.department_id', 'left');
			$db2->where('tbl_mom_records.is_deleted', '0');
			if(!empty($condition))
		    { 
			    foreach($condition as $key=>$val) {
				    $db2->where($key, $val);
			    }			
		    }
			$query = $db2->get('tbl_mom_records');
			#echo $db2->last_query();die;
            return $query->result_array();
	}
	
	
	public function update_status($id, $status) {
        $this->db->where('id', $id);
        return $this->db->update('tbl_mom_records', ['dean_status' => $status]);
    }
	public function update_vcstatus($id, $status) {
        $this->db->where('id', $id);
        return $this->db->update('tbl_mom_records', ['vc_status' => $status]);
    }
	
}


