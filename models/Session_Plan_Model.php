<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Session_Plan_Model extends CI_Model {
	private $dbreport;

    public function __construct()
    {
        parent::__construct();
        $this->dbreport = $this->load->database('dbreport', TRUE);
    }
	public function getUnit() {
		$this->dbreport->select('id, status, is_deleted, title, description');
		$this->dbreport->where('status', '1');
		$this->dbreport->where('is_deleted', '0');
		$query = $this->dbreport->get('tbl_unit');
		return $query->result();
	}

	public function saveinfo($tbl_name='', $post)
	{
		//add data in topic master
		$syllabus_post = array(
			'academic_year_id' 	=> $post['academic_year_id'],
			'syllabus_topic' 	=> $post['topic_name'],
			'topic_description'	=> $post['topic_desc'],
			'proposed_date' 	=> $post['proposed_date'],
			'course_id' 		=> $post['course_id'],
			'programme_id' 		=> $post['programme_id'],
			'created_on' 		=> date('Y-m-d H:i:s', strtotime('now')),
			'added_by' 			=> $this->session->userdata('qb_adminloggedin')->id,
			'status' 			=> '1'
		);
		$this->db->insert('tbl_syllabustopiclist', $syllabus_post);
		$topic_insertId = $this->db->insert_id();

		if($topic_insertId > 0) {
			$session_data = array(
				'last_id' 			=> $post['last_id'],
				'academic_id' 		=> $post['academic_id'],
				'school_id' 		=> $post['school_id'],
				'department_id' 	=> $post['department_id'],
				'academic_year_id'	=> $post['academic_year_id'],
				'program_type' 		=> $post['program_type'],
				'programme_id' 		=> $post['programme_id'],
				'semester_id' 		=> $post['semester_id'],
				'course_type' 		=> $post['course_type'],
				'course_id' 		=> $post['course_id'],
				'unit_id' 			=> $post['unit_id'],
				'topic_name' 		=> $topic_insertId,
				'topic_desc' 		=> $post['topic_desc'],
				'status' 			=> '1',
				'lec_no' 			=> $post['lec_no'],
				'duration' 			=> $post['duration'],
				'proposed_date' 	=> $post['proposed_date'],
				'mode_id' 			=> $post['mode_id'],
				'activities_id' 	=> $post['activities_id'],
				'co_mapping_id' 	=> $post['co_mapping_id'],
				'created_at' 		=> date('Y-m-d H:i:s', strtotime('now')),
				'bloom_level_id' 	=> $post['bloom_level_id'],
				'user_id' 			=> $this->session->userdata('qb_adminloggedin')->id
			);
			$this->db->insert($tbl_name, $session_data);
			$session_insertId = $this->db->insert_id();
			return array('Session Plan Inserted ID' => $session_insertId, 'Topic Master Inserted ID' => $topic_insertId);
		} else {
			return array('error' => 'Failed to insert data into the topic master table');
		}
	}

  	public function getAllSessionPlan($condition='')
	{
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->dbreport->where($key, $val);
			}
		}
		$this->dbreport->where('tbl_sessionplan.status', '1');
		$this->dbreport->select("tbl_sessionplan.*, tbl_sessionplan.id as sessionplan_id, tbl_course.catalog_nbr, tbl_course.course_title, tbl_course.catalog_nbr, tbl_unit.id, tbl_unit.description as unit_description, tbl_lecturemode.id, tbl_lecturemode.description as lecture_mode, tbl_activities.id, tbl_activities.description as activity_desc,  tbl_btl.id, tbl_btl.description as bloom_level, tbl_syllabustopiclist.id, tbl_syllabustopiclist.syllabus_topic");
		$this->dbreport->from('tbl_sessionplan');
		$this->dbreport->join('tbl_course', 'tbl_course.id = tbl_sessionplan.course_id');
		$this->dbreport->join('tbl_unit', 'tbl_unit.id = tbl_sessionplan.unit_id');
		$this->dbreport->join('tbl_lecturemode', 'tbl_lecturemode.id = tbl_sessionplan.mode_id');
		$this->dbreport->join('tbl_activities', 'tbl_activities.id = tbl_sessionplan.activities_id');
		#$this->dbreport->join('tbl_cos', 'tbl_cos.id = tbl_sessionplan.co_mapping_id');
		$this->dbreport->join('tbl_btl', 'tbl_btl.id = tbl_sessionplan.bloom_level_id');
		$this->dbreport->join('tbl_syllabustopiclist', 'tbl_syllabustopiclist.id = tbl_sessionplan.topic_name');
		$this->dbreport->distinct();
		$query = $this->dbreport->get();
		return $query->result_array();
	}

  	public function getSingleRecord($tbl_name, $col = ' * ', $condition=null , $type='')
	{
    	$time = time();
    	$this->dbreport->select($col);
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->dbreport->where($key, $val);
			}
		}
		$query = $this->dbreport->get($tbl_name);
		if($type){
		  return $query->row();
		} else {
			return $query->row_array();
		}
  	}

  	/* Get All session plan records */
  	public function getAllSessionPlanRecords($tbl_name = 'tbl_sessionplan', $col='*', $id=null)
  	{
    	$session_plan_data = $this->getSingleRecord($tbl_name, $col, $condition = array('id'=> $id,'status'=>'1','is_deleted'=>'0'));
    	return $session_plan_data;
  	}
	public function updateinfo($tbl_name='', $post, $field, $value)
  	{
		$this->db->where($field, $value);
    	if (!$this->db->update($tbl_name, $post)) {
      		log_message('error', print_r($this->db->error(), true));
    	}
	}
	public function updateSessionPlan($tbl_name='', $post, $field, $value)
	{
		$loginDetails               = $this->session->userdata('qb_adminloggedin');
		//add topic name in syllabustopic_tbl first
		$topic_data = array(
			'modifiedon'		=> $post['modifiedon'],
			'programme_id'		=> $post['programme_id'],
			'course_id'			=> $post['course_id'],
			'syllabus_topic'	=> $post['topic_name'],
			'topic_description'	=> $post['topic_desc'],
			'academic_year_id'	=> $post['academic_year_id'],
			'proposed_date'		=> $post['proposed_date'],
			'added_by'			=> $loginDetails->id
		);
		$this->db->where('id', $post['topic_id']);
		$this->db->update('tbl_syllabustopiclist', $topic_data);
		
		$data = array(
			'modifiedon'		=> $post['modifiedon'],
			'academic_id'		=> $post['academic_id'],
			'school_id'			=> $post['school_id'],
			'department_id'		=> $post['department_id'],
			'academic_year_id'	=> $post['academic_year_id'],
			'program_type'		=> $post['program_type'],
			'programme_id'		=> $post['programme_id'],
			'semester_id'		=> $post['semester_id'],
			'course_type'		=> $post['course_type'],
			'course_id'			=> $post['course_id'],
			'unit_id'			=> $post['unit_id'],
			'topic_desc'		=> $post['topic_desc'],
			'status'			=> $post['status'],
			'lec_no'			=> $post['lec_no'],
			'duration'			=> $post['duration'],
			'proposed_date'		=> $post['proposed_date'],
			'mode_id'			=> $post['mode_id'],
			'activities_id'		=> $post['activities_id'],
			'co_mapping_id'		=> $post['co_mapping_id'],
			'bloom_level_id'	=> $post['bloom_level_id'],
			'total_hrs'	=> $post['total_hrs'],
			'user_id'			=> $loginDetails->id,
		);
		$this->db->where($field, $value);
		$this->db->update('tbl_sessionplan', $data);
		if ($this->db->affected_rows() > 0) {
			return true; 
		} else {
			log_message('error', 'Failed to update data in tbl_sessionplan');
			return false;
		}
	}
	/**
	 * Get Records
	 */
	public function getSingleRecordId($table, $field, $conditions = array())
	{
		$this->dbreport->select($field);
		$this->dbreport->from($table);
		$this->dbreport->where($conditions);
		$this->dbreport->where('is_deleted', '0');
   		$query = $this->dbreport->get();
    	if ($query->num_rows() > 0) {
        	$row = $query->row();
        	return $row->$field;
    	} else {
        	return null;
    	}
	}

	/**
	 * Get All topic lists
	 */
	public function getAllTopics($lastId , $conditions='') 
	{
		if(!empty($condition)) {
			foreach($condition as $key=>$val) {
				$this->dbreport->where($key, $val);
			}
		}
		$this->dbreport->select("tbl_syllabustopiclist.*, tbl_sessionplan.course_id, tbl_sessionplan.last_id");
		$this->dbreport->from('tbl_syllabustopiclist');
		$this->dbreport->join('tbl_sessionplan', 'tbl_syllabustopiclist.course_id = tbl_sessionplan.course_id');
		$this->dbreport->where('tbl_sessionplan.last_id', $lastId);
		$this->dbreport->distinct();
		$query = $this->dbreport->get();
		// echo $this->db->last_query(); die;
		return $query->result_array();
	}
	
	public function getLists($table, $academicYear, $courseId, $class_nbr=null, $section=null)
	{
		//echo $table.'--'.$academicYear.'--'.$courseId.'--'.$class_nbr.'--'.$section;die;
		$this->dbreport->select("*");
		$this->dbreport->from($table);
		$this->dbreport->where("$table.academic_year_id", $academicYear);
		if($courseId){
			$this->dbreport->where("$table.course_id", $courseId);
		}if($class_nbr){
			$this->dbreport->where("$table.class_nbr", $class_nbr);
		}if($section){
			$this->dbreport->where("$table.class_section_name", $section);
		}
        $this->dbreport->where("$table.status", '1');
        $this->dbreport->where("$table.is_deleted", '0');
    	$this->dbreport->distinct();
		//echo $this->dbreport->last_query(); die('11');
		$query = $this->dbreport->get();
		return $query->result_array();
	}
	public function getListsNursing($table = 'tbl_sessionplan', $academicYear, $courseId, $class_nbr=null, $section=null, $emp=null)
	{
		//echo $table.'--'.$academicYear.'--'.$courseId.'--'.$class_nbr.'--'.$section;die;
		$this->dbreport->select("tbl_syllabustopiclist.*");
		$this->dbreport->from($table);		
		$this->dbreport->join('tbl_syllabustopiclist', "$table.topic_name = tbl_syllabustopiclist.id");
		$this->dbreport->where("$table.academic_year_id", $academicYear);
		if($courseId){
			$this->dbreport->where("$table.course_id", $courseId);
		}if($class_nbr){
			$this->dbreport->where("$table.class_nbrs", $class_nbr);
		}if($section){
			$this->dbreport->where("$table.sections_name", $section);
		}if($emp){
			$this->dbreport->where("$table.user_id", $emp);
		}
        $this->dbreport->where("$table.status", '1');
        $this->dbreport->where("$table.is_deleted", '0');
    	$this->dbreport->distinct();
		//echo $this->dbreport->last_query(); die('11');
		$query = $this->dbreport->get();
		return $query->result_array();
	}
	/**
	 * Map Study Material
	 */
	public function mapStudyMaterial($tbl_name, $data)
	{
		$this->db->insert_batch($tbl_name, $data);
		return $this->db->insert_id();
	}
	/**
	 * Get All Topics Attachements Mapping Records
	*/
	public function getAllTopicsAttachement($conditions = "")
	{
//print_r($conditions);die;
		if (!empty($conditions)) {
			foreach ($conditions as $key => $val) {
				$this->dbreport->where($key, $val);
			}
		}
		$this->dbreport->select("tbl_studymaterial_mapping.*, tbl_academicyear.id as academic_id, tbl_academicyear.academic_year, tbl_syllabustopiclist.id as syllabus_id, tbl_syllabustopiclist.syllabus_topic, proposed_date, tbl_studymaterial.id as material_id, tbl_studymaterial.title, tbl_studymaterial.link");
		$this->dbreport->from('tbl_studymaterial_mapping');
		$this->dbreport->join('tbl_academicyear', 'tbl_studymaterial_mapping.academic_year_id = tbl_academicyear.id');
		$this->dbreport->join('tbl_syllabustopiclist', 'tbl_studymaterial_mapping.topic_id = tbl_syllabustopiclist.id');
		$this->dbreport->join('tbl_studymaterial', 'tbl_studymaterial_mapping.study_material_id = tbl_studymaterial.id');
		$this->dbreport->distinct();
		$query = $this->dbreport->get();
		//echo $this->dbreport->last_query(); //die;
    	return $query->result_array();
	}
	/**
	 * Soft Delete Records
	 */
	public function softDeleteRecords($tbl_name, $id)
	{
    	$this->db->where('id', $id);
    	$data = array('is_deleted' => '1', 'status' => '0');
    	if ($this->db->table_exists($tbl_name)) {
			if (!$this->db->update($tbl_name, $data)) {
				log_message('error', print_r($this->db->error(), true));
				show_error(lang('database_error'));
			}
    	} else {
			// Handle case where $tbl_name is not a valid table name
			log_message('error', 'Invalid table name: ' . $tbl_name);
			show_error('Invalid table name');
    	}	
	}
	
	function getFinalOPEListAarray($academic_id='4') {
		
		$this->dbreport->select('tbl_course.id, catalog_nbr');
		$this->dbreport->from('tbl_managebucket');
		$this->dbreport->join('tbl_managebucket_ref', 'tbl_managebucket.course_reference = tbl_managebucket_ref.course_reference');
		$this->dbreport->join('tbl_course', 'tbl_course.id = tbl_managebucket_ref.course_code');
		$this->dbreport->where('tbl_managebucket.is_deleted', '0');
		$this->dbreport->where('tbl_managebucket.status', '1');
		$this->dbreport->where('tbl_managebucket.academic_id', $academic_id);
		$this->dbreport->where('tbl_managebucket_ref.status', '1');
		$this->dbreport->where('tbl_managebucket_ref.openelec', '1');

		$queryResult = $this->dbreport->get();
		$results = $queryResult->result_array();
		//print_r($results); die;
		
		$responseArray = array();
		foreach($results as $frow){
			$responseArray[$frow['catalog_nbr']]=$frow['id'];
		}
		//echo $this->db->last_query(); die;
		return $responseArray;
		
	  }


	public function getMappedRecords($user_id)
	{
		$this->dbreport->select('tbl_studymaterial.id AS study_material_key, tbl_studymaterial.link, tbl_studymaterial.title, tbl_studymaterial_mapping.*');
		$this->dbreport->from('tbl_studymaterial');
		$this->dbreport->join('tbl_studymaterial_mapping', 'tbl_studymaterial.id = tbl_studymaterial_mapping.study_material_id');
		$this->dbreport->where('tbl_studymaterial.status', '1');
		$this->dbreport->where('tbl_studymaterial.is_deleted', '0');
		$this->dbreport->where('tbl_studymaterial_mapping.added_by', $user_id);
		$query = $this->dbreport->get();
        return $query->result_array();
	}

}
?>