<?php
class Model_adminuser_levels extends CI_Model {
	
	var $table = "adminuser_levels";
	
	function __construct()
	{
		parent::__construct();
		$this->groupID = $this->session->userdata('sess_admin')['adminuser_levels_id'];
	}
	
	
	function getTotal($sch_pack)
	{
		if($sch_pack){
			if($sch_pack['sch1_parm'] != 'null' && !empty($sch_pack['sch1_parm']))
			{
				$this->db->like($this->table.'.title',$sch_pack['sch1_parm']);
			}
		
			if($sch_pack['sch2_parm'] != 'null' && !empty($sch_pack['sch2_parm']))
			{
				$this->db->where($this->table.'.is_enabled',$sch_pack['sch2_parm']);
			}
		}
		
		if($this->groupID <> 1){
			$this->db->where($this->table.".id !=",1);
		}
		$this->db->select("COUNT(id) AS total");
		$query = $this->db->get($this->table);
		$r = $query->row();
		return $r->total;
	}
	
	function getList($per_page,$lmt,$sch_pack,$refDropdown=NULL)
	{
		if($sch_pack){
			if($sch_pack['sch1_parm'] != 'null' && !empty($sch_pack['sch1_parm']))
			{
				$this->db->like($this->table.'.title',$sch_pack['sch1_parm']);
			}
		
			if($sch_pack['sch2_parm'] != 'null' && !empty($sch_pack['sch2_parm']))
			{
				$this->db->where($this->table.'.is_enabled',$sch_pack['sch2_parm']);
			}
		}
		
		if($this->groupID <> 1){
			$this->db->where($this->table.".id !=",1);
		}
		
		if($refDropdown){
			$this->db->order_by($this->table.'.created_date','asc');
		}else{
			$this->db->order_by($this->table.'.created_date','desc');
		}
		
		$query = $this->db->get($this->table,$per_page,$lmt);
		return $query;
	}
	
	function getChild($id)
	{
		$this->db->where("parent_id",$id);
		$query = $this->db->get($this->table);
		return $query;
	}
	
	function getParent($id)
	{
		$this->db->where("id",$id);
		$query = $this->db->get($this->table);
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
		$status = $num1 = $num2 = 0;
		#select first
		$this->db->where('id',$id);
		$this->db->where('is_enabled',1);
		$query1 = $this->db->get($this->table);
		$num1 = $query1->num_rows();
	
		
		if($num1 == 0){
			#check admin level in admins
			$this->db->where('adminuser_levels_id',$id);
			$q1 = $this->db->get('adminuser_auths');
			if($q1->num_rows() == 0){
				$status = 1;
			}else{
				$status = 2;
			}
			
			if($status == 1){
				$this->db->where('id',$id);
				$this->db->delete($this->table);
			}
		}
		
		return $status;
	}
	
}
?>