<?php
class Model_menus extends CI_Model {
	
	var $table = "menus";
	var $table_menu_auth = 'menu_auths';
	
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
		
			if($sch_pack['sch2_parm'] != 'null')
			{
				$this->db->where($this->table.'.is_enabled',$sch_pack['sch2_parm']);
			}
			
			if($sch_pack['sch3_parm'] != 'null' && !empty($sch_pack['sch3_parm']))
			{
				$this->db->where($this->table.'.parent_id',$sch_pack['sch3_parm']);
			}
		}
	
		if($this->groupID <> 1){
			$this->db->where($this->table.".id !=",6);
		}
		$this->db->select("COUNT(id) AS total");
		$query = $this->db->get($this->table);
		$r = $query->row();
		return $r->total;
	}
	
	
	function getList($per_page=null,$lmt=null,$sch_pack=null)
	{
		if($sch_pack){
			if($sch_pack['sch1_parm'] != 'null' && !empty($sch_pack['sch1_parm']))
			{
				$this->db->like($this->table.'.title',$sch_pack['sch1_parm']);
			}
		
			if($sch_pack['sch2_parm'] != 'null')
			{
				$this->db->where($this->table.'.is_enabled',$sch_pack['sch2_parm']);
			}
			
			if($sch_pack['sch3_parm'] != 'null' && !empty($sch_pack['sch3_parm']))
			{
				$this->db->where($this->table.'.parent_id',$sch_pack['sch3_parm']);
			}
		}
	
		if($this->groupID <> 1){
			$this->db->where($this->table.".id !=",6);
		}
		$this->db->order_by('ordered','asc');
		$query = $this->db->get($this->table,$per_page,$lmt);
		return $query;
	}
	
	
	function getParentList($parent_id)
	{
		$parent_title = "#";
		$this->db->select("title,parent_id");
		$this->db->where('id',$parent_id);
		$query = $this->db->get($this->table);
		$num = $query->num_rows();
		if($num > 0 ){
			$r = $query->row();
			$parent_title = $r->title;
		}
		return $parent_title;
	}
	
	
	function getMenuList($id)
	{
		$this->db->where($this->table.'.id !=',$id);
		$this->db->order_by($this->table.'.ordered','asc');
		$query = $this->db->get($this->table);
		return $query;
	}
	

	function getDetail($id)
	{
		$this->db->where($this->table.'.id',$id);
		$query = $this->db->get($this->table);
		return $query;
	}
	
	function ajaxsort($id,$order)
	{
			$data = array(
					"ordered"=>$order
					);
			$this->db->where("id",$id);
			$this->db->update($this->table,$data);
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
		
			#check menu in menu auth
			$this->db->where('menus_id',$id);
			$q1 = $this->db->get($this->table_menu_auth);
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
	
	function getMax()
	{
		$this->db->select_max('ordered','max_ordered');
		$query = $this->db->get($this->table);
		return $query;
	}
}
?>