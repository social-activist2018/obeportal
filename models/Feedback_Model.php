<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Class Feedback_Model extends CI_Model{
	private $db2;
	public function __construct()
	{
		parent::__construct();
		$this->db2 = $this->load->database('db2', TRUE);
	}

	public function getAllReportsfaculty($searchCond){
	
		

        $program_id = $searchCond['program_id'];
		$acadmic_id = $searchCond['academic_year_id'];
		$department = $this->db->select('department_id') // Selecting department_id
		->from('tbl_programme_master') // Fetching data from table
		->where('id', $searchCond['program_id']) // Applying filter
		->get();
	
	$department_data = $department->result_array(); // Fetch result as array
	$department_id = $department_data['0']['department_id'];
	$query = $this->db->select('employee_id') // Selecting employee_id
					  ->from('tbl_employee_master') // Fetching data from table
					  ->where('job_family', 'ACAD')
					  ->where('department_id', $department_id)
					  ->where('is_deleted', '0')
					  ->where('status', '1')
					  ->get();
	
	$emp_id = $query->result_array(); // Fetching data as an array
	
	// Extract all employee IDs into a single array
	$employee_ids = array_column($emp_id, 'employee_id');
	 //print_r($employee_ids); die();
		$query = $this->db->distinct()
		  ->select('c.catalog_nbr') // Selecting course_id from first table and course_name from second table
		->from('tbl_department_course_slot_assignment dcsa') // Using alias for readability
		->join('tbl_course c', 'dcsa.course_id  = c.id', 'left') // Joining with tbl_courses on course_id
		->where('dcsa.programme_id', $searchCond['program_id'])
		->where('dcsa.academic_year_id', $searchCond['academic_year_id'])
		->where('dcsa.is_deleted', '0')
		->where('dcsa.status', '1')
		->get();

		$courseid = $query->result_array(); 

		$catalogNbrs = array_column($courseid, 'catalog_nbr');


		$query1 = $this->db->select('refid.ques, refid.ans, qrpstry.id as questionid')
		->from('tbl_employeefeedback rmpfd')
		->join('tbl_employeefeedback_ref refid', 'rmpfd.id = refid.ef_id')
		->join('tbl_questionfeedbackrepository qrpstry', 'refid.ques = qrpstry.id')
		->where('rmpfd.academic_year_id', $acadmic_id)
		->where('rmpfd.status', '1')
		->where('rmpfd.is_deleted', '0');
	
	if (!empty($employee_ids)) {
		$query1->where_in('rmpfd.emp_code', $employee_ids);
	}
	
	$query1 = $query1->order_by('refid.ques', 'ASC')->get();
	$results2 = $query1->result_array();
	

		$query4 = $this->db->select('count(DISTINCT rmpfd.emp_code) as total_emp')
		->from('tbl_employeefeedback rmpfd')
		->join('tbl_employeefeedback_ref refid', 'rmpfd.id = refid.ef_id')
		->join('tbl_questionfeedbackrepository qrpstry', 'refid.ques = qrpstry.id')
		->where('rmpfd.status', '1')
		->where('rmpfd.is_deleted', '0')
		->where('rmpfd.academic_year_id', $acadmic_id);
	
	if (!empty($employee_ids)) {
		$query4->where_in('rmpfd.emp_code', $employee_ids);
	}
	
	$query4 = $query4->order_by('refid.ques', 'ASC')->get();
	$results4 = $query4->result_array();
	

		$questionWiseData = [];

		foreach ($results2 as $response) {
		$ques = $response['ques'];
		$ans = $response['ans'];

		// Ensure only numeric answers are processed
		if (!is_numeric($ans)) {
		continue;
		}

		// Initialize question entry if not exists
		if (!isset($questionWiseData[$ques])) {
		$questionWiseData[$ques] = [
		'total' => 0,
		'answers' => []
		];
		}

		// Count total responses for the question
		$questionWiseData[$ques]['total']++;

		// Count occurrences of each numeric answer per question
		if (!isset($questionWiseData[$ques]['answers'][$ans])) {
		$questionWiseData[$ques]['answers'][$ans] = 0;
		}
		$questionWiseData[$ques]['answers'][$ans]++;
		}

		// Calculate percentages
		$questionWisePercentage = [];

		foreach ($questionWiseData as $ques => $data) {
		$totalResponses = $data['total'];
		$percentages = [];

		foreach ($data['answers'] as $ans => $count) {
		$percentages[$ans] = round(($count / $totalResponses) * 100, 2); // Percentage calculation
		}

		$questionWisePercentage[$ques] = [
		'total_responses' => $totalResponses,
		'answer_distribution' => $percentages
		];
		}




		return [$questionWisePercentage, $results4];
	}
	
	

	public function get_record_almuni($searchCond) {
		// Fetch program code based on program_id
		$program_id = $searchCond["program_id"]; 
		$query2 = $this->db->select('program_code')
			->from('tbl_programme_master')
			->where('id', $program_id)
			->get();
	
		$program_code = $query2->result();
		$programcode = $program_code[0]->program_code;
	
		// Fetch survey responses
		$query1 = $this->db->select('*')
			->from('overall_survey_answers')
			->where('survey_id', '5')
			->where('programme',$programcode)
			->where('is_deleted', '0')
			->get();
	
		$data = $query1->result_array();
	
		// List of target answers
		$target_answers = [
			'NnEAz', 'hFnXE', 'CDQZE', 'akqBz', 'iTlhO',
			'BgIPj', 'qJhTY', 'iReaX', 'dEQUH', 'TkiYB',
			'hXGBs', 'sDibS', 'UtWCM', 'pGtJz', 'iZHDt',
			'fyxjD', 'VKIQp', 'ZJNvu', 'GUtuW', 'ExpNz',
			'nXQyI', 'qIUEe', 'ftxJY', 'yTgfl', 'VGvFb',
			'jnDgu', 'iFpoR', 'gjtPe', 'yirfg', 'yjRmG',
			'NyYVi', 'ZCSme', 'mNgOk', 'nVbgH', 'RHdni',
			'JFQuK', 'unRwY', 'zfYCM', 'NHarW', 'NdnCr'
		];
	
		// Initialize array to store question-wise data
		$question_wise_data = [];
	
		// Process each entry
		foreach ($data as $entry) {
			$question_id = $entry['question_id'];
			$system_id = $entry['system_id']; // Unique user identifier
			$answer = $entry['answer'];
	
			// Ensure question_id exists in array
			if (!isset($question_wise_data[$question_id])) {
				$question_wise_data[$question_id] = [
					'unique_users' => [], // Track unique system IDs
					'answers' => array_fill_keys($target_answers, 0),
					'total_unique_users' => 0 // Store unique system_id count
				];
			}
	
			// Track unique system_id (prevent duplicate counting)
			$question_wise_data[$question_id]['unique_users'][$system_id] = true;
	
			// If the answer is in the target list, increase its count
			if (in_array($answer, $target_answers)) {
				$question_wise_data[$question_id]['answers'][$answer]++;
			}
		}
	
		// Calculate percentage for each answer per question based on unique users
		$result = [];
		foreach ($question_wise_data as $question_id => $data) {
			$total_unique_users = count($data['unique_users']); // Unique system_id count
			$percentages = [];
	
			foreach ($data['answers'] as $answer => $count) {
				$percentage = $total_unique_users > 0 ? round(($count / $total_unique_users) * 100, 2) : 0;
				if ($percentage > 0) { // Remove answers with 0%
					$percentages[$answer] = $percentage;
				}
			}
	
			// Store results with question_id, total unique system_ids, and percentage data
			if (!empty($percentages)) { // Only include questions with valid data
				$result[$question_id] = [
					'total_unique_users' => $total_unique_users,
					'percentages' => $percentages
				];
			}
		}
	
		// Debugging Output (Remove in Production)
		// echo '<pre>';
		// print_r($result);
		// exit;
	
		return $result;
	}
	public function get_record_parents($searchCond) {
		// Fetch program code based on program_id
		$query3 = $this->db->select('department_id')
		->from('tbl_department_course_slot_assignment')
		->where('programme_id',$program_id)
		->where('academic_year_id',$searchCond["academic_year_id"])//teaching_scheme_type
		->where('teaching_scheme_type',$searchCond["teaching_scheme_type"])
		->get();
		$department_id = $query3->result();
		$department_id = $department_id[0]->department_id;
		$program_id = $searchCond["program_id"]; 
		$query2 = $this->db->select('program_code')
			->from('tbl_programme_master')
			->where('id', $program_id)
			->get();
	
		$program_code = $query2->result();
		$programcode = $program_code[0]->program_code;
	
		// Fetch survey responses
		$query1 = $this->db->select('*')
			->from('survey_answers')
			->where('survey_id', '6')
			->where('department', $department_id)
			->where('is_deleted', '0')
			->get();
	
		$data = $query1->result_array();
		$target_answers = []; // Store unique answers

		foreach ($data as $entry) {
			if ($entry['question_id'] == 55) { 
				continue; // Skip entries where question_id is 55
			}
			
			$answer = $entry['answer']; // Extract answer
			$target_answers[$answer] = true; // Store as key to prevent duplicates
		}
		
		// Convert keys back to an indexed array
		$target_answers = array_keys($target_answers);
		

		$target_answers = [
			"Xtvlj", "Cekwy", "JbrOc", "sQPxJ", "QmXNL",
			"JKBVm", "HmsLT", "rWUyE", "dUIHY", "MVmlC",
			"fEvHO", "bImrR", "fwMCB", "CgLbx", "oPxub",
			"nHSMJ", "ewGrF", "cnoeL", "mQIPn", "zcyAU",
			"TxKUV", "Kcdxu", "cFGiR", "uFhVO", "xukmP",
			"rajiq", "nStTA", "pLWzf", "maVTs", "CfdYS",
			"ABhIs", "KcJBH", "zAwEk", "RankH", "BjFAV",
			"TKEhF", "unihk", "RcsNm", "BjpeM", "hTmSx"
		];
			
		// Initialize array to store question-wise data
		$question_wise_data = [];
	
		// Process each entry
		foreach ($data as $entry) {
			$question_id = $entry['question_id'];
			$system_id = $entry['system_id']; // Unique user identifier
			$answer = $entry['answer'];
	
			// Ensure question_id exists in array
			if (!isset($question_wise_data[$question_id])) {
				$question_wise_data[$question_id] = [
					'unique_users' => [], // Track unique system IDs
					'answers' => array_fill_keys($target_answers, 0),
					'total_unique_users' => 0 // Store unique system_id count
				];
			}
	
			// Track unique system_id (prevent duplicate counting)
			$question_wise_data[$question_id]['unique_users'][$system_id] = true;
	
			// If the answer is in the target list, increase its count
			if (in_array($answer, $target_answers)) {
				$question_wise_data[$question_id]['answers'][$answer]++;
			}
		}
	
		// Calculate percentage for each answer per question based on unique users
		$result = [];
		foreach ($question_wise_data as $question_id => $data) {
			$total_unique_users = count($data['unique_users']); // Unique system_id count
			$percentages = [];
	
			foreach ($data['answers'] as $answer => $count) {
				$percentage = $total_unique_users > 0 ? round(($count / $total_unique_users) * 100, 2) : 0;
				if ($percentage > 0) { // Remove answers with 0%
					$percentages[$answer] = $percentage;
				}
			}
	
			// Store results with question_id, total unique system_ids, and percentage data
			if (!empty($percentages)) { // Only include questions with valid data
				$result[$question_id] = [
					'total_unique_users' => $total_unique_users,
					'percentages' => $percentages
				];
			}
		}
	
		return $result;
	}

// Academic Peers 
	public function get_record_academicpeers($searchCond){
		$program_id = $searchCond["program_id"]; 
		$query2 = $this->db->select('program_code')
			->from('tbl_programme_master')
			->where('id', $program_id)
			->get();
	
		$program_code = $query2->result();
		$programcode = $program_code[0]->program_code;
	
		// Fetch survey responses
		$query1 = $this->db->select('*')
			->from('overall_survey_answers')
			->where('survey_id', '7')
			->where('programme',$programcode)
			->where('is_deleted', '0')
			->where('status','1')
			->get();
	
		$data = $query1->result_array();
	//echo '<pre>'; print_r($data); die(); 
		// List of target answers
		$target_answers = [
			'wfPvd', 'BKtDI', 'jNhXL', 'wkMiH', 'zTnVC',
			'mfGwq', 'OTdaN', 'EmCdT', 'XevqO', 'Ztgad',
			'mpeWA', 'Zsdev', 'mvqQg', 'ukmbX', 'HJpnD',
			'wbONf', 'iVbrH', 'NDOZj', 'FMVsE', 'yXRYE',
			'WzlmB', 'vRLAs', 'OZovL', 'faQjP', 'ezLAh',
			'cFfbU', 'JpyEn', 'RfTtH', 'SaRkJ', 'TjGtb'
			
		];
	
		// Initialize array to store question-wise data
		$question_wise_data = [];
	
		// Process each entry
		foreach ($data as $entry) {
			$question_id = $entry['question_id'];
			$system_id = $entry['id']; // Unique user identifier
			$answer = $entry['answer'];
	
			// Ensure question_id exists in array
			if (!isset($question_wise_data[$question_id])) {
				$question_wise_data[$question_id] = [
					'unique_users' => [], // Track unique system IDs
					'answers' => array_fill_keys($target_answers, 0),
					'total_unique_users' => 0 // Store unique system_id count
				];
			}
	
			// Track unique system_id (prevent duplicate counting)
			$question_wise_data[$question_id]['unique_users'][$system_id] = true;
	
			// If the answer is in the target list, increase its count
			if (in_array($answer, $target_answers)) {
				$question_wise_data[$question_id]['answers'][$answer]++;
			}
		}
	
		// Calculate percentage for each answer per question based on unique users
		$result = [];
		foreach ($question_wise_data as $question_id => $data) {
			$total_unique_users = count($data['unique_users']); // Unique system_id count
			$percentages = [];
	
			foreach ($data['answers'] as $answer => $count) {
				$percentage = $total_unique_users > 0 ? round(($count / $total_unique_users) * 100, 2) : 0;
				if ($percentage > 0) { // Remove answers with 0%
					$percentages[$answer] = $percentage;
				}
			}
	
			// Store results with question_id, total unique system_ids, and percentage data
			if (!empty($percentages)) { // Only include questions with valid data
				$result[$question_id] = [
					'total_unique_users' => $total_unique_users,
					'percentages' => $percentages
				];
			}
		}
	
		// Debugging Output (Remove in Production)
		// echo '<pre>';
		// print_r($result);
		// exit;
	
		return $result;
	}



	public function get_record_empoloyee($searchCond){
		$program_id = $searchCond["program_id"]; 
		$query2 = $this->db->select('program_code')
			->from('tbl_programme_master')
			->where('id', $program_id)
			->get();
	
		$program_code = $query2->result();
		$programcode = $program_code[0]->program_code;
	
		// Fetch survey responses
		$query1 = $this->db->select('*')
			->from('overall_survey_answers')
			->where('survey_id', '3')
			->where('programme',$programcode)
			->where('is_deleted', '0')
			->where('status','1')
			->get();

	
		$data = $query1->result_array();
		// 			echo '<pre>';
		// print_r($data);
		//exit;
		$query3 = $this->db->select('department_id')
		         ->from('tbl_department_course_slot_assignment')
				 ->where('programme_id',$program_id)
				 ->where('academic_year_id',$searchCond["academic_year_id"])//teaching_scheme_type
				 ->where('teaching_scheme_type',$searchCond["teaching_scheme_type"])
				 ->get();
		$department_id = $query3->result();
		$department_id = $department_id[0]->department_id;
	

	//echo $department_id;  die(); 
		// List of target answers
		$target_answers = [
			'fbGOR', 'MApEo', 'jkPBN', 'JeHcg', 'ydRtB',
			'dFXVY', 'HcODx', 'EwZMD', 'XiyWm', 'tACqx',
			'LpxiJ', 'yjorX', 'gjoqs', 'Evfto', 'AjUSb',
			'NfzWO', 'ZPOEe', 'uEPdj', 'dxABG', 'yJDtH',
			'nEvNK', 'DbqKR', 'GlxyF', 'rOkdG', 'ULPud',
			'sjTfu', 'FaGLE', 'fTDCL', 'CILdu', 'zJciH',
			'xQLRa', 'dcEbg', 'MIwbL', 'gODhL', 'dWhvi'
			
		];
	
		// Initialize array to store question-wise data
		$question_wise_data = [];
	
		// Process each entry
		foreach ($data as $entry) {
			$question_id = $entry['question_id'];
			$system_id = $entry['id']; // Unique user identifier
			$answer = $entry['answer'];
	
			// Ensure question_id exists in array
			if (!isset($question_wise_data[$question_id])) {
				$question_wise_data[$question_id] = [
					'unique_users' => [], // Track unique system IDs
					'answers' => array_fill_keys($target_answers, 0),
					'total_unique_users' => 0 // Store unique system_id count
				];
			}
	
			// Track unique system_id (prevent duplicate counting)
			$question_wise_data[$question_id]['unique_users'][$system_id] = true;
	
			// If the answer is in the target list, increase its count
			if (in_array($answer, $target_answers)) {
				$question_wise_data[$question_id]['answers'][$answer]++;
			}
		}
	
		// Calculate percentage for each answer per question based on unique users
		$result = [];
		foreach ($question_wise_data as $question_id => $data) {
			$total_unique_users = count($data['unique_users']); // Unique system_id count
			$percentages = [];
	
			foreach ($data['answers'] as $answer => $count) {
				$percentage = $total_unique_users > 0 ? round(($count / $total_unique_users) * 100, 2) : 0;
				if ($percentage > 0) { // Remove answers with 0%
					$percentages[$answer] = $percentage;
				}
			}

			if (!empty($percentages)) { // Only include questions with valid data
				$result[$question_id] = [
					'total_unique_users' => $total_unique_users,
					'percentages' => $percentages
				];
			}
		}
	
		return $result;
	}


public function get_record_student($searchCond){
	$program_id = $searchCond['program_id'];
	$acadmic_id = $searchCond['academic_year_id'];
	$query = $this->db->distinct()
	  ->select('c.catalog_nbr') // Selecting course_id from first table and course_name from second table
	->from('tbl_department_course_slot_assignment dcsa') // Using alias for readability
	->join('tbl_course c', 'dcsa.course_id  = c.id', 'left') // Joining with tbl_courses on course_id
	->where('dcsa.programme_id', $searchCond['program_id'])
	->where('dcsa.academic_year_id', $searchCond['academic_year_id'])
	->where('dcsa.is_deleted', '0')
	->where('dcsa.status', '1')
	->get();

	$courseid = $query->result_array(); 

	$catalogNbrs = array_column($courseid, 'catalog_nbr');
	$program_code = $this->db->select('program_code') // Selecting department_id
	->from('tbl_programme_master') // Fetching data from table
	->where('id', $searchCond['program_id']) // Applying filter
	->where('status','1')
	->where('is_deleted','0')
	->get();

	$program_code = $program_code->result_array(); // Fetch result as array
	$program_code = $program_code['0']['program_code'];
	//echo $program_code; die();
	$query = $this->db->select('system_id') // Selecting employee_id
					  ->from('tbl_student_details') // Fetching data from table
					  ->where('program_code', $program_code)
					  ->where('is_deleted', '0')
					  ->where('status', '1')
					  ->get();
	
	$student_id = $query->result_array(); // Fetching data as an array
	
	// Extract all employee IDs into a single array
	$student_id = array_column($student_id, 'system_id');
	//echo '<pre>';print_r($catalogNbrs); die;
	$query1 = $this->db->select('refid.ques_id, refid.answer, qrpstry.id as questionid')
	->from('tbl_course_feedback rmpfd')
	->join('tbl_course_feedback_ref refid', 'rmpfd.reference = refid.reference')
	->join('tbl_questionfeedbackrepository qrpstry', 'refid.ques_id = qrpstry.id') // Fixed syntax
	->where('rmpfd.academic_year_id',$acadmic_id)
	->where('rmpfd.status','1')
	->where('rmpfd.is_deleted','0')
	->where_in('rmpfd.system_id', $student_id)
	->order_by('refid.ques_id', 'ASC') // Order by question
	->get();

$results2 = $query1->result_array();
//echo '<pre>';print_r($results2); die;
$query4 = $this->db->select('count(DISTINCT rmpfd.system_id) as total_emp')
		->from('tbl_course_feedback rmpfd')
		->join('tbl_course_feedback_ref refid', 'rmpfd.reference = refid.reference')
		->join('tbl_questionfeedbackrepository qrpstry', 'refid.ques_id = qrpstry.id') // Fixed syntax
		->where('rmpfd.status','1')
		->where('rmpfd.is_deleted','0')
		->where('rmpfd.academic_year_id',$acadmic_id)
		->where_in('rmpfd.system_id', $student_id)

		->order_by('refid.ques_id', 'ASC') // Order by question
		->get();
	
	     $results4 = $query4->result_array();
		 //echo '<pre>';print_r($results2); die;
		 $questionWiseData = [];

		foreach ($results2 as $response) {
		$ques = $response['ques_id'];
		$ans = $response['answer'];

		// Ensure only numeric answers are processed
		if (!is_numeric($ans)) {
		continue;
		}

		// Initialize question entry if not exists
		if (!isset($questionWiseData[$ques])) {
		$questionWiseData[$ques] = [
		'total' => 0,
		'answers' => []
		];
		}

		// Count total responses for the question
		$questionWiseData[$ques]['total']++;

		// Count occurrences of each numeric answer per question
		if (!isset($questionWiseData[$ques]['answers'][$ans])) {
		$questionWiseData[$ques]['answers'][$ans] = 0;
		}
		$questionWiseData[$ques]['answers'][$ans]++;
		}

		// Calculate percentages
		$questionWisePercentage = [];

		foreach ($questionWiseData as $ques => $data) {
		$totalResponses = $data['total'];
		$percentages = [];

		foreach ($data['answers'] as $ans => $count) {
		$percentages[$ans] = round(($count / $totalResponses) * 100, 2); // Percentage calculation
		}

		$questionWisePercentage[$ques] = [
		'total_responses' => $totalResponses,
		'answer_distribution' => $percentages
		];
		}




		return [$questionWisePercentage, $results4];
}




public function getAllReportscomments($searchCond){
	$program_id = $searchCond['program_id'];
	$acadmic_id = $searchCond['academic_year_id'];
	$department = $this->db->select('department_id') // Selecting department_id
	->from('tbl_programme_master') // Fetching data from table
	->where('id', $searchCond['program_id']) // Applying filter
	->get();

$department_data = $department->result_array(); // Fetch result as array
$department_id = $department_data['0']['department_id'];
$query = $this->db->select('employee_id') // Selecting employee_id
				  ->from('tbl_employee_master') // Fetching data from table
				  ->where('job_family', 'ACAD')
				  ->where('department_id', $department_id)
				  ->where('is_deleted', '0')
				  ->where('status', '1')
				  ->get();

$emp_id = $query->result_array(); // Fetching data as an array

// Extract all employee IDs into a single array
$employee_ids = array_column($emp_id, 'employee_id');
 //print_r($employee_ids); die();
	$query = $this->db->distinct()
	  ->select('c.catalog_nbr') // Selecting course_id from first table and course_name from second table
	->from('tbl_department_course_slot_assignment dcsa') // Using alias for readability
	->join('tbl_course c', 'dcsa.course_id  = c.id', 'left') // Joining with tbl_courses on course_id
	->where('dcsa.programme_id', $searchCond['program_id'])
	->where('dcsa.academic_year_id', $searchCond['academic_year_id'])
	->where('dcsa.is_deleted', '0')
	->where('dcsa.status', '1')
	->get();

	$courseid = $query->result_array(); 

	$catalogNbrs = array_column($courseid, 'catalog_nbr');

	$excludedAnswers = ['None', 'Agree', 'Ok', 'Thank you', 'Good', 'Bad', 'NA', 'no', 'N/A', 'done', 'Nil', 'Nothing', 'All Ok', 'All FINE', 'No suggestions', 'All are Good', 'None', 'Hi', 'No Suggestion.', 'na--', 'All is good', 'Well done', 'No suggestion', 'NOT APPLICABLE', 'Test', 'All good'];
	$excludedAnswersString = "'" . implode("','", $excludedAnswers) . "'";
	
	$this->db->select('refid.ans')
		->from('tbl_employeefeedback_ref refid')
		->join('tbl_employeefeedback rmpfd', 'rmpfd.id = refid.ef_id')
		->where("refid.ans REGEXP '[a-zA-Z]'", null, false) // Matches text values
		->where("refid.ans NOT IN ($excludedAnswersString)")
		->where('rmpfd.academic_year_id', $acadmic_id);
	
	if (!empty($employee_ids)) {
		$this->db->where_in('rmpfd.emp_code', $employee_ids);
	}
	
	$query1 = $this->db->get();
	$results2 = $query1->result_array();
	
	return $results2;
	
}


// public function getAllReportscomments($searchCond){
//     $program_id = $searchCond['program_id'];
//     $acadmic_id = $searchCond['academic_year_id'];

//     // First query to get course IDs
//     $query = $this->db->distinct()
//         ->select('c.catalog_nbr') 
//         ->from('tbl_department_course_slot_assignment dcsa') 
//         ->join('tbl_course c', 'dcsa.course_id  = c.id', 'left') 
//         ->where('dcsa.programme_id', $searchCond['program_id'])
//         ->where('dcsa.academic_year_id', $searchCond['academic_year_id'])
//         ->where('dcsa.is_deleted', '0')
//         ->where('dcsa.status', '1')
//         ->get();

//     $courseid = $query->result_array();
//     $catalogNbrs = array_column($courseid, 'catalog_nbr');

//     // Second query to filter out specific answers
//     $excludedAnswers = ['None', 'Agree', 'Ok', 'Thank you', 'Good', 'Bad','NA','no','N/A','done','Nil','Nothing','All Ok','All FINE','No suggestions','All are Good','None','Hi','No Suggestion.','na--','All is good','Well done','No suggestion','NOT APPLICABLE','Test','All good'];
//     $excludedAnswersString = "'" . implode("','", $excludedAnswers) . "'";

//     $query1 = $this->db->select('answer')
//         ->from('tbl_course_feedback_ref')
//         ->where("answer REGEXP '[a-zA-Z]'", null, false) // Matches text values
//         ->where("answer NOT IN ($excludedAnswersString)") // Excludes certain answers
//         ->get();

//     $results2 = $query1->result_array();

//     // For debugging - output results
//     // echo '<pre>';
//     // print_r($results2);
//     // die();

//     // Assuming you want to return other results as well
//     return $results2;
// }


public function get_record_studentcomments($searchCond){
	$program_id = $searchCond['program_id'];
	$acadmic_id = $searchCond['academic_year_id'];
	$query = $this->db->distinct()
	  ->select('c.catalog_nbr') // Selecting course_id from first table and course_name from second table
	->from('tbl_department_course_slot_assignment dcsa') // Using alias for readability
	->join('tbl_course c', 'dcsa.course_id  = c.id', 'left') // Joining with tbl_courses on course_id
	->where('dcsa.programme_id', $searchCond['program_id'])
	->where('dcsa.academic_year_id', $searchCond['academic_year_id'])
	->where('dcsa.is_deleted', '0')
	->where('dcsa.status', '1')
	->get();

	$courseid = $query->result_array(); 

	$catalogNbrs = array_column($courseid, 'catalog_nbr');
	$program_code = $this->db->select('program_code') // Selecting department_id
	->from('tbl_programme_master') // Fetching data from table
	->where('id', $searchCond['program_id']) // Applying filter
	->where('status','1')
	->where('is_deleted','0')
	->get();

	$program_code = $program_code->result_array(); // Fetch result as array
	$program_code = $program_code['0']['program_code'];
	//echo $program_code; die();
	$query = $this->db->select('system_id') // Selecting employee_id
					  ->from('tbl_student_details') // Fetching data from table
					  ->where('program_code', $program_code)
					  ->where('is_deleted', '0')
					  ->where('status', '1')
					  ->get();
	
	$student_id = $query->result_array(); // Fetching data as an array
	
	// Extract all employee IDs into a single array
	$student_id = array_column($student_id, 'system_id');

		 //echo '<pre>';print_r($results2); die;

		// Ensure only numeric answers are processed

	
		$excludedAnswers = ['None', 'Agree', 'Ok', 'Thank you', 'Good', 'Bad','NA','no','N/A','done','Nil','Nothing','All Ok','All FINE','No suggestions','All are Good','None','Hi','No Suggestion.','na--','All is good','Well done','No suggestion','NOT APPLICABLE','Test','All good','No comments','Good.','Neutral','NON','non','N','Satisfied','No any suggestions'];
		$excludedAnswersString = "'" . implode("','", $excludedAnswers) . "'";

		$query1 = $this->db->select('refid.answer')
		->from('tbl_course_feedback rmpfd')
		->join('tbl_course_feedback_ref refid', 'rmpfd.reference = refid.reference')
		->where("refid.answer REGEXP '[a-zA-Z]'", null, false);
	
	if (!empty($excludedAnswersString)) {
		$query1 = $query1->where("refid.answer NOT IN ($excludedAnswersString)", null, false);
	}
	
	$query1 = $query1->where('rmpfd.academic_year_id', $acadmic_id)
		->where_in('rmpfd.system_id', $student_id)
		->get();
	
	$results2 = $query1->result_array();
	//echo '<pre>'; print_r($results2); die();
	
		return $results2;

}

public function get_record_almunicomments($searchCond) {
	// Fetch program code based on program_id
	$program_id = $searchCond["program_id"]; 
	$query2 = $this->db->select('program_code')
		->from('tbl_programme_master')
		->where('id', $program_id)
		->get();

	$program_code = $query2->result();
	$programcode = $program_code[0]->program_code;
	$excludedAnswers = ['None', 'Agree', 'Ok', 'Thank you', 'Good', 'Bad','NA','no','N/A','done','Nil','Nothing','All Ok','All FINE','No suggestions','All are Good','None','Hi','No Suggestion.','na--','All is good','Well done','No suggestion','NOT APPLICABLE','Test','All good','No comments','Good.','Neutral','NON','non','N','Satisfied','No any suggestions'];
		$excludedAnswersString = "'" . implode("','", $excludedAnswers) . "'";
	// Fetch survey responses
	$query1 = $this->db->select('answer')
		->from('overall_survey_answers')
		->where('survey_id', '5')
		->where("answer REGEXP '[a-zA-Z]'", null, false)
		->where("answer NOT IN ($excludedAnswersString)")
		->where('programme',$programcode)
		->where('question_id','39')
		->where('is_deleted', '0')
		->get();

		$result = $query1->result_array();
                

	return $result;
}





public function get_record_academicpeerscomments($searchCond){
	$program_id = $searchCond["program_id"]; 
	$query2 = $this->db->select('program_code')
		->from('tbl_programme_master')
		->where('id', $program_id)
		->get();

	$program_code = $query2->result();
	$programcode = $program_code[0]->program_code;
	$excludedAnswers = ['None', 'Agree', 'Ok', 'Thank you', 'Good', 'Bad','NA','no','N/A','done','Nil','Nothing','All Ok','All FINE','No suggestions','All are Good','None','Hi','No Suggestion.','na--','All is good','Well done','No suggestion','NOT APPLICABLE','Test','All good','No comments','Good.','Neutral','NON','non','N','Satisfied','No any suggestions'];
		$excludedAnswersString = "'" . implode("','", $excludedAnswers) . "'";
	// Fetch survey responses
	$query1 = $this->db->select('answer')
		->from('overall_survey_answers')
		->where('survey_id', '7')
		->where('programme',$programcode)//[question_id] => 46
		->where('question_id','46')
		->where("answer REGEXP '[a-zA-Z]'", null, false)
		->where("answer NOT IN ($excludedAnswersString)")
		->where('is_deleted', '0')
		->where('status','1')
		->get();

		$result = $query1->result_array();


	// Debugging Output (Remove in Production)
	// echo '<pre>';
	// print_r($result);
	// exit;

	return $result;
}

public function get_record_empoloyeecomments($searchCond){
	$program_id = $searchCond["program_id"]; 
	$query2 = $this->db->select('program_code')
		->from('tbl_programme_master')
		->where('id', $program_id)
		->get();

	$program_code = $query2->result();
	$programcode = $program_code[0]->program_code;
	$excludedAnswers = ['None', 'Agree', 'Ok', 'Thank you', 'Good', 'Bad','NA','no','N/A','done','Nil','Nothing','All Ok','All FINE','No suggestions','All are Good','None','Hi','No Suggestion.','na--','All is good','Well done','No suggestion','NOT APPLICABLE','Test','All good','No comments','Good.','Neutral','NON','non','N','Satisfied','No any suggestions'];
		$excludedAnswersString = "'" . implode("','", $excludedAnswers) . "'";
	// Fetch survey responses
	$query1 = $this->db->select('*')
		->from('overall_survey_answers')
		->where('survey_id', '3')
		->where('programme',$programcode)
		->where('question_id' ,'30')
		->where("answer REGEXP '[a-zA-Z]'", null, false)
		->where("answer NOT IN ($excludedAnswersString)")
		->where('is_deleted', '0')
		->where('status','1')
		->get();


	$result = $query1->result_array();
	return $result;
		//echo '<pre>'; print_r($data); die(); 
}


public function get_record_parents_comments($searchCond){
	$query3 = $this->db->select('department_id')
	->from('tbl_department_course_slot_assignment')
	->where('programme_id',$program_id)
	->where('academic_year_id',$searchCond["academic_year_id"])//teaching_scheme_type
	->where('teaching_scheme_type',$searchCond["teaching_scheme_type"])
	->get();
	$department_id = $query3->result();
	$department_id = $department_id[0]->department_id;
	$program_id = $searchCond["program_id"]; 
	$query2 = $this->db->select('program_code')
		->from('tbl_programme_master')
		->where('id', $program_id)
		->get();

	$program_code = $query2->result();
	$programcode = $program_code[0]->program_code;
	$excludedAnswers = ['None', 'Agree', 'Ok', 'Thank you', 'Good', 'Bad','NA','no','N/A','done','Nil','Nothing','All Ok','All FINE','No suggestions','All are Good','None','Hi','No Suggestion.','na--','All is good','Well done','No suggestion','NOT APPLICABLE','Test','All good','No comments','Good.','Neutral','NON','non','N','Satisfied','No any suggestions'];
		$excludedAnswersString = "'" . implode("','", $excludedAnswers) . "'";
	// Fetch survey responses
	$query1 = $this->db->select('*')
		->from('survey_answers')
		->where('survey_id', '6')
    	->where('department', $department_id)
		->where('question_id','55')
		->where("answer REGEXP '[a-zA-Z]'", null, false)
		->where("answer NOT IN ($excludedAnswersString)")
		->where('is_deleted', '0')
		->get();

	$result = $query1->result_array();
	//echo '<pre>' ; print_r($result); die;
}
}
