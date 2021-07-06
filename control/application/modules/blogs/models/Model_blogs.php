<?php
class Model_blogs extends CI_Model {

	var $table = "blogs";
	var $tbl_ref_blogs = "ref_blogs";
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
				$this->db->like($this->table.'.ref_blogs_id',$sch_pack['sch3_parm']);
			}

			if($sch_pack['sch4_parm'] != 'null' && !empty($sch_pack['sch4_parm']))
			{
				$this->db->like($this->table.'.status',$sch_pack['sch4_parm']);
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
				$this->db->like($this->table.'.ref_blogs_id',$sch_pack['sch3_parm']);
			}

			if($sch_pack['sch4_parm'] != 'null' && !empty($sch_pack['sch4_parm']))
			{
				$this->db->like($this->table.'.status',$sch_pack['sch4_parm']);
			}
		}
		
		$this->db->select($this->table.".*,
						 (SELECT name FROM tbl_ref_blogs WHERE tbl_ref_blogs.id = tbl_".$this->table.".ref_blogs_id) AS ref_blogs_name");
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

	function getAssignedStores($id)
	{
		$this->db->select($this->table.".*,
						 (SELECT name FROM ".$this->tbl_ref_blogs." WHERE ".$this->tbl_ref_blogs.".id = tbl_".$this->table.".ref_blogs_id) AS ref_blogs_name");
		$this->db->where($this->tbl_assigned_ref_blogs.'.ref_blogs_id',$id);
		$query = $this->db->get($this->tbl_assigned_ref_blogs);
		return $query;
	}
	
	function getMulti($id)
	{
		$this->db->join($this->table_multi,$this->table_multi.".id=".$this->table.".".$this->table_multi."_id");
		$this->db->where($this->table_multi.".id",$id);
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

	function setFileUpload($file_image,$file_image_tmp,$file_image_old)
	{
		$d = date("Ymdhis");
		$file_image = $d.$file_image;
		$this->load->library('image_moo');
		
		$w = 1920;
		$h = 440;					
		
 		$this->image_moo->load($file_image_tmp)->resize_crop($w,$h)->save("../uploads/blogs/".$file_image);
		$this->image_moo->load($file_image_tmp)->resize_crop(192,44)->save("../uploads/blogs/thumbs/".$file_image);
   		
   		if(!$file_image)
		{
			 	$file_image = $file_image_old;
		}

		return $file_image;
	}


	function setFileUploadThumb($file_image,$file_image_tmp,$file_image_old)
	{
		$d = date("Ymdhis");
		$file_image = $d.$file_image;
		$this->load->library('image_moo');
		
		$w = 586;
		$h = 300;					
		
 		$this->image_moo->load($file_image_tmp)->resize_crop($w,$h)->save("../uploads/blogs/".$file_image);
		$this->image_moo->load($file_image_tmp)->resize_crop(320,220)->save("../uploads/blogs/thumbs/".$file_image);
   		
   		if(!$file_image)
		{
			 	$file_image = $file_image_old;
		}

		return $file_image;
	}
	
	
	function deleteFileUpload($file_image)
	{
		if(!empty($file_image)){
			if(file_exists("../uploads/blogs/".$file_image)){ unlink("../uploads/blogs/".$file_image); }
			if(file_exists("../uploads/blogs/thumbs/".$file_image)){ unlink("../uploads/blogs/thumbs/".$file_image); }
		}
	}

	function deleteFileUploadThumb($file_image)
	{
		if(!empty($file_image)){
			if(file_exists("../uploads/blogs/".$file_image)){ unlink("../uploads/blogs/".$file_image); }
			if(file_exists("../uploads/blogs/thumbs/".$file_image)){ unlink("../uploads/blogs/thumbs/".$file_image); }
		}
	}
	
	function setPictureDelete($id, $file_image)
	{
		$this->db->where("id",$id);
		$this->db->update($this->table,array("file_image"=>""));
		$this->deleteFileUpload($file_image);
	}

	function setPictureDeleteThumb($id, $file_image)
	{
		$this->db->where("id",$id);
		$this->db->update($this->table,array("file_thumb"=>""));
		$this->deleteFileUpload($file_image);
	}

	function setDocumentUpload($file,$file_tmp,$file_old)
	{
		$d = date("Ymdhis");
		$file = $d."_".str_replace(" ","_",strtolower($file));
		
		if (!move_uploaded_file($file_tmp, "../uploads/blogs/docs/".$file)) {
			return false;
		}

		if(!$file)
		{
			 	$file = $file_old;
		}

		return $file;
	}
	
	
	function deleteDocumentUpload($file)
	{
		if(!empty($file)){
			if(file_exists("../uploads/blogs/docs/".$file)){ unlink("../uploads/blogs/docs/".$file); }
		}
	}
	
	function setDocumentDelete($id, $file)
	{
		$this->db->where("id",$id);
		$this->db->update($this->table,array("file_doc"=>""));
		$this->deleteDocumentUpload($file);
	}
	
}
?>
