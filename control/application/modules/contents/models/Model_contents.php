<?php
class model_contents extends CI_Model {
	
	var $table = "contents";
	
	function __construct()
	{
		parent::__construct();
	}
	
	function getTotal($sch_pack)
	{
		if($sch_pack){
			if($sch_pack['sch1_parm'] != 'null' && !empty($sch_pack['sch1_parm']))
			{
				$this->db->like($this->table.'.title',$sch_pack['sch1_parm']);
			}
			
			if($sch_pack['sch1_parm'] != 'null' && !empty($sch_pack['sch1_parm']))
			{
				$this->db->like($this->table.'.content',$sch_pack['sch1_parm']);
			}
		
			if($sch_pack['sch2_parm'] != 'null' && !empty($sch_pack['sch2_parm']))
			{
				$this->db->where($this->table.'.is_enabled',$sch_pack['sch2_parm']);
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
				$this->db->like($this->table.'.title',$sch_pack['sch1_parm']);
			}
			
			if($sch_pack['sch1_parm'] != 'null' && !empty($sch_pack['sch1_parm']))
			{
				$this->db->like($this->table.'.content',$sch_pack['sch1_parm']);
			}
		
			if($sch_pack['sch2_parm'] != 'null' && !empty($sch_pack['sch2_parm']))
			{
				$this->db->where($this->table.'.is_enabled',$sch_pack['sch2_parm']);
			}
		}
		
		$this->db->order_by($this->table.".created_date","desc");
		$query = $this->db->get($this->table,$per_page,$lmt);
		//die($this->db->last_query());
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
			$this->db->where('id',$id);
			$this->db->delete($this->table);
			$status = 1;
		}
		return $status;
	}
	
	function setFileUpload($file_image,$file_image_tmp,$file_image_old, $types=null)
	{
		$d = date("Ymdhis");
		$file_image = $d.$file_image;
		$this->load->library('image_moo');
		
		$w = 800;
		$h = 400;					
		
 		$this->image_moo->load($file_image_tmp)->resize_crop($w,$h)->save("../uploads/contents/".$file_image);
 		$this->image_moo->load($file_image_tmp)->resize_crop(400,200)->save("../uploads/contents/medium/".$file_image);
		$this->image_moo->load($file_image_tmp)->resize_crop(200,100)->save("../uploads/contents/thumbs/".$file_image);
   		
   		if(!$file_image)
		{
			 	$file_image = $file_image_old;
		}

		return $file_image;
	}
	
	
	function deleteFileUpload($file_image)
	{
		if(file_exists("../uploads/contents/".$file_image)){ unlink("../uploads/contents/".$file_image); }
		if(file_exists("../uploads/contents/medium/".$file_image)){ unlink("../uploads/contents/medium/".$file_image); }
		if(file_exists("../uploads/contents/thumbs/".$file_image)){ unlink("../uploads/contents/thumbs/".$file_image); }
	}
	
	function unlinkFileUpload($id)
	{
		$this->db->where("id",$id);
		$this->db->update($this->table,array("files"=>""));
	}

}
?>
