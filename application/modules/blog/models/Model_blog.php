<?php

class Model_blog extends CI_Model {

	var $table_blog = "blogs";
	var $table_ref_blog = "ref_blogs";

	function __construct()
	{
		parent::__construct();
	}

	function getCategories()
	{
		$this->db->where($this->table_ref_blog.'.is_enabled',1);
		$this->db->order_by($this->table_ref_blog.'.name','asc');
		$query = $this->db->get($this->table_ref_blog);
		return $query;
	}

	function getHighlight()
	{
		$this->db->where($this->table_blog.'.is_enabled',1);
		$this->db->where($this->table_blog.'.is_highlight',1);
		$this->db->order_by($this->table_blog.'.id','desc');
		$this->db->limit(1);
		$query = $this->db->get($this->table_blog);
		return $query;
	}

	function getList($sch_pack=array())
	{
		if($sch_pack){
			if($sch_pack['sch1_parm'] != 'null' && !empty($sch_pack['sch1_parm']))
			{
				$this->db->like($this->table_blog.'.name',$sch_pack['sch1_parm']);
			}
		
			if($sch_pack['sch2_parm'] != 'null')
			{
				$this->db->where($this->table_blog.'.ref_blogs_id',$sch_pack['sch2_parm']);
			}
		}

		$this->db->where($this->table_blog.'.is_enabled',1);
		$this->db->order_by($this->table_blog.'.id','desc');
		$query = $this->db->get($this->table_blog);
		return $query;
	}

	function getDetail($id)
	{
		$this->db->select($this->table_blog.".*,
						 (SELECT name FROM tbl_ref_blogs WHERE tbl_ref_blogs.id = tbl_".$this->table_blog.".ref_blogs_id) AS ref_blogs_name");
		$this->db->where($this->table_blog.'.id',$id);
		$query = $this->db->get($this->table_blog);
		return $query;
	}

}