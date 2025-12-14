<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Class Attendance_Model extends CI_Model{
	private $db2;
	public function __construct()
	{
		parent::__construct();
		$this->db2 = $this->load->database('db2', TRUE);
	}
	
	public function get_student_registration_details($class_nbr = '', $academic_year_id = '', $added_by='')
	{
		$this->db2->select('spd.admit_term, spd.class_nbr, spd.class_section, spd.system_id, sd.name as full_name');
		$this->db2->from('PS_S_PRD_STDREG_VW spd');
		$this->db2->join('tbl_student_details sd', 'spd.system_id = sd.system_id');
		$this->db2->where('spd.stdnt_enrl_status', '1');
		$this->db2->where('spd.status', '1');
		
		if (!empty($class_nbr)) {
			$this->db2->where('spd.class_nbr', $class_nbr);
		}

		if (!empty($added_by)) {
			$this->db2->where('sd.added_by', $added_by);
		}

		if (!empty($academic_year_id)) {
			$this->db2->where('spd.academic_year_id', $academic_year_id);
		}

		$query = $this->db2->get();
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
	* Function : getRecordsByGroupFullRow
	*/
	public function getRecordsByGroupFullRow($tbl_name='', $col = '*', $condition=null, $group_by_column='school_id')
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
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}
		}
		$this->db2->group_by($group_by_column);
		$query = $this->db2->get($tbl_name); 
		//echo $this->db2->last_query(); die;
		$resutls = array();
		foreach($query->result_array() as $row){
			$resutls[$row[$group_by_column]] = $row;
		}
        return $resutls;
    }
	
	/**
	* Get All topic lists
	*/
	public function getAllTopics($conditions='', $lastId='') 
	{
		if(!empty($conditions)) {
			foreach($conditions as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		$this->db2->select("`tbl_sessionplan`.`course_id`, `tbl_sessionplan`.`activities_id`, `tbl_sessionplan`.`mode_id`, `tbl_sessionplan`.`proposed_date`,tbl_syllabustopiclist.*");
		$this->db2->from('tbl_sessionplan');
		$this->db2->join('tbl_syllabustopiclist', 'tbl_syllabustopiclist.id = tbl_sessionplan.topic_name');
		if($lastId>0){
		$this->db2->where('tbl_sessionplan.last_id', $lastId);
		}
		$this->db2->distinct();
		$query = $this->db2->get();
		//echo $this->db2->last_query(); die;
		return $query->row_array();
	}


	/*
	* Function: totalClassesBySystemidQuery
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	
	public function totalClassesBySystemidQuery($course_id='', $class_number='', $system_id='', $class_section='')
	{
		$sql = "SELECT COUNT(distinct attendance_date,slot_name) as total_classes,course_id FROM tbl_attendance_master WHERE status='1' AND is_deleted='0' AND course_id = '".$course_id."' AND class_number = '".$class_number."' AND attendance_val IN('0','1','2','3','5') AND system_id='".$system_id."' AND class_section_name='".$class_section."'";
		$query = $this->db2->query($sql);
		//echo $this->db->last_query();die;
		$results = $query->row_array();
		return $results['total_classes'];
		
	}
	
	/*
	* Function: presentClassesBySystemIdQuery
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function presentClassesBySystemIdQuery ($course_id='',$class_number='', $system_id='', $class_section='')
	{
		$sql = "SELECT COUNT(distinct attendance_date,slot_name) as present_classes,system_id FROM tbl_attendance_master WHERE status='1' AND is_deleted='0' AND course_id = '".$course_id."' AND class_number = '".$class_number."' AND attendance_val IN('1','2','3') AND system_id='".$system_id."' AND class_section_name='".$class_section."'";
		$query = $this->db2->query($sql);
		//echo $this->db->last_query();die;
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['system_id']] = $row['present_classes'];
		}
		return $results;
	}
	/*
	* Function: getStudentCourseAttendance
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getStudentCourseAttendance ($course_id='',$class_number='', $class_section='')
	{
		$class_number = trim($class_number);
		$sql = "SELECT status ,is_deleted ,display_order,department_id,created_on ,admit_term,semester_name,employee_name,class_section,course_id ,catalog_nbr,system_id,student_name  ,eligibility,academic_year_id,attendance_date,total_classes_conducted,total_classes_attended,percentage,lec_type,class_number,term ,attendance_marked_by   FROM tbl_coursewise_attendance_master WHERE status='1' AND is_deleted='0' AND course_id = '".$course_id."' AND class_number = '".$class_number."' AND class_section='".$class_section."'";
		$query = $this->db2->query($sql);
		//echo $this->db->last_query();die;
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['system_id']] = $row['percentage'];
		}
		return $results;
	}
	/*
	* Function: getAvailableRoomsRecords
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	
	public function totalClassesQuery($course_id='', $class_number='')
	{
		$sql = "SELECT COUNT(distinct attendance_date,slot_name) as total_classes,course_id FROM tbl_attendance_master WHERE status='1' AND is_deleted='0' AND course_id = '".$course_id."' AND class_number = '".$class_number."' AND attendance_val IN('0','1','2','3','5')";
		$query = $this->db2->query($sql);
		//echo $this->db->last_query();die;
		$results = $query->row_array();
		return $results['total_classes'];
		
	}
	public function presentClassesQuery ($course_id='',$class_number='')
	{
		$sql = "SELECT COUNT(distinct attendance_date,slot_name) as present_classes,system_id FROM tbl_attendance_master WHERE status='1' AND is_deleted='0' AND course_id = '".$course_id."' AND class_number = '".$class_number."' AND attendance_val IN('1','2','3') group by system_id";
		$query = $this->db2->query($sql);
		//echo $this->db->last_query();die;
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['system_id']] = $row['present_classes'];
		}
		return $results;
	}
	public function getAvailableRoomsRecords($condition='', $last_id='')
	{
		if($last_id){
			$sql = "SELECT tbl_block_room_master.* FROM tbl_block_room_master LEFT JOIN tbl_deroomassignment ON tbl_block_room_master.id = tbl_deroomassignment.room_id WHERE tbl_deroomassignment.room_id ='".$last_id."'";
		} else {
			$sql = "SELECT tbl_block_room_master.* FROM tbl_block_room_master LEFT JOIN tbl_deroomassignment ON tbl_block_room_master.id = tbl_deroomassignment.room_id WHERE tbl_deroomassignment.room_id IS NULL";
			
		}
		$query = $this->db2->query($sql);
		//echo $this->db->last_query();die;
		return $query->result_array();
		
	}
	
	public function getAvailableRoomsRecordList($academic_year_id, $room_id='')
	{
		$results = array();
		$sharingRoom = $this->getSharingRoomsRecordList();
		if($academic_year_id>0){
			if($room_id){
				array_push($sharingRoom,$room_id);
				$room_list = implode(',',$sharingRoom);
				$sqlQuery = "SELECT * FROM tbl_block_room_master WHERE id NOT IN(SELECT room_id FROM tbl_deroomassignment WHERE academic_year_id = '".$academic_year_id."' AND room_id NOT IN($room_list)) AND status='1' order by room_number asc";
			} else {
				$room_list = implode(',',$sharingRoom);
				if(!empty($sharingRoom)) {
					$sqlQuery = "SELECT * FROM tbl_block_room_master WHERE id NOT IN(SELECT room_id FROM tbl_deroomassignment WHERE academic_year_id = '".$academic_year_id."' AND room_id NOT IN($room_list))  AND status='1' order by room_number asc ";
				} else {
					$sqlQuery = "SELECT * FROM tbl_block_room_master WHERE id NOT IN(SELECT room_id FROM tbl_deroomassignment WHERE academic_year_id = '".$academic_year_id."')  AND status='1' order by room_number asc";					
				}
				
			}
			$query = $this->db2->query($sqlQuery);
			$results = $query->result_array();
		}
		
		/*if($academic_year_id>0){
			if($room_id){
				$sql = "SELECT * FROM tbl_block_room_master WHERE id NOT IN(select room_id from tbl_deroomassignment WHERE academic_year_id = '".$academic_year_id."' AND id!='".$room_id."')";
			} else {
				$sql = "SELECT * FROM tbl_block_room_master WHERE id NOT IN(select room_id from tbl_deroomassignment WHERE academic_year_id = '".$academic_year_id."')";
			}
			$query = $this->db->query($sql);
			$results = $query->result_array();
		}*/
		//echo $this->db->last_query();die;
		return $results;
		
	}
	
	
	public function getAvailableDRoomsList($academic_year_id, $room_id='')
	{
		$results = array();
		$sharingRoom = $this->getSharingRoomsRecordList();
		if($academic_year_id>0){
			if($room_id){
				array_push($sharingRoom,$room_id);
				$room_list = implode(',',$sharingRoom);
				$sqlQuery = "SELECT * FROM tbl_block_room_master WHERE id NOT IN(SELECT room_id FROM tbl_deroomassignment WHERE academic_year_id = '".$academic_year_id."' AND room_id NOT IN($room_list)) AND status='1' order by room_number asc";
			} else {
				$room_list = implode(',',$sharingRoom);
				if(!empty($sharingRoom)) {
					$sqlQuery = "SELECT * FROM tbl_block_room_master WHERE id NOT IN(SELECT room_id FROM tbl_deroomassignment WHERE academic_year_id = '".$academic_year_id."' AND room_id NOT IN($room_list))  AND status='1' order by room_number asc ";
				} else {
					$sqlQuery = "SELECT * FROM tbl_block_room_master WHERE id NOT IN(SELECT room_id FROM tbl_deroomassignment WHERE academic_year_id = '".$academic_year_id."')  AND status='1' order by room_number asc";					
				}
				
			}
			$query = $this->db2->query($sqlQuery);
			$results = $query->result_array();
			//print_r($results); die;
		}
		
		return $results;
		
	}
	
	/*
	* Function: getSharingRoomsRecordList
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getSharingRoomsRecordList()
	{
		$roomListArray = array();
		$sql = "SELECT id FROM tbl_block_room_master WHERE room_sharing='1' AND status='1'";
		$query = $this->db2->query($sql);
		$roomListArray = array();
		foreach($query->result_array() as $row){
			$roomListArray[$row['id']] = $row['id'];
		}
		
		//echo $this->db->last_query();die;
		return $roomListArray;
	}
	
	/*
	* Function: getExistingAvailableRoomsRecords
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	
	public function getExistingAvailableRoomsRecords($condition='',$room_id='')
	{
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		
		if($room_id>0){
			$this->db2->where('tbl_deroomassignment.room_id', $room_id);
		}
		$this->db2->where('tbl_block_room_master.status', '1');
		$query = $this->db2->join('tbl_deroomassignment', 'tbl_deroomassignment.room_id = tbl_block_room_master.id');
		$this->db2->distinct();
		$query = $this->db2->select("tbl_block_room_master.*")->get('tbl_block_room_master');
		//echo $this->db->last_query();die;
		return $query->result_array();

		
	}
	
	public function getAllSyllabustopiclist($condition='',$course_id='',$class_number='',$section='',$pi='')
	{
		//echo $pi; die;
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		
		if($course_id>0){
			$this->db2->where('tbl_syllabustopiclist.course_id', $course_id);
		}
		if($class_number>0){
			$this->db2->where('tbl_syllabustopiclist.class_nbr', $class_number);
		}
		if($section>0){
			$this->db2->where('tbl_syllabustopiclist.class_section_name', $section);
		}
		$this->db2->where('tbl_syllabustopiclist.status', '1');
		$query = $this->db2->join('tbl_studymaterial_mapping', 'tbl_studymaterial_mapping.topic_id = tbl_syllabustopiclist.id');
		$this->db2->where('tbl_studymaterial_mapping.added_by', $pi);
		$this->db2->distinct();
		$query = $this->db2->select("tbl_syllabustopiclist.id, tbl_syllabustopiclist.syllabus_topic,tbl_studymaterial_mapping.study_material_id, tbl_studymaterial_mapping.course_id")->get('tbl_syllabustopiclist');
		#echo $this->db2->last_query();die;
		return $query->result_array();
	}
	
	public function getAllSyllabustopiclist_live($condition='',$course_id='',$class_number='',$section='')
	{
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		
		if($course_id>0){
			$this->db2->where('tbl_syllabustopiclist.course_id', $course_id);
		}
		if($class_number>0){
			$this->db2->where('tbl_syllabustopiclist.class_nbr', $class_number);
		}
		if($section>0){
			$this->db2->where('tbl_syllabustopiclist.class_section_name', $section);
		}
		$this->db2->where('tbl_syllabustopiclist.status', '1');
		$query = $this->db2->join('tbl_studymaterial_mapping', 'tbl_studymaterial_mapping.topic_id = tbl_syllabustopiclist.id');
		$this->db2->distinct();
		$query = $this->db2->select("tbl_syllabustopiclist.id, tbl_syllabustopiclist.syllabus_topic,tbl_studymaterial_mapping.study_material_id, tbl_studymaterial_mapping.course_id")->get('tbl_syllabustopiclist');
		//echo $this->db->last_query();die;
		return $query->result_array();
	}
	
	/*
	* Function: getAllSyllabustopicCountlist
	*
	*/
	public function getAllSyllabustopicCountlist($ACADEMIC_ID, $course_id='', $class_number='')
	{ 
	   
		$responseArray = array();
		if($ACADEMIC_ID){
			if($course_id) {
				$query = $this->db2->query("SELECT count(distinct slot_name,attendance_date) as total, syllabus_topic_id, course_id  FROM `tbl_attendance_master` where status='1' AND academic_year_id='".$ACADEMIC_ID."' AND  course_id = '".$course_id."' AND  class_number = '".$class_number."' group by syllabus_topic_id");
			} else {
				$query = $this->db2->query("SELECT count(distinct slot_name,attendance_date) as total, syllabus_topic_id, course_id  FROM `tbl_attendance_master` where status='1' AND academic_year_id='".$ACADEMIC_ID."' group by syllabus_topic_id");
			}
			$results = array();
			$results = $query->result_array();
			
			foreach($results as $row){
				$responseArray[$row['syllabus_topic_id']] = $row['total'];
			}
		}
		return  $responseArray;
	}
	
	/*
	* Function: getAllAvailableSlotRecords
	*
	*/
	public function getAllAvailableSlotRecords($lecture_type='', $roomArray=null, $classnArray=null, $semester_id=null, $is_medical='0', $academic_year_id='4')
	{
		//print_r($classnArray); //die;
		//print_r($roomArray); die;
		$roomArray = array();
		foreach($roomArray as $room){
			$roomArray[$room['room_id']] = $room['room_id'];
		}
		
		$classnArray = array();
		foreach($classnArray as $class){
			$classnArray[$class->CLASS_NBR] = $class->CLASS_NBR;
		}
		$sql = "";
		//$sql .= "SELECT tbl_slot_master.* FROM tbl_slot_master LEFT JOIN tbl_assign_room_slot_section ON tbl_assign_room_slot_section.slot_id = tbl_slot_master.id WHERE tbl_assign_room_slot_section.slot_id IS NULL AND  lecture_type='".$lecture_type."' ";
		$sql .= "SELECT tbl_slot_master.* FROM tbl_slot_master LEFT JOIN tbl_assign_room_slot_section ON tbl_assign_room_slot_section.slot_id = tbl_slot_master.id WHERE  lecture_type='".$lecture_type."' AND is_medical='".$is_medical."' AND tbl_slot_master.academic_year_id='".$academic_year_id."' AND tbl_slot_master.is_deleted='0' ";
		if($semester_id) {
			$sql .= " AND tbl_assign_room_slot_section.semester_id!='".$semester_id."'";
		}
		$query = $this->db2->query($sql);
		//echo $this->db->last_query();die;   
		return $query->result_array();
		
	}
	
	/*
	* Function : getAllAvailableRoomSlotArray
	* DESC:
	* Createdby: Amit Verma
	*/
	
	public function getAllAvailableRoomSlotArray($roomIds=null, $slot_id=null,$ACADEMIC_ID = ACADEMIC_ID)
	{
		$results = array();
		
		if($roomIds>0 && $slot_id>0) {
			$sql = "SELECT slot_id FROM tbl_assign_room_slot_section ss JOIN tbl_department_course_slot_assignment ds ON ds.id=ss.dept_course_id WHERE ss.room_number = '".$roomIds."' AND ss.slot_id = '".$slot_id."' AND ds.academic_year_id='".$ACADEMIC_ID."' AND ss.status='1'  AND ss.is_deleted='0' AND ds.status='1'";
			$query = $this->db2->query($sql);
			//echo $this->db->last_query();die;   
			$results =  $query->row_array();
		}
		
		return $results;
	}
	/*
	* Function : getAllAvailablePIRoomSlotArray
	* DESC:
	* Createdby: Amit Verma
	*/
	
	public function getAllAvailablePIRoomSlotArray($roomIds=null, $slot_id=null, $course_pi=null, $class_nbr=null,$ACADEMIC_ID =ACADEMIC_ID)
	{
		$results = array();
		
		if($roomIds>0 && $course_pi>0 && $slot_id>0) {
			
			$sharedRoomArray = $this->getSharedRoomDetails($ACADEMIC_ID);
			$where_in = '';
			if(!in_array($roomIds,$sharedRoomArray)) {
				$where_in = "ss.room_number = '".$roomIds."' AND ";
			} 
			
			$sql = "SELECT slot_id FROM tbl_assign_room_slot_section ss JOIN tbl_department_course_slot_assignment ds ON ds.id=ss.dept_course_id WHERE ss.room_number = '".$roomIds."' AND ss.slot_id = '".$slot_id."' AND ds.academic_year_id='".$ACADEMIC_ID."' AND ss.course_pi='".$course_pi."' AND ss.status='1'  AND ss.is_deleted='1'  AND ds.status='1' ";
			
			//$sql = "SELECT slot_id FROM tbl_assign_room_slot_section WHERE room_number = '".$roomIds."' AND slot_id = '".$slot_id."' AND course_pi='".$course_pi."' ";
			$query = $this->db2->query($sql);
			//echo $this->db->last_query();die;   
			$results =  $query->row_array();
		}
		
		return $results;
	}
	/*
	* Function : getAllAvailablePIRoomVersionSlotArray
	* DESC:
	* Createdby: Amit Verma
	*/
	
	public function getAllAvailablePIRoomVersionSlotArray($roomIds=null, $slot_id=null, $course_pi=null, $class_nbr=null,$ACADEMIC_ID =ACADEMIC_ID)
	{
		$results = array();
		
		if($roomIds>0 && $course_pi>0 && $slot_id>0) {
			
			$sharedRoomArray = $this->getSharedRoomDetails($ACADEMIC_ID);
			$where_in = '';
			if(!in_array($roomIds,$sharedRoomArray)) {
				$where_in = "ss.room_number = '".$roomIds."' AND ";
			} 
			
			$sql = "SELECT slot_id FROM tbl_assign_room_slot_section ss JOIN tbl_department_course_slot_assignment ds ON ds.id=ss.dept_course_id JOIN tbl_timetable_management tm ON tm.id=ds.tt_version_id WHERE ss.room_number = '".$roomIds."' AND ss.slot_id = '".$slot_id."' AND ds.academic_year_id='".$ACADEMIC_ID."' AND ss.course_pi='".$course_pi."' AND tm.status='1'  AND ss.status='1'  AND ss.is_deleted='1'  AND ds.status='1' ";
			
			//$sql = "SELECT slot_id FROM tbl_assign_room_slot_section WHERE room_number = '".$roomIds."' AND slot_id = '".$slot_id."' AND course_pi='".$course_pi."' ";
			$query = $this->db2->query($sql);
			//echo $this->db->last_query();die;   
			$results =  $query->row_array();
		}
		
		return $results;
	}
	
	/*
	* Function : getAllAvailableSlotListArray
	* DESC:
	* Createdby: Amit Verma
	*/
	public function getAllAvailableSlotListArray($lecture_type='', $roomIds=null, $classnNbr=null, $slot_id='', $semester_id='', $is_medical='0', $academic_year_id=SLOT_ACTIVE_ADMIT_TERM)
	{
		$lecture_type = $lecture_type>0 ? $lecture_type:'2';
		$results = array();
		if($lecture_type>0 && $roomIds>0) {
			if(!empty($slot_id)) {
				$sql = "select id, display_name FROM tbl_slot_master where id NOT IN( SELECT slot_id FROM tbl_assign_room_slot_section WHERE lecture_type='".$lecture_type."' AND room_number = '".$roomIds."' AND slot_id NOT IN($slot_id)) AND lecture_type='".$lecture_type."' AND is_medical='".$is_medical."' AND is_deleted='0' AND tbl_slot_master.academic_year_id='".$academic_year_id."' ";
			} else {
				$sql = "select id, display_name FROM tbl_slot_master where id NOT IN( SELECT slot_id FROM tbl_assign_room_slot_section WHERE lecture_type='".$lecture_type."' AND room_number = '".$roomIds."') AND lecture_type='".$lecture_type."'  AND is_medical='".$is_medical."' AND is_deleted='0' AND tbl_slot_master.academic_year_id='".$academic_year_id."'";
			}
			$query = $this->db2->query($sql);
			//echo $this->db->last_query();die;   
			$results =  $query->result_array();
			
		}
		
		return $results;
	}
	
	
	/* Function : getAllAvailableSlotByPIArray
	* DESC:
	* Createdby: Amit Verma
	*/
	public function getAllAvailableSlotByPIArray($lecture_type='', $roomIds=null, $classnNbr=null, $slot_id='', $semester_id='', $course_id='', $course_pi='',$is_medical='0',$academic_year_id='4')
	{
			$results = array();

			if($course_id>0 && $roomIds>0 && $course_pi>0) {
				$sql = "select id, display_name FROM tbl_slot_master where id IN( SELECT slot_id FROM tbl_assign_room_slot_section WHERE lecture_type='".$lecture_type."' AND room_number = '".$roomIds."'  AND course_id='".$course_id."' AND course_pi='".$course_pi."' AND tbl_assign_room_slot_section.status='1' ) AND lecture_type='".$lecture_type."' AND is_medical='".$is_medical."' AND tbl_slot_master.is_deleted='0' AND tbl_slot_master.is_deleted='0' AND tbl_slot_master.academic_year_id='".$academic_year_id."'";
				$query = $this->db2->query($sql);
				//echo $this->db->last_query();die;   
				$results =  $query->result_array();
			}
			
		return $results;
	}
	
	/*
	* Function : getAllAvailableSlotArray
	* DESC:
	* Createdby: Amit Verma
	*/
	public function getAllAvailableSlotArray($lecture_type='', $roomIds=null, $classnNbr=null, $slot_id='', $semester_id='',$is_medical='0', $academic_year_id='3')
	{
		
		$results = array();
		if($lecture_type>0 && $roomIds>0) {
			$sharedRoomArray = '';
			if($academic_year_id>0) {
				$sharedRoomArray = $this->getSharedRoomDetails($academic_year_id);
			}
				
			if(!empty($slot_id)) {
				$sql = "select id, display_name FROM tbl_slot_master where id IN( SELECT slot_id FROM tbl_assign_room_slot_section WHERE lecture_type='".$lecture_type."' AND room_number = '".$roomIds."' AND slot_id NOT IN($slot_id) AND semester_id!='".$semester_id."' AND status='1') AND lecture_type='".$lecture_type."' AND is_medical='".$is_medical."' AND tbl_slot_master.is_deleted='0' AND tbl_slot_master.academic_year_id='".$academic_year_id."' AND tbl_slot_master.is_winter='0'";
			} else {
				$where_in = " ";
				if(!in_array($roomIds, $sharedRoomArray)) {
					$sql = "SELECT tsm.id, tsm.display_name FROM tbl_slot_master tsm LEFT JOIN tbl_assign_room_slot_section tars ON tsm.id = tars.slot_id AND tsm.lecture_type = '".$lecture_type."' AND tars.room_number = '".$roomIds."' AND tars.semester_id != '".$semester_id."' AND tars.is_deleted = '0' AND tars.status = '1' WHERE tars.slot_id IS NULL AND tsm.lecture_type = '".$lecture_type."' AND tsm.is_medical = '0' AND tsm.is_deleted = '0' AND tsm.academic_year_id = '".$academic_year_id."' AND tsm.is_winter='0'";
			
				} else {
					$sql = "select id, display_name FROM tbl_slot_master where $where_in lecture_type='".$lecture_type."' AND is_medical='".$is_medical."' AND tbl_slot_master.is_deleted='0' AND tbl_slot_master.academic_year_id='".$academic_year_id."' AND tbl_slot_master.is_winter='0'";
				}
			}
			$query = $this->db2->query($sql);
			//echo $this->db->last_query();die;   
			$results =  $query->result_array();
			
		}
		
		return $results;
	}
	
	/*
	* Function : getAllAvailableSlotArray
	* DESC:
	* Createdby: Amit Verma
	*/
	public function getAllAvailableSlotArray_orig($lecture_type='', $roomIds=null, $classnNbr=null, $slot_id='', $semester_id='',$is_medical='0', $academic_year_id='3')
	{
		
		$results = array();
		if($lecture_type>0 && $roomIds>0) {
			$sharedRoomArray = '';
			if($academic_year_id>0) {
				$sharedRoomArray = $this->getSharedRoomDetails($academic_year_id);
			}
				
			if(!empty($slot_id)) {
				$sql = "select id, display_name FROM tbl_slot_master where id IN( SELECT slot_id FROM tbl_assign_room_slot_section WHERE lecture_type='".$lecture_type."' AND room_number = '".$roomIds."' AND slot_id NOT IN($slot_id) AND semester_id!='".$semester_id."' AND status='1') AND lecture_type='".$lecture_type."' AND is_medical='".$is_medical."' AND tbl_slot_master.is_deleted='0' AND tbl_slot_master.academic_year_id='".$academic_year_id."'";
			} else {
				$where_in = " ";
				if(!in_array($roomIds, $sharedRoomArray)) {
					$where_in = " id NOT IN( SELECT slot_id FROM tbl_assign_room_slot_section WHERE lecture_type='".$lecture_type."' AND room_number = '".$roomIds."' AND semester_id!='".$semester_id."' AND tbl_assign_room_slot_section.is_deleted='0' AND tbl_assign_room_slot_section.status='1' ) AND ";
				}
				$sql = "select id, display_name FROM tbl_slot_master where $where_in lecture_type='".$lecture_type."' AND is_medical='".$is_medical."' AND tbl_slot_master.is_deleted='0' AND tbl_slot_master.academic_year_id='".$academic_year_id."'";
			}
			$query = $this->db2->query($sql);
			//echo $this->db->last_query();die;   
			$results =  $query->result_array();
			
		}
		
		return $results;
	}
	
	/*
	* Function : getSharedRoomDetails
	*/	
	public function getSharedRoomDetails($academic_year_id) 
	{
		$resutls = array();
		if($academic_year_id>0){
			
			$sql = "select id, title,room_number,room_sharing from tbl_block_room_master WHERE room_sharing='1' AND racademic_year_id='".$academic_year_id."'";
			$query = $this->db2->query($sql);
			$resutl = $query->result_array();
			foreach($resutl as $row){
				$resutls[$row['id']] = $row['id'];
			}
		}
		return $resutls;
	}
		
	/*
	* Function : getBloomsTaxonomy
	*/
	public function getBloomsTaxonomy($condArray)
	{
		$btl_id = $condArray['btl_id'];
		$course_id = $condArray['course_id'];
		$cos_id = $condArray['cos_id'];
		$qTitle = $condArray['cos_title'];
		
		$this->db2->select('id,btl_id,title');
		$this->db2->where('is_deleted', '0');
		$this->db2->where('status', '1');
		$qTitle = str_replace(array("'s",'?','.','`','@','http',':','#','','"','\'', '"',',' , ';', '<', '>', '.', '*'),' ', $qTitle);
		$qTitleArray = explode(' ', $qTitle);
		if(!empty($qTitleArray)){
			$orArray = array();
			foreach($qTitleArray as $rvalue){
				$frvalue = '';
				$frvalue = str_replace(array("'s",'?','.','`','@','http',':','#','','"','\'', '"',',' , ';', '<', '>'),'', $rvalue);
				if(preg_match('/^[-a-zA-Z0-9_]+$/i', $frvalue)) {
					//$orArray[] = ' title  like "%'.$frvalue.'%" ';
					if(ltrim($frvalue)){
						$orArray[] = ' title  = "'.ltrim($frvalue).'" ';
					}
				}
			}
			
			if(!empty($orArray)){
				$or_like = implode(' OR ', $orArray);
				$or_like = '( '.$or_like.' )';
			}
			
			$this->db2->where($or_like);
		}
		
		//$this->db->where('btl_id', $btl_id);
		$query = $this->db2->get('tbl_bloomstaxonomy ');
		//echo $this->db->last_query(); die;
		$results = array();
		$results = $query->num_rows();
		return $query->row_array();
	}
	
	/*
	* Function : getAllTransferCourseRecords
	*/	
	public function getAllTransferCourseRecords($department_id='',$semester_id='') 
	{
		$resutls = array();
		if($department_id>0){
			$where = '';
			if($semester_id){
				$where = ' AND qs.semester_id='.$semester_id;
			}
			$sql = "select qs.* from tbl_teaching_scheme qs JOIN tbl_course cs ON qs.course_id=cs.id  WHERE is_transfer='1' AND cs.`subject_area` ='".$department_id."' $where order by id DESC";
			$query = $this->db2->query($sql);
			$resutls = $query->result_array();
		}
		return $resutls;
	}
	/*
	* Function : getAllotedRoomDetails
	*/	
	public function getAllotedRoomDetails($recordsArray) 
	{
		$resutls = array();
		if($recordsArray['department_id']>0){
			$where = '';
			$department_id = $recordsArray['department_id'];
			$semester_id = $recordsArray['semester_id'];
			if($semester_id){
				//$where = ' AND ds.semester_id='.$semester_id;
			}
			$sql = "select ds.*, rs.room_number, rs.title,rs.id as room_id from tbl_deroomassignment ds JOIN tbl_block_room_master rs ON ds.room_id=rs.id  WHERE rs.status='1' AND ds.`department_id` ='".$department_id."' $where order by rs.room_number DESC";
			$query = $this->db2->query($sql);
			$resutls = $query->result_array();
		}
		return $resutls;
	}
	
	/*
	* Function : questionBankReport
	*/	
	public function questionBankReport($academicYear='2') 
	{
		
		$sql = "select count(*) as total, school_name,school_id from tbl_questionbank qs JOIN tbl_teaching_scheme cs ON qs.course_id=cs.course_id LEFT JOIN tbl_school_master sm ON sm.id=cs.school_id WHERE cs.academic_year_id='".$academicYear."' AND  qs.unit_id IS NULL group by school_id order by total DESC";
				
		$query = $this->db2->query($sql);
		$resutls = array();
		foreach($query->result_array() as $row){
			$resutls[$row['school_id']] = $row;
		}
		
		return $resutls;
	}
	/*
	* Function : questionBankActiveReport
	*/	
	public function questionBankActiveReport($academicYear='2') 
	{
		$sql = "select count(*) as total, school_name,school_id from tbl_questionbank qs JOIN tbl_teaching_scheme cs ON qs.course_id=cs.course_id LEFT JOIN tbl_school_master sm ON sm.id=cs.school_id WHERE cs.academic_year_id='".$academicYear."' AND qs.unit_id IS NOT NULL group by school_id order by total DESC";
		$query = $this->db2->query($sql);
		$resutls = array();
		foreach($query->result_array() as $row){
			$resutls[$row['school_id']] = $row;
		}
		
		return $resutls;
	}
	
	/* 
	* Function : getAllRecordsGroupBy
	*/
	public function getAllRecordsGroupBy($tbl_name, $col = '*', $condition=null, $order_by = NULL, $limit=NULL, $start=NULL)
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
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}
		}
		$this->db2->group_by('school_id');
		if ($limit !== null && $start !== null) {
           $query = $this->db2->get($tbl_name,$limit, $start);
        } else {
			$query = $this->db2->get($tbl_name);
		}
		
		//echo $this->db->last_query(); die;
		$resutls = array();
		foreach($query->result_array() as $row){
			$resutls[$row['school_id']] = $row['total'];
		}
        return $resutls;
    }
	
	/*
	* Function : getRecordsByGroup
	*/
	public function getRecordsByGroup($tbl_name='', $col = '*', $condition=null, $group_by_column='school_id')
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
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}
		}
		$this->db2->group_by($group_by_column);
		$query = $this->db2->get($tbl_name); 
		//echo $this->db->last_query(); die;
		$resutls = array();
		foreach($query->result_array() as $row){
			$resutls[$row[$group_by_column]] = $row['total'];
		}
        return $resutls;
    }
	/*
	* Function : getAllRecords
	*/
	public function getAllRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL, $likeSearch=NULL, $where_in = NULL)
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
		if(!empty($where_in)){
			$this->db2->where_in('program_id', $where_in);
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
			$this->db2->where($likewhere);
		}
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}
		}
		if ($limit !== null && $start !== null) {
           $query = $this->db2->get($tbl_name,$limit, $start);
        } else {
			$query = $this->db2->get($tbl_name);
		}
		//echo $this->db->last_query(); die;
		return $query->result_array();
    }
	
	/*
	* Function : getAllRecords
	*/
	public function getAllDistinctRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL, $likeSearch=NULL, $where_in = NULL)
    {
        $time = time();
		$this->db2->distinct();
        $this->db2->select($col);
        $this->db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		if(!empty($where_in)){
			$this->db2->where_in('program_id', $where_in);
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
			$this->db2->where($likewhere);
		}
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}
		}
		if ($limit !== null && $start !== null) {
           $query = $this->db2->get($tbl_name,$limit, $start);
        } else {
			$query = $this->db2->get($tbl_name);
		}
		//echo $this->db->last_query(); die;
		return $query->result_array();
    }
	
	/*
	* Function : getAllRecordsWhereNotin
	*/
	public function getAllRecordsWhereNotin($tbl_name, $col = ' * ', $condition=null, $date = NULL, $where_not_in = NULL)
    {
        $time = time();
		$system_idsArray = array();
		foreach($where_not_in as $row){
			$system_idsArray[$row->EMPLID] = $row->EMPLID;
		}
        $this->db2->select($col);
        $this->db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				
				if($key=='ATTENDANCE_DATE')
				{
					$this->db2->where("from_date <= '".$val."' AND to_date >= '".$val."'");
				} else {
					$this->db2->where($key, $val);
				}
			}
		}
		if(!empty($where_not_in)){
			$this->db2->where_in('system_id', array_keys($system_idsArray));
		}
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}
		}
		$query = $this->db2->get($tbl_name);
		//echo $this->db->last_query(); die;
		$recordsArray = array();
		foreach($query->result_array() as $nrow){
			$recordsArray[$nrow['system_id']] = $nrow;
		}
		return $recordsArray;
    }
	
	/*
	* Function : getAllRecordsWhereNotin
	*/
	public function getAllRecordsWhereGETNOCNotin($tbl_name, $col = ' * ', $condition=null, $where_not_in = NULL)
    {
        $time = time();
		$system_idsArray = array();
		foreach($where_not_in as $row){
			$system_idsArray[$row['system_id']] = $row['system_id'];
		}
        $this->db2->select($col);
        $this->db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				
				if($key=='ATTENDANCE_DATE')
				{
					$this->db2->where("from_date < '".$val."' AND to_date > '".$val."'");
				} else {
					$this->db2->where($key, $val);
				}
			}
		}
		if(!empty($where_not_in)){
			$this->db2->where_in('system_id', array_keys($system_idsArray));
		}
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}
		}
		$query = $this->db2->get($tbl_name);
		//echo $this->db->last_query(); die;
		$recordsArray = array();
		foreach($query->result_array() as $nrow){
			$recordsArray[$nrow['system_id']] = $nrow;
		}
		return $recordsArray;
    }
	
	/*
	* Function : getAllRecordsSingleCount
	*/
	public function getAllRecordsSingleCount($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL, $likeSearch=NULL, $where_in = NULL)
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
		if(!empty($where_in)){
			$this->db2->where_in('program_id', $where_in);
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
			$this->db2->where($likewhere);
		}
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}
		}
		if ($limit !== null && $start !== null) {
           $query = $this->db2->get($tbl_name,$limit, $start);
        } else {
			$query = $this->db2->get($tbl_name);
		}
		//echo $this->db->last_query(); die;
		return $results = $query->num_rows();;
    }
	
	/*
	* Function : getAllRecordsWhereIn
	*/
	public function getAllRecordsWhereIn($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL, $likeSearch=NULL, $where_in = NULL)
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
		if(!empty($where_in)){
			$this->db2->where_in('id', $where_in);
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
			$this->db2->where($likewhere);
		}
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}
		}
		if ($limit !== null && $start !== null) {
           $query = $this->db2->get($tbl_name,$limit, $start);
        } else {
			$query = $this->db2->get($tbl_name);
		}
		//echo $this->db->last_query(); die;
		return $query->result_array();
    }
	
	
	/*
	* Function : getAllMyRecords
	*/
	public function getAllMyRecords($tbl_name, $col = ' * ', $condition=null)
    {
        $time = time();
        $this->db2->select($col);
     	if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->like($key, $val);
			}
		}
		$this->db2->order_by('id', 'desc');
		$query = $this->db2->get($tbl_name,'500', '0');
		//echo $this->db->last_query(); //die;
		return $query->result_array();
    }
	
	
	/*
	* Function : getAllGrievanceHistoryRecords
	*/
	public function getAllGrievanceHistoryRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL)
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
		$dateCond = 'now()-interval 3 month';
		$this->db2->where('lastUpdationDate >=', $dateCond, FALSE);
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}
		}
		if ($limit !== null && $start !== null) {
           $query = $this->db2->get($tbl_name,$limit, $start);
        } else {
			$query = $this->db2->get($tbl_name);
		}
		//echo $this->db->last_query(); die;
		return $query->result_array();
    }
	
	/*
	* Function : getAllAcademicprogrammeRecords
	*/
	public function getAllAcademicprogrammeRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL)
    {
        $time = time();
        $this->db2->select($col);
        $this->db2->select($col);
        $this->db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		
		$this->db2->where('MONTH(regDate)', date('m'));
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}
		}
		$query = $this->db2->get($tbl_name);
        return $query->result_array();
    }
	/*
	* Function : getAllRecords
	*/
	public function getAllMonthlyRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL)
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
		
		$this->db2->where('MONTH(regDate)', date('m'));
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}
		}
		$query = $this->db2->get($tbl_name);
        return $query->result_array();
    }
	
	/*
	* Function : getAllModuleList
	*/
	public function getAllModuleList($tbl_name, $col = ' * ', $condition=null, $where_in=NULL, $where_key = 'id')
    {
        $time = time();
        $this->db2->select($col);
        $this->db2->where('is_deleted', '0');
        $this->db2->where_in($where_key, $where_in);
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
		//$this->db2->order_by('display_order', 'asc');
        $query = $this->db2->get($tbl_name);
		//echo $this->db->last_query(); die;
        return $query->result_array();
    }
	
	/*
	* Function : getCommonPMArray
	*/
	public function getCommonPMArray($tbl_name='tbl_course_papersetter_moderator', $cond = '')
	{
		if(!empty($cond)) {
			foreach($cond as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		$query = $this->db2->select("*")->get($tbl_name);
		//echo $this->db->last_query(); die;
		$resp = $query->result_array();
		$results = array();
		foreach( $resp as $val){
			$results[$val['id']] = $val;
		}
		return $results;
		
    }
	
	/*
	* Function : getSingleRecord
	*/
	public function getSingleRecord($tbl_name, $col = ' * ', $condition=null , $type='')
	{
        $time = time();
        $this->db2->select($col);
       // $this->db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
				
		$query = $this->db2->get($tbl_name);
		//echo $this->db2->last_query(); die;
		if($type){
			return $query->row();
		} else {
			return $query->row_array();
		}
    }
	
	
	/*
	* Function : getCourseWiseCPMArray
	*/
	public function getCourseWiseCPMArray($tbl_name='tbl_course_papersetter_moderator', $cond = '')
	{
		if(!empty($cond)) {
			foreach($cond as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		$query = $this->db2->select("*")->get($tbl_name);
		//echo $this->db->last_query(); die;
		$resp = $query->result_array();
		$results = array();
		foreach( $resp as $val){
			$results[$val['course_id']] = $val;
		}
		return $results;
		
    }
	
	/*
	* Function : getCommonPModeratorArray
	*/
	public function getCommonPModeratorArray($tbl_name='tbl_course_papersetter_moderator', $cond = '')
	{
		if(!empty($cond)) {
			foreach($cond as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		$query = $this->db2->select("*")->get($tbl_name);
		//echo $this->db->last_query(); die;
		$resp = $query->result_array();
		$results = array();
		foreach( $resp as $val){
			$results[$val['id']] = $val;
		}
		return $results;
		
    }
	/*/*
	* Function : getInteraction
	*/
	public function getInteraction($cond = '', $betweenDate = '')
	{
		if(!empty($cond)) {
			foreach($cond as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		if(!empty($betweenDate))
		{
			$from_date = $betweenDate['from_date'];
			$to_date = $betweenDate['to_date'];
			$this->db2->where("DATE_FORMAT(tbl_create_counselling.createdon,'%m/%d/%Y') >='$from_date'");
			$this->db2->where("DATE_FORMAT(tbl_create_counselling.createdon,'%m/%d/%Y') <='$to_date'");
		}
		
		$this->db2->group_by('mentor_id');
		$query = $this->db->select("mentor_id, count(*)as total")->get('tbl_create_counselling');
		//echo $this->db->last_query(); die;
		$resp = $query->result_array();
		$results = array();
		foreach( $resp as $val){
			$results[$val['mentor_id']] = $val['total'];
		}
		return $results;
		
    }
	
	
	/*
	* Function : getTransferCourseList
	*/
	public function getTransferCourseList($academic_id = '1', $condition='', $where_in = '')
	{
		//$this->db->order_by('tbl_create_counselling.createdon', 'asc');
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		
		$this->db2->where('tbl_teaching_scheme.transfer_status', '1');
		$this->db2->where('tbl_teaching_scheme.status', '1');
		$this->db2->where('tbl_teaching_scheme.academic_id', $academic_id);
		if(!empty($where_in)){
			$this->db2->where_in('tbl_teaching_scheme.program_id', $where_in);
		}
		//$this->db->group_by('`tbl_teaching_scheme`.`course_id`');
		$query = $this->db2->join('tbl_course', 'tbl_course.id = tbl_teaching_scheme.course_id');
		$query = $this->db2->join('tbl_department_master', 'tbl_department_master.id = tbl_course.subject_area');
		$query = $this->db2->join('tbl_school_master', 'tbl_school_master.id = tbl_course.acad_group');
		//$query = $this->db->join('tbl_programme_master', 'tbl_programme_master.id = tbl_teaching_scheme.program_id');
		$this->db->distinct();
		$query = $this->db2->select("tbl_teaching_scheme.id,tbl_teaching_scheme.course_id,tbl_teaching_scheme`.`program_id`,  `school_name`, `school_code`,  `tbl_course`.`catalog_nbr`, `tbl_course`.`course_title`, `tbl_course`.`su_paper_id`,`tbl_course`.`catalog_nbr`, `tbl_course`.`acad_group`, tbl_department_master.name, tbl_department_master.department_code")->get('tbl_teaching_scheme');
		//echo $this->db->last_query();die;
		return $query->result_array();
		
	}
	
	/*
	* Function : getAllFullAssignmentRecords
	*/
	public function getAllFullAssignmentRecords($condition='', $where_in = '')
	{
		//$this->db->order_by('tbl_create_counselling.createdon', 'asc');
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		
		if(!empty($where_in)){
			$this->db2->where_in('tbl_course.id', $where_in);
		}
		$this->db2->where('tbl_department_course_slot_assignment.status', '1');
		$query = $this->db2->join('tbl_course', 'tbl_course.id = tbl_department_course_slot_assignment.course_id');
		$this->db2->distinct();
		$query = $this->db2->select("tbl_department_course_slot_assignment.*,`tbl_course`.`catalog_nbr`, `tbl_course`.`course_title`, `tbl_course`.`catalog_nbr`")->get('tbl_department_course_slot_assignment');
		//echo $this->db->last_query();die;
		return $query->result_array();
		
	}
	/*
	* Function : getAllActiveClassNumberRecords
	*/
	public function getAllActiveClassNumberRecords($condition='')
	{
		//$this->db->order_by('tbl_create_counselling.createdon', 'asc');
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		$this->db2->where('tbl_department_course_slot_assignment.status', '1');
		$query = $this->db2->join('tbl_assign_room_slot_section', 'tbl_assign_room_slot_section.dept_course_id = tbl_department_course_slot_assignment.id', 'INNER');
		$this->db2->distinct();
		$query = $this->db2->select("tbl_assign_room_slot_section.class_number,tbl_department_course_slot_assignment.*")->get('tbl_department_course_slot_assignment');
		//echo $this->db->last_query();die;       
		return $query->result_array();
		
	}
	/*
	* Function : getAssignedSlotsRecords
	*/
	public function getAssignedSlotsRecords($condition='')
	{
		//$this->db->order_by('tbl_create_counselling.createdon', 'asc');
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		
		$this->db2->where('tbl_assign_room_slot_section.status', '1');
		$query = $this->db2->join('tbl_slot_master', 'tbl_slot_master.id = tbl_assign_room_slot_section.slot_id');
		$query = $this->db2->join('tbl_block_room_master', 'tbl_block_room_master.id = tbl_assign_room_slot_section.room_number');
		$this->db2->distinct();
		$query = $this->db2->select("tbl_assign_room_slot_section.*, tbl_slot_master.display_name as slot_name, tbl_block_room_master.room_number as room_no, tbl_block_room_master.title")->get('tbl_assign_room_slot_section');
		//echo $this->db->last_query();die;
		$records = $query->result_array();
		$response = array();
	    foreach($records as $row) {
			$response[$row['dept_course_id']][] = $row;
		}	
		return $response;
	}
	/*
	* Function : getAllSemesterRecords
	*/
	public function getAllSemesterRecords($condition='')
	{
		//$this->db->order_by('tbl_create_counselling.createdon', 'asc');
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		
		$this->db2->where('tbl_credits.status', '1');
		$query = $this->db2->join('tbl_semester', 'tbl_semester.id = tbl_credits.semester_id');
		$this->db2->distinct();
		$query = $this->db2->select("tbl_credits.*,`tbl_semester`.`title`,`tbl_semester`.`psoft_name`")->get('tbl_credits');
		//echo $this->db->last_query();die;
		$records = $query->result_array();
		$response = array();
	    foreach($records as $row) {
			$response[$row['id']] = $row;
		}	
		return $response;
	}
	
	/*
	* Function : getTransferCourseList
	*/
	public function getTransferCourseList_myold($academic_id = '1', $condition='')
	{
		//$this->db->order_by('tbl_create_counselling.createdon', 'asc');
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		
		$this->db2->where('tbl_teaching_scheme.transfer_status', '1');
		$this->db2->where('tbl_teaching_scheme.academic_id', $academic_id);
		
		//$this->db->group_by('`tbl_teaching_scheme`.`course_id`');
		$query = $this->db2->join('tbl_course', 'tbl_course.id = tbl_teaching_scheme.course_id');
		$query = $this->db2->join('tbl_school_master', 'tbl_school_master.id = tbl_course.acad_group');
		//$query = $this->db->join('tbl_department_master', 'tbl_department_master.id = tbl_course.acad_org','left');
		$query = $this->db2->join('tbl_department_master', 'tbl_department_master.id = tbl_course.subject_area');
		//$query = $this->db->join('tbl_programme_master', 'tbl_programme_master.id = tbl_teaching_scheme.program_id');
		$this->db->distinct();
		$query = $this->db2->select("tbl_teaching_scheme.course_id,tbl_teaching_scheme`.`program_id`,  `school_name`, `school_code`,  `tbl_course`.`catalog_nbr`, `tbl_course`.`course_title`, `tbl_course`.`su_paper_id`,`tbl_course`.`catalog_nbr`,`tbl_course`.`acad_group`, tbl_department_master.name, tbl_department_master.department_code")->get('tbl_teaching_scheme');
		//echo $this->db->last_query();die;
		return $query->result_array();
		
	}
	
	/*
	* Function : getProgramWiseCourseList
	*/
	public function getProgramWiseCourseList($condition='')
	{
		
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		$query = $this->db2->join('tbl_course', 'tbl_course.id = tbl_teaching_scheme.course_id');
		$this->db2->distinct();
		$query = $this->db2->select(" tbl_teaching_scheme.course_id, tbl_teaching_scheme`.`program_id`, `tbl_course`.`course_title`, `tbl_course`.`su_paper_id`,`tbl_course`.`catalog_nbr` ")->get('tbl_teaching_scheme');
		//echo $this->db->last_query();die;
		return $query->result_array();
		
	}
	
	/*
	* Function : getMenteeStats
	*/
	public function getMenteeStats($cond = '')
	{
		if(!empty($cond)) {
			foreach($cond as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		$this->db2->group_by('mentor_id');
		$query = $this->db2->select("mentor_id, count(*)as total")->get('tbl_mentee');
		//echo $this->db->last_query(); die;
		$results = array();
		foreach($query->result_array() as $val) {
			$results[$val['mentor_id']] = $val['total'];
		}
		return $results;
		
    }	
	
	/*
	* Function : getMissInteraction
	*/
	public function getMissInteraction($umid='')
	{
		$this->db2->where('status', '1');
		$this->db2->where('is_deleted', '0');
		$today = date('Y-m-d h:i:s');
		$this->db2->where("DATE_FORMAT(next_appointment, '%Y-%m-%d') <= NOW()");
		$this->db2->where_not_in('counselling_status','3');
		$this->db2->where('mentor_id', $umid);
		$this->db2->order_by('createdon', 'asc');
		$query = $this->db2->select("*")->get('tbl_create_counselling');
		//echo $this->db->last_query();die;
		return $query->result_array();
    }
	/*
	* Function : getUpInteraction
	*/
	public function getUpInteraction($umid='')
	{
        $this->db2->where('status', '1');
        $this->db2->where('is_deleted', '0');
        $this->db2->where("DATE_FORMAT(next_appointment, '%Y-%m-%d') >= NOW()");
		$this->db2->where('mentor_id', $umid);
		$this->db2->order_by('next_appointment', 'asc');
		$query = $this->db2->select("*")->get('tbl_create_counselling');
		//echo $this->db->last_query();die;
		return $query->result_array();
    }	
	
	/*
	* Function : getAllNonAcademicComplaintsRecords
	*/
	public function getAllInteraction($condition=null, $createdon='', $betweenDate = '')
	{
        $this->db2->where('tbl_create_counselling.is_deleted', '0');
        if(!empty($createdon)){
		  $this->db2->where("DATE_FORMAT(createdon, '%Y-%m-%d') <= '$createdon'");
		}
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				if($key=='from_date' || $key=='to_date') {
					$this->db2->where("DATE_FORMAT(tbl_create_counselling.createdon,'%m/%d/%Y') >='$val'");
				}  else {
					$this->db2->where($key, $val);
				}
			}
			
		}
		if(!empty($betweenDate))
		{
			$from_date = $betweenDate['from_date'];
			$to_date = $betweenDate['to_date'];
			$this->db2->where("DATE_FORMAT(tbl_create_counselling.createdon,'%m/%d/%Y') >='$from_date'");
			$this->db2->where("DATE_FORMAT(tbl_create_counselling.createdon,'%m/%d/%Y') <='$to_date'");
		}
		$this->db2->order_by('tbl_create_counselling.createdon', 'asc');
		$query = $this->db2->join('tbl_mentee', 'tbl_mentee.id = tbl_create_counselling.mente_id');
		$query = $this->db2->join('tbl_admin', 'tbl_admin.id = tbl_create_counselling.mentor_id');
		$query = $this->db2->select("tbl_create_counselling.id,tbl_create_counselling.issue_type, tbl_create_counselling.point_covered, tbl_create_counselling.critically_level, tbl_create_counselling.next_appointment, tbl_create_counselling.school_id,tbl_create_counselling.department_id,tbl_create_counselling.createdon,tbl_create_counselling.mentor_id, tbl_create_counselling.counselling_status , tbl_mentee.full_name, tbl_admin.first_name,tbl_admin.last_name")->get('tbl_create_counselling');
		//echo $this->db->last_query();die;
		return $query->result_array();
    }
	/*
	* Function : getAllVideo
	*/
	public function getAllVideo($condition=null, $createdon='', $betweenDate = '')
	{
        $this->db2->where('tbl_library_master.is_deleted', '0');
        if(!empty($createdon)){
		  $this->db2->where("DATE_FORMAT(createdon, '%Y-%m-%d') <= '$createdon'");
		}
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				if($key=='from_date' || $key=='to_date') {
					$this->db2->where("DATE_FORMAT(tbl_library_master.createdon,'%m/%d/%Y') >='$val'");
				}  else {
					$this->db2->where($key, $val);
				}
			}
			
		}
		if(!empty($betweenDate))
		{
			$from_date = $betweenDate['from_date'];
			$to_date = $betweenDate['to_date'];
			$this->db2->where("DATE_FORMAT(tbl_library_master.createdon,'%m/%d/%Y') >='$from_date'");
			$this->db2->where("DATE_FORMAT(tbl_library_master.createdon,'%m/%d/%Y') <='$to_date'");
		}
		$this->db2->order_by('tbl_library_master.createdon', 'asc');
		$query = $this->db2->join('tbl_campus', 'tbl_campus.id = tbl_library_master.campus_id');
		$query = $this->db2->join('tbl_admin', 'tbl_admin.id = tbl_library_master.author_id');
		$query = $this->db2->select("tbl_library_master.*, , tbl_campus.campus_name, tbl_admin.first_name,tbl_admin.last_name")->get('tbl_library_master');
		//echo $this->db->last_query();die;
		return $query->result_array();
    }
	
	/*
	* Function : getAllMentorInteraction
	*/
	public function getAllMentorInteraction($condition=null, $createdon='', $betweenDate = '')
	{
        $this->db2->where('tbl_create_counselling.is_deleted', '0');
        if(!empty($createdon)){
		  $this->db2->where("DATE_FORMAT(createdon, '%Y-%m-%d') <= '$createdon'");
		}
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
		if(!empty($betweenDate))
		{
			$from_date = $betweenDate['from_date'];
			$to_date = $betweenDate['to_date'];
			$this->db2->where("DATE_FORMAT(tbl_create_counselling.createdon,'%m/%d/%Y') >='$from_date'");
			$this->db2->where("DATE_FORMAT(tbl_create_counselling.createdon,'%m/%d/%Y') <='$to_date'");
		}
		$this->db2->order_by('tbl_create_counselling.createdon', 'asc');
		$query = $this->db2->join('tbl_mentee', 'tbl_mentee.id = tbl_create_counselling.mente_id');
		$query = $this->db2->join('tbl_admin', 'tbl_admin.id = tbl_create_counselling.mentor_id');
		$query = $this->db2->select("tbl_create_counselling.id,tbl_create_counselling.issue_type, tbl_create_counselling.point_covered, tbl_create_counselling.critically_level, tbl_create_counselling.next_appointment, tbl_create_counselling.school_id,tbl_create_counselling.department_id,tbl_create_counselling.createdon, tbl_create_counselling.status, tbl_mentee.full_name, tbl_admin.first_name,tbl_admin.last_name")->get('tbl_create_counselling');
		//echo $this->db->last_query();die;
		return $query->result_array();
    }
	
	/*
	* Function : validatelogin
	*/
	public function validatelogin($tbl_name, $col = ' * ', $condition= array())
    {
        $time = time();
        $this->db2->select($col);
        $this->db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				if($key=='password'){
					$this->db2->where($key, md5($val));
				} else {
					
					$this->db2->where($key, $val);
				}
			}
			
		}
		$query = $this->db2->get($tbl_name);
		//echo $this->db->last_query(); die;
        return $query->row_array();
    }
	
	/*
	* Function : registrationCount
	*/
	
	public function registrationCount($tbl_name = 'patient_registration', $col='*', $cond= array('is_deleted'=>'0'))
    {
		 $this->db2->select($col);
		 if(count($cond)){
			 foreach($cond as $key=>$val){
				$this->db2->where($key, $val);
			}
		}
		$query = $this->db2->get($tbl_name);
        return $query->num_rows();
	}
	/*
	* Function : questionsTotalCount
	*/
	
	public function questionsTotalCount($tbl_name = 'patient_registration', $col='*', $condition= array('tbl_questionbank.is_deleted'=>'0'), $academic_year_id='2')
    {
			$this->db2->select('count(tbl_questionbank.id) as total');
			$this->db2->join('tbl_teaching_scheme ts', "ts.course_id = $tbl_name.course_id", 'left');
			$this->db2->where('academic_year_id', $academic_year_id);
    		if(!empty($condition))
		    { 
			    foreach($condition as $key=>$val) {  
				    $this->db2->where($key, $val);
			    }			
		    }
            $query = $this->db2->get($tbl_name);
			//echo $this->db->last_query();die;
            $results = $query->row_array();
		
        return $results['total'];
	}
	/*
	* Function : countrylist
	*/
	
	public function countrylist($tbl_name = 'su_country', $col = ' * ')
    {
        $time = time();
        $this->db2->select($col);
        $this->db2->where('is_deleted', '0');
		$this->db2->order_by('country_name', 'asc');
        $query = $this->db2->get($tbl_name);
        return $query->result_array();
    }
	/*
	* Function : occupationlist
	*/
	public function occupationlist($tbl_name = 'sh_occupation', $col = ' * ')
    {
        $time = time();
        $this->db2->select($col);
        $this->db2->where('is_deleted', '0');
		$this->db2->order_by('title', 'asc');
        $query = $this->db2->get($tbl_name);
        return $query->result_array();
    }
	
	/*
	* Function : getAllDoctorsProfile
	*/
	
	public function getAllDoctorsProfile($tbl_name = 'doctors_master', $col = ' * ', $condition)
    {
        
        $this->db2->select($col);
		$this->db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
       
		$this->db2->order_by('dr_name', 'asc');
        $query = $this->db2->get($tbl_name);
        return $query->result_array();
    }
	
	
	
	/*
	* Function : getCommon2Query
	*/
	
	public function getCommon2Query($tbl_name = 'tbl_schools', $col = ' * ', $condition='')
    {
        
        $this->db2->select($col);
		$this->db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
        $query = $this->db2->get($tbl_name);
		echo $this->db2->last_query(); die;
        return $query->result_array();
    }
	/*
	* Function : getCommonQuery
	*/
	
	public function getCommonQuery($tbl_name = 'tbl_school_master', $col = ' * ', $condition='',$order_by='',$condition_like='')
    {
        
        $this->db2->select($col);
		$this->db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
		// Like condition_like
		if(!empty($condition_like))
		{   $k=1;
			foreach($condition_like as $key=>$val) {
				$this->db2->like($key, $val);
				if($k>1) {
					$this->db2->or_like($key, $val);
				}
				$k++;
			}
			
		}
		
		if(!empty($order_by))
		{ 
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}		
		}
        $query = $this->db2->get($tbl_name);
		//echo $this->db->last_query(); //die;
        return $query->result_array();
    }
	
	
	/*
	* Function : getDoctorsAvailableSlots
	*/
	public function getDoctorsAvailableSlots($dcotor_id)
	{
        if($dcotor_id>0)
		{
			$this->db2->select('*');
			$this->db2->where('id', $dcotor_id);
			$this->db2->where('is_deleted', '0');
			$this->db2->where('status', '1');
			$query = $this->db2->get('doctors_master');;
			return $query->row_array();
		}
    }
	
	
	/*
	* Function : opdSlotsMaster
	*/
	public function opdSlotsMaster()
	{
		$this->db2->select('*');
		$this->db2->where('status', '1');
		$this->db2->order_by('is_deleted', '0');
		$query = $this->db2->get('sh_opd_slots');
		$results = array();
		foreach($query->result_array() as $value) {
			$results[$value['id']] = $value['name'];
		}
		return $results;
	}
	
	
	/*
	* Function : checkApplicationDetails
	*/
	
	public function checkApplicationDetails($tbl_name = 'grievance_users', $col = ' * ', $cond)
    {
        $time = time();
        $this->db2->select($col);
		
		foreach($cond as $key=>$val) {
			if($val!='' && $key!=''){
				$this->db2->where($key, $val);
			}
		}
        $this->db2->where('is_deleted', '0');
		$query = $this->db2->get($tbl_name);
		//echo $this->db->last_query(); die;
        return $query->row_array();
    }
	
	
	/*
	* Function : scheduleAppointmentDetails
	*/
	
	public function scheduleAppointmentDetails($tbl_name = 'schedule_appointment', $col = ' * ', $cond)
    {
        $this->db2->select($col);
		foreach($cond as $key=>$val) {
			if($val!='' && $key!=''){
				$this->db2->where($key, $val);
			}
		}
        $this->db2->where('appointment_date>=', date('Y-m-d'));
        $this->db2->where('is_deleted', '0');
        $this->db2->where('slots_available', '1');
        $this->db2->where('status', '1');
		$query = $this->db2->get($tbl_name);
        return $query->row_array();
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
	
	public function updateCommoninfo($tbl_name='', $post, $cond)
    {
		if(!empty($cond)){
			foreach($cond as $field=>$value){
				$this->db->where($field, $value);
			}
		}
        if (!$this->db->update($tbl_name, $post)) {
            log_message('error', print_r($this->db->error(), true));
        }
		//echo $this->db->last_query(); die;
	}
	
	
	public function updateProfile($post)
    {
        $array = array(
            'name' => $post['name'],
            'phone' => $post['phone'],
            'email' => $post['email']
        );
        if (trim($post['pass']) != '') {
            $array['password'] = md5($post['pass']);
        }
        $this->db->where('id', $post['id']);
        $this->db->update('users_public', $array);
    }
	
	/*
	* Function: removeAllItems
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function removeAllItems($table_name="", $user_id, $col_name = 'user_id'){
		
		if($user_id>0){
			
			$this->db->where($col_name, $user_id);
			$this->db->delete($table_name); 
			
			return true;
		}
	}
	
	/*
	* Function: getRegisterationDetails
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	
	public function getSemesterArray($condition, $hodProgramList='')
	{
		$this->db2->select('tbl_credits.id,tbl_semester.title,tbl_semester.description');
		$this->db2->join('tbl_semester', 'tbl_semester.id = tbl_credits.semester_id', 'left');
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		if(!empty($hodProgramList)){
			    //$this->db2->where('a.id=(select max(id) from tbl_comments b where a.indent_id=b.indent_id)');			
			    $this->db2->where_in('tbl_credits.program_id',$hodProgramList);
			}
			
		//$this->db2->limit(1);
		$query = $this->db2->get('tbl_credits');
		//echo $this->db->last_query();die;
		return $query->result_array();
		
	}
	/*
	* Function: encryptpassword
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	
	function encryptpassword($plainText) 
	{ 
		$base64 = base64_encode($plainText);
		$base64url = strtr($base64, '+/=', '-_,');
		return $base64url;
	} 
	
	/*
	* Function: decryptpassword
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	function decryptpassword($plainText) 
	{ 
	
    $base64url = strtr($plainText, '-_,', '+/=');
    $base64 = base64_decode($base64url);
    return $base64;

	} 

	/*
	* Function: getSchoolList
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getSchoolList($tbl_name='tbl_schools', $col = ' * ', $condition=null)
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
		//$this->db2->order_by('id', 'asc');
        $query = $this->db2->get($tbl_name);
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['id']] = $row['school_name'];
		}
        return $results;
    }
	
	/*
	* Function: getDepartmentList
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getDepartmentList($tbl_name='tbl_departments', $col = ' * ', $condition=null)
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
		//$this->db2->order_by('id', 'asc');
        $query = $this->db2->get($tbl_name);
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['id']] = $row['name'];
		}
        return $results;
    }
	
	/*
	* Function: getFullDepartmentList
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getFullDepartmentList($tbl_name='tbl_departments', $col = ' * ', $condition=null)
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
		//$this->db2->order_by('id', 'asc');
        $query = $this->db2->get($tbl_name);
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['id']] = $row;
		}
        return $results;
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
        $this->db2->select($col);
        $this->db2->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
		if(!empty($order_by))
		{ 
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}
			
		}
		//$this->db->order_by('id', 'asc');
        $query = $this->db2->get($tbl_name);
		$results = array();
		//echo $this->db->last_query(); die;
		foreach($query->result_array() as $row) {
			$results[$row['id']] = $row;
		}
        return $results;
	}
	/*
	* Function: getCommonIdListArray
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getCommonIdListArray($tbl_name='tbl_schools', $col = ' * ', $condition=null, $order_by=null)
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
		if(!empty($order_by))
		{ 
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}
			
		}
		//$this->db->order_by('id', 'asc');
        $query = $this->db2->get($tbl_name);
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['programme_id']] = $row;
		}
        return $results;
	}
	/*
	* Function: getCommonEmpListArray
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getCommonEmpListArray($tbl_name='tbl_employee_master', $col = ' * ', $condition=null, $order_by = NULL)
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
		if(!empty($order_by))
		{ 
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}
			
		}
		//$this->db->like('( is_hod="1" OR is_pc="1" )');
		//$this->db->order_by('id', 'asc');
        $query = $this->db2->get($tbl_name);
		//echo $this->db->last_query(); //die;
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['id']] = $row;
		}
        return $results;
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
	public function getCommonEmployeeArray($tbl_name='tbl_employee_master', $col = ' * ', $condition=null, $order_by = NULL)
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
		if(!empty($order_by))
		{ 
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}
			
		}
		$this->db2->like('( is_hod="1" OR is_pc="1" )');
		//$this->db->order_by('id', 'asc');
        $query = $this->db2->get($tbl_name);
		//echo $this->db->last_query(); //die;
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['id']] = $row;
		}
        return $results;
	}
	
	/*
	* Function: getCommonSingleRecord
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
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
	

	public function getAllRecordscount($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL)
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
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}
		}
		if ($limit !== null && $start !== null) {
           $query = $this->db2->get($tbl_name,$limit, $start);
        } else {
			$query = $this->db2->get($tbl_name);
		}
		//echo $this->db->last_query(); die;
		return $query->num_rows();
    }
	
	
	// Function for deletion
	public function deleterecords($tbl_name, $id){
		if($tbl_name!='' && $id>0){
			$sql_query=$this->db->where('id', $id)->delete($tbl_name);
		}
	}
	
	/*
	* Function : remove_mentee
	* Description: Upload xls data to remove mentor from mentee profile
	*/
	public function remove_mentee($menteeArray,$uid='')
	{
		$k=0;
		foreach($menteeArray as $key => $singlerow)
		{
			if($key != 0){
				$system_id= str_replace('"','&#34;',$singlerow['0']);
				$updateArray = array('mentor_id'=>'0','modifiedon'=>date('Y-m-d h:i:s'),'xls_remove_by'=>$uid->id);
				if(!empty($system_id))
				{
					$respe = $this->updateinfo($tbl_name='tbl_mentee', $updateArray, $field='system_id', $system_id);
					$k++; 
				}
			}
		}
		return $k;
	}
	
	/*
	* Function : assign_mentee
	* Description: Upload xls data to Assign mentor
	*/
	public function assign_mentee($menteeArray,$uid=''){
		
		$k=0;
		foreach($menteeArray as $key => $singlerow)
		{
			if($key != 0){
				$system_id= str_replace('"','&#34;',$singlerow['0']);
				$system_id= str_replace("`",'&#39;',$system_id);
				$system_id= str_replace("‘",'&#39;',$system_id);
				$system_id= str_replace("’",'&#39;',$system_id);
				$system_id= str_replace("â€œ",'&#34;',$system_id);
				$system_id= str_replace("â€˜",'&#39;',$system_id);
				$system_id= str_replace("â€™",'&#39;',$system_id);
				
				$employee_id= str_replace('"','&#34;',$singlerow['6']);
				$employee_id= str_replace("`",'&#39;',$employee_id);
				$employee_id= str_replace("‘",'&#39;',$employee_id);
				$employee_id= str_replace("’",'&#39;',$employee_id);
				$employee_id= str_replace("â€œ",'&#34;',$employee_id);
				$employee_id= str_replace("â€˜",'&#39;',$employee_id);
				$employee_id= str_replace("â€™",'&#39;',$employee_id);
			
				// Get Mentor Id from mentor table_name
				$mentor_id = $this->getMentorID($employee_id);
				$menteeDetails = $this->getSingleRecord($tbl_name='tbl_mentee', $col = ' * ', $condition=array('system_id'=>$system_id));
				//$system_id = $this->getMenteeID($system_id);
				$nsystem_id = $menteeDetails['system_id'];
				//print_r($menteeDetails); die;
				if($mentor_id>0 && $nsystem_id!=''){
					$updateArray = array('mentor_id'=>$mentor_id,'modifiedon'=>date('Y-m-d h:i:s'),'xls_update_by'=>$uid->id);
					if(!empty($nsystem_id))
					{
						$respe = $this->updateinfo($tbl_name='tbl_mentee', $updateArray, $field='system_id', $nsystem_id);
						
						// Send Mentor Assignment Email
						$school_id= $menteeDetails['school_id'];
						$department_id= $menteeDetails['department_id'];
						$mentee_id= $menteeDetails['id'];
						$reassignDetails = '';
						$reassignDetails = $this->getSingleRecord($tbl_name='tbl_reassign_mentor', $col = ' * ', $condition=array('school_id'=>$school_id,'department_id'=>$department_id,'mentee_id'=>$mentee_id,'mentor_id'=>$mentor_id));
						
						if(empty($reassignDetails))
						{
							$insertArray = array('mentor_id'=>$mentor_id, 'school_id'=>$school_id, 'department_id'=>$department_id, 'mentee_id'=>$mentee_id,'remarks'=>'Mentor Assign Successfully', 'createdon'=>date('Y-m-d h:i:s'));
							$respe = $this->db->insert('tbl_reassign_mentor',$insertArray);
							
							$mentorDetails = $this->getSingleRecord($tbl_name='tbl_admin', $col = ' * ', $condition=array('id'=>$mentor_id));
							
							// Send Email
							$to_emails = 'aadil.hasan@shardatech.org'; // $menteeDetails['email_id'];
							$mentee_name = $menteeDetails['full_name'];
							$message = $this->load->view('admin/email_template/mentro_assignment_email', $params, true);
							$point_covered = 'We have successfully assigned you a Mentor.';
							$mentor_name = $mentorDetails['first_name'].' '.$mentorDetails['last_name'];
							$mentor_contact = $mentorDetails['contact_number'];
							$mentor_email = $mentorDetails['email_id'];
							$message = str_replace('##STUDENTS##',$mentee_name,$message);
							$message = str_replace('##MESSAGE##',$point_covered,$message);
							$message = str_replace('##MENTOR_NAME##',$mentor_name,$message);
							$message = str_replace('##MENTOR_EMAIL##',$mentor_email,$message);
							$message = str_replace('##MENTOR_CONTACT##',$mentor_contact,$message);
							$subject = 'Mentor Assignment Alert!!';
							$emailResp = send_email_pepipost($to_emails, $subject, $message);
						}
						$k++; 
					}
				}
			}
		}
		return $k;
	}
	
	/*
	* Function : assignMentortoMentee
	* Description: Upload xls data to Assign mentor
	*/
	public function assignMentortoMentee($mentorList,$uid=''){
		$k=1;
		foreach($mentorList as $key => $singlerow)
		{
			//echo "<pre>";print_r($singlerow); 
			// Student Details
			if($key != 0){
			$emp_id= str_replace('"','&#34;',$singlerow['0']);
			$emp_id= str_replace("`",'&#39;',$emp_id);
			$emp_id= str_replace("‘",'&#39;',$emp_id);
			$emp_id= str_replace("’",'&#39;',$emp_id);
			$emp_id= str_replace("â€œ",'&#34;',$emp_id);
			$emp_id= str_replace("â€˜",'&#39;',$emp_id);
			$emp_id= str_replace("â€™",'&#39;',$emp_id);
			$full_name= str_replace('"','&#34;',$singlerow['1']);
			$contact_number= str_replace('"','&#34;',$singlerow['2']);
			$email_id= str_replace('"','&#34;',$singlerow['3']);
			// School Details
			$school_name= str_replace('"','&#34;',$singlerow['4']);
			$school_id = $this->getschoolID($school_name);
			$dept_name= str_replace('"','&#34;',$singlerow['5']);
			$department_id = $this->getdepartmentID($dept_name);
			
			// Create insertArray
			$insertArray = array(
			'employee_id' => $emp_id,
			'userName' => $email_id,
			'password' => substr($contact_number,6,4),
			'first_name' => $full_name,
			'contact_number' =>$contact_number,
			'email_id' => $email_id,
			'school_id' => $school_id,
			'department_id' => $department_id,
			'role_id' => '4',
			'access_id' => '3',
			'added_by' => $uid->id,
			'status' => '1',
			'createdon' => date('Y-m-d H:i:s'),
			'is_deleted' => '0'
			);
			//echo "<pre>";
			//print_r($insertArray); die('DONE');
			if(!empty($insertArray)){
				$respe = $this->db->insert('tbl_admin',$insertArray);
				$k++;
			}
			}
		}
		return $k;
	}
	
	/*
	* Function : getMenteeID
	*/
	public function getMenteeID($system_id)
	{
		$this->db2->select('id,system_id');
		$this->db2->where('is_deleted', '0');
		$this->db2->where('status', '1');
		$this->db2->where('system_id', $system_id);
		$query = $this->db2->get('tbl_mentee');
		$results = array();
		$row = $query->row_array();
		$results = $row['system_id'];
		return $results;
	}
	/*
	* Function : getMentorID
	*/
	public function getMentorID($employee_id)
	{
		$employee_id = $this->getAllDigitsNumber($employee_id);
		$this->db2->select('id');
		$this->db2->where('is_deleted', '0');
		$this->db2->where('status', '1');
		$this->db2->like('employee_id', $employee_id, 'before');
		$query = $this->db2->get('tbl_admin');
		//echo $this->db->last_query(); die;
		$results = array();
		$row = $query->row_array();
		$results = $row['id'];
		return $results;
	}
	
	/*
	* Function : getAllDigitsNumber
	*/
	public function getAllDigitsNumber($number)
	{
		$no_of_digit = 7;
		$length = strlen((string)$number);
		for($i = $length;$i<$no_of_digit;$i++)
		{
			$number = '0'.$number;
		}
		return $number;
   }
	
	/*
	* Function : getschoolID
	*/
	public function getschoolID($name_value)
	{
		$this->db2->select('id,school_name, school_code');
		$this->db2->where('is_deleted', '0');
		$this->db2->where('status', '1');
		$this->db2->where('school_name', $name_value);
		$query = $this->db2->get('tbl_schools');
		$results = array();
		$row = $query->row_array();
		$results = $row['id'];
		return $results;
	}
	
	/*
	* Function : getdepartmentID
	*/
	public function getdepartmentID($name_value)
	{
		$this->db2->select('id,department_name');
		$this->db2->where('is_deleted', '0');
		$this->db2->where('status', '1');
		$this->db2->where('department_name', $name_value);
		$query = $this->db2->get('tbl_departments');
		$results = array();
		$row = $query->row_array();
		$results = $row['id'];
		return $results;
	}
	
		/*
	* Function: getCommonstatsArray 
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getCommonstatsArray($tbl_name='tbl_stats_master', $col = ' * ', $condition=null)
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
		$menteesArray = $query->result_array();
		$results = array();
		foreach($menteesArray as $val) {
			$results[$val['admin_id']] = $val;
		}	
        return $results;
	}
	
	/*
	* Function : getAPIResponse
	* Description :  send request and get response in JSON format
	* Date: 19 Oct 2020
	* Created By: Amit Verma
	*/

	function getAPIResponse($post)
	{
		$url = 'https://slotbooking.sharda.ac.in/mentorapi/getCommonDetails'; 
		if (!empty($url) && !empty($post)) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
			$response = curl_exec($ch);
		}
		//print_r($response); die;
	   return $response;
	}
	
	/*
	* Function : setAPIResponseUAT
	* Description :  send request and get response in JSON format
	* Date: 19 Oct 2020
	* Created By: Amit Verma
	*/

	function setAPIResponseUAT($data)
	{
		$url = 'https://ezone.sharda.ac.in/api/requestApi_uat.php'; 
		// The data you want to send (as an associative array)
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
		curl_setopt($ch, CURLOPT_POST, true); // Use POST method
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json', // Specify JSON content type
			'Authorization: STech12rdm@12*%', // Optional: if your API requires authentication
		]);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Encode data as JSON
		$response = curl_exec($ch);
	
		// Check for errors
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		} else {
			// Decode the response if it's JSON
			$responseData = json_decode($response, true);
		}

		// Close the cURL session
		curl_close($ch);
		return $response;
	}
	/*
	* Function : setAPIResponse
	* Description :  send request and get response in JSON format
	* Date: 19 Oct 2020
	* Created By: Amit Verma
	*/

	function setAPIResponse($data)
	{
		$url = 'https://ezone.sharda.ac.in/api/requestApi.php'; 
		// The data you want to send (as an associative array)
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
		curl_setopt($ch, CURLOPT_POST, true); // Use POST method
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json', // Specify JSON content type
			'Authorization: STech12rdm@12*%', // Optional: if your API requires authentication
		]);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Encode data as JSON
		$response = curl_exec($ch);
	
		// Check for errors
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		} else {
			// Decode the response if it's JSON
			$responseData = json_decode($response, true);
		}

		// Close the cURL session
		curl_close($ch);
		return $response;
	}


        /* Function :saverecords
        * Description :Used to save internship registration  data to Database 
        * Date: 2 june 2020
        * Created By: Divyansh Dixit
        */

    function saverecords($tbl_name='registration', $formArray)
	{
		$this->db->insert($tbl_name,$formArray);
		//echo $this->db->last_query();die;
		return true;
	}

	public function getIndentRecordsForDean($tbl_name = 'tbl_indents', $col = ' * ', $where_role= '' ,$condition='',$order_by='')
    {
        
        $this->db2->select($col);
		$this->db2->where('is_deleted', '0');
		if(!empty($where_role)){
		    $this->db2->where($where_role);
		}
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
		if(!empty($order_by))
		{ 
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}		
		}
        $query = $this->db2->get($tbl_name);
		//echo $this->db->last_query();die;
        return $query->result_array();
    }

	public function getCommonJoinRecords($tbl_name,$col='*',$where_role='',$indentLevel='',$condition='',$order_by='')
	{		
			$this->db2->select('tbl_indents.*,a.indentLevel,a.id as commentId,a.actionUserId,a.actionRoleId');
			//$this->db->select('tbl_indents.*,a.indentLevel');
			$this->db2->join('tbl_comments a', 'tbl_indents.id = a.indent_id', 'left');
			$this->db2->where('a.id=(select max(id) from tbl_comments b where a.indent_id=b.indent_id)');			
			if(!empty($where_role)){
			    $this->db2->where($where_role);
			}
			if(!empty($indentLevel)){
			    //$this->db->where('a.id=(select max(id) from tbl_comments b where a.indent_id=b.indent_id)');			
			    $this->db2->where_in('a.indentLevel',$indentLevel);
			}
			$this->db2->where('tbl_indents.is_deleted', '0');
			if(!empty($condition))
		    { 
			    foreach($condition as $key=>$val) {
				    $this->db2->where($key, $val);
			    }			
		    }
			if(!empty($order_by))
		    { 
			    foreach($order_by as $key=>$val) {
				    $this->db2->order_by($key, $val);
			    }		
		    }
            $query = $this->db2->get($tbl_name);
			//echo $this->db->last_query();die;
            return $query->result_array();
	}

	public function getMaxActionId($col='*',$indent_id='',$condition='')
	{		
			$this->db2->select($col);
			$this->db2->from('tbl_comments a');
			$this->db2->where('a.indent_id', $indent_id);
			$this->db2->where('a.id=(select max(id) from tbl_comments b where a.indent_id=b.indent_id)');			
			$this->db2->where('a.is_deleted', '0');
			if(!empty($condition))
		    { 
			    foreach($condition as $key=>$val) {
				    $this->db2->where($key, $val);
			    }			
		    }
            $query = $this->db2->get();
			//echo $this->db->last_query();die;
            return $query->row_array();
	}
	
	/*
	* Function : getSingleSQLRecord
	* DB Connection : db2
	*
	*/
	public function getSingleSQLRecord($tbl_name, $col = ' * ', $condition=null, $order_by = NULL, $where_like=NULL, $where_like_key = 'id', $or_condition = NULL)
	{
		$otherdb = $this->load->database('db2', TRUE);
        $time = time();
        $otherdb->select($col);
        $otherdb->where('is_deleted', '0');
		if(!empty($where_like)) {
		$otherdb->like($where_like_key, $where_like);
		}
		 
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
		$query = $otherdb->get($tbl_name);
		//echo $otherdb->last_query(); 
        return $query->row();
    }
	
	/*
	* Function : getSQLAllRecords
	* DB Connection : db2
	*
	*
	*/
	public function getSQLAllRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL, $or_condition = NULL)
    {
		$otherdb = $this->load->database('db2', TRUE);
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
		return $query->result();
    }
	
	/*
	* Function : Sqlsaveinfo
	*
	* DB Connection : db2
	*
	*
	*/
	public function Sqlsaveinfo($tbl_name='', $post)
	{
		//$otherdb = $this->load->database('db2', TRUE);
		 $this->db->insert($tbl_name, $post);
		//echo $this->db->last_query(); die;
		return  $this->db->insert_id();
    }
	/*
	* Function : Sqlupdateinfo
	*
	* DB Connection : db2
	*
	*
	*/
	public function Sqlupdateinfo($tbl_name='', $post, $field, $value)
    {
		//$otherdb = $this->load->database('db2', TRUE);
		 $this->db->where($field, $value);
        if (!$this->db->update($tbl_name, $post)) {
            log_message('error', print_r($this->db->error(), true));
        }
		//echo $this->db->last_query(); die;
	}

	/*
	* Function : SqlgetSingleRecord
	* DB Connection : db2
	*/
	public function SqlgetSingleRecord($tbl_name, $col = ' * ', $condition=null)
	{
		$otherdb = $this->load->database('db2', TRUE);
        $time = time();
        $otherdb->select($col);
       // $this->db->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$otherdb->where($key, $val);
			}
			
		}
		$query = $otherdb->get($tbl_name);
		//echo $this->db->last_query(); die;
        return $query->row_array();
    }

	/*
	* Function : getKeyValueRecordsArray
	* DB Connection : db
	*/
	public function getKeyValueRecordsArray($tbl_name='tbl_schools', $col = ' * ', $condition=null)
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
		//$otherdb2->order_by('id', 'asc');
        $query = $this->db2->get($tbl_name);
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['id']] = $row;
		}
        return $results;
	}
	/*
	* Function : SqlgetSingleRecord
	* DB Connection : db2
	*/
	public function SqlgetCommonIdArray($tbl_name='tbl_school_master', $col = ' * ', $condition=null)
    {
		$otherdb = $this->load->database('db2', TRUE);
        $time = time();
        $otherdb->select($col);
        $otherdb->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$otherdb->where($key, $val);
			}
			
		}
		//$otherdb->order_by('id', 'asc');
        $query = $otherdb->get($tbl_name);
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['id']] = $row;
		}
        return $results;
	}

	/*
	* Function : SqlgetCommonQuery
	* DB Connection : db2
	*/
	
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
	* Function : getSQLBucketRecords   
	*/
	public function getSQLBucketRecords($course_reference = NULL, $prog_code=null, $prog_cond=null, $course_area=NULL)
    {
		
		if(!empty($course_area)){
			$sql = "SELECT * FROM `tbl_managebucket_ref` WHERE FIND_IN_SET('".$prog_code."',`program_code`) AND `program_cond`='".$prog_cond."' AND `course_reference`='".$course_reference."' AND `course_area`='".$course_area."' AND is_deleted='0' AND status='1'";
		}
		else{
			$sql = "SELECT * FROM `tbl_managebucket_ref` WHERE FIND_IN_SET('".$prog_code."',`program_code`) AND `program_cond`='".$prog_cond."' AND `course_reference`='".$course_reference."'  AND is_deleted='0' AND status='1'";
		}
		//$sql = "SELECT DISTINCT sd.system_id, sd.rollno,sd.name,sd.email,sg.current_term ,sg.sgpa,sg.cgpa FROM `student_details` sd JOIN student_grade sg ON sd.system_id=sg.system_id where sd.school_code='".$school_code."' AND `department`='".$department."' AND sd.prog_name='".$programm."' ";

		$query = $this->db2->query($sql);
		//echo $this->db->last_query(); die;
		return $query->result_array();   
    }
	
	/*
	* Function: getBlockArray
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	
	public function getBlockArray($id='')
	{
		
		$this->db2->select('tbl_academic_block_master.*, tbl_academic_master.academic_name as campus');
		$this->db2->join('tbl_academic_master', 'tbl_academic_master.id = tbl_academic_block_master.campus_id', 'left');
		if($id>0){
			$this->db2->where('tbl_academic_block_master.id', $id);
			$this->db2->limit(1);
		}
		$query = $this->db2->get('tbl_academic_block_master');
		//echo $this->db->last_query();die;
		return $query->result_array();
		
	}
	
	/*
	* Function: getRoomArray
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	
	public function getRoomArray($id='')
	{
		
		$this->db2->select('tbl_block_room_master.*, tbl_academic_master.academic_name as campus, tbl_academic_block_master.display_name as block');
		$this->db2->join('tbl_academic_master', 'tbl_academic_master.id = tbl_block_room_master.campus_id', 'left');
		$this->db2->join('tbl_academic_block_master', 'tbl_academic_block_master.id = tbl_block_room_master.block_id', 'left');
		if($id>0){
			$this->db2->where('tbl_block_room_master.id', $id);
			$this->db2->limit(1);
		}
		$query = $this->db2->get('tbl_block_room_master');
		//echo $this->db->last_query();die;
		return $query->result_array();
		
	}
	
	/*
	* Function: getSeasonArray
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	
	public function getSeasonArray($id='')
	{
		
		$this->db2->select('tbl_academic_season_master.*, tbl_academic_master.academic_name as campus,tbl_academicyear.academic_year as year, tbl_school_master.school_name');
		$this->db2->join('tbl_academic_master', 'tbl_academic_master.id = tbl_academic_season_master.campus_id', 'left');
		$this->db2->join('tbl_academicyear', 'tbl_academicyear.id = tbl_academic_season_master.year_id', 'left');
		$this->db2->join('tbl_school_master', 'tbl_school_master.id = tbl_academic_season_master.school_id', 'left');
		if($id>0){
			$this->db2->where('tbl_academic_season_master.id', $id);
			$this->db2->limit(1);
		}
		$query = $this->db2->get('tbl_academic_season_master');
		//echo $this->db->last_query();die;
		return $query->result_array();
		
	}
	/*
	* Function: getSeasonArray
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	
	public function getSemesterWiseCredits($condArray= '' )
	{
		return true;
		$this->db2->select('sum(tbl_course.units_acad_prog) as total,tbl_teaching_scheme.semester_id,tbl_teaching_scheme.course_id,tbl_teaching_scheme.program_id, credits');
		$this->db2->join('tbl_course', 'tbl_course.id = tbl_teaching_scheme.course_id');
		$this->db2->join('tbl_credits', 'tbl_credits.id = tbl_teaching_scheme.semester_id');
		if(!empty($condArray)){
			foreach($condArray as $key=>$val) {
				$this->db2->where($key, $val);
			}
			
		}
		$this->db2->where('tbl_course.grading_basis', 'GRD');
		
		$query = $this->db2->get('tbl_teaching_scheme');
		//echo $this->db->last_query(); die;
		$results = array();
		$resp = $query->result_array();
		foreach($resp as $row){
			$results[$row['program_id']][$row['semester_id']] = $row;
		}
		return $results;
		
	}
	public function getCommonIdProgram($tbl_name='tbl_schools', $col = ' * ', $condition=null, $prog_id=null, $order_by=null)
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
		if(!empty($prog_id) && $prog_id!=''){			
		    $this->db2->where_in('id',$prog_id);
		}
		if(!empty($order_by))
		{ 
			foreach($order_by as $key=>$val) {
				$this->db2->order_by($key, $val);
			}
			
		}
		//$this->db->order_by('id', 'asc');
        $query = $this->db2->get($tbl_name);
		$results = array();
		//echo $this->db->last_query(); die;
		foreach($query->result_array() as $row) {
			$results[$row['id']] = $row;
		}
        return $results;
	}
	
	/*
	* Function : getEnrollmentCount
	* DB Connection : db2
	*/
	public function getTotalEnrollmentCount($tbl_name='stu_enrollment')
	{
		$otherdb = $this->load->database('db3', TRUE);
       	$otherdb->where('status', '1');
		$otherdb->where('is_deleted', '0');
		$queryResult = $otherdb->select('count(distinct system_id) as total')->get($tbl_name);
		//echo $this->db->last_query();die;
		return $queryResult->row_array();
    }
	
	/*
	* Function : getTodayEnrollmentCount
	* DB Connection : db2
	*/
	public function getTodayEnrollmentCount($tbl_name='stu_enrollment')
	{
		$otherdb = $this->load->database('db3', TRUE);
       	$otherdb->like('createdon', date('Y-m-d'));
       	$otherdb->where('status', '1');
		$otherdb->where('is_deleted', '0');
		$queryResult = $otherdb->select('count(distinct system_id) as total')->get($tbl_name);
		//echo $this->db->last_query();die;
		return $queryResult->row_array();
    }
	
	/*
	* Function: getOBESemesterArray
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* CreatedBy:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	
	public function getOBESemesterArray($condition, $hodProgramList='')
	{
		$this->db2->select('tbl_obesemestercredits.id,tbl_semester.title,tbl_semester.description');
		$this->db2->join('tbl_semester', 'tbl_semester.id = tbl_obesemestercredits.semester_id', 'left');
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		if(!empty($hodProgramList)){
			    //$this->db2->where('a.id=(select max(id) from tbl_comments b where a.indent_id=b.indent_id)');			
			    $this->db2->where_in('tbl_obesemestercredits.program_id',$hodProgramList);
			}
			
		//$this->db2->limit(1);
		$query = $this->db2->get('tbl_obesemestercredits');
		//echo $this->db->last_query();die;
		return $query->result_array();
		
	}
	
	function getPeoplesoftCourseRADMSections($condArray)
	{
		$tbl_name  = 'PS_S_PRD_STDREG_VW';
		$resultsArray = '';
		
		if (!empty($condArray)) {
			$post = [
				'username' => 'ATTEST',
				'password' => 'TFsgt^I8',
				'num_rows' => '200',		 
				'table' => $tbl_name,
		         'conditions' => serialize($condArray)
			];
			$resultsArray = '';
			$fullArray = json_decode($this->getStudentAPIResponse($post));
			print_r($fullArray); die;
			//$resultsArray = 'ALLOWED'; 
			$resultsArray = $fullArray;
		} else {
			$resultsArray = 'Invalid Request';
		}
		return $resultsArray;
	}
	
	function getPeoplesoftCourseSections($condArray)
	{
		$tbl_name  = 'PS_S_PRD_TT_PI_VW';
		$resultsArray = '';
		
		if (!empty($condArray)) {
			$post = [
				'username' => 'ATTEST',
				'password' => 'TFsgt^I8',
				'num_rows' => '200',		 
				'table' => $tbl_name,
		         'conditions' => serialize($condArray)
			];
			$resultsArray = '';
			$fullArray = json_decode($this->getStudentAPIResponse($post));
			//$resultsArray = 'ALLOWED'; 
			$resultsArray = $fullArray;
		} else {
			$resultsArray = 'Invalid Request';
		}
		return $resultsArray;
	}
	
	/*
	* Function : getAllOBETechCourseRecords
	*/
	public function getAllOBETechCourseRecords($condition='')
	{
		//$this->db->order_by('tbl_create_counselling.createdon', 'asc');
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		
		$this->db2->where(' tbl_obe_teaching_scheme.status', '1');
		$this->db2->where(' tbl_obe_teaching_scheme.is_deleted', '0');
		
		if(!empty($where_in)){
			$this->db2->where_in('tbl_obe_teaching_scheme.id', $where_in);
		}
		$query = $this->db2->join('tbl_course', 'tbl_course.id =  tbl_obe_teaching_scheme.course_id');
		$this->db2->distinct();
		$query = $this->db2->select(" tbl_obe_teaching_scheme.id,tbl_obe_teaching_scheme.school_id,tbl_obe_teaching_scheme.department_id,tbl_obe_teaching_scheme.semester_id, tbl_obe_teaching_scheme.course_id, tbl_obe_teaching_scheme`.`academic_year_id`, tbl_obe_teaching_scheme`.`program_id`,tbl_obe_teaching_scheme`.`program_id`, `tbl_course`.`catalog_nbr`, `tbl_course`.`course_title`, `tbl_course`.`su_paper_id`,`tbl_course`.`catalog_nbr`, `tbl_course`.`acad_group`, `tbl_course`.`lecture`, `tbl_course`.`tutorial`, `tbl_course`.`practical`, `tbl_course`.`units_maximum`")->get('tbl_obe_teaching_scheme');
		//echo $this->db->last_query();die;
		return $query->result_array();
		
	}
	
	/*
	* Function : getAvailableRoomDetails
	*/
	public function getAvailableRoomDetails($condition='', $bookedRooms='')
	{
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		
		$this->db2->where(' tbl_block_room_master.status', '1');
		$this->db2->where(' tbl_block_room_master.is_deleted', '0');
		if(!empty($bookedRooms)){
		$this->db2->where_not_in('id', $bookedRooms);
		}
		$this->db2->distinct();
		$query = $this->db2->select("*")->get('tbl_block_room_master');
		//echo $this->db->last_query();die;
		return $query->result_array();
		
	}
	
	
	function getActivePeoplesoftCourseSections($condArray)
	{
		$tbl_name  = 'PS_S_PRD_CLS_PI_VW';
		$resultsArray = '';
		
		if (!empty($condArray)) {
			$post = [
				'username' => 'ATTEST',
				'password' => 'TFsgt^I8',
				'num_rows' => '200',		 
				'table' => $tbl_name,
		         'conditions' => serialize($condArray)
			];
			$resultsArray = '';
			$fullArray = json_decode($this->getStudentAPIResponse($post));
			//$resultsArray = 'ALLOWED'; 
			$resultsArray = $fullArray;
		} else {
			$resultsArray = 'Invalid Request';
		}
		return $resultsArray;
	}
	
	function getStudentAPIResponse($post)
	{
		$url = 'https://slotbooking.sharda.ac.in/mentorapi/getCommonDetails'; // die;
		if (!empty($url) && !empty($post))
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
			$response = curl_exec($ch);
		}
		//print_r($response); die('TEST');
		return $response;
	} 
	
	/*
	* Function : getCourseRecordsByGroup
	* Desc:
	* Createdon :
	*/
	public function getCourseRecordsByGroup($condition=''){
		
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		$results = array();
		
			$this->db2->where(' tbl_managecoursecos.status', '1');
			$this->db2->where(' tbl_managecoursecos.is_deleted', '0');
			
			if(!empty($catalogNbr)){
				$this->db2->where_in('tbl_course.catalog_nbr', $catalogNbr);
			}
			$this->db2->group_by('tbl_course.id');
			$query = $this->db2->join('tbl_cos', 'tbl_cos.id =  tbl_managecoursecos.cos_id');
			$query = $this->db2->join('tbl_course', 'tbl_course.id =  tbl_managecoursecos.course_id');
			//$this->db2->distinct();
			$query = $this->db2->select(" count(tbl_managecoursecos.id) as total,tbl_cos.title, tbl_course.course_title,tbl_course.catalog_nbr,tbl_managecoursecos.course_id")->get('tbl_managecoursecos');
			#echo $this->db2->last_query();die;
			foreach($query->result_array() as $row) {
				$results[$row['course_id']] = $row['total'];
			}
		
		return $results;
	}
	
	/*
	* Function : getProgrammeRecordsByGroup
	* Desc:
	* Createdon :
	*/
	public function getProgrammeRecordsByGroup($posArray){
		
		$results = array();
		$this->db2->where('status', '1');
		$this->db2->where('is_deleted', '0');

		if(!empty($posArray)){
			foreach($posArray as $key=>$val) {
				$this->db2->where_in($key, $val);
			}
		}
		
		$this->db2->group_by('programme_id');
		$this->db2->distinct();
		$query = $this->db2->select("count(*) as total,programme_id")->get('tbl_popsostatement');
		//echo $this->db->last_query();die;
		foreach($query->result_array() as $row) {
			$results[$row['programme_id']] = $row['total'];
		}

		return $results;
	}
	
	/*
	* Function : getProgrammeMatrixRecordsByGroup
	* Desc:
	* Createdon :
	*/
	public function getProgrammeMatrixRecordsByGroup($programmeList=''){
		
		$results = array();
		$this->db2->where('status', '1');
		$this->db2->where('is_deleted', '0');

		if(!empty($programmeList)){
			$this->db2->where_in('program_id', $programmeList);
		}

		$this->db2->group_by('program_id');
		$this->db2->distinct();
		$query = $this->db2->select("count(*) as total,program_id")->get('tbl_coursecosponpsomapping');
		//echo $this->db->last_query();die;
		foreach($query->result_array() as $row) {
			$results[$row['program_id']] = $row['total'];
		}

		return $results;
	}
	
	public function getCourseBachess($CLASS_NBR) {
		
		$this->db2->where('class_nbr', trim($CLASS_NBR));
		$this->db2->where('tbl_coursebatches.status', '1');
		$query = $this->db2->join('tbl_credits', 'tbl_credits.id =  tbl_coursebatches.semester_id');
		$query = $this->db2->join('tbl_semester', 'tbl_semester.id =  tbl_credits.semester_id');
		$query = $this->db2->join('tbl_course', 'tbl_course.id =  tbl_coursebatches.course_id');
		$query = $this->db2->select("assigned_stu_ids,class_nbr, class_section,tbl_semester.title,tbl_course.catalog_nbr")->get('tbl_coursebatches');
		
		//echo $this->db2->last_query();die;
		$result = $query->row_array();
		$assigned_stu_ids = explode(',',$result['assigned_stu_ids']);
		$CLASS_NBR = $result['class_nbr'];
		$SECTION = $result['class_section'];
		$ACAD_LEVEL_BOT = $result['title'];
		$CATALOG_NBR = $result['catalog_nbr'];
		//	print_r($assigned_stu_ids); die;
		$this->db2->where_in('system_id', $assigned_stu_ids);
		$sql = $this->db2->select("system_id as EMPLID,name as NAME_FORMAL")->get('tbl_student_details');
							
		//echo $this->db2->last_query();die;
		foreach($sql->result_array() as $row) {
			$row['CLASS_NBR'] = $CLASS_NBR;
			$row['SECTION'] = $SECTION;
			$row['ACAD_LEVEL_BOT'] = $ACAD_LEVEL_BOT;
			$row['CATALOG_NBR'] = $CATALOG_NBR;
			$results[] = $row;
		}
		$response = json_encode($results);
		$finalresponse = json_decode($response);
		
		return $finalresponse;
	}
	
	/**
	* Get All topic lists
	*/
	public function getOverAllRecords($conditions='') 
	{
		if(!empty($conditions)) {
			foreach($conditions as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		$this->db2->select("tbl_coursewise_attendance_master.*,tbl_student_details.department_id, tbl_student_details.school_id, tbl_student_details.school_name, tbl_student_details.department,tbl_course.ppsoft_ssr_component");
		$this->db2->from('tbl_coursewise_attendance_master');
		$this->db2->join('tbl_student_details', 'tbl_coursewise_attendance_master.system_id = tbl_student_details.system_id');
		$this->db2->join('tbl_course', 'tbl_course.id = tbl_coursewise_attendance_master.course_id');
		$query = $this->db2->get();
		#echo $this->db2->last_query(); die;
		$resutls = array();
		foreach($query->result_array() as $row){
			$resutls[] = $row;
		}
        return $resutls;
	}
	/**
	* Get All topic lists
	*/
	public function getOthOverAllRecords($conditions='') 
	{
		if(!empty($conditions)) {
			foreach($conditions as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		$this->db2->select("tbl_coursewise_attendance_master_mse.*,tbl_student_details.department_id, tbl_student_details.school_id, tbl_student_details.school_name, tbl_student_details.department,tbl_course.ppsoft_ssr_component");
		$this->db2->from('tbl_coursewise_attendance_master_mse');
		$this->db2->join('tbl_student_details', 'tbl_coursewise_attendance_master_mse.system_id = tbl_student_details.system_id');
		$this->db2->join('tbl_course', 'tbl_course.id = tbl_coursewise_attendance_master_mse.course_id');
		$query = $this->db2->get();
		#echo $this->db2->last_query(); die;
		$resutls = array();
		foreach($query->result_array() as $row){
			$resutls[] = $row;
		}
        return $resutls;
	}
	/**
	* Get All topic lists
	*/
	public function getOthOverSNSRAllRecords($conditions='') 
	{
		if(!empty($conditions)) {
			foreach($conditions as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		$this->db2->select("tbl_coursewise_attendance_master_SNSR.*,tbl_course.ppsoft_ssr_component");
		$this->db2->from('tbl_coursewise_attendance_master_SNSR');
		$this->db2->join('tbl_course', 'tbl_course.catalog_nbr = tbl_coursewise_attendance_master_SNSR.catalog_nbr');
		$query = $this->db2->get();
		##echo $this->db2->last_query(); die;
		$resutls = array();
		foreach($query->result_array() as $row){
			$resutls[] = $row;
		}
        return $resutls;
	}
	
	/**
	* function: getOthOverAllRecords2401
	* Get All topic lists
	*/
	public function getOthOverAllRecords2401($conditions='') 
	{
		if(!empty($conditions)) {
			foreach($conditions as $key=>$val) {
				$this->db2->where($key, $val);
			}
		}
		$this->db2->select("tbl_coursewise_attendance_master_2401.*,tbl_course.ppsoft_ssr_component");
		$this->db2->from('tbl_coursewise_attendance_master_2401');
		$this->db2->join('tbl_course', 'tbl_course.catalog_nbr = tbl_coursewise_attendance_master_2401.catalog_nbr');
		$query = $this->db2->get();
		##echo $this->db2->last_query(); die;
		$resutls = array();
		foreach($query->result_array() as $row){
			$resutls[] = $row;
		}
        return $resutls;
	}
	
	public function getallSchoolSystemIds($school_code, $flag='0')
	{
		$resutls = array();
		if($flag=='1'){
				$sql = "SELECT 
			sm.system_id
			FROM tbl_coursewise_attendance_master sm 
			JOIN tbl_course cr ON cr.id=sm.course_id
			LEFT JOIN tbl_student_details sd ON sm.system_id=sd.system_id
			WHERE sd.school_code='".$school_code."' AND sm.status_flag='0'";
		} else {
				$sql = "SELECT 
			sm.system_id
			FROM tbl_coursewise_attendance_master sm 
			JOIN tbl_course cr ON cr.id=sm.course_id
			LEFT JOIN tbl_student_details sd ON sm.system_id=sd.system_id
			WHERE sd.school_code IN('SUSET') AND sm.status_flag='0'";
		}
		$query = $this->db2->query($sql);
		#echo $this->db2->last_query();die;
		$result = array();
		foreach($query->result_array() as $row) {
			$result[$row['system_id']] = $row;
		}

		return $result;
				
	}
	public function getallCommonnSystemIds($system_id='0', $status_flag = '0')
	{
		$result = array();
		if($system_id>0){
			$sql = "SELECT 
			sm.system_id
			FROM tbl_coursewise_attendance_master sm 
			JOIN tbl_course cr ON cr.id=sm.course_id
			LEFT JOIN tbl_student_details sd ON sm.system_id=sd.system_id
			WHERE sd.system_id ='".$system_id."' AND sm.status_flag='".$status_flag."'";

			$query = $this->db2->query($sql);
			#echo $this->db2->last_query();die;
			
			foreach($query->result_array() as $row) {
				$result[$row['system_id']] = $row;
			}
		}
		return $result;
				
	}
	
	
	public function getallnsSchoolSystemIds()
	{
		$resutls = array();
		
		$sql = "SELECT 
		sm.system_id
		FROM tbl_coursewise_attendance_master sm 
		JOIN tbl_course cr ON cr.id=sm.course_id
		LEFT JOIN tbl_student_details sd ON sm.system_id=sd.system_id
		WHERE sm.status_flag='0'";
		
		$query = $this->db2->query($sql);
		#echo $this->db2->last_query();die;
		$result = array();
		foreach($query->result_array() as $row) {
			$result[$row['system_id']] = $row;
		}

		return $result;
				
	}
	
}