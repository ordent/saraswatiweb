<?php
class Model_login extends CI_Model {
	function __construct()
	{
		parent::__construct();
	}

	
	function cekUserLogin($email,$password)
	{		
		$this->db->where("email",$email);
		$this->db->where("password",$password);
		$this->db->where("is_enabled",1);
		$query = $this->db->get("adminuser_auths");
		
		return $query;
	}

	
	function cekEmailExists($email)
	{
	
		$this->db->where("email",$email);
		$query = $this->db->get("adminuser_auths");
		return $query;
	}
	
	
	function getUserById($id)
	{
			$this->db->join("adminuser_levels","adminuser_levels.adminuser_levels_id=adminuser_auths.adminuser_levels_id");
			$this->db->where("adminuser_auths.id",$id);
			$this->db->where("adminuser_auths.is_enabled",1);
			$query = $this->db->get("adminuser_auths");
			return $query;
	}

	
	function getUserTotal($keyword)
	{
		if($keyword != 'null' && !empty($keyword) )
		{
			$this->db->like('adminuser_auths.email',$keyword);
		}
		$this->db->join("adminuser_levels","adminuser_levels.adminuser_levels_id=adminuser_auths.adminuser_levels_id");
		$this->db->select("COUNT(id) AS total");
		$query = $this->db->get("adminuser_auths");
		$r = $query->row();
		return $r->total;
	}
	
	
	function getUser($per_page,$lmt,$keyword)
	{
		
		if($keyword != 'null' && !empty($keyword) )
		{
			$this->db->like('adminuser_auths.email',$keyword);
		}
		$this->db->join("adminuser_levels","adminuser_levels.adminuser_levels_id=adminuser_auths.adminuser_levels_id");
		$query = $this->db->get("adminuser_auths",$per_page,$lmt);
		return $query;
	}
	

	function setLoginDate($id)
	{
		$data = array("login_date"=>date('Y-m-d H:i:s', now()));
		$this->db->where('id',$id);
		return $this->db->update('adminuser_auths',$data);	
	}
}
?>