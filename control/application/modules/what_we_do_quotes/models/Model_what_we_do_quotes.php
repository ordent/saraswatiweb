<?php
class Model_what_we_do_quotes extends CI_Model {

	var $table = "what_we_do_quotes";
	var $tbl_ref_what_we_dos = "what_we_dos";
	var $table_multi = "";
	
	function __construct()
	{
		parent::__construct();
	}
	
	function getTotal($sch_pack)
	{
		if($sch_pack){
			if($sch_pack['sch1_parm'] != 'null' && !empty($sch_pack['sch1_parm']))
			{
				$this->db->like($this->table.'.name',$sch_pack['sch1_parm']);
			}
		
			if($sch_pack['sch2_parm'] != 'null')
			{
				$this->db->where($this->table.'.is_enabled',$sch_pack['sch2_parm']);
			}

			if($sch_pack['sch3_parm'] != 'null' && !empty($sch_pack['sch3_parm']))
			{
				$this->db->like($this->table.'.what_we_dos_id',$sch_pack['sch3_parm']);
			}
		}
		
		
		$this->db->select("COUNT(id) AS total");
		$query = $this->db->get($this->table);
		$r = $query->row();
		return $r->total;
	}
	
	function getList($per_page,$lmt,$sch_pack)
	{
		if($sch_pack){
			if($sch_pack['sch1_parm'] != 'null' && !empty($sch_pack['sch1_parm']))
			{
				$this->db->like($this->table.'.name',$sch_pack['sch1_parm']);
			}
		
			if($sch_pack['sch2_parm'] != 'null')
			{
				$this->db->where($this->table.'.is_enabled',$sch_pack['sch2_parm']);
			}

			if($sch_pack['sch3_parm'] != 'null' && !empty($sch_pack['sch3_parm']))
			{
				$this->db->like($this->table.'.what_we_dos_id',$sch_pack['sch3_parm']);
			}
		}
		
		$this->db->select($this->table.".*,
						 (SELECT name FROM tbl_what_we_dos WHERE tbl_what_we_dos.id = tbl_".$this->table.".what_we_dos_id) AS ref_what_we_dos_name");
		$this->db->order_by($this->table.".id","desc");
		$query = $this->db->get($this->table,$per_page,$lmt);

		return $query;
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
	
	function setInsert($data_pack)
	{
		$this->db->insert($this->table,$data_pack);
		$last_id = $this->db->insert_id();
		return $last_id;
	}
	

	function setDelete($id)
	{
		$status = 0;
		#select first
		$this->db->where('id',$id);
		$this->db->where('is_enabled',1);
		
		$query = $this->db->get($this->table);
		
		if($query->num_rows() == 0){
			
			if($status == 0){
				$this->db->where('id',$id);
				$this->db->delete($this->table);

				$status = 1;
			}
		}
		return $status;
	}
	
}
?>
