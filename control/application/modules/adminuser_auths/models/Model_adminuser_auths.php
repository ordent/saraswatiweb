<?php
class Model_adminuser_auths extends CI_Model {
	
	var $table = "adminuser_auths";
	var $join1 = "adminuser_levels";
	var $tbl_brands = "brands";
	
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
				$this->db->like($this->table.'.email',$sch_pack['sch1_parm']);
			}
		
			if($sch_pack['sch2_parm'] != 'null' && !empty($sch_pack['sch2_parm']))
			{
				$this->db->like($this->table.'.is_enabled',$sch_pack['sch2_parm']);
			}
			
			if($sch_pack['sch3_parm'] != 'null' && !empty($sch_pack['sch3_parm']))
			{
				$this->db->where($this->table.'.'.$this->join1.'_id',$sch_pack['sch3_parm']);
			}
		}
		
		
		if($this->groupID <> 1){
			$this->db->where($this->table.".adminuser_levels_id !=",1);
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
				$this->db->like($this->table.'.email',$sch_pack['sch1_parm']);
			}
		
			if($sch_pack['sch2_parm'] != 'null' && !empty($sch_pack['sch2_parm']))
			{
				$this->db->like($this->table.'.is_enabled',$sch_pack['sch2_parm']);
			}
			
			if($sch_pack['sch3_parm'] != 'null' && !empty($sch_pack['sch3_parm']))
			{
				$this->db->where($this->table.'.'.$this->join1.'_id',$sch_pack['sch3_parm']);
			}
		}
		
		$this->db->select($this->table.".*,  
						  ".$this->join1.".title,
						  ".$this->join1.".parent_id,
						  ".$this->join1.".is_enabled AS pub,
						  ".$this->join1.".created_date AS crt_date");
		$this->db->join($this->join1,$this->join1.".id=".$this->table.".".$this->join1."_id");
		
		if($this->groupID <> 1){
			$this->db->where($this->table.".".$this->join1."_id !=",1);
		}
		
		$this->db->order_by($this->table.".created_date","desc");
		$query = $this->db->get($this->table,$per_page,$lmt);
		return $query;
	}
	

	function getDetail($id)
	{
		$this->db->select($this->table.".*, 
						  ".$this->join1.".title,
						  ".$this->join1.".parent_id,
						  ".$this->join1.".is_enabled AS pub,
						  ".$this->join1.".created_date AS crt_date");
		$this->db->join($this->join1,$this->join1.".id=".$this->table.".".$this->join1."_id");
		$this->db->where($this->table.'.id',$id);
		$query = $this->db->get($this->table);
		return $query;
	}
	
	function checkUsername($email,$id)
	{
		$this->db->where('id !=',$id);
		$this->db->where('email',$email);
		$query = $this->db->get($this->table);
		return $query->num_rows();
	}
	
	function getAccount($id)
	{
		$this->db->select($this->table.".*,
						  ".$this->join1.".title,
						  ".$this->join1.".parent_id,
						  ".$this->join1.".is_enabled AS pub,
						  ".$this->join1.".created_date AS crt_date");
		$this->db->join($this->join1,$this->join1.".id=".$this->table.".".$this->join1."_id");
		$this->db->where($this->table.".is_enabled",1);
		$this->db->where($this->table.'.id',$id);
		$query = $this->db->get($this->table);
		return $query;
	}
	
	function getParent($id)
	{
		$this->db->where("id",$id);
		$query = $this->db->get($this->table);
		return $query;
	}
	
	function setUpdate($data_pack,$where_pack)
	{
		if(isset($where_pack['id'])){
			$this->db->where('id',$where_pack['id']);
		}
		if(isset($where_pack['email'])){
			$this->db->where('email',$where_pack['email']);
		}

		return $this->db->update($this->table,$data_pack,$where_pack);		
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
			$this->db->where('id',$id);
			$this->db->delete($this->table);
			$status = 1;
		}
		return $status;
	}

	function checkEmailExists($email,$id=null)
	{
		if(!empty($id)){
			$this->db->where('id !=',$id);
		}
		$this->db->where('email',$email);
		$query = $this->db->get($this->table);
		return $query->num_rows();
	}

	function getDetailByEmail($email)
	{
		$this->db->select($this->table.".*,
						  ".$this->join1.".*,
						  ".$this->join1.".title AS adminuser_levels,
						  ".$this->join1.".created_date AS crt_date");
		$this->db->join($this->join1, $this->join1.".id=".$this->table.".adminuser_levels_id");
		$this->db->where($this->table.'.email',$email);
		$query = $this->db->get($this->table);
		return $query;
	}
	
}
?>
