<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Studentlist extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
		$this->load->model(array('Attendance_Model'));
        if(!$this->session->userdata('qb_adminloggedin')){
			setHistory('Error: Manage Enrollmentverification Controller Page'); 
			redirect('admin/facultylogin');
		} 
    } 

    /*
	* Function: index
	*/
    public function index($page = 0)
    {
      
        $data = array();
        $head = array();
		$userDetails = 	$this->session->userdata('qb_adminloggedin');
		$qb_role_id = $this->session->userdata('qb_role_id'); 
		$data['skipnArray'] = $skipnArray = TT_ROLE_ID;
        $data['recordArray'] =  array();
		$data['portalArray'] = array();
		
		if($_GET){
			if (!empty($_GET['term']))  { 
				$condArray = array('ADMIT_TERM'=>$ACTIVE_ADMIT_TERM);
				
				if($_GET['class_number']){
					$condArray['CLASS_NBR']=>$_GET['class_number'];
				
				}
				if($_GET['section']){
						$condArray['CLASS_SECTION']=>$_GET['section'];
				}
							
				$data['recordArray'] =  $this->Attendance_Model->getPeoplesoftCourseSections($condArray);
				
			}
		}  
		
        $data['header'] = $data['title'] = 'Manage studentlist PPSoft Class & Section - ';
		$this->load->view('admin/_parts/header',$data);
		$this->load->view('admin/studentlist/index',$data);
		$this->load->view('admin/_parts/footer');
		setHistory('Go to Faculty PPSoft Class & Section Page');  
		
	}
	
}
