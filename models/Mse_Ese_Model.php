<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Class Mse_Ese_Model extends CI_Model{
	//private $db2;
	public function __construct()
	{
		parent::__construct();
		$this->db2 = $this->load->database('db2', TRUE);
	}

    public function get_mse_marks()
    {
        $this->db2->select('*');
        $this->db2->from('mse_max_marks');
        $this->db2->where('is_deleted', '0');
        $this->db2->where('CLASS_NBR !=', '');
        $this->db2->group_by('CLASS_NBR');
    
        $query = $this->db2->get();
    
        return ($query->num_rows() > 0) ? $query->result_array() : [];
    }
    
public function deletemse($CLASS_NBR, $ADMIT_TERM, $catalog_nbr, $INSTRUCTOR_ID){

    $data = array(
        'is_deleted'   => '1'
    );
    
    $this->db->where('CLASS_NBR', $CLASS_NBR);
    $this->db->where('ADMIT_TERM', $ADMIT_TERM);
    $this->db->where('catalog_nbr', $catalog_nbr);
    $this->db->where('INSTRUCTOR_ID', $INSTRUCTOR_ID);
    $this->db->update('mse_max_marks', $data);

}

}

