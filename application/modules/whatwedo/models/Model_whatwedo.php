<?php

class Model_whatwedo extends CI_Model {

	var $table_what_we_do = "what_we_dos";
	var $table_what_we_do_photos = "what_we_do_photos";
	var $table_what_we_do_quotes = "what_we_do_quotes";

	function __construct()
	{
		parent::__construct();
	}

	function getDetail($id)
	{
		$this->db->where($this->table_what_we_do.'.id',$id);
		$query = $this->db->get($this->table_what_we_do);
		return $query;
	}

	function getPhotos($id)
	{
		$this->db->where($this->table_what_we_do_photos.'.what_we_dos_id',$id);
		$this->db->where($this->table_what_we_do_photos.'.is_enabled',1);
		$this->db->order_by($this->table_what_we_do_photos.'.created_date','desc');
		$this->db->limit(2);
		$query = $this->db->get($this->table_what_we_do_photos);
		return $query;
	}

	function getQuotes($id)
	{
		$this->db->where($this->table_what_we_do_quotes.'.what_we_dos_id',$id);
		$this->db->where($this->table_what_we_do_quotes.'.is_enabled',1);
		$this->db->order_by($this->table_what_we_do_quotes.'.created_date','desc');
		$this->db->limit(2);
		$query = $this->db->get($this->table_what_we_do_quotes);
		return $query;
	}

}