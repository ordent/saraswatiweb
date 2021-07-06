<?php
class Model_configs extends CI_Model {
	
	var $table = "configs";
	
	function __construct()
	{
		parent::__construct();
	}

	

	function getDetail($id)
	{
		$this->db->where($this->table.'.id',$id);
		$query = $this->db->get($this->table);
		return $query;
	}
	
	
	function setUpdate($data_pack,$where_pack)
	{	
		$this->db->update($this->table,$data_pack,$where_pack);
	}

	function setInsert($data_pack,$where_pack)
	{	
		$this->db->insert($this->table,$data_pack);
	}
	
}
?>