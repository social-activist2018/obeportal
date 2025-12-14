<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Class Home_Model extends CI_Model{
		
	/*
	* Function : getAllRecords
	*/
	public function getAllRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL)
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
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$this->db->order_by($key, $val);
			}
		}
		if ($limit !== null && $start !== null) {
           $query = $this->db->get($tbl_name,$limit, $start);
        } else {
			$query = $this->db->get($tbl_name);
		}
		//echo $this->db->last_query(); die;
		return $query->result_array();
    }

	/*
	* Function : deleteRecord
	*/	
	function deleteRecord($tbl_name = 'tbl_user_registration', $field = '', $uid = ''){
	 
		 $this->db->where($field,$uid);
		 if($this->db->delete($tbl_name)){
			 return true;
		 }else{
			 
			 return false;
		 }
	 
	 
	}
	/*
	* Function : getAllModuleList
	*/
	public function getAllModuleList($tbl_name, $col = ' * ', $condition=null, $where_in=NULL, $where_key = 'id')
    {
        $time = time();
        $this->db->select($col);
        $this->db->where('is_deleted', '0');
        $this->db->where_in($where_key, $where_in);
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db->where($key, $val);
			}
			
		}
		//$this->db->order_by('display_order', 'asc');
        $query = $this->db->get($tbl_name);
		//echo $this->db->last_query(); die;
        return $query->result_array();
    }
	
	/*
	* Function : getSingleRecord
	*/
	public function getSingleRecord($tbl_name, $col = ' * ', $condition=null)
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
		$query = $this->db->get($tbl_name);
		//echo $this->db->last_query(); die;
        return $query->row_array();
    }
	
	
	/*
	* Function : registrationCount
	*/
	
	public function registrationCount($tbl_name = 'patient_registration')
    {
        $result = $this->db->query("SELECT id FROM ".$tbl_name." where is_deleted='0'");
        return $result->num_rows();
        
    }
	/*
	* Function : countrylist
	*/
	
	public function countrylist($tbl_name = 'su_country', $col = ' * ')
    {
        $time = time();
        $this->db->select($col);
        $this->db->where('is_deleted', '0');
		$this->db->order_by('country_name', 'asc');
        $query = $this->db->get($tbl_name);
        return $query->result_array();
    }
	
	
	/*
	* Function : getCommonQuery
	*/
	
	public function getCommonQuery($tbl_name = 'tbl_schools', $col = ' * ', $condition='')
    {
        
        $this->db->select($col);
		$this->db->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db->where($key, $val);
			}
			
		}
        $query = $this->db->get($tbl_name);
        return $query->result_array();
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
	
	
	/*
	* Function: getAllbookingRecords
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	
	public function getAllbookingRecords($id='', $condition = '') 
	{
			$this->db->select('tbl_user_registration.*,tbl_booking_confirmation.id as bid,tbl_booking_confirmation.test_id, tbl_booking_confirmation.slot_id, tbl_booking_confirmation.slot_date, tbl_booking_confirmation.payment_method, tbl_booking_confirmation.ip_address,order_id,tbl_booking_confirmation.address,tbl_booking_confirmation.pincode');
			$this->db->join('tbl_user_registration', 'tbl_user_registration.id = tbl_booking_confirmation.user_id');
			if($id>0){
			 $this->db->where('tbl_booking_confirmation.id', $id);	
			}
			if(!empty($condition)){
				foreach($condition as $key=>$val){
					$this->db->like($key, $val);					
				}
			 
			}
			$this->db->where('tbl_booking_confirmation.is_deleted', '0');	
			$query = $this->db->get('tbl_booking_confirmation');
			//echo $this->db->last_query();die;
			if($id>0){
				return $query->row_array();
			} else {
				return $query->result_array();
			}
		
	}
	/*
	* Function: getSingleEventDetails
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	
	public function getSingleEventDetails($id='', $condition = '')
	{
			$this->db->select('tbl_event_master.*, tbl_schools.school_name,tbl_event_date_time.start_date,tbl_event_date_time.start_time, tbl_event_date_time.end_date,tbl_event_date_time.end_time,tbl_event_date_time.time_zone,tbl_event_location_details.venue_name,tbl_event_organizer_contactinfo.contact_name,tbl_event_organizer_contactinfo.phone,tbl_event_organizer_contactinfo.email,tbl_event_organizer_contactinfo.website_url');
			$this->db->join('tbl_event_date_time', 'tbl_event_date_time.event_id = tbl_event_master.id', 'left');
			$this->db->join('tbl_schools', 'tbl_schools.id = tbl_event_master.organizing_school', 'left');
			$this->db->join('tbl_event_organizer_contactinfo', 'tbl_event_organizer_contactinfo.event_id = tbl_event_master.id', 'left');
			$this->db->join('tbl_event_location_details', 'tbl_event_location_details.event_id = tbl_event_master.id', 'left');
			if($id>0){
			 $this->db->where('tbl_event_master.id', $id);	
			}
			if(!empty($condition)){
				foreach($condition as $key=>$val){
					$this->db->like($key, $val);					
				}
			 
			}
			$this->db->where('tbl_event_master.is_deleted', '0');	
			$this->db->where('tbl_event_master.status', '4');	
			$query = $this->db->get('tbl_event_master');
			//echo $this->db->last_query();die;
			return $query->row_array();
		
	}
	
	/*
	* Function : getAllCategoryList
	*/
	public function getAllCategoryList()
    {
		$this->db->select("count(tbl_event_master.id) as total, tbl_category.category_name ");
		$this->db->join('tbl_event_master', 'tbl_event_master.categories = tbl_category.id', 'left');
		$this->db->where('tbl_category.is_deleted', '0');	
		$this->db->where('tbl_category.status', '1');	
		$this->db->group_by('category_name');	
		$this->db->having('count(tbl_event_master.id)>', 0);
		$this->db->limit('20');
		$query = $this->db->get('tbl_category');
		//echo $this->db->last_query();die;
		return $query->result_array();
    }
	
	/*
	* Function : getAllTagList
	*/
	public function getAllTagList()
    {
		$this->db->select('count(tbl_event_master.id) as total, tbl_tags.tag_name');
		$this->db->join('tbl_event_master', 'tbl_event_master.tags = tbl_tags.id', 'left');
		$this->db->where('tbl_tags.is_deleted', '0');	
		$this->db->where('tbl_tags.status', '1');	
		$this->db->group_by('tag_name');	
		$this->db->having('count(tbl_event_master.id)>', 0);
		$this->db->limit('20');
		$query = $this->db->get('tbl_tags');
		//echo $this->db->last_query();die;
		return $query->result_array();
    }
	/*
	* Function : send_email_pepipost
	*/	
	function send_email_pepipost($to_emails, $subject, $message, $fromname='', $fromemail='', $replyto='')
	{
		$fromname=$fromname?$fromname:'Sharda Hospital';
		$fromemail=$fromemail?$fromemail:'enquiry@shardahospital.org'; //'info@shardauniversity.com';
		$replyto=$replyto?$replyto:'enquiry@shardahospital.org'; //'info@shardauniversity.com';
		
		if(!$to_emails){
			return;
		}
		if(is_string($to_emails)){
			$to_emails=explode(",", $to_emails);
		}
		foreach($to_emails as $to){
			$d=array (
				'personalizations' => array (0 => array ('recipient' => $to)),
				'from' => array ('fromEmail' => $fromemail, 'fromName' => $fromname),
				'replyToId'=>$replyto,
				'subject' => $subject,
				'content' => $message,
			);
			$email_jason_data=json_encode($d);

			$curl = curl_init();

			curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.pepipost.com/v2/sendEmail",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $email_jason_data,
			CURLOPT_HTTPHEADER => array(
				//"api_key: c77184012dcf9bd5cd1886b4e0a2bb89",
				"api_key: aab3f77715e90569034f0c6e5d912714",
				"content-type: application/json"
			),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
				//echo "cURL Error #:" . $err;
			} else {
				//echo $response;
			}
		}
	}
	
}