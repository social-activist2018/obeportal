<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Class Coporeport_Model extends CI_Model{
	//private $db2;
	public function __construct()
	{
		parent::__construct();
		$this->db2 = $this->load->database('db2', TRUE);
	}

    public function get_course_evaluations()
    {

        $this->db->select('
             tbl_obe_course_evaluation.id, 
            tbl_obe_course_evaluation.teacher_id, 
            tbl_employee_master.full_name AS full_name, 
            tbl_obe_course_evaluation.evaluation_name AS evaluation_names, 
            tbl_obe_course_evaluation.course_id,
            tbl_obe_course_evaluation.lecture_type AS lecture_type_name, 
            tbl_obe_course_evaluation.term AS terms, 
            tbl_department_master.name AS department, 
            tbl_school_master.school_name AS school, 
            tbl_course.catalog_nbr AS course_catalog_numbers, 
            tbl_obe_course_evaluation.classnumber AS class_numbers
        ');
        $this->db->from('tbl_obe_course_evaluation');
        $this->db->join('tbl_course', 'tbl_course.id = tbl_obe_course_evaluation.course_id'); 
        $this->db->join('tbl_department_master', 'tbl_department_master.id = tbl_obe_course_evaluation.department_id'); 
        $this->db->join('tbl_school_master', 'tbl_school_master.id = tbl_obe_course_evaluation.school_id');
        $this->db->join('tbl_academicyear', 'tbl_academicyear.id = tbl_obe_course_evaluation.academic_year_id');
        $this->db->join('tbl_employee_master', 'tbl_employee_master.employee_id = tbl_obe_course_evaluation.teacher_id');
        $this->db->where('tbl_obe_course_evaluation.is_deleted', '0');
        $this->db->order_by('tbl_obe_course_evaluation.evaluation_name'); // Grouping by teacher_id
        $this->db->order_by('tbl_obe_course_evaluation.teacher_id','ASC');
        
        $query = $this->db->get(); 
        if ($query->num_rows() > 0) {
            return $query->result_array(); 
        } else {
            return []; 
        }
    }
    
    

public function deletecasetup($terms,$course_id,$evaluation_names, $class_numbers,$id){

    $data = array(
        'is_deleted'   => '1'
    );
    
    $this->db->where('id', $id);
    $this->db->where('term', $terms);
    $this->db->where('course_id', $course_id);
    $this->db->where('evaluation_name', $evaluation_names);
    $this->db->where('classnumber', $class_numbers);
    $this->db->update('tbl_obe_course_evaluation', $data);
  //  $query = $this->db->last_query();
   // echo $query;die;

}

public function deletecasetupevalutionmarks($terms, $class_numbers, $course_code,$id){

        $dataupdate = array(
            'is_deleted'   => '1'
        );
    
        $this->db->where('term', $terms);
        $this->db->where('course_code', $course_code);
        $this->db->where('evaluation_id', $id);
        $this->db->where('classnumber', $class_numbers);
        $this->db->update('evaluation_marks_table', $dataupdate);
   
    }
    
    public function getEvaluationById($id)
    {
        return $this->db->get_where('tbl_obe_course_evaluation', ['id' => $id])->row_array();
    }
    public function select_course_code($course_id)
    {
        $this->db->select('catalog_nbr');
        $this->db->from('tbl_course');
        $this->db->where('id', $id);
        return $this->db->get()->row_array();
    }
    

}

