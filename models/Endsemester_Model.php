<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Class Endsemester_Model extends CI_Model{
	private $db2;
	public function __construct()
	{
		parent::__construct();
		$this->db2 = $this->load->database('db2', TRUE);
	}

	public function get_ques_end_lessthan_three() {
    
		$credit = '2';
		$this->db->select('id, q_name,qus_max_marks,co_id');
		$this->db->from('endsemester_fix_ques');
		$this->db->where('creadit', $credit);
		$query = $this->db->get(); 
		return $query->result_array();
	}
	
	public function get_ques_end_wq_grt_three() {
		$credit = '3';
		$this->db->select('id, q_name,qus_max_marks, co_id');
		$this->db->from('endsemester_fix_ques');
		$this->db->where('creadit', $credit);
		$query = $this->db->get(); 
		return $query->result_array();
	}

	public function get_marks($system_id, $catalog_nbr, $su_paper_id, $term, $semester_id, $class, $question) {
		$this->db->select('obtained_marks');
		$this->db->from('end_semester_marks');
		$this->db->where([
			'system_id' => $system_id,
			'catalog_nbr' => $catalog_nbr,
			'su_paper_id' => $su_paper_id,
			'term' => $term,
			'semester_id' => $semester_id,
			'class' => $class,
			'question' => $question,
			'is_deleted' => 0
		]);
		return $this->db->get()->row();
	}
	
	public function all_record($emp_id, $catalog_nbr, $lecture_id, $class, $term, $semester_id) {
		$this->db->select('*');
		$this->db->from('end_semester_marks');
		$this->db->where('teacher_id', $emp_id);
		$this->db->where('catalog_nbr', $catalog_nbr);
		$this->db->where('class', $class);
		$this->db->where('term', $term);
		$this->db->where('semester_id', $semester_id);
		$this->db->where('is_deleted', '0');
		$query = $this->db->get();
		
		return $query->result_array(); // Returns result as an array
	}

	public function get_all_marks($studentIds, $catalog_nbr, $su_paper_id, $term, $semester_id, $class) {
		$this->db->select('system_id,question, obtained_marks');
		$this->db->from('end_semester_marks');
		$this->db->where_in('system_id', $studentIds);
		$this->db->where('catalog_nbr', $catalog_nbr);
		$this->db->where('su_paper_id', $su_paper_id);
		$this->db->where('term', $term);
		$this->db->where('semester_id', $semester_id);
		$this->db->where('class', $class);
	
		return $this->db->get()->result();
	}
	
	
}

