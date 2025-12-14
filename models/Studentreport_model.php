<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Class Studentreport_Model extends CI_Model{
	
    private $db2;
	public function __construct()
	{
		parent::__construct();
		$this->db2 = $this->load->database('db2', TRUE);
	}

	public function assignedcourselistarray($condArray = array())
	{
		 
		$resutls = array();
	
			$sql = "SELECT sp.*, cs.catalog_nbr, cs.course_title,cs.lecture,cs.tutorial, cs.practical, cs.units_maximum, prd.modifiedon
			FROM `PS_S_PRD_CLS_PI_VW` sp 
			JOIN PS_S_PRD_STDREG_VW prd 
			ON sp.class_nbr=prd.class_nbr 
			JOIN tbl_course cs ON cs.id=sp.course_id 
			WHERE cs.status='1'
			";
			if(!empty($condArray)){
				foreach($condArray as $key=>$val) {
					$sql .= " AND $key= $val";	
				}
			}
		
			$query = $this->db2->query($sql);
			#echo $this->db2->last_query();die;
			$resutls = $query->result_array();
			
			return $resutls;
	
	}
	
	public function caselectionnewlist($academic_year_id, $course_id = '', $classnumber = '', $term = '1')
	{
	   $sql = "
			SELECT oe.*, oce.assessment_type, oce.id as key_id
			FROM tbl_obe_course_evaluation oce
			JOIN obe_evaluationmaster oe ON oe.id = oce.course_evaluation_type
			WHERE 
				oe.is_deleted = '0'
				AND oe.status = '1'
				AND oce.academic_year_id = ?
				AND oce.course_id = ?
				AND oce.classnumber = ?
				AND oce.term = ?
		";  

		$params = [$academic_year_id, $course_id, $classnumber, $term];

		$query = $this->db2->query($sql, $params);
		#echo $this->db2->last_query(); die;
		return $query->result_array();
	}

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
		//$this->db2->order_by('id', 'asc');
		$query = $this->db2->get($tbl_name);
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['id']] = $row;
		}
		return $results;
	}

public function getOverallAssessmentData($courseCode = '',$evaluation_id='')
	{
		$this->db2->select('
			ev.id AS evaluation_id,
			qu.title AS question_title,
			co.title AS co_name,
			al.title AS assessment_name,
			em.question_id,
			em.course_code,
			em.co_id,
			em.marks AS student_marks,
			ev.co_marks,
			ev.bloom_level,
			ev.assessment_type,
			ev.teacher_id
		');
		$this->db2->from('evaluation_marks_table em');
		$this->db2->join('tbl_obe_course_evaluation ev', 'ev.id = em.evaluation_id');
		$this->db2->join('tbl_qus qu', 'qu.id = em.question_id');
		$this->db2->join('tbl_cos co', 'co.id = em.co_id');
		$this->db2->join('tbl_assessment_level al', 'al.id = ev.assessment_type');
		if($courseCode){
	    	$this->db2->where('em.course_code', $courseCode);
		}
		if($evaluation_id>0){
	    	$this->db2->where('em.evaluation_id', $evaluation_id);
		}

		$query = $this->db2->get();
		$results = $query->result_array();
		$bloomList = $this->getCommonIdArray('tbl_taxonomy_level','id,title', array('is_deleted'=>'0', 'status'=>'1'));
		foreach ($results as &$row) {
			$coMarksArray = explode(',', $row['co_marks']);
			$bloomLevelsArray = explode(',', $row['bloom_level']);

			$coIndex = (int)$row['co_id'] - 1; // assuming CO ID starts from 1

			$row['co_mark'] = isset($coMarksArray[$coIndex]) ? (int)$coMarksArray[$coIndex] : null;
			$bloom_id = isset($bloomLevelsArray[$coIndex]) ? (int)$bloomLevelsArray[$coIndex] : null;
			if($bloom_id){
				$row['bloom_id'] = $bloomList[$bloom_id]['title'];
			}
			unset($row['co_marks'], $row['bloom_level']); // Optional: clean up
		}

		return $results;
	}
	
	public function getStudentAssessmentData($studentId, $courseCode = '', $evaluation_id='')
	{
		$this->db2->select('
			ev.id AS evaluation_id,
			qu.title AS question_title,
			co.title AS co_name,
			al.title AS assessment_name,
			em.question_id,
			em.course_code,
			em.co_id,
			em.marks AS student_marks,
			ev.co_marks,
			ev.bloom_level,
			ev.assessment_type,
			ev.teacher_id
		');
		$this->db2->from('evaluation_marks_table em');
		$this->db2->join('tbl_obe_course_evaluation ev', 'ev.id = em.evaluation_id');
		$this->db2->join('tbl_qus qu', 'qu.id = em.question_id');
		$this->db2->join('tbl_cos co', 'co.id = em.co_id');
		$this->db2->join('tbl_assessment_level al', 'al.id = ev.assessment_type');

		$this->db2->where('em.student_id', $studentId);
		if($evaluation_id>0){
		$this->db2->where('em.evaluation_id', $evaluation_id);
		}

		if (!empty($courseCode)) {
			$this->db2->where('em.course_code', $courseCode);
		}

		$query = $this->db2->get();
		#echo $this->db2->last_query();die;
		
		$results = $query->result_array();
		#print_r($results); die('test');
		$bloomList = $this->getCommonIdArray('tbl_taxonomy_level','id,title', array('is_deleted'=>'0', 'status'=>'1'));
		
		$prevEvalId = null;
		$k = 0;

		foreach ($results as &$row) {
			$coMarksArray = !empty($row['co_marks']) ? explode(',', $row['co_marks']) : [];
			$bloomLevelsArray = !empty($row['bloom_level']) ? explode(',', $row['bloom_level']) : [];

			// Reset $k if evaluation_id changes
			if ($row['evaluation_id'] !== $prevEvalId) {
				$k = 0;
				$prevEvalId = $row['evaluation_id'];
			}

			// Assign co_mark based on current index
			$row['co_mark'] = isset($coMarksArray[$k]) ? (int)trim($coMarksArray[$k]) : null;

			// Assign bloom_id if it exists
			$bloom_id = isset($bloomLevelsArray[$k]) ? (int)trim($bloomLevelsArray[$k]) : null;
			$row['bloom_id'] = ($bloom_id && isset($bloomList[$bloom_id])) ? $bloomList[$bloom_id]['title'] : null;

			$k++; // Move to next index for next row of same evaluation_id

			// Optional cleanup
			unset($row['co_marks'], $row['bloom_level']);
		}


		#print_r($results); die;
		return $results;
	}



	public function caselectionlist($academic_year_id,$course_id='', $classnumber='', $term='1')
	{
		$resutls = array();
		$dbreport = $this->load->database('dbreport', TRUE);
		$sql = "SELECT oe.*, oce.assessment_type
			FROM tbl_obe_course_evaluation oce
			JOIN obe_evaluationmaster oe ON oe.id = oce.course_evaluation_type
			WHERE 
			oe.is_deleted = '0' 
			AND oce.academic_year_id = '".$academic_year_id."'
			AND oce.course_id = '".$course_id."'
			AND oce.classnumber = '".$classnumber."'
			AND oce.term = '".$term."'
			AND oe.status = '1'";
			$query = $dbreport->query($sql);
			#echo $dbreport->last_query();die;
			$resutls = $query->result_array();
			
			return $resutls;
	
	}
	
	public function getCourses($system_id)
	{
		$otherdb = $this->load->database('readerdb', TRUE);		
		$sqlQ = "SELECT DISTINCT tcb.class_nbr, tcb.course_id, tc.catalog_nbr, tc.course_title, GROUP_CONCAT(DISTINCT ars.course_pi ORDER BY ars.course_pi SEPARATOR ' + ') AS course_pi, GROUP_CONCAT(DISTINCT emp.full_name ORDER BY emp.full_name SEPARATOR ' + ') AS full_name FROM PS_S_PRD_CLS_PI_VW tcb JOIN tbl_course tc ON tc.id = tcb.course_id JOIN tbl_assign_room_slot_section ars ON tcb.class_nbr = ars.class_number JOIN tbl_employee_master emp ON emp.employee_id = ars.course_pi JOIN PS_S_PRD_STDREG_VW prd ON tcb.class_nbr= prd.class_nbr WHERE tcb.status = '1' AND FIND_IN_SET('$system_id', prd.system_id) != 0 GROUP BY tcb.class_nbr, tcb.course_id";
			
		$query = $otherdb->query($sqlQ);
		##echo $otherdb->last_query();die;
		return $query->result_array();
	}
	
	
}
