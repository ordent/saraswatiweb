<?php

class Model_layout extends CI_Model {

	var $table_configs = "configs";
	var $table_what_we_do = "what_we_dos";
	var $table_programs = "programs";

	function __construct()
	{
		parent::__construct();
	}
	
	function getWhatWeDoDropdown()
	{
		$this->db->where($this->table_what_we_do.'.is_enabled',1);
		$query = $this->db->get($this->table_what_we_do);
		return $query;
	}

	function getPrograms()
	{
		$this->db->select($this->table_programs.'.name');
		$this->db->where($this->table_programs.'.types','parent');
		$this->db->where($this->table_programs.'.is_enabled',1);
		$query = $this->db->get($this->table_programs);
		return $query;
	}

	function getProgramLinks()
	{
		$this->db->select($this->table_programs.'.slug');
		$this->db->where($this->table_programs.'.types','sub');
		$this->db->where($this->table_programs.'.is_enabled',1);
		$this->db->order_by($this->table_programs.'.id','desc');
		$this->db->limit(1);
		$query = $this->db->get($this->table_programs);
		return $query;
	}

	function getMeta()
	{
		$this->db->select("meta_title,meta_keyword,meta_description,meta_author");
		$query = $this->db->get($this->table_configs);
		return $query;
	}

}