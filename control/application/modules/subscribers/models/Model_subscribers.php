<?php
class Model_subscribers extends CI_Model {

	var $tbl_subscribers = "subscribers";
	
	function __construct()
	{
		parent::__construct();
	}
	
	function getTotal($sch_pack)
	{
		if($sch_pack){

			if($sch_pack['sch1_parm'] != 'null')
			{
				$this->db->like($this->tbl_subscribers.'.email',$sch_pack['sch1_parm']);
				$this->db->or_like($this->tbl_subscribers.'.ip_address',$sch_pack['sch1_parm']);
			}

			if($sch_pack['sch2_parm'] != 'null' && !empty($sch_pack['sch2_parm']))
			{
				$this->db->where("DATE(tbl_".$this->tbl_subscribers.'.created_date) >=',$sch_pack['sch2_parm']);
			}
		
			if($sch_pack['sch3_parm'] != 'null' && !empty($sch_pack['sch3_parm']))
			{
				$this->db->where("DATE(tbl_".$this->tbl_subscribers.'.created_date) <=',$sch_pack['sch3_parm']);
			}
		}
		
		$this->db->select("COUNT(tbl_".$this->tbl_subscribers.".id) AS total");
		$query = $this->db->get($this->tbl_subscribers);
		$r = $query->row();
		return $r->total;
	}
	
	function getList($per_page,$lmt,$sch_pack)
	{
		if($sch_pack){

			if($sch_pack['sch1_parm'] != 'null')
			{
				$this->db->like($this->tbl_subscribers.'.email',$sch_pack['sch1_parm']);
				$this->db->or_like($this->tbl_subscribers.'.ip_address',$sch_pack['sch1_parm']);
			}

			if($sch_pack['sch2_parm'] != 'null' && !empty($sch_pack['sch2_parm']))
			{
				$this->db->where("DATE(tbl_".$this->tbl_subscribers.'.created_date) >=',$sch_pack['sch2_parm']);
			}
		
			if($sch_pack['sch3_parm'] != 'null' && !empty($sch_pack['sch3_parm']))
			{
				$this->db->where("DATE(tbl_".$this->tbl_subscribers.'.created_date) <=',$sch_pack['sch3_parm']);
			}
		}
	
		$this->db->order_by($this->tbl_subscribers.".id","desc");
		$query = $this->db->get($this->tbl_subscribers,$per_page,$lmt);

		return $query;
	}
	
}
?>
