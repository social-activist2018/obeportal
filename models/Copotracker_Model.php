<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Copotracker_Model extends CI_Model 
{
	private $db2;
	public function __construct()
	{
		parent::__construct();
		$this->db2 = $this->load->database('db2', TRUE);
	}
	
    public function getAllSchools() 
    {
        $this->db2->select('id, school_name');
        return $this->db2->get('tbl_school_master')->result_array();
    }

    public function getAllAcademicYear() 
    {
        $this->db2->select('id, academic_year');
        return $this->db2->get('tbl_academicyear')->result_array();
    }

    public function getEmployeesBySchool($school_id, $department_id,$academic_year) 
    {
        /*if ($academic_year != 4 && !empty($academic_year)) {
            return [];
        }*/
        if ($school_id) {
            $this->db2->where('tbl_employee_master.school_id', $school_id); 
        }

        if ($department_id) {
            $this->db2->where('tbl_employee_master.department_id', $department_id); 
        }

        $this->db2->where(['tbl_employee_master.status' => '1', 'tbl_employee_master.is_deleted' => '0']);
        return $this->db2->get('tbl_employee_master')->result_array();
    }


    public function get_department_by_school($school_id) {

        $this->db2->select('id, name'); 
        $this->db2->from('tbl_department_master');
        $this->db2->where('status', '1'); 
        $this->db2->where('school_id', $school_id); 
        $this->db2->distinct();
    
        $query = $this->db2->get();
        $semester = $query->result_array();
        return $semester;
    }


    public function get_semesters_by_school($school_id) {

        $this->db2->select('tbl_semester.id, tbl_semester.psoft_name'); 
        $this->db2->from('tbl_credits');
        $this->db2->join('tbl_department_course_slot_assignment', 'tbl_department_course_slot_assignment.semester_id = tbl_credits.id');
        $this->db2->join('tbl_semester', 'tbl_semester.id = tbl_credits.semester_id'); 
        $this->db2->where('tbl_department_course_slot_assignment.school_id', $school_id); 
        $this->db2->distinct();
    
        $query = $this->db2->get();
        $semester = $query->result_array();
        return $semester;
    }

    

   // Get all courses assigned to employees
   public function getEmployeeCourses($emp_ids, $academic_year = CURRENT_ACTIVE_ACADEMIC_YEAR) {
                $this->db2->select('course_id, course_pi, section, semester_id, class_number');
                $this->db2->from('tbl_assign_room_slot_section');
                $this->db2->where('status', '1');
                $this->db2->where('is_deleted', '0');
                $this->db2->where_in('course_pi', $emp_ids);
                $this->db2->distinct();
                $query = $this->db2->get();
                return $query->result_array();
    }

    // Get all courses based on the provided course ids
    public function getAllCourses($course_ids) {
        $this->db2->select('*');
        $this->db2->from('tbl_department_course_slot_assignment');
        $this->db2->where('status', '1');
        $this->db2->where('is_deleted', '0');
        $this->db2->where_in('course_id', $course_ids);
        // $this->db2->where('academic_year_id', $academic_year);
        $query = $this->db2->get();
        return $query->result_array();
    }

    // Get attendance for the given employees and courses
    public function getAttendances($emp_ids, $course_ids) {
    
        $this->db2->where('status', '1');
        $this->db2->where('is_deleted', '0');
        $this->db2->select('id, attendance_marked_by, employee_name, percentage, course_id');
        $this->db2->from('tbl_coursewise_attendance_master');
        $this->db2->where_in('attendance_marked_by', $emp_ids);
        $this->db2->where_in('course_id', $course_ids);
        $query = $this->db2->get();
		#echo $this->db2->last_query();  die;
        return $query->result_array();
    }

    // Get semester details for a given semester_id
    public function getSemesterDetails($semester_id) {
        $this->db2->where('status', '1');
        $this->db2->where('id', $semester_id);
        $this->db2->select('id, semester_id');
        $query = $this->db2->get('tbl_credits');
        return $query->result_array();
    }

    // Get all semester information
    public function getAllSemesterInfo($semester_ids) {
        $this->db2->where_in('id', $semester_ids);
        $this->db2->select('id, psoft_name');
        $query = $this->db2->get('tbl_semester');
        return $query->result_array();
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
	
	function getAllstudentsRecords ($term='', $academic_year_id='', $conditions=''){
		
		$resutls = array();
		$where = '';
		if($conditions) {
			foreach($conditions as $key=>$val){
				$where .= ' AND '.$key.'= "'.$val.'"';
			}
		}
		
		$table_name = 'tbl_student_courses';
		if($term!=ACTIVE_ADMIT_TERM) {
			$table_name = 'tbl_student_courses_'.$term;
		}
		
		$sql = 'SELECT 
			em.full_name,
			em.employee_id,
			cr.catalog_nbr,
			cr.course_title,
			cr.id as course_id,
			cr.lecture,
			cr.tutorial,
			cr.practical,
			cs.class_section,
			cs.class_number,
			cs.class_semester,
			COUNT(DISTINCT system_id) AS total_student
		FROM 
			'.$table_name.' cs
		JOIN 
			tbl_employee_master em 
			ON cs.course_pi_id = em.employee_id
		JOIN 
			tbl_course cr 
			ON cr.catalog_nbr = cs.catalog_nbr
		WHERE 
			cs.current_term = "'.$term.'" 
			AND cs.academic_year_id = "'.$academic_year_id.'" 
			'.$where.'
		GROUP BY 
			cs.course_pi_id, cs.catalog_nbr,cs.class_number
		ORDER BY 
			em.employee_id;';
		#echo $sql; die;		
		$query = $this->db2->query($sql);
		$resutls = array();
		foreach($query->result_array() as $row) {
			$resutls[$row['employee_id']][]=$row;
		}
		return $resutls;
		 
	}
	
	function getAllFacultyRecords ($term='', $academic_year_id='', $conditions=''){
		
		$resutls = array();
		$where = '';
		if($conditions) {
			foreach($conditions as $key=>$val){
				$where .= ' AND '.$key.'= "'.$val.'"';
			}
		}
		$table_name = 'tbl_student_courses';
		if($term!=ACTIVE_ADMIT_TERM) {
			$table_name = 'tbl_student_courses_'.$term;
		}
		$sql = 'SELECT 
			em.full_name,
			em.employee_id
			
		FROM 
			'.$table_name.' cs
		JOIN 
			tbl_employee_master em 
			ON cs.course_pi_id = em.employee_id
		
		WHERE 
			cs.current_term = "'.$term.'" 
			AND cs.academic_year_id = "'.$academic_year_id.'" 
			'.$where.'
		GROUP BY 
			cs.course_pi_id
		ORDER BY 
			em.employee_id;';
		#echo $sql; die;	
		$query = $this->db2->query($sql);
		$resutls = array();
		foreach($query->result_array() as $row) {
			$resutls[]=$row;
		}
		return $resutls;
		 
	}
		
	
	/*
	* Function : getCAsetupRecords
	*
	*/
	function getCAsetupRecords ($ACTIVE_ADMIT_TERM, $department_id='', $employee_id=''){
		
		$resutls = array();
		$sql .= 'SELECT 
				tbl_obe_course_evaluation.id,
				course_evaluation_type,
				lecture_type,
				semester_id,
				course_id,
				evaluation_name,
				em.employee_id,
				MIN(academic_year_id) AS academic_year_id,
				classnumber,
				term
			FROM 
			  tbl_obe_course_evaluation
			  
			LEFT JOIN tbl_employee_master AS em ON em.id = tbl_obe_course_evaluation.user_id 
			WHERE 
			tbl_obe_course_evaluation.status = "1" 
			AND tbl_obe_course_evaluation.is_deleted = "0" 
			AND term = "'.$ACTIVE_ADMIT_TERM.'"';
			if($employee_id){	
				$sql .= ' AND em.employee_id="'.$employee_id.'"';
			}
			if($department_id){	
				$sql .= ' AND tbl_obe_course_evaluation.department_id="'.$department_id.'"';
			}
			$sql .= ' GROUP BY  semester_id, course_id, classnumber,evaluation_name';
			$sql .= ' ORDER BY lecture_type ASC ';	
		
		$query = $this->db2->query($sql);
		foreach($query->result_array() as $row) {
			$resutls[$row['employee_id']][$row['course_id']][$row['classnumber']][]=$row;
		}
		return $resutls;
		 
	}
	
	/*
	* Function : getMSEsetupRecords
	*
	*/
	function getMSEsetupRecords ($ACTIVE_ADMIT_TERM, $department_id='', $employee_id=''){
		
		$resutls = array();
		$sql .= '
			SELECT 
			  Q_name,
			  Q_obtain_marks, 
			  em.employee_id, 
			  CLASS_NBR, 
			  ADMIT_TERM, 
			  catalog_nbr
			FROM 
			  mse_max_marks 
			INNER JOIN tbl_employee_master AS em 
			  ON em.employee_id = mse_max_marks.INSTRUCTOR_ID 
			WHERE 
			  mse_max_marks.created IS NOT NULL
			   AND ADMIT_TERM = "'.$ACTIVE_ADMIT_TERM.'"';
		
			if($employee_id){	
				$sql .= ' AND em.employee_id="'.$employee_id.'"';
			}
			if($department_id){	
				$sql .= ' AND em.department_id="'.$department_id.'"';
			}
		#echo $sql; die;	
		$query = $this->db2->query($sql);
		foreach($query->result_array() as $row) {
			$resutls[$row['employee_id']][$row['catalog_nbr']][$row['CLASS_NBR']][]=$row;
		}
		return $resutls;
		 
	}
	/*
	* Function : getESEsetupRecords
	*
	*/
	function getESEsetupRecords ($ACTIVE_ADMIT_TERM, $department_id='', $employee_id=''){
		
		$resutls = array();
		$sql .= '
			SELECT 
				esm.obtained_marks,
				em.employee_id,
				esm.catalog_nbr,
				esm.term,
				esm.class
			FROM 
				end_semester_marks AS esm
			INNER JOIN 
				tbl_employee_master AS em 
				ON em.employee_id = esm.teacher_id
			WHERE 
				esm.created_date IS NOT NULL
			 AND term = "'.$ACTIVE_ADMIT_TERM.'"';
			if($employee_id){	
				$sql .= ' AND em.employee_id="'.$employee_id.'"';
			}
			if($department_id){	
				$sql .= ' AND em.department_id="'.$department_id.'"';
			}
		#echo $sql; die;	
		$query = $this->db2->query($sql);
		foreach($query->result_array() as $row) {
			$resutls[$row['employee_id']][$row['catalog_nbr']][$row['class']][]=$row;
		}
		return $resutls;
		 
	}
	/*
	* Function : getCAMarksRecords
	*
	*/
	function getCAMarksRecords ($ACTIVE_ADMIT_TERM, $department_id='', $employee_id=''){
		
		$resutls = array();
		$sql .= '
			SELECT 
				evaluation_type,
				evaluation_id,
				marks,
				course_code,
				created_at,
				LPAD(teacher_id, 7, "0") AS teacher_id,
				classnumber,
				term
			FROM 
				evaluation_marks_table
				
			INNER JOIN 
				tbl_employee_master AS em 
				ON em.employee_id = teacher_id
			WHERE 
				evaluation_marks_table.status = "1"
				AND evaluation_marks_table.is_deleted = "0"
				 AND term = "'.$ACTIVE_ADMIT_TERM.'"';
			if($employee_id){	
				$sql .= ' AND LPAD(teacher_id, 7, "0") = "'.$employee_id.'"';
			}
			if($department_id){	
				$sql .= ' AND em.department_id="'.$department_id.'"';
			}
			$sql .= ' GROUP BY 
				evaluation_type,
				evaluation_id,
				course_code,
				classnumber,
				teacher_id
				order by  marks desc;
				';
				
		$query = $this->db2->query($sql);
		foreach($query->result_array() as $row) {
			$resutls[$row['evaluation_id']][]=$row;
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
	
}
