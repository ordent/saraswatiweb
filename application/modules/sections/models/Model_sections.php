<?php

class Model_sections extends CI_Model {

	var $table_ref_social = "ref_socials";
	var $table_about = "abouts";
	var $table_what_we_do = "what_we_dos";
	var $table_programs = "programs";
	var $table_program_photos = "program_photos";
	var $table_devi = "devi";
	var $table_devi_photos = "devi_photos";
	var $table_blog = "blogs";
	var $table_team = "teams";
	var $table_partner = "partners";
	var $table_contact = "contacts";

	function __construct()
	{
		parent::__construct();
	}

	function getBlog()
	{
		$this->db->where($this->table_blog.'.is_enabled',1);
		$this->db->order_by($this->table_blog.'.id','desc');
		$this->db->limit(6);
		$query = $this->db->get($this->table_blog);
		return $query;
	}

	function getSocial()
	{
		$this->db->where("is_enabled",1);
		$this->db->order_by("created_date","asc");
		$q = $this->db->get($this->table_ref_social);
		return $q;
	}

	function getAbout()
	{
		$query = $this->db->get($this->table_about);
		return $query;
	}

	function getWhatWeDo($slug=null)
	{
		$this->db->where($this->table_what_we_do.'.slug',$slug);
		$this->db->where($this->table_what_we_do.'.is_enabled',1);
		$query = $this->db->get($this->table_what_we_do);
		//$this->db->last_query($query);
		return $query;
	}

	function getPrograms()
	{
		$this->db->where($this->table_programs.'.types','parent');
		$query = $this->db->get($this->table_programs);
		return $query;
	}

	function getSubPrograms($id=null)
	{
		if($id){
			$this->db->where($this->table_programs.'.id',$id);
		}

		$this->db->where($this->table_programs.'.types','sub');
		$this->db->where($this->table_programs.'.is_enabled',1);
		$this->db->order_by($this->table_programs.'.id','desc');
		$query = $this->db->get($this->table_programs);
		return $query;
	}

	function getProgramPhotos($id=null)
	{
		if($id){
			$this->db->where($this->table_program_photos.'.programs_id',$id);
		}
		$this->db->where($this->table_program_photos.'.is_enabled',1);
		$query = $this->db->get($this->table_program_photos);
		return $query;
	}

	function getDevi()
	{
		$query = $this->db->get($this->table_devi);
		return $query;
	}

	function getDeviPhotos()
	{
		$this->db->where($this->table_devi_photos.'.is_enabled',1);
		$this->db->order_by($this->table_devi_photos.'.created_date','desc');
		$query = $this->db->get($this->table_devi_photos);
		return $query;
	}

	function getTeam()
	{
		$this->db->where($this->table_team.'.is_enabled',1);
		$this->db->order_by($this->table_team.'.created_date','desc');
		$this->db->limit(20);
		$query = $this->db->get($this->table_team);
		return $query;
	}

	function getPartner()
	{
		$this->db->where($this->table_partner.'.is_enabled',1);
		$this->db->order_by($this->table_partner.'.created_date','desc');
		$this->db->limit(25);
		$query = $this->db->get($this->table_partner);
		return $query;
	}

	function getContact()
	{
		$query = $this->db->get($this->table_contact);
		return $query;
	}

}