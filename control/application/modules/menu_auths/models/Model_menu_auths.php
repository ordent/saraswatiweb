<?php
class model_menu_auths extends CI_Model {
	
	var $table = "menu_auths";
	var $join1 = "adminuser_levels";
	var $join2 = "menus";
	
	function __construct()
	{
		parent::__construct();
		$this->groupID = $this->session->userdata('sess_admin')['adminuser_levels_id'];
	}
	
	
	function getMenu($id,$parent_id=0)
	{
		$this->db->select( $this->table.".*,
						   ".$this->join1.".title,
						   ".$this->join1.".is_enabled AS pub,
						   ".$this->join1.".created_date AS crt_date,
						   ".$this->join2.".title AS menus_title,
						   ".$this->join2.".icon,
						   ".$this->join2.".id AS menus_id,
						   ".$this->join2.".id AS parent_id,
						   ".$this->join2.".uri AS menus_uri,
						   ".$this->join2.".divider AS menus_divider,
						   ".$this->join2.".is_enabled AS pub,
						   ".$this->join2.".created_date AS menus_crt_date");
		$this->db->join($this->join2,$this->join2.".id=".$this->table.".".$this->join2."_id");
		$this->db->join($this->join1,$this->join1.".id=".$this->table.".".$this->join1."_id");
		$this->db->where($this->table.".adminuser_levels_id",$id);
		$this->db->where("menus.parent_id",$parent_id);
		$this->db->where("menus.is_enabled",1);
		$this->db->order_by("menus.ordered","asc");
		$query = $this->db->get($this->table);
		return $query;
	}
	
	function getSelectParentMenu($uri,$id,$space=NULL)
	{
		#recursive 		
		if(is_numeric($uri)){
			$this->db->where($this->join2.".id",$uri);
		}else{
			$this->db->where($this->join2.".uri",$uri);
		}
		
		$this->db->where($this->join2.".parent_id",$id);
		$this->db->where($this->join2.".is_enabled",1);
		$this->db->order_by($this->join2.".ordered","asc");
		$query = $this->db->get($this->join2);
	
		if($query->num_rows() > 0){		
			$r = $query->row();
			$uri = $r->id;
			return array("status"=>1,"uri"=>$uri);
		}else{
			
			$this->db->where($this->join2.".parent_id",$id);
			$this->db->where($this->join2.".is_enabled",1);
			$this->db->order_by($this->join2.".ordered","asc");
			$q = $this->db->get($this->join2);
			if($q->num_rows() > 0){
				foreach($q->result() as $r){
					return $this->getSelectParentMenu($uri,$r->id,"&nbsp;&nbsp;&nbsp;");
				}
			}else{
				return array("status"=>0,"uri"=>$uri);
			}
		}
		
	}
	
	function getMenuFromUri($uri)
	{
		$this->db->like($this->join2.".uri",$uri);
		$this->db->where($this->join2.".is_enabled",1);
		$query = $this->db->get($this->join2);
		return $query;
	}
	
	function getMenuPermission($id,$menus_id)
	{
		$this->db->join($this->join2,$this->join2.".id=".$this->table.".menus_id");
		$this->db->where($this->table.".".$this->join2."_id",$menus_id);
		$this->db->where($this->table.".".$this->join1."_id",$id);
		$this->db->where($this->join2.".is_enabled",1);
		$query = $this->db->get($this->table);
		return $query;
	}

	function getTotal($sch_pack)
	{
		if($sch_pack){
			if($sch_pack['sch1_parm'] != 'null' && !empty($sch_pack['sch1_parm']))
			{
				$this->db->like($this->join2.'.title',$sch_pack['sch1_parm']);
			}
		
			if($sch_pack['sch2_parm'] != 'null' && !empty($sch_pack['sch2_parm']))
			{
				$this->db->where($this->table.'.adminuser_levels_id',$sch_pack['sch2_parm']);
			}
		}
		
		if($this->groupID <> 1){
			$this->db->where($this->join1.".id !=",1);
		}
		$this->db->select($this->table.".*,
						   COUNT(tbl_".$this->table.".id) AS total,
						   ".$this->join1.".title,
						   ".$this->join1.".is_enabled AS pub,
						   ".$this->join1.".created_date AS crt_date,
						   ".$this->join2.".title AS menus_title,
						   ".$this->join2.".is_enabled AS pub,
						   ".$this->join2.".created_date AS menus_crt_date");
		$this->db->join($this->join2,$this->join2.".id=".$this->table.".".$this->join2."_id");
		$this->db->join($this->join1,$this->join1.".id=".$this->table.".".$this->join1."_id");
		$query = $this->db->get($this->table);
		$r = $query->row();
		return $r->total;
	}
	
	
	function getList($per_page,$lmt,$sch_pack)
	{
		
		if($sch_pack){
			if($sch_pack['sch1_parm'] != 'null' && !empty($sch_pack['sch1_parm']))
			{
				$this->db->like($this->join2.'.title',$sch_pack['sch1_parm']);
			}
		
			if($sch_pack['sch2_parm'] != 'null' && !empty($sch_pack['sch2_parm']))
			{
				$this->db->where($this->table.'.adminuser_levels_id',$sch_pack['sch2_parm']);
			}
		}
		
		if($this->groupID <> 1){
			$this->db->where($this->join1.".id !=",1);
		}
		
		$this->db->select($this->table.".*,
						  ".$this->join1.".title,
						  ".$this->join1.".is_enabled AS pub,
						  ".$this->join1.".created_date AS crt_date,
						  ".$this->join2.".title AS menus_title,
						  ".$this->join2.".is_enabled AS pub,
						  ".$this->join2.".created_date AS menus_crt_date");
		$this->db->join($this->join2,$this->join2.".id=".$this->table.".".$this->join2."_id");
		$this->db->join($this->join1,$this->join1.".id=".$this->table.".".$this->join1."_id");
		$this->db->order_by($this->join1.".id","desc");
		$this->db->order_by($this->table.".id","asc");
		$query = $this->db->get($this->table,$per_page,$lmt);
		return $query;
	}
	

	function getDetail($id)
	{
		$this->db->where($this->table.'.id',$id);
		$query = $this->db->get($this->table);
		return $query;
	}
	
	function setInsert($data_pack)
	{
		$this->db->insert($this->table,$data_pack);
	}
	
	function cekInsert($menus_id,$adminuser_levels_id)
	{
		$this->db->where($this->table.'.'.$this->join2.'_id',$menus_id);
		$this->db->where($this->table.'.'.$this->join1.'_id',$adminuser_levels_id);
		$query = $this->db->get($this->table);
		return $query->num_rows();
	}
	
	
	function setDelete($id)
	{
		$status = 0;
		$this->db->where('id',$id);
		$this->db->delete($this->table);
		$status = 1;
		return $status;
	}
	
	function setFileUpload($file_image,$file_image_tmp,$file_image_old)
	{
		$this->load->library('image_moo');
 		$this->image_moo->load($file_image_tmp)->resize_crop(150,150)->save("./uploads/".$file_image);
   		
   			if(!$file_image)
			{
			 	$file_image = $file_image_old;
			}

			return $file_image;
	}
}
?>