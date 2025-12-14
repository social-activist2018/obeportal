<?php
defined('BASEPATH') OR exit('No direct script access allowed');
Class Resultanalysis_Model extends CI_Model{ 
	private $dbreport;

    public function __construct()
    {
        parent::__construct();
        $this->dbreport = $this->load->database('dbreport', TRUE);
    }
	/*
	* Function : getAllCustomSRecords   
	*/
	public function getAllCustomSRecords($school_code=null, $department = NULL,$programm=NULL)
    {
		$sql = "SELECT DISTINCT sd.system_id, sd.rollno,sd.name,sd.email,sg.current_term ,sg.sgpa,sg.cgpa FROM `student_details` sd JOIN student_grade sg ON sd.system_id=sg.system_id where sd.school_code='".$school_code."' AND `department`='".$department."' AND sd.prog_name='".$programm."' ";

		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();   
    }
	/* ALL SEM -> PROG ACTION LEN MIG
	ODD TERM ->  2023 START FORM SYSTEM ID   ///STUDENT ADMIT TERM = SELECTED TERM
	* Function : getAllCustomSRecords   
	*/
	public function getAllRecordsProgramIN($list=null,$term=null,$semester=null,$table=null)
    {
		if(!empty($term)){$startStu = substr($term, 0, 2);}
		if(!empty($semester) && isset($semester) && !empty($term) && isset($term)){ //die('1');
			//$condition = "where semester!='00' AND registered='yes' AND student_id != '' AND plan_code IN(".$list.") AND term = '".$term."'AND semester = '".$semester."'";
			$sql = "SELECT * FROM ".$table." A WHERE A.STUDENT_ID IN ( SELECT DISTINCT V.STUDENT_ID FROM ".$table." V WHERE V.PLAN_CODE IN(".$list.") AND V.STUDENT_ID != '' AND V.REGISTERED = 'yes' AND V.SEMESTER != '00'AND V.PROG_ACTION NOT IN ('WAPP') OR  V.student_id like '20$startStu%' AND V.STUDENT_ID != '' AND V.PROG_ACTION NOT IN ('WAPP') AND V.PLAN_CODE IN(".$list.")) AND A.semester= '".$semester."' AND A.term= '".$term."' order by term, semester";
		}
		else if(!empty($term) && isset($term) && empty($semester)){ //die('2');
			//$condition = "where semester!='00' AND registered='yes' AND student_id != '' AND plan_code IN(".$list.") AND term = '".$term."' ";
			$sql = "SELECT * FROM ".$table." A WHERE A.STUDENT_ID IN ( SELECT DISTINCT V.STUDENT_ID FROM ".$table." V WHERE V.PLAN_CODE IN(".$list.") AND V.STUDENT_ID != '' AND V.REGISTERED = 'yes' AND V.SEMESTER != '00' AND V.PROG_ACTION NOT IN ('WAPP') OR  V.student_id like '20$startStu%'  AND V.STUDENT_ID != '' AND V.PROG_ACTION NOT IN ('WAPP') AND V.PLAN_CODE IN(".$list."))  AND A.term= '".$term."' order by semester";
		}
		else if(empty($semester) && empty($term) && !empty($list) && isset($list)){ //die('3');
			//$condition = "where semester!='00' AND registered='yes' AND student_id != '' AND plan_code IN(".$list.")";
			$sql = "SELECT * FROM ".$table." A WHERE A.STUDENT_ID IN ( SELECT DISTINCT V.STUDENT_ID FROM ".$table." V WHERE V.PLAN_CODE IN(".$list.") AND V.STUDENT_ID != '' AND V.REGISTERED = 'yes' AND V.SEMESTER != '00' AND V.PROG_ACTION NOT IN ('WAPP') OR  V.student_id like '20$startStu%'  AND V.STUDENT_ID != '' AND V.PROG_ACTION NOT IN ('WAPP') AND V.PLAN_CODE IN(".$list."))";
		}
		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();   
    }
	/*
	* Function : getAllCustomSGRecords  :SUM( CASE WHEN sg.grade= 'DEB' THEN 1 ELSE 0 END) AS debarred, 
	*/
	public function getAllCustomSGRecords($list=null,$term=null,$semester=null,$table=null)
    {
		if(!empty($term)){$startStu = substr($term, 0, 2);}
		if(!empty($semester) && isset($semester) && !empty($term) && isset($term)){ //die('1');
			$condition = "where semester!='00' AND  plan_code IN(".$list.") AND ss.term = '".$term."' AND ss.prog_action NOT IN('WAPP') AND ss.semester = '".$semester."' AND (ss.registered = 'yes' OR (CASE WHEN ss.student_id LIKE '20$startStu%' THEN '1' END ))  ";
		}
		else if(!empty($term) && isset($term) && empty($semester)){ //die('2');
			$condition = "where semester!='00' AND  plan_code IN(".$list.") AND ss.term = '".$term."' AND ss.prog_action NOT IN('WAPP') AND (ss.registered = 'yes' OR (CASE WHEN ss.student_id LIKE '20$startStu%' THEN '1' END )) GROUP BY semester ";
		}
		else if(empty($semester) && empty($term) && !empty($list) && isset($list)){ //die('3');
			$condition = "where semester!='00' AND  plan_code IN(".$list.") AND ss.prog_action NOT IN('WAPP') AND (ss.registered = 'yes' OR (CASE WHEN ss.student_id LIKE '20$startStu%' THEN '1' END )) GROUP BY term, semester";
		}
		 $sql = "SELECT ss.semester, COUNT(ss.student_id) AS total_student,SUM( CASE WHEN  ss.sgpa >= 0  AND ss.sgpa <= 2.5000 THEN 1 ELSE 0 END) AS sgpa1,  SUM( CASE WHEN ss.sgpa > 2.5001  AND ss.sgpa <= 5.000 THEN 1 ELSE 0 END) AS sgpa2, SUM( CASE WHEN ss.sgpa > 5.0001  AND ss.sgpa <= 6.000 THEN 1 ELSE 0 END) AS sgpa3, SUM( CASE WHEN ss.sgpa > 6.001  AND ss.sgpa <= 7.000 THEN 1 ELSE 0 END) AS sgpa4, SUM( CASE WHEN ss.sgpa > 7.001  AND ss.sgpa <= 8.0 THEN 1 ELSE 0 END) AS sgpa5, GROUP_CONCAT( CASE WHEN ss.sgpa > 7.0  AND ss.sgpa <= 8.0 THEN ss.student_id END) AS sgpa5_sys, SUM( CASE WHEN ss.sgpa > 8.000  AND ss.sgpa <= 10 THEN 1 ELSE 0 END) AS sgpa6, ss.term, ss.semester  FROM `".$table."` ss ".$condition." ";

		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();   
    }
	/*
	* Function : getAllCustomSGRecords  :SUM( CASE WHEN sg.grade= 'DEB' THEN 1 ELSE 0 END) AS debarred, 
	*/
	public function getAllCustomSGRecordsDynamic($list=null,$term=null,$semester=null,$to=null,$from=null)
    {
		if(!empty($semester) && isset($semester) && !empty($term) && isset($term)){ //die('1');
			$condition = "where semester!='00' AND  plan_code IN(".$list.") AND ss.term = '".$term."' AND ss.semester = '".$semester."' AND `sgpa` BETWEEN  '".$to."' AND  '".$from."' AND (ss.registered = 'yes' OR (CASE WHEN ss.semester = 'S1' AND ss.prog_action NOT IN('WAPP') THEN '1' END ))";
		}
		else if(!empty($term) && isset($term) && empty($semester)){ //die('2');
			$condition = "where semester!='00' AND  plan_code IN(".$list.") AND ss.term = '".$term."' AND `sgpa` BETWEEN  '".$to."' AND  '".$from."' AND (ss.registered = 'yes' OR (CASE WHEN ss.semester = 'S1' AND ss.prog_action NOT IN('WAPP') THEN '1' END ))";
		}
		 $sql = "SELECT * FROM `stu_score` ss ".$condition." ";

		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();   
    }
	/*
	* Function : getAllStudentDetailRecords  :SUM( CASE WHEN sg.grade= 'DEB' THEN 1 ELSE 0 END) AS debarred, 
	*/
	public function getAllStudentDetailRecords($cond=null, $stu=null, $term=null, $sem=null, $table=null)
    {	if(!empty($term)){$startStu = substr($term, 0, 2);}
		//if($sem=='S1'){
		 //$sql = "SELECT DISTINCT(student_id) AS total_student FROM `".$table."` ss where ".$cond." AND ss.student_id IN(".$stu.") AND ss.term = '".$term."' AND ss.semester = '".$sem."'  AND prog_action NOT IN('WAPP') "; 
		//}
		//else{
		 $sql = "SELECT DISTINCT(student_id) AS total_student FROM `".$table."` ss where ".$cond." AND ss.student_id IN(".$stu.") AND ss.term = '".$term."' AND ss.semester = '".$sem."' AND prog_action NOT IN('WAPP') AND (ss.registered = 'yes' OR ss.student_id LIKE '20$startStu%') "; 
		//}

		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();   
    }
	/*
	* Function : gettotalStudentDetailRecords  :SUM( CASE WHEN sg.grade= 'DEB' THEN 1 ELSE 0 END) AS debarred, 
	*/
	public function gettotalStudentDetailRecords($cond=null, $stu=null, $term=null, $sem=null, $table=null)
    {	if(!empty($term)){$startStu = substr($term, 0, 2);}
		if(!empty($cond)){
			//if($sem=='S1'){
			//$sql = "SELECT DISTINCT(student_id) AS total_student FROM `".$table."` ss where ss.plan_code IN(".$stu.") AND ss.term = '".$term."' AND ss.semester = '".$sem."'  AND prog_action NOT IN('WAPP') "; 
			//}else{
			$sql = "SELECT DISTINCT(student_id) AS total_student FROM `".$table."` ss where ss.plan_code IN(".$stu.") AND ss.term = '".$term."' AND ss.semester = '".$sem."'  AND (ss.registered = '".$cond."' OR ss.student_id LIKE '20$startStu%') AND  prog_action NOT IN('WAPP')"; 
			//}
		}else{
		 $sql = "SELECT DISTINCT(student_id) AS total_student FROM `".$table."` ss where ss.plan_code IN(".$stu.") AND ss.term = '".$term."' AND ss.semester = '".$sem."'";  
		 }

		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();   
    }
	/*
	* Function : gettotalStudentDetailRecords  :SUM( CASE WHEN sg.grade= 'DEB' THEN 1 ELSE 0 END) AS debarred, 
	*/
	public function getcourseStudentDetailRecords($listStu=null, $term=null, $sem=null,$course_code=null, $class_id=null, $section_id=null, $table=null)
    {	
		$sql = "SELECT DISTINCT(student_id) AS total_student FROM `".$table."` sc where student_id IN(".$listStu.")  AND sc.term = '".$term."' AND sc.semester = '".$sem."' AND `course_code` ='".$course_code."' AND `class_id`='".$class_id."' AND `section_id`='".$section_id."' AND section_id NOT LIKE '%FR%' AND section_id NOT LIKE '%FI%'  "; 
		
		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();   
    }
	/*
	* Function : getStuCoursRecords  :SUM( CASE WHEN sg.grade= 'DEB' THEN 1 ELSE 0 END) AS debarred, 
	*/
	public function getStuCoursRecords($cond=null, $stu=null, $term=null, $sem=null, $table=null)
    {	
		 $sql = "SELECT DISTINCT(`course_code`) AS total_courses FROM `".$table."` where term = '".$term."' AND semester = '".$sem."' AND student_id = '".$stu."'  ";

		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();   
    }
	/*
	* Function : getStuDebaredRecords  :SUM( CASE WHEN sg.grade= 'DEB' THEN 1 ELSE 0 END) AS debarred, 
	*/
	public function getStuDebaredRecords($cond=null, $stu=null, $term=null, $sem=null, $table=null)
    {	
		 $sql = "SELECT * FROM ( SELECT DISTINCT student_id,student_name, term, course_code, grade  FROM ".$table." where student_id IN(".$stu.")  AND term = '".$term."' AND semester = '".$sem."' AND grade = '".$cond."') AS p";

		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();   
    }
	/*
	* Function : getStuDebaredRecords  :SUM( CASE WHEN sg.grade= 'DEB' THEN 1 ELSE 0 END) AS debarred, 
	*/
	public function getcourseStuDebaredRecords($listStu=null, $term=null, $semester=null,$course_code=null, $class_id=null, $section_id=null, $table=null)
    {	
		 $sql = "SELECT * FROM ( SELECT DISTINCT student_id,student_name, term, course_code, grade  FROM ".$table." where student_id IN(".$listStu.")  AND term = '".$term."' AND semester = '".$semester."' AND course_code = '".$course_code."' AND class_id = '".$class_id."'  AND section_id = '".$section_id."' AND grade NOT IN ('DEB','AB')) AS p";

		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();   
    }
	/*
	* Function : getAllCustomSGraphRecords  :SUM( CASE WHEN sg.grade= 'DEB' THEN 1 ELSE 0 END) AS debarred, 
	*/
	public function getAllCustomSGraphRecords($sterm = CURRENT_TERM)
    {	if(!empty($sterm)){$startStu = substr($sterm, 0, 2);}
		 $sql = "SELECT COUNT(ss.student_id) AS total_student, SUM( CASE WHEN  ss.sgpa >= 0  AND ss.sgpa <= 4.0 THEN 1 ELSE 0 END) AS sgpa1, SUM( CASE WHEN ss.sgpa > 4.0  AND ss.sgpa <= 6.0 THEN 1 ELSE 0 END) AS sgpa2, SUM( CASE WHEN ss.sgpa > 6.0  AND ss.sgpa <= 8.0 THEN 1 ELSE 0 END) AS sgpa3, SUM( CASE WHEN ss.sgpa > 8.0  AND ss.sgpa <= 10 THEN 1 ELSE 0 END) AS sgpa4, ss.term FROM `stu_score_".$sterm."` ss where ss.term = '".$sterm."' AND ss.prog_action NOT IN('WAPP') AND  (ss.registered = 'yes' OR ss.student_id  LIKE '20$startStu%')";

		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();   
    }
	/*
	* Function : getAllCustomSGraphSchoolRecords  :SUM( CASE WHEN sg.grade= 'DEB' THEN 1 ELSE 0 END) AS debarred, 
	*/
	public function getAllCustomSGraphSchoolRecords($school_code=null,$sterm = CURRENT_TERM)
    {	//die('1');
	if(!empty($sterm)){$startStu = substr($sterm, 0, 2);}
		if(!empty($school_code)){
		 $sql = "SELECT COUNT(ss.student_id) AS total_student, SUM( CASE WHEN  ss.sgpa >= 0  AND ss.sgpa <= 4.0 THEN 1 ELSE 0 END) AS sgpa1, SUM( CASE WHEN ss.sgpa > 4.0  AND ss.sgpa <= 6.0 THEN 1 ELSE 0 END) AS sgpa2, SUM( CASE WHEN ss.sgpa > 6.0  AND ss.sgpa <= 8.0 THEN 1 ELSE 0 END) AS sgpa3, SUM( CASE WHEN ss.sgpa > 8.0  AND ss.sgpa <= 10 THEN 1 ELSE 0 END) AS sgpa4, ss.school_code FROM `stu_score_".$sterm."` ss where ss.term = '".$sterm."' AND ss.prog_action NOT IN('WAPP') AND  (ss.registered = 'yes' OR ss.student_id  LIKE '20$startStu%') AND school_code IN('".$school_code."') AND school_code IS NOT NULL GROUP BY school_code";
		}else{
		 $sql = "SELECT COUNT(ss.student_id) AS total_student, SUM( CASE WHEN  ss.sgpa >= 0  AND ss.sgpa <= 4.0 THEN 1 ELSE 0 END) AS sgpa1, SUM( CASE WHEN ss.sgpa > 4.0  AND ss.sgpa <= 6.0 THEN 1 ELSE 0 END) AS sgpa2, SUM( CASE WHEN ss.sgpa > 6.0  AND ss.sgpa <= 8.0 THEN 1 ELSE 0 END) AS sgpa3, SUM( CASE WHEN ss.sgpa > 8.0  AND ss.sgpa <= 10 THEN 1 ELSE 0 END) AS sgpa4, ss.school_code FROM `stu_score_".$sterm."` ss where ss.term = '".$sterm."' AND ss.prog_action NOT IN('WAPP') AND (ss.registered = 'yes' OR ss.student_id  LIKE '20$startStu%') AND school_code IS NOT NULL GROUP BY school_code";
		}

		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();   
    }
	/*
	* Function : getstudentIdSem for particular semester   :SUM( CASE WHEN sg.grade= 'DEB' THEN 1 ELSE 0 END) AS debarred, 
	*/
	public function getstudentIdSem($list=null,$term=null,$semester=null,$table=null)
    {
		// $sql = "SELECT COUNT(DISTINCT(`course_code`)) AS total_courses FROM `stu_course` where plan_code IN(".$list.") AND term = '".$term."' ";
		 $sql = "SELECT student_id FROM `".$table."` where plan_code IN(".$list.") AND term = '".$term."' AND semester = '".$semester."' limit 1 ";

		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); 
		return $query->result_array();   
    }
	/*
	* Function : getAllCourseCount  :SUM( CASE WHEN sg.grade= 'DEB' THEN 1 ELSE 0 END) AS debarred, 
	*/
	public function getAllCourseCount($term=null,$semester=null, $student_id=null, $table=null)
    {
		// $sql = "SELECT COUNT(DISTINCT(`course_code`)) AS total_courses FROM `stu_course` where plan_code IN(".$list.") AND term = '".$term."' ";
		 $sql = "SELECT COUNT(DISTINCT(`course_code`)) AS total_courses FROM `".$table."` where term = '".$term."' AND semester = '".$semester."' AND student_id = '".$student_id."'  ";

		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();   
    }
	/*
	* Function : getAllTotalStudent  :SUM( CASE WHEN sg.grade= 'DEB' THEN 1 ELSE 0 END) AS debarred, 
	*/
	public function getAllTotalStudent($list=null,$term=null,$semester=null,$table=null)
    {
		 $sql = "SELECT COUNT(ss.student_id) as overallstudent FROM `".$table."` ss where plan_code IN(".$list.") AND ss.term = '".$term."' AND semester!='00' AND  ss.semester = '".$semester."'";
    
		 //$sql = "SELECT ss.student_id FROM `stu_score` ss where plan_code IN(".$list.") AND ss.term = '".$term."' AND ss.semester = '".$semester."' AND ss.registered = 'yes'";

		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();   
    }
	public function getAllRegisStudent($list=null,$term=null,$semester=null,$table=null)
    {	if(!empty($term)){$startStu = substr($term, 0, 2);}
		//if($semester=='S1'){
		// $sql = "SELECT ss.student_id FROM `".$table."` ss where plan_code IN(".$list.") AND ss.term = '".$term."' AND ss.semester = '".$semester."' AND  semester!='00' AND ss.prog_action NOT IN('WAPP')";
		//} else{
		 $sql = "SELECT ss.student_id FROM `".$table."` ss where plan_code IN(".$list.") AND ss.term = '".$term."' AND ss.semester = '".$semester."' AND  semester!='00' AND ss.prog_action NOT IN('WAPP') AND (ss.registered = 'yes' OR ss.student_id LIKE '20$startStu%') ";
		// }
    
		 //$sql = "SELECT ss.student_id FROM `stu_score` ss where plan_code IN(".$list.") AND ss.term = '".$term."' AND ss.semester = '".$semester."' AND ss.registered = 'yes'";

		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();   
    }
	public function getAllGradeDistinct($list=null,$term=null,$semester=null,$table=null)
    {
		// $sql = "SELECT grade FROM `stu_course` where plan_code IN(".$list.") AND term = '".$term."' AND semester = '".$semester."' AND grade !=''  ORDER BY FIELD(grade,'O','A+','A','B+','B','C','D','P','Q','NQ','FR','Debared','FI','AB','F') ";
		$sql = "SELECT DISTINCT(grade) FROM `".$table."` where plan_code IN(".$list.") AND term = '".$term."' AND semester = '".$semester."' AND grade !=''  ORDER BY FIELD(grade,'O','A+','A','B+','B','C','D','P','Q','NQ','FR','Debared','FI','AB','F') ";
    
		 //$sql = "SELECT ss.student_id FROM `stu_score` ss where plan_code IN(".$list.") AND ss.term = '".$term."' AND ss.semester = '".$semester."' AND ss.registered = 'yes'";

		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();   
    }
	public function getAllDistinctSection($list=null,$term=null,$semester=null,$table=null)
    {
		 $sql = "SELECT DISTINCT(section_id) FROM `".$table."` where plan_code IN(".$list.") AND term = '".$term."' AND semester = '".$semester."' AND section_id NOT LIKE '%FR%' AND section_id NOT LIKE '%FI%' ";
    
		 //$sql = "SELECT ss.student_id FROM `stu_score` ss where plan_code IN(".$list.") AND ss.term = '".$term."' AND ss.semester = '".$semester."' AND ss.registered = 'yes'";

		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();   
    }
	/*
	* Function : getAllRegisStudent  :SUM( CASE WHEN sg.grade= 'DEB' THEN 1 ELSE 0 END) AS debarred, 
	*/
	public function getAllRegisStudentTotalCourse($list=null, $term=null,$semester=null,$table=null)
    {
		// $sql = "SELECT SUM(total_courses) as total FROM stu_course where student_id IN(".$list.")  AND term = '".$term."'";
		 $sql = "SELECT SUM(total_courses) as total FROM ".$table." where student_id IN(".$list.")  AND term = '".$term."' AND semester = '".$semester."'";

		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();   
    }
	/*
	* Function : getAllRegisStudentDEB  :SUM( CASE WHEN sg.grade= 'DEB' THEN 1 ELSE 0 END) AS debarred, 
	*/
	public function getAllRegisStudentDEB($list=null,$term=null,$semester=null,$grade=null,$table=null)
    {
		 $sql = "SELECT COUNT(*) as total FROM ( SELECT DISTINCT student_id, term, course_code, grade  FROM ".$table." where student_id IN(".$list.")  AND term = '".$term."' AND semester = '".$semester."' AND grade = '".$grade."') AS p";
		// $sql = "SELECT COUNT(*) as total FROM ( SELECT DISTINCT student_id, term, course_code, grade  FROM stu_course where student_id IN(".$list.")  AND term = '".$term."' AND semester = '".$semester."' AND grade = '".$grade."') AS p";
		// $sql = "SELECT COUNT(*) as total FROM stu_course where student_id IN(".$list.")  AND term = '".$term."' AND semester = '".$semester."' AND grade = '".$grade."'";

		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();   
    }
	/*
	* Function : getAllRegisCourseStudentDEB  :SUM( CASE WHEN sg.grade= 'DEB' THEN 1 ELSE 0 END) AS debarred, 
	*/
	public function getAllRegisCourseStudentDEB($list=null,$term=null,$semester=null,$class_id=null,$section_id=null,$grade=null,$table=null)
    {
		 $sql = "SELECT COUNT(*) as total FROM ( SELECT DISTINCT student_id, term, course_code, grade  FROM ".$table." where student_id IN(".$list.")  AND term = '".$term."' AND semester = '".$semester."' AND class_id = '".$class_id."' AND section_id = '".$section_id."' AND grade = '".$grade."') AS p";
		// $sql = "SELECT COUNT(*) as total FROM ( SELECT DISTINCT student_id, term, course_code, grade  FROM stu_course where student_id IN(".$list.")  AND term = '".$term."' AND semester = '".$semester."' AND grade = '".$grade."') AS p";
		// $sql = "SELECT COUNT(*) as total FROM stu_course where student_id IN(".$list.")  AND term = '".$term."' AND semester = '".$semester."' AND grade = '".$grade."'";

		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();   
    }/*
	* Function : getAllCustomCGRecords   
	*/
	public function getAllCustomCGRecords($list=null,$term=null,$semester=null,$table=null)
    {
		if(!empty($term)){$startStu = substr($term, 0, 2);}
		if(!empty($semester) && isset($semester) && !empty($term) && isset($term)){ //die('1');
			$condition = "where semester!='00' AND plan_code IN(".$list.") AND ss.prog_action NOT IN('WAPP') AND ss.term = '".$term."' AND ss.semester = '".$semester."' AND (ss.registered = 'yes' OR (CASE WHEN ss.student_id LIKE '20$startStu%' THEN '1' END )) ";
		}
		else if(!empty($term) && isset($term) && empty($semester)){ //die('2');
			$condition = "where semester!='00' AND plan_code IN(".$list.") AND ss.prog_action NOT IN('WAPP') AND ss.term = '".$term."' AND (ss.registered = 'yes' OR (CASE WHEN ss.student_id LIKE '20$startStu%' THEN '1' END )) GROUP BY semester ";
		}
		else if(empty($semester) && empty($term) && !empty($list) && isset($list)){ //die('3');
			$condition = "where semester!='00' AND  plan_code IN(".$list.") AND ss.prog_action NOT IN('WAPP') AND (ss.registered = 'yes' OR (CASE WHEN ss.student_id  LIKE '20$startStu%' THEN '1' END )) GROUP BY term, semester";
		}
		$sql = "SELECT COUNT(ss.student_id) AS total_student, SUM( CASE WHEN  ss.cgpa >= 0  AND ss.cgpa <= 2.500 THEN 1 ELSE 0 END) AS cgpa1, SUM( CASE WHEN ss.cgpa >= 0  AND ss.cgpa <= 3.5000 THEN 1 ELSE 0 END) AS cgpa2, SUM( CASE WHEN ss.cgpa > 3.5001  AND ss.cgpa <= 4.000 THEN 1 ELSE 0 END) AS cgpa3, SUM( CASE WHEN ss.cgpa > 4.0001  AND ss.cgpa <= 5.000 THEN 1 ELSE 0 END) AS cgpa4, SUM( CASE WHEN ss.cgpa > 5.0001  AND ss.cgpa <= 6.000 THEN 1 ELSE 0 END) AS cgpa5, SUM( CASE WHEN ss.cgpa > 6.0001  AND ss.cgpa <= 8.000 THEN 1 ELSE 0 END) AS cgpa6, SUM( CASE WHEN ss.cgpa > 8.0001  AND ss.cgpa <= 10.0 THEN 1 ELSE 0 END) AS cgpa7, ss.term, ss.semester FROM `".$table."` ss ".$condition."";

		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();   
    }
	/*
	* Function : getAllCustomCGraphRecords   
	*/
	public function getAllCustomCGraphRecords($sterm=CURRENT_TERM)
    {	if(!empty($sterm)){$startStu = substr($sterm, 0, 2);}
		$sql = "SELECT COUNT(ss.student_id) AS total_student, SUM( CASE WHEN  ss.cgpa >= 0  AND ss.cgpa <= 4.0 THEN 1 ELSE 0 END) AS cgpa1, SUM( CASE WHEN ss.cgpa >= 4.0  AND ss.cgpa <= 6.0 THEN 1 ELSE 0 END) AS cgpa2, SUM( CASE WHEN ss.cgpa > 6.0  AND ss.cgpa <= 8.0 THEN 1 ELSE 0 END) AS cgpa3, SUM( CASE WHEN ss.cgpa > 8.0  AND ss.cgpa <= 10 THEN 1 ELSE 0 END) AS cgpa4, ss.term FROM `stu_score_".$sterm."` ss where ss.term = '".$sterm."' AND  ss.prog_action NOT IN('WAPP') AND (ss.registered = 'yes' OR ss.student_id  LIKE '20$startStu%')";

		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();   
    }
	/*
	* Function : getAllCustomCGraphSchoolRecords   
	*/
	public function getAllCustomCGraphSchoolRecords($school_code=null,$sterm=CURRENT_TERM)
    {	if(!empty($sterm)){$startStu = substr($sterm, 0, 2);}
		if(!empty($school_code)){
		$sql = "SELECT COUNT(ss.student_id) AS total_student, SUM( CASE WHEN  ss.cgpa >= 0  AND ss.cgpa <= 4.0 THEN 1 ELSE 0 END) AS cgpa1, SUM( CASE WHEN ss.cgpa >=4.0  AND ss.cgpa <= 6.0 THEN 1 ELSE 0 END) AS cgpa2, SUM( CASE WHEN ss.cgpa > 6.0  AND ss.cgpa <= 8.0 THEN 1 ELSE 0 END) AS cgpa3, SUM( CASE WHEN ss.cgpa > 8.0  AND ss.cgpa <= 10 THEN 1 ELSE 0 END) AS cgpa4, ss.school_code FROM `stu_score_".$sterm."` ss where ss.term = '".$sterm."' AND  ss.prog_action NOT IN('WAPP') AND (ss.registered = 'yes' OR ss.student_id  LIKE '20$startStu%') AND school_code IN('".$school_code."')  and school_code IS NOT NULL GROUP BY school_code ";
		}else{
		$sql = "SELECT COUNT(ss.student_id) AS total_student, SUM( CASE WHEN  ss.cgpa >= 0  AND ss.cgpa <= 4.0 THEN 1 ELSE 0 END) AS cgpa1, SUM( CASE WHEN ss.cgpa >=4.0  AND ss.cgpa <= 6.0 THEN 1 ELSE 0 END) AS cgpa2, SUM( CASE WHEN ss.cgpa > 6.0  AND ss.cgpa <= 8.0 THEN 1 ELSE 0 END) AS cgpa3, SUM( CASE WHEN ss.cgpa > 8.0  AND ss.cgpa <= 10 THEN 1 ELSE 0 END) AS cgpa4, ss.school_code FROM `stu_score_".$sterm."` ss where ss.term = '".$sterm."' AND  ss.prog_action NOT IN('WAPP') AND (ss.registered = 'yes' OR ss.student_id  LIKE '20$startStu%') and school_code IS NOT NULL GROUP BY school_code ";
		}
		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();   
    }
	/*
	* Function : getAllCustomBKLRecords    SUM( CASE WHEN sg.grade= 'DEB' THEN 1 ELSE 0 END) AS debarred, 
	*/
	public function getAllCustomBKLRecords($listStu=null, $list=null, $term=null, $semester=null, $table=null)
    {	
		//if(!empty($semester) && isset($semester) && !empty($term) && isset($term)){ die('1');
		//	$condition = "where student_id IN(".$listStu.") AND plan_code IN(".$list.") AND sc.term = '".$term."' AND semester = '".$semester."'";
		//}
		//else if(!empty($term) && isset($term) && empty($semester)){ die('2');
		//	$condition = "where student_id IN(".$listStu.") AND plan_code IN(".$list.") AND sc.term = '".$term."' GROUP BY semester";
		//}
		//else if(empty($semester) && empty($term) && !empty($list) && isset($list)){ die('3');
		//	$condition = "where student_id IN(".$listStu.") AND plan_code IN(".$list.") GROUP BY term, semester";
		//}
		$sql = "SELECT DISTINCT(student_id),term,semester FROM `".$table."` sc where student_id IN(".$listStu.") AND plan_code IN(".$list.") AND sc.term = '".$term."' AND semester = '".$semester."'";
		//$sql = "SELECT sc.program_code,COUNT(sc.student_id) AS total_student, sc.term, sc.semester, sc.course_code, sc.course_type, sc.grade FROM `stu_course` sc where sc.program_code='".$programme."' AND sc.grade IN('FR','FI','DEB') GROUP BY sc.program_code, sc.term";

		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();
    }
	public function getAllCustomBKLcountRecords($listStu=null, $list=null, $term=null, $semester=null, $table=null)
    {
		$sql = "SELECT COUNT(DISTINCT(student_id)) as county FROM `".$table."` sc where student_id IN(".$listStu.") AND plan_code IN(".$list.") AND sc.term = '".$term."'  AND semester = '".$semester."'";
		//$sql = "SELECT sc.program_code,COUNT(sc.student_id) AS total_student, sc.term, sc.semester, sc.course_code, sc.course_type, sc.grade FROM `stu_course` sc where sc.program_code='".$programme."' AND sc.grade IN('FR','FI','DEB') GROUP BY sc.program_code, sc.term";

		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();   
    }
	public function getAllCustomBKLcntRecords($student_id=null, $term = NULL, $semester = NULL, $table = NULL)
    {
		//$sql = "SELECT COUNT(*) as county FROM `stu_course` sc where sc.student_id='".$student_id."' AND sc.term='".$term."' AND sc.grade IN('FR','FI','DEB','AB')" ;
		$sql = "SELECT COUNT(*) as county FROM ( SELECT DISTINCT student_id, term, semester, course_code, grade  FROM ".$table." where student_id IN(".$student_id.")  AND term = '".$term."'  AND semester = '".$semester."' AND grade IN('FR','FI','DEB','AB')) AS p";
		//$sql = "SELECT COUNT(*) as county FROM `stu_course` sc where sc.student_id='".$student_id."' AND sc.term='".$term."' AND sc.semester='".$semester."'  AND sc.grade IN('FR','FI','DEB','AB')" ;
		

		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();   
    }
	public function getAllCustomBKLPRcntRecords($student_id=null, $term = NULL, $semester = NULL, $table = NULL)
    {
		
		//$sql = "SELECT COUNT(*) as county FROM `stu_course` sc where sc.student_id='".$student_id."' AND sc.term='".$term."' AND sc.course_type IN('PRA','PRJ','VIV', 'LAB', 'PGR', 'SEM', 'CLN', 'FLT','FLW','TNG', 'PNG', 'PJW','DIN','RPT') AND sc.grade IN('FR','FI','DEB','AB')" ;
		
		$sql = "SELECT COUNT(*) as county FROM ( SELECT DISTINCT student_id, term, course_code, grade  FROM ".$table." where student_id IN(".$student_id.")  AND term = '".$term."' AND semester = '".$semester."'  AND course_type IN('PRA','PRJ','VIV', 'LAB', 'PGR', 'SEM', 'CLN', 'FLT','FLW','TNG', 'PNG', 'PJW','DIN','RPT','PRE') AND grade IN('FR','FI','DEB','AB')) AS p";
		//$sql = "SELECT COUNT(*) as county FROM `stu_course` sc where sc.student_id='".$student_id."' AND sc.term='".$term."' AND sc.semester='".$semester."' AND sc.course_type IN('PRA','PRJ','VIV', 'LAB', 'PGR', 'SEM', 'CLN', 'FLT','FLW','TNG', 'PNG', 'PJW','DIN','RPT') AND sc.grade IN('FR','FI','DEB','AB')" ;

		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();   
    }
	public function getAllCustomBKLTPcntRecords($student_id=null, $term = NULL, $semester = NULL, $table = NULL)
    {
		//$sql = "SELECT COUNT(*) as county FROM `stu_course` sc where sc.student_id='".$student_id."' AND sc.term='".$term."' AND sc.course_type IN('LEC','THY') AND sc.grade IN('FR','FI','DEB','AB') " ;
		
		$sql = "SELECT COUNT(*) as county FROM ( SELECT DISTINCT student_id, term, course_code, grade  FROM ".$table." where student_id IN(".$student_id.")  AND term = '".$term."' AND semester = '".$semester."'  AND course_type IN('LEC','THY') AND grade IN('FR','FI','DEB','AB')) AS p";

		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();   
    }
	public function getAllStudentCustomBKLTPcntRecords($listStuRegis=null, $term = NULL, $semester = NULL, $table = NULL)
    {
		//$sql = "SELECT COUNT(*) as county FROM `stu_course` sc where sc.student_id='".$student_id."' AND sc.term='".$term."' AND sc.course_type IN('LEC','THY') AND sc.grade IN('FR','FI','DEB','AB') " ;
		
		$sql = "SELECT COUNT(*) as county,GROUP_CONCAT(student_id) as system_id FROM ( SELECT DISTINCT student_id, term, course_code, grade  FROM ".$table." where student_id IN(".$listStuRegis.")  AND term = '".$term."' AND semester = '".$semester."'  AND course_type IN('LEC','THY') AND grade IN('FR','FI','DEB','AB')) AS p";

		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();   
    }
	public function getAllCustomCrseRecords($listStu=null, $list=null,$term=null,$semester=null,$section_id=null,$table=null)
    {
		if($section_id=='1'){
			$sql = "SELECT sc.program_code,COUNT(DISTINCT(sc.student_id)) AS total_student, sc.term, sc.semester, sc.course_code, sc.course_name, sc.class_id, sc.section_id, sc.teacher_name, sc.grade FROM `".$table."` sc where student_id IN(".$listStu.") AND plan_code IN(".$list.") AND sc.term = '".$term."' AND sc.semester = '".$semester."' AND sc.section_id NOT LIKE '%FR%' AND sc.section_id NOT LIKE '%FI%' GROUP BY sc.course_code, sc.class_id, sc.section_id";
		}else{
			$sql = "SELECT sc.program_code,COUNT(DISTINCT(sc.student_id)) AS total_student, sc.term, sc.semester, sc.course_code, sc.course_name, sc.class_id, sc.section_id, sc.teacher_name, sc.grade FROM `".$table."` sc where student_id IN(".$listStu.") AND plan_code IN(".$list.") AND sc.term = '".$term."' AND sc.semester = '".$semester."' AND sc.section_id IN('".$section_id."') AND sc.section_id NOT LIKE '%FR%' AND sc.section_id NOT LIKE '%FI%' GROUP BY sc.course_code, sc.class_id, sc.section_id";
		}

		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();   
    }
	public function getAllCustomCrseGradeCnt($listStu=null, $course_code=null, $term=null, $semester=null, $class_id=null, $section_id=null,  $grade=null,  $table=null)
    {
		//$sql = "SELECT COUNT(*) AS total_student FROM `stu_course` sc where  student_id IN(".$listStu.") AND sc.course_code='".$course_code."' AND sc.term='".$term."' AND sc.class_id='".$class_id."' AND sc.section_id='".$section_id."' AND sc.grade='".$grade."'";
		$sql = "SELECT COUNT(*) as total_student FROM ( SELECT DISTINCT student_id, course_code, grade  FROM ".$table." sc where student_id IN(".$listStu.") AND sc.course_code='".$course_code."' AND sc.term='".$term."' AND sc.semester='".$semester."' AND sc.class_id='".$class_id."' AND sc.section_id='".$section_id."' AND sc.grade='".$grade."') AS p";
		

		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();   
    }
	public function getAllCustomCrseGradeStudent($listStu=null, $course_code=null, $term=null, $semester=null, $class_id=null, $section_id=null,  $grade=null,  $table=null)
    {
		//$sql = "SELECT COUNT(*) AS total_student FROM `stu_course` sc where  student_id IN(".$listStu.") AND sc.course_code='".$course_code."' AND sc.term='".$term."' AND sc.class_id='".$class_id."' AND sc.section_id='".$section_id."' AND sc.grade='".$grade."'";
		$sql = "SELECT student_id FROM ( SELECT DISTINCT student_id, course_code, grade  FROM ".$table." sc where student_id IN(".$listStu.") AND sc.course_code='".$course_code."' AND sc.term='".$term."' AND sc.semester='".$semester."' AND sc.class_id='".$class_id."' AND sc.section_id='".$section_id."' AND sc.grade='".$grade."') AS p";
		

		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();   
    }
	
	public function getAllCustomGradeRecords($system_id=null, $term=null, $semester=null, $grade=null, $grades=null, $table=null)
    {
		$sql = "SELECT sc.student_id, sc.term, sc.course_code, sc.course_credit FROM `".$table."` sc where sc.student_id='".$system_id."' AND sc.term='".$term."' AND sc.semester='".$semester."' AND sc.grade IN('".$grade."','".$grades."') GROUP BY sc.course_code";
		//$sql = "SELECT sc.student_id, sc.term, sc.course_code, sc.course_credit FROM `stu_course` sc where sc.student_id='".$system_id."' AND sc.term='".$term."' AND sc.grade='".$grade."' OR sc.grade='".$grades."'";

		$query = $this->dbreport->query($sql);
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();   
    }
	
	/* Function : getAllRecords   $this->dbreport->distinct('');
	*/
	public function getAllRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL)
    {
        $time = time();
        $this->dbreport->select($col);
        //$this->dbreport->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->dbreport->where($key, $val);
			}
		}
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$this->dbreport->order_by($key, $val);
			}
		}
		if ($limit !== null && $start !== null) {
           $query = $this->dbreport->get($tbl_name,$limit, $start);
        } else {
			$query = $this->dbreport->get($tbl_name);
		}
		//echo $this->dbreport->last_query(); die;
		return $query->result_array();
    }
	
	/*
	* Function : getSingleRecord
	*/
	public function getSingleRecord($tbl_name, $col = ' * ', $condition=null)
	{
        $time = time();
        $this->dbreport->select($col);
       // $this->dbreport->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->dbreport->where($key, $val);
			}
			
		}
		$query = $this->dbreport->get($tbl_name);
		//echo $this->dbreport->last_query(); die;
        return $query->row_array();
    }
	
	/*
	* Function: getCommonIdArray
	* Parameters: 
	* Purpose:
	* CreatedOn:
	* Createdbreporty:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getCommonIdArray($tbl_name='tbl_schools', $col = ' * ', $condition=null)
    {
        $time = time();
        $this->dbreport->select($col);
        $this->dbreport->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->dbreport->where($key, $val);
			}
			
		}
		//$this->dbreport->order_by('id', 'asc');
        $query = $this->dbreport->get($tbl_name);
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
	* Createdbreporty:
	* ModifiedOn:
	* Modified By:
	* Return:
	*/
	public function getCommonSingleRecord($tbl_name='tbl_schools', $col = ' * ', $condition=null)
	{
        $this->dbreport->select($col);
        $this->dbreport->where('status', '1');
        $this->dbreport->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->dbreport->where($key, $val);
			}
			
		}
		$query = $this->dbreport->get($tbl_name);
		$results = array();
		$results = $query->row_array();
	    return $results;
	}
	

	public function getAllRecordscount($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL)
    {
        $time = time();
        $this->dbreport->select($col);
        $this->dbreport->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				$this->dbreport->where($key, $val);
			}
		}
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				$this->dbreport->order_by($key, $val);
			}
		}
		if ($limit !== null && $start !== null) {
           $query = $this->dbreport->get($tbl_name,$limit, $start);
        } else {
			$query = $this->dbreport->get($tbl_name);
		}
		//echo $this->dbreport->last_query(); die;
		return $query->num_rows();
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
	* Function : getSingleSQLRecord
	* dbreport Connection : dbreport2
	*
	*/
	public function getSingleSQLRecord($tbl_name, $col = ' * ', $condition=null, $order_by = NULL, $where_like=NULL, $where_like_key = 'id', $or_condition = NULL)
	{
		
		$time = time();
		$this->dbreport->select($col);
		$this->dbreport->where('is_deleted', '0');
		if(!empty($where_like)) {
		 $this->dbreport->like($where_like_key, $where_like);
		}
		 
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				 $this->dbreport->where($key, $val);
			}
			
		} 
		if(!empty($or_condition))
		{ 
			foreach($or_condition as $key=>$val) {
				 $this->dbreport->or_where($key, $val);
			}
			
		}
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				 $this->dbreport->order_by($key, $val);
			}
		}
		$query =  $this->dbreport->get($tbl_name);
		//echo  $this->dbreport->last_query(); die;
        Return $query->row(); //die('111');
    }
	
	/*
	* Function : getSQLAllRecords
	* dbreport Connection : dbreport2
	*
	*
	*/
	public function getSQLAllRecords($tbl_name, $col = ' * ', $condition=null, $order_by = NULL,$limit=NULL, $start=NULL, $or_condition = NULL)
    {
		
        $time = time();
         $this->dbreport->select($col);
         $this->dbreport->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				 $this->dbreport->where($key, $val);
			}
		}
		if(!empty($or_condition))
		{ 
			foreach($or_condition as $key=>$val) {
				 $this->dbreport->or_where($key, $val);
			}
		}
		
		if(!empty($order_by)){
			foreach($order_by as $key=>$val) {
				 $this->dbreport->order_by($key, $val);
			}
		}
		if ($limit !== null && $start !== null) {
           $query =  $this->dbreport->get($tbl_name,$limit, $start);
        } else {
			$query =  $this->dbreport->get($tbl_name);
		}
		//echo $this->dbreport->last_query(); die;
		return $query->result();
    }
	
	
	/*
	* Function : SqlgetSingleRecord
	* dbreport Connection : dbreport2
	*/
	public function SqlgetSingleRecord($tbl_name, $col = ' * ', $condition=null)
	{
		 $this->dbreport = $this->load->database('dbreport2', TRUE);
        $time = time();
         $this->dbreport->select($col);
       // $this->dbreport->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				 $this->dbreport->where($key, $val);
			}
			
		}
		$query =  $this->dbreport->get($tbl_name);
		//echo $this->dbreport->last_query(); die;
        return $query->row_array();
    }

	/*
	* Function : SqlgetSingleRecord
	* dbreport Connection : dbreport2
	*/
	public function SqlgetCommonIdArray($tbl_name='tbl_schools', $col = ' * ', $condition=null)
    {
		 $this->dbreport = $this->load->database('dbreport2', TRUE);
        $time = time();
         $this->dbreport->select($col);
         $this->dbreport->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				 $this->dbreport->where($key, $val);
			}
			
		}
		// $this->dbreport->order_by('id', 'asc');
        $query =  $this->dbreport->get($tbl_name);
		$results = array();
		foreach($query->result_array() as $row) {
			$results[$row['id']] = $row;
		}
        return $results;
	}

	/*
	* Function : SqlgetCommonQuery
	* dbreport Connection : dbreport2
	*/
	
	public function SqlgetCommonQuery($tbl_name = 'su_schools', $col = ' * ', $condition='',$order_by='')
    {
		 $this->dbreport = $this->load->database('dbreport2', TRUE);
         $this->dbreport->select($col);
		 $this->dbreport->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				 $this->dbreport->where($key, $val);
			}
			
		}
		if(!empty($order_by))
		{ 
			foreach($order_by as $key=>$val) {
				 $this->dbreport->order_by($key, $val);
			}		
		}
        $query =  $this->dbreport->get($tbl_name);
        return $query->result_array();
    }
	/*
	* Function : SqlgetCommon
	* dbreport Connection : dbreport2
	*/
	
	public function SqlgetCommon($tbl_name = 'tbl_school_master', $col = ' * ', $condition='',$order_by='')
    {
		
         $this->dbreport->select($col);
		 $this->dbreport->where('is_deleted', '0');
		if(!empty($condition))
		{ 
			foreach($condition as $key=>$val) {
				 $this->dbreport->where($key, $val);
			}
			
		}
		if(!empty($order_by))
		{ 
			foreach($order_by as $key=>$val) {
				 $this->dbreport->order_by($key, $val);
			}		
		}
        $query =  $this->dbreport->get($tbl_name);
        return $query->result_array();
    }
	public function backlog(){
		// $sql = "SELECT student_backlog.system_id, student_backlog.current_term, student_backlog.subject, student_backlog.catalog_nbr, student_backlog.subject_name, student_backlog.course_credit, student_details.rollno, student_details.name FROM student_backlog LEFT JOIN student_details ON student_backlog.system_id = student_details.system_id";
		//$sql = "SELECT student_backlog.system_id, student_backlog.current_term, GROUP_CONCAT(student_backlog.subject separator ', ') AS subject, GROUP_CONCAT(student_backlog.catalog_nbr separator ', ') AS catalog_nbr, GROUP_CONCAT(student_backlog.subject_name separator ', ') AS subject_name, GROUP_CONCAT(student_backlog.course_credit separator ', ') AS course_credit, student_details.rollno, student_details.name FROM student_backlog LEFT JOIN student_details ON student_backlog.system_id = student_details.system_id GROUP BY student_backlog.system_id";
		//$sql = "SELECT * FROM student_backlog";
		$sql = "SELECT system_id,current_term,GROUP_CONCAT(student_backlog.catalog_nbr separator ', ') AS catalog_nbr, GROUP_CONCAT(student_backlog.course_credit separator ', ') AS course_credit FROM `student_backlog` WHERE `current_term`=2102 GROUP BY system_id;
";
		$query = $this->dbreport->query($sql);
	
		return $query->result_array();
	}

}

