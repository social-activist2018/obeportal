<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dean_Feedback_Report_Model extends CI_Model 
{
  
	private $db2;
	public function __construct()
	{
		parent::__construct();
		$this->db2 = $this->load->database('db2', TRUE);
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
        $query = $this->db2->get($tbl_name);
		//echo $this->db2->last_query(); die;
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['id']] = $row;
		}
        return $results;
	}
	
	/*
	* Function : getAllRecords
	*/
	public function getAllRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL)
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
		//echo $this->db2->last_query(); die;
		return $query->result_array();
    }
	
    // public function getSchools() 
    // {
    //     $this->db->select('id, school_name,school_code');
    //     $this->db->where(['tbl_school_master.status' => '1', 'tbl_school_master.is_deleted' => '0']);



        
    //     return $this->db->get('tbl_school_master')->result_array();
    // }

    public function getSchoolsandDepartment($condArray = NULL){

        $this->db2->select('tbl_department_master.id, tbl_department_master.name,tbl_school_master.school_name,tbl_school_master.school_code,tbl_school_master.id as school_id');
		if(!empty($condArray)){
			foreach($condArray as $key=>$row) {
				$this->db2->where($key, $row);
			}			
		}
        $this->db2->from('tbl_department_master');
        $this->db2->join('tbl_school_master', 'tbl_school_master.id = tbl_department_master.school_id');
        $query = $this->db2->get();
        $department = $query->result_array();
        return $department;

    }


    public function getClassesBySchool($schoolId) {
        // Get classes for a specific school
        $this->db->select('tbl_department_course_slot_assignment.id, tbl_department_course_slot_assignment.programme_id, tbl_department_course_slot_assignment.section_name, tbl_programme_master.program_name, tbl_department_course_slot_assignment.school_id');
        $this->db->from('tbl_department_course_slot_assignment');
        $this->db->join('tbl_programme_master', 'tbl_programme_master.id = tbl_department_course_slot_assignment.programme_id');
        $this->db->where('tbl_department_course_slot_assignment.school_id', $schoolId);
        $this->db->group_by('tbl_department_course_slot_assignment.programme_id');
        return $this->db->get()->result_array();
    }

    public function getActiveFacultiesBySchool($schoolId) {
        // Get active faculties for a specific school
        $this->db->select('id, employee_id, full_name, school_id');
        $this->db->from('tbl_employee_master');
        $this->db->where('tbl_employee_master.status', '1');
        $this->db->where('school_id', $schoolId);
        return $this->db->get()->result_array();
    }
	
	
	/**
	* Get All topic lists
	*/
	public function getCustomAllMyRecordsReports($table_name='tbl_course_feedback', $table_feedback_ref='tbl_course_feedback_ref', $conditions='') 
	{
		$resutls = array();
		$where = '';
		if($conditions) {
			foreach($conditions as $key=>$val){
				$where .= ' AND '.$key.'= "'.$val.'"';
			}
		}
		$sql = 'SELECT 
			sm.school_name,
			dm.name AS department_name,
			cf.faculty_id,
			em.full_name,
			ssm.psoft_name,
			cf.course_id,
			ca.course_title,
			cf.reference,
			ay.academic_year,
			COUNT(DISTINCT cf.system_id) AS total_feedbacks
		FROM 
			'.$table_name.' cf
		JOIN 
			tbl_employee_master em ON em.employee_id = cf.faculty_id
		LEFT JOIN 
			tbl_school_master sm ON sm.id = em.school_id
		LEFT JOIN 
			tbl_department_master dm ON dm.id = em.department_id
		LEFT JOIN 
			tbl_credits cr ON cr.id = cf.semester_id
		LEFT JOIN 
			tbl_semester ssm ON ssm.id = cr.semester_id
		LEFT JOIN 
			tbl_academicyear ay ON ay.id = cf.academic_year_id
		LEFT JOIN 
			tbl_course ca ON ca.catalog_nbr = cf.course_id
        			
		WHERE cr.status="1"
		'.$where.'
		GROUP BY 
			cf.faculty_id, cf.course_id
		ORDER BY 
			cf.faculty_id;';
		#echo $sql; die;
		$query = $this->db2->query($sql);
		$resutls = $query->result_array();
		return $resutls;
	}
	
	function getAVGfeedback ($table_name='', $ref_table_name='', $conditions='', $turn='1'){
		
		$resutls = array();
		$where = '';
		if($conditions) {
			foreach($conditions as $key=>$val){
				$where .= ' AND '.$key.'= "'.$val.'"';
			}
		}
		$sql = 'SELECT 
			AVG(ref.answer) AS avgf,
			(AVG(ref.answer) / 5) * 100 AS percentage,
			cf.course_id,
			faculty_id 
		FROM 
			'.$table_name.' cf
		JOIN 
			'.$ref_table_name.' ref ON cf.reference=ref.reference
		JOIN 
			tbl_employee_master em ON em.employee_id = cf.faculty_id
			WHERE cf.turn="'.$turn.'"
			AND ref.ques_id>0
			'.$where.'
		GROUP BY  
			cf.course_id,faculty_id
		ORDER BY 
			cf.faculty_id;';
		$query = $this->db2->query($sql);
		$resutls = array();
		foreach($query->result_array() as $row) {
			$resutls[$row['faculty_id']][$row['course_id']]=$row;
		}
		return $resutls;
		 
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
	
	function getTotalStudents ($academic_year_id='', $term = ''){
		
		$resutls = array();
		$sql = 'SELECT 
			COUNT(DISTINCT system_id) AS total_student, 
			class_number, 
			course_pi_id, 
			catalog_nbr 
		FROM 
			tbl_student_courses'.$term.'
			
			WHERE academic_year_id="'.$academic_year_id.'"
		GROUP BY  
			catalog_nbr, course_pi_id, class_number';
		
		$query = $this->db2->query($sql);
		$resutls = array();
		foreach($query->result_array() as $row) {
			$resutls[$row['course_pi_id']][$row['catalog_nbr']][]=$row;
		}
		return $resutls;
		 
	}
	function getquestions ($academic_id='', $turn = '1'){
		
		$resutls = array();
		$sql = 'SELECT 
			qb.id, qb.question 
		FROM 
			tbl_coursefeedbackquestion qc 
		JOIN 
			tbl_questionfeedbackrepository qb 
			
		ON qc.questions=qb.id 
		WHERE 
			qc.academic_id="'.$academic_id.'" 
			AND qc.turn="'.$turn.'" 
			AND qb.status="1"';
		
		$query = $this->db2->query($sql);
		$resutls = array();
		foreach($query->result_array() as $row) {
			$resutls[$row['id']]=$row['question'];
		}
		return $resutls;
		 
	}


}    