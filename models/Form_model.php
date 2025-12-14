<?php

class Form_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	/*
	* Function: setForm
	* Description : set form format
	*/

	function setForm($post) {
	   if($post['form_data']!=''){
		$this->load->dbforge();
		//$post['banner_url'] = str_replace(' ','-',$post['banner_url']); // Replace space with -
		$column = array();
		$fields=json_decode($post['form_data']);
		foreach($fields as $field)
		{
			switch($field->type){
				case "text":
				case "select":
				case "radio-group":
				case "checkbox-group":
					$column = array($field->name => array(
					'type' => 'VARCHAR',
					'constraint' => 250,
					'unsigned' => TRUE
					));
					if (!$this->db->field_exists($field->name, $post['table'])) {
						$this->dbforge->add_column($post['table'], $column);
					 } else {
						$this->dbforge->modify_column($post['table'], $column); 
					 }
					break;		
				
				case "textarea":
					$column = array($field->name => array(
					'type' => 'TEXT',
					'constraint' => 500,
					'unsigned' => TRUE
					));
					if (!$this->db->field_exists($field->name, $post['table'])) {
						$this->dbforge->add_column($post['table'], $column);
					 } else {
						$this->dbforge->modify_column($post['table'], $column); 
					 }
					break;
					
				case "file":
					$column = array($field->name => array(
					'type' => 'TEXT',
					'constraint' => 250,
					'unsigned' => TRUE
					));
					if (!$this->db->field_exists($field->name, $post['table'])) {
						$this->dbforge->add_column($post['table'], $column);
					 } else {
						$this->dbforge->modify_column($post['table'], $column); 
					 }
					break;
				case "date":
					$column = array($field->name => array(
					'type' => 'date',
					));
					if (!$this->db->field_exists($field->name, $post['table'])) {
						$this->dbforge->add_column($post['table'], $column);
					 } else {
						$this->dbforge->modify_column($post['table'], $column); 
					 }
					break;	
				default:
				break;	
			}
		}
		$criteria_id = $post['criteria_id'];
		$sub_table = $post['sub_table'];
		$edit = $post['edit'];
		unset($post['edit']);
		unset($post['table']);
		unset($post['sub_table']);
		unset($post['criteria_id']);
		if ( $edit> 0) {
			$this->db->where('id', $edit);

			if (!$this->db->update('tbl_nacc_form', $post)) {
				print_r($this->db->error());
				log_message('error', print_r($this->db->error(), true));
				show_error(lang('database_error'));
			}
			return $edit;
			
		} else {
			if (!$this->db->insert('tbl_nacc_form', $post)) {
				print_r($this->db->error()); 
				log_message('error', print_r($this->db->error(), true));
				show_error(lang('database_error'));
			} else {
				$last_id = $this->db->insert_id();
				// Update stz_naaccriterias
				$params = array();
				$params = array('form_id'=>$last_id);
				$this->db->where('id', $criteria_id);
				$this->db->update($sub_table, $params);
				return $last_id;
			}
		} } else {
			return 403;
		}
	}
	
	/*
	* Function: createTable
	* Description : set form format
	*/

	function createTable($post,$table) {
		$this->load->dbforge();
		//$post['banner_url'] = str_replace(' ','-',$post['banner_url']); // Replace space with -
		$fields = array(
			'id' => array(
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'auto_increment' => TRUE
			),
			'status' => array(
				'type' => 'ENUM("0","1")',
				'default' => '1',
				'null' => FALSE,
			),
			'is_deleted' => array(
				'type' => 'ENUM("0","1")',
				'default' => '0',
				'null' => FALSE,
			),
			'display_order' => array(
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'auto_increment' => FALSE,
				'null' => TRUE
			),
			'created_on' => array(
				'type' => 'DATETIME',
				'null' => TRUE
			),
			'modifiedon' => array(
				'type' => 'DATETIME',
				'null' => TRUE
			),
			'verify_status' => array(
				'type' => 'ENUM("0","1","2")',
				'default' => '0',
				'null' => FALSE,
			),
			'uploaded_by' => array(
				'type' => 'VARCHAR(150)',
				'default' => 'NULL',
				'null' => TRUE,
			),
			'uploaded_type' => array(
				'type' => 'TINYINT(2)',
				'default' => '0',
				'null' => FALSE,
			),
			'school_id' => array(
				'type' => 'VARCHAR(200)',
				'default' => 'NULL',
				'null' => TRUE,
			),
			'department_id' => array(
				'type' => 'VARCHAR(200)',
				'default' => 'NULL',
				'null' => TRUE,
			),
			'year' => array(
				'type' => 'VARCHAR(50)',
				'default' => 'NULL',
				'null' => TRUE,
			)			
        );
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		if(!$this->dbforge->create_table($table, TRUE)){
			echo $this->db->last_query();echo '</br>';
			print_r($this->db->error());
				log_message('error', print_r($this->db->error(), true));
				show_error(lang('database_error'));
		}
	}

	/*
		* Function : getFrom
		* Description : Get specific data
	*/

	function getForm($id) {

		$this->db->select('tbl_submodule_form.form_data');
		$this->db->from('tbl_submodule_form');
		$this->db->where('tbl_submodule_form.id', $id);
		$queryResult = $this->db->get();
		//echo $this->db->last_query(); die;
		return $queryResult->row_array(); 
	}

	/*
	* Function: setFormForPolicy
	* Description : set form format
	*/

	function setFormForPolicy($post) {
		print_r($post); die;
		if($post['form_data']!=''){
		 $this->load->dbforge();
		 //$post['banner_url'] = str_replace(' ','-',$post['banner_url']); // Replace space with -
		 $column = array();
		 $fields=json_decode($post['form_data']);
		 foreach($fields as $field)
		 {
			 switch($field->type){
				 case "text":
				 case "select":
				 case "radio-group":
				 case "checkbox-group":
					 $column = array($field->name => array(
					 'type' => 'VARCHAR',
					 'constraint' => 250,
					 'unsigned' => TRUE
					 ));
					 if (!$this->db->field_exists($field->name, $post['table'])) {
						$this->dbforge->add_column($post['table'], $column);
					 } else {
						$this->dbforge->modify_column($post['table'], $column); 
					 }
					 break;		
				 
				 case "textarea":
					 $column = array($field->name => array(
					 'type' => 'TEXT',
					 'constraint' => 500,
					 'unsigned' => TRUE
					 ));
					 if (!$this->db->field_exists($field->name, $post['table'])) {
						$this->dbforge->add_column($post['table'], $column);
					 } else {
						$this->dbforge->modify_column($post['table'], $column); 
					 }
					 break;
					 
				 case "file":
					 $column = array($field->name => array(
					 'type' => 'TEXT',
					 'constraint' => 250,
					 'unsigned' => TRUE
					 ));
					 if (!$this->db->field_exists($field->name, $post['table'])) {
						$this->dbforge->add_column($post['table'], $column);
					 } else {
						$this->dbforge->modify_column($post['table'], $column); 
					 }
					 break;
				 case "date":
					 $column = array($field->name => array(
					 'type' => 'date',
					 ));
					 if (!$this->db->field_exists($field->name, $post['table'])) {
						$this->dbforge->add_column($post['table'], $column);
					 } else {
						$this->dbforge->modify_column($post['table'], $column); 
					 }
					 break;	
				 default:
				 break;	
			 }
		 }
		 $category_id = $post['category_id'];
		 $sub_table = $post['sub_table'];
		 $edit = $post['edit'];
		 unset($post['edit']);
		 unset($post['table']);
		 unset($post['sub_table']);
		 unset($post['category_id']);
		 if ( $edit> 0) {
			 $this->db->where('id', $edit);
 
			 if (!$this->db->update('tbl_policy_form', $post)) {
				 print_r($this->db->error());
				 log_message('error', print_r($this->db->error(), true));
				 show_error(lang('database_error'));
			 }
			 return $edit;
			 
		 } else {
			 if (!$this->db->insert('tbl_policy_form', $post)) {
				 print_r($this->db->error()); 
				 log_message('error', print_r($this->db->error(), true));
				 show_error(lang('database_error'));
			 } else {
				 $last_id = $this->db->insert_id();
				 // Update tbl_policies_category
				 $params = array();
				 $params = array('form_id'=>$last_id);
				 $this->db->where('id', $category_id);
				 $this->db->update($sub_table, $params);
				 return $last_id;
			 }
		 } } else {
			 return 403;
		 }
	 }

	 function getFormForPolicy($id) {

		$this->db->select('tbl_policy_form.form_data');
		$this->db->from('tbl_policy_form');
		$this->db->where('tbl_policy_form.id', $id);
		$queryResult = $this->db->get();
		//echo $this->db->last_query(); die;
		return $queryResult->row_array();
	}
	
	/*
	* Function : getAllRecords
	*/
	 function getAllRecords($id='') {

		$this->db->select('id, form_data');
		$this->db->from('tbl_nacc_form');
		if($id>0) {
			$this->db->where('tbl_nacc_form.id', $id);
		}
		$queryResult = $this->db->get();
		//echo $this->db->last_query(); die;
		if ($queryResult !== false) {
            foreach ($queryResult->result_array() as $row) {
                $arr[] = $row;
            }
        }
		
		return $arr;
	}
	
	/*
	* Function: updateBulkForm
	* Description : set form format
	*/

	function updateBulkForm($post) {
	   if($post['form_data']!=''){
		$this->load->dbforge();
		//$post['banner_url'] = str_replace(' ','-',$post['banner_url']); // Replace space with -
		$column = array();
		$fields=json_decode($post['form_data']);
		
		foreach($fields as $field)
		{
			switch($field->type){
				case "text":
				case "select":
				case "radio-group":
				case "checkbox-group":
					$column = array($field->name => array(
					'type' => 'VARCHAR',
					'constraint' => 250,
					'unsigned' => TRUE
					));
					if (!$this->db->field_exists($field->name, $post['table'])) {
						$this->dbforge->add_column($post['table'], $column);
					 } else {
						$this->dbforge->modify_column($post['table'], $column); 
					 }
					break;		
				
				case "textarea":
					$column = array($field->name => array(
					'type' => 'TEXT',
					'constraint' => 500,
					'unsigned' => TRUE
					));
					if (!$this->db->field_exists($field->name, $post['table'])) {
						$this->dbforge->add_column($post['table'], $column);
					 } else {
						$this->dbforge->modify_column($post['table'], $column); 
					 }
					break;
					
				case "file":
					$column = array($field->name => array(
					'type' => 'TEXT',
					'constraint' => 250,
					'unsigned' => TRUE
					));
					if (!$this->db->field_exists($field->name, $post['table'])) {
						$this->dbforge->add_column($post['table'], $column);
					 } else {
						$this->dbforge->modify_column($post['table'], $column); 
					 }
					break;
				case "date":
					$column = array($field->name => array(
					'type' => 'date',
					));
					if (!$this->db->field_exists($field->name, $post['table'])) {
						$this->dbforge->add_column($post['table'], $column);
					 } else {
						$this->dbforge->modify_column($post['table'], $column); 
					 }
					break;	
				default:
				break;	
			}
		}
		
		$edit = $post['edit'];
		unset($post['edit']);
		unset($post['table']);
		if ( $edit> 0) {
			$this->db->where('id', $edit);
			if (!$this->db->update('tbl_nacc_form', $post)) {
				print_r($this->db->error());
				log_message('error', print_r($this->db->error(), true));
				show_error(lang('database_error'));
			}
			return $edit;
			
		} } else {
			return 403;
		}
	}

	/*
	* Function: setFormForAuthorities
	* Description : set form format
	*/

	function setFormForAuthorities($post) {
		if($post['form_data']!=''){
		 $this->load->dbforge();
		 //$post['banner_url'] = str_replace(' ','-',$post['banner_url']); // Replace space with -
		 $column = array();
		 $fields=json_decode($post['form_data']);
		 foreach($fields as $field)
		 {
			 switch($field->type){
				 case "text":
				 case "select":
				 case "radio-group":
				 case "checkbox-group":
					 $column = array($field->name => array(
					 'type' => 'VARCHAR',
					 'constraint' => 250,
					 'unsigned' => TRUE
					 ));
					 if (!$this->db->field_exists($field->name, $post['table'])) {
						$this->dbforge->add_column($post['table'], $column);
					 } else {
						$this->dbforge->modify_column($post['table'], $column); 
					 }
					 break;		
				 
				 case "textarea":
					 $column = array($field->name => array(
					 'type' => 'TEXT',
					 'constraint' => 500,
					 'unsigned' => TRUE
					 ));
					 if (!$this->db->field_exists($field->name, $post['table'])) {
						$this->dbforge->add_column($post['table'], $column);
					 } else {
						$this->dbforge->modify_column($post['table'], $column); 
					 }
					 break;
					 
				 case "file":
					 $column = array($field->name => array(
					 'type' => 'TEXT',
					 'constraint' => 250,
					 'unsigned' => TRUE
					 ));
					 if (!$this->db->field_exists($field->name, $post['table'])) {
						$this->dbforge->add_column($post['table'], $column);
					 } else {
						$this->dbforge->modify_column($post['table'], $column); 
					 }
					 break;
				 case "date":
					 $column = array($field->name => array(
					 'type' => 'date',
					 ));
					 if (!$this->db->field_exists($field->name, $post['table'])) {
						$this->dbforge->add_column($post['table'], $column);
					 } else {
						$this->dbforge->modify_column($post['table'], $column); 
					 }
					 break;	
				 default:
				 break;	
			 }
		 }
		 $category_id = $post['category_id'];
		 $sub_table = $post['sub_table'];
		 $edit = $post['edit'];
		 unset($post['edit']);
		 unset($post['table']);
		 unset($post['sub_table']);
		 unset($post['category_id']);
		 if ( $edit> 0) {
			 $this->db->where('id', $edit);
 
			 if (!$this->db->update('tbl_authorities_form', $post)) {
				 print_r($this->db->error());
				 log_message('error', print_r($this->db->error(), true));
				 show_error(lang('database_error'));
			 }
			 return $edit;
			 
		 } else {
			 if (!$this->db->insert('tbl_authorities_form', $post)) {
				 print_r($this->db->error()); 
				 log_message('error', print_r($this->db->error(), true));
				 show_error(lang('database_error'));
			 } else {
				 $last_id = $this->db->insert_id();
				 // Update tbl_policies_category
				 $params = array();
				 $params = array('form_id'=>$last_id);
				 $this->db->where('id', $category_id);
				 $this->db->update($sub_table, $params);
				 return $last_id;
			 }
		 } } else {
			 return 403;
		 }
	 }

	 function getFormForAuthorities($id) {

		$this->db->select('tbl_authorities_form.form_data');
		$this->db->from('tbl_authorities_form');
		$this->db->where('tbl_authorities_form.id', $id);
		$queryResult = $this->db->get();
		//echo $this->db->last_query(); die;
		return $queryResult->row_array();
	}

	/*
	* Function: createTableForPolicy
	* Description : set form format
	*/

	function createTableForPolicy($post,$table) {
		$this->load->dbforge();
		//$post['banner_url'] = str_replace(' ','-',$post['banner_url']); // Replace space with -
		$fields = array(
			'id' => array(
				'type' => 'BIGINT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'auto_increment' => TRUE
			),
			'status' => array(
				'type' => 'TINYINT',
				'constraint' => 2,
				'default' => '1',
				'null' => FALSE,
			),
			'is_deleted' => array(
				'type' => 'TINYINT',
				'constraint' => 2,
				'default' => '0',
				'null' => FALSE,
			),
			'display_order' => array(
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => TRUE,
				'auto_increment' => FALSE,
				'null' => TRUE
			),
			'created_on' => array(
				'type' => 'DATETIME',
				'null' => TRUE
			),
			'modifiedon' => array(
				'type' => 'DATETIME',
				'null' => TRUE
			),
			'slug' => array(
				'type' => 'VARCHAR(250)',
				'default' => 'NULL',
				'null' => TRUE,
			)			
        );
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		//$table = 'ems_'.$table;
		if(!$this->dbforge->create_table($table, TRUE)){
			echo $this->db->last_query();echo '</br>';
			print_r($this->db->error());
				log_message('error', print_r($this->db->error(), true));
				show_error(lang('database_error'));
		}
	}

	/*
	* Function: setFormForAuthorities
	* Description : set form format
	*/

	function setFormForSubproducts($post) {
		if($post['form_data']!=''){
		 $this->load->dbforge();
		 //$post['banner_url'] = str_replace(' ','-',$post['banner_url']); // Replace space with -
		 $column = array();
		 $fields=json_decode($post['form_data']);
		 foreach($fields as $field)
		 {
			 switch($field->type){
				 case "text":
				 case "select":
				 case "radio-group":
				 case "checkbox-group":
					 $column = array($field->name => array(
					 'type' => 'VARCHAR',
					 'constraint' => 250,
					 'unsigned' => TRUE
					 ));
					if (!$this->db->field_exists($field->name, $post['table'])) {
						$this->dbforge->add_column($post['table'], $column); 
					} else {
						$this->dbforge->modify_column($post['table'], $column); 
					}
					break; 	
				 
				 case "textarea":
					 $column = array($field->name => array(
					 'type' => 'TEXT',
					 'constraint' => 500,
					 'unsigned' => TRUE
					 ));
					 if (!$this->db->field_exists($field->name, $post['table'])) {
						$this->dbforge->add_column($post['table'], $column);
					 } else {
						$this->dbforge->modify_column($post['table'], $column); 
					 }
					 break;
					 
				 case "file":
					 $column = array($field->name => array(
					 'type' => 'TEXT',
					 'constraint' => 250,
					 'unsigned' => TRUE
					 ));
					 if (!$this->db->field_exists($field->name, $post['table'])) {
						$this->dbforge->add_column($post['table'], $column);
					} else {
						$this->dbforge->modify_column($post['table'], $column); 
					}
					break; 
				 case "date":
					 $column = array($field->name => array(
					 'type' => 'date',
					 ));
					if (!$this->db->field_exists($field->name, $post['table'])) {
						$this->dbforge->add_column($post['table'], $column);
					} else {
						$this->dbforge->modify_column($post['table'], $column); 
					}
					break; 
				 default:
				 break;	
			 }
		 }
		 $submodule_id = $post['submodule_id'];
		 $sub_table = $post['sub_table'];
		 $edit = $post['edit'];
		 unset($post['edit']);
		 unset($post['table']);
		 unset($post['sub_table']);
		 unset($post['submodule_id']);
		 if ( $edit> 0) {
			 $this->db->where('id', $edit);
 
			 if (!$this->db->update('tbl_submodule_form', $post)) {
				 print_r($this->db->error());
				 log_message('error', print_r($this->db->error(), true));
				 show_error(lang('database_error'));
			 }
			 return $edit;
			 
		 } else {
			 if (!$this->db->insert('tbl_submodule_form', $post)) {
				 print_r($this->db->error()); 
				 log_message('error', print_r($this->db->error(), true));
				 show_error(lang('database_error'));
			 } else {
				 $last_id = $this->db->insert_id();
				 // Update stz_subproducts
				 $params = array();
				 $params = array('form_id'=>$last_id);
				 $this->db->where('id', $submodule_id);
				 $this->db->update($sub_table, $params);
				 return $last_id;
			 }
		 } } else {
			 return 403;
		 }
	 }

	 function getFormForSubModule($id) {

		$this->db->select('tbl_submodule_form.form_data');
		$this->db->from('tbl_submodule_form');
		$this->db->where('tbl_submodule_form.id', $id);
		$queryResult = $this->db->get();
		//echo $this->db->last_query(); die;
		return $queryResult->row_array();
	}

	
}
