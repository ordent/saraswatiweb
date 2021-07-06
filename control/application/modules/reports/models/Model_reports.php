<?php
class Model_reports extends CI_Model {

	var $tbl_brands = "brands";
	var $tbl_activities = "activities";
	var $tbl_assigned_stores = "assigned_stores";
	var $tbl_assigned_employees = "assigned_employees";
	var $tbl_projects = "projects";
	var $tbl_stores = "stores";
	var $tbl_employees = "employees";
	var $tbl_log_activities = "log_activities";
	var $tbl_reports = "reports";
	var $tbl_report_details = "report_details";
	var $tbl_ref_products = "ref_products";
	var $tbl_products = "products";
	var $tbl_activities_multi = "";
	
	function __construct()
	{
		parent::__construct();
	}
	
	function getTotal($sch_pack)
	{
		if($sch_pack){

			if($sch_pack['sch1_parm'] != 'null')
			{
				$this->db->like("(SELECT name FROM tbl_".$this->tbl_employees." WHERE tbl_".$this->tbl_employees.".id = tbl_".$this->tbl_reports.".employees_id)",$sch_pack['sch1_parm']);
			}

			if($sch_pack['sch2_parm'] != 'null')
			{
				$this->db->where("(SELECT stores_id FROM tbl_".$this->tbl_assigned_stores." WHERE tbl_".$this->tbl_assigned_stores.".id = tbl_".$this->tbl_reports.".assigned_stores_id) = ".$sch_pack['sch2_parm'],null,false);
			}

			if($sch_pack['sch3_parm'] != 'null' && !empty($sch_pack['sch3_parm']))
			{
				$this->db->where("(SELECT brands_id FROM tbl_".$this->tbl_assigned_stores." WHERE tbl_".$this->tbl_assigned_stores.".id = tbl_".$this->tbl_reports.".assigned_stores_id) = ".$sch_pack['sch3_parm'],null,false);
			}

			if($sch_pack['sch4_parm'] != 'null' && !empty($sch_pack['sch4_parm']))
			{
				$this->db->where("(SELECT projects_id FROM tbl_".$this->tbl_assigned_stores." WHERE tbl_".$this->tbl_assigned_stores.".id = tbl_".$this->tbl_reports.".assigned_stores_id) = ".$sch_pack['sch4_parm'],null,false);
			}

			if($sch_pack['sch5_parm'] != 'null' && !empty($sch_pack['sch5_parm']))
			{
				$this->db->where("DATE(tbl_".$this->tbl_reports.'.activities_date) >=',$sch_pack['sch5_parm']);
			}
		
			if($sch_pack['sch6_parm'] != 'null' && !empty($sch_pack['sch6_parm']))
			{
				$this->db->where("DATE(tbl_".$this->tbl_reports.'.activities_date) <=',$sch_pack['sch6_parm']);
			}
		}
		
		$this->db->select("COUNT(tbl_".$this->tbl_reports.".id) AS total");
		$this->db->join($this->tbl_reports,'tbl_'.$this->tbl_report_details.".reports_id = tbl_".$this->tbl_reports.".id");
		$query = $this->db->get($this->tbl_report_details);
		$r = $query->row();
		return $r->total;
	}
	
	function getList($per_page,$lmt,$sch_pack)
	{
		if($sch_pack){

			if($sch_pack['sch1_parm'] != 'null')
			{
				$this->db->like("(SELECT name FROM tbl_".$this->tbl_employees." WHERE tbl_".$this->tbl_employees.".id = tbl_".$this->tbl_reports.".employees_id)",$sch_pack['sch1_parm']);
			}

			if($sch_pack['sch2_parm'] != 'null')
			{
				$this->db->where("(SELECT stores_id FROM tbl_".$this->tbl_assigned_stores." WHERE tbl_".$this->tbl_assigned_stores.".id = tbl_".$this->tbl_reports.".assigned_stores_id) = ".$sch_pack['sch2_parm'],null,false);
			}

			if($sch_pack['sch3_parm'] != 'null' && !empty($sch_pack['sch3_parm']))
			{
				$this->db->where("(SELECT brands_id FROM tbl_".$this->tbl_assigned_stores." WHERE tbl_".$this->tbl_assigned_stores.".id = tbl_".$this->tbl_reports.".assigned_stores_id) = ".$sch_pack['sch3_parm'],null,false);
			}

			if($sch_pack['sch4_parm'] != 'null' && !empty($sch_pack['sch4_parm']))
			{
				$this->db->where("(SELECT projects_id FROM tbl_".$this->tbl_assigned_stores." WHERE tbl_".$this->tbl_assigned_stores.".id = tbl_".$this->tbl_reports.".assigned_stores_id) = ".$sch_pack['sch4_parm'],null,false);
			}

			if($sch_pack['sch5_parm'] != 'null' && !empty($sch_pack['sch5_parm']))
			{
				$this->db->where("DATE(tbl_".$this->tbl_reports.'.activities_date) >=',$sch_pack['sch5_parm']);
			}
		
			if($sch_pack['sch6_parm'] != 'null' && !empty($sch_pack['sch6_parm']))
			{
				$this->db->where("DATE(tbl_".$this->tbl_reports.'.activities_date) <=',$sch_pack['sch6_parm']);
			}
		}
		
		$this->db->select($this->tbl_report_details.".*,
						 ".$this->tbl_reports.".activities_date,
						 (SELECT name FROM tbl_".$this->tbl_products." WHERE tbl_".$this->tbl_products.".id = tbl_".$this->tbl_report_details.".products_id) AS products_name,
						 (SELECT price FROM tbl_".$this->tbl_products." WHERE tbl_".$this->tbl_products.".id = tbl_".$this->tbl_report_details.".products_id) AS products_price, 
						 (SELECT name FROM tbl_".$this->tbl_employees." WHERE tbl_".$this->tbl_employees.".id = tbl_".$this->tbl_reports.".employees_id) AS employees_name, 
						 (SELECT (SELECT name FROM tbl_".$this->tbl_projects." WHERE tbl_".$this->tbl_projects.".id = tbl_".$this->tbl_assigned_stores.".projects_id) FROM tbl_".$this->tbl_assigned_stores." WHERE tbl_".$this->tbl_assigned_stores.".id = tbl_".$this->tbl_reports.".assigned_stores_id) AS projects_name,
						 (SELECT (SELECT (SELECT name FROM tbl_".$this->tbl_brands." WHERE tbl_".$this->tbl_brands.".id = tbl_".$this->tbl_stores.".brands_id) FROM tbl_".$this->tbl_stores." WHERE tbl_".$this->tbl_stores.".id = tbl_".$this->tbl_assigned_stores.".stores_id) FROM tbl_".$this->tbl_assigned_stores." WHERE tbl_".$this->tbl_assigned_stores.".id = tbl_".$this->tbl_reports.".assigned_stores_id) AS brands_name,
						 (SELECT (SELECT name FROM tbl_".$this->tbl_stores." WHERE tbl_".$this->tbl_stores.".id = tbl_".$this->tbl_assigned_stores.".stores_id) FROM tbl_".$this->tbl_assigned_stores." WHERE tbl_".$this->tbl_assigned_stores.".id = tbl_".$this->tbl_reports.".assigned_stores_id) AS stores_name");
		$this->db->order_by($this->tbl_reports.".id","desc");
		$this->db->join($this->tbl_reports,'tbl_'.$this->tbl_report_details.".reports_id = tbl_".$this->tbl_reports.".id");
		$query = $this->db->get($this->tbl_report_details,$per_page,$lmt);

		return $query;
	}

	function getTotalSales($sch_pack)
	{
		if($sch_pack){

			if($sch_pack['sch1_parm'] != 'null')
			{
				$this->db->like("(SELECT name FROM tbl_".$this->tbl_employees." WHERE tbl_".$this->tbl_employees.".id = tbl_".$this->tbl_reports.".employees_id)",$sch_pack['sch1_parm']);
			}

			if($sch_pack['sch2_parm'] != 'null')
			{
				$this->db->where("(SELECT stores_id FROM tbl_".$this->tbl_assigned_stores." WHERE tbl_".$this->tbl_assigned_stores.".id = tbl_".$this->tbl_reports.".assigned_stores_id) = ".$sch_pack['sch2_parm'],null,false);
			}

			if($sch_pack['sch3_parm'] != 'null' && !empty($sch_pack['sch3_parm']))
			{
				$this->db->where("(SELECT brands_id FROM tbl_".$this->tbl_assigned_stores." WHERE tbl_".$this->tbl_assigned_stores.".id = tbl_".$this->tbl_reports.".assigned_stores_id) = ".$sch_pack['sch3_parm'],null,false);
			}

			if($sch_pack['sch4_parm'] != 'null' && !empty($sch_pack['sch4_parm']))
			{
				$this->db->where("(SELECT projects_id FROM tbl_".$this->tbl_assigned_stores." WHERE tbl_".$this->tbl_assigned_stores.".id = tbl_".$this->tbl_reports.".assigned_stores_id) = ".$sch_pack['sch4_parm'],null,false);
			}

			if($sch_pack['sch5_parm'] != 'null' && !empty($sch_pack['sch5_parm']))
			{
				$this->db->where("DATE(tbl_".$this->tbl_reports.'.activities_date) >=',$sch_pack['sch5_parm']);
			}
		
			if($sch_pack['sch6_parm'] != 'null' && !empty($sch_pack['sch6_parm']))
			{
				$this->db->where("DATE(tbl_".$this->tbl_reports.'.activities_date) <=',$sch_pack['sch6_parm']);
			}
		}
		
		$this->db->select("SUM((SELECT price FROM tbl_".$this->tbl_products." WHERE tbl_".$this->tbl_products.".id = tbl_".$this->tbl_report_details.".products_id) * tbl_".$this->tbl_report_details.".qty) AS total_sales");
		$this->db->join($this->tbl_reports,'tbl_'.$this->tbl_report_details.".reports_id = tbl_".$this->tbl_reports.".id");
		$query = $this->db->get($this->tbl_report_details);

		return $query;
	}
	
	function getDetail($id)
	{
		$this->db->select($this->tbl_reports.".*,
						 (SELECT name FROM tbl_".$this->tbl_employees." WHERE tbl_".$this->tbl_employees.".id = tbl_".$this->tbl_reports.".employees_id) AS employees_name, 
						 (SELECT in_time FROM tbl_".$this->tbl_assigned_employees." WHERE tbl_".$this->tbl_assigned_employees.".assigned_stores_id = tbl_".$this->tbl_reports.".assigned_stores_id AND tbl_".$this->tbl_assigned_employees.".employees_id = tbl_".$this->tbl_reports.".employees_id) AS in_time,
						 (SELECT out_time FROM tbl_".$this->tbl_assigned_employees." WHERE tbl_".$this->tbl_assigned_employees.".assigned_stores_id = tbl_".$this->tbl_reports.".assigned_stores_id AND tbl_".$this->tbl_assigned_employees.".employees_id = tbl_".$this->tbl_reports.".employees_id) AS out_time,
						 (SELECT (SELECT name FROM tbl_".$this->tbl_projects." WHERE tbl_".$this->tbl_projects.".id = tbl_".$this->tbl_assigned_stores.".projects_id) FROM tbl_".$this->tbl_assigned_stores." WHERE tbl_".$this->tbl_assigned_stores.".id = tbl_".$this->tbl_reports.".assigned_stores_id) AS projects_name,
						 (SELECT (SELECT (SELECT name FROM tbl_".$this->tbl_brands." WHERE tbl_".$this->tbl_brands.".id = tbl_".$this->tbl_stores.".brands_id) FROM tbl_".$this->tbl_stores." WHERE tbl_".$this->tbl_stores.".id = tbl_".$this->tbl_assigned_stores.".stores_id) FROM tbl_".$this->tbl_assigned_stores." WHERE tbl_".$this->tbl_assigned_stores.".id = tbl_".$this->tbl_reports.".assigned_stores_id) AS brands_name,
						 (SELECT (SELECT name FROM tbl_".$this->tbl_stores." WHERE tbl_".$this->tbl_stores.".id = tbl_".$this->tbl_assigned_stores.".stores_id) FROM tbl_".$this->tbl_assigned_stores." WHERE tbl_".$this->tbl_assigned_stores.".id = tbl_".$this->tbl_reports.".assigned_stores_id) AS stores_name,
						 (SELECT (SELECT address FROM tbl_".$this->tbl_stores." WHERE tbl_".$this->tbl_stores.".id = tbl_".$this->tbl_assigned_stores.".stores_id) FROM tbl_".$this->tbl_assigned_stores." WHERE tbl_".$this->tbl_assigned_stores.".id = tbl_".$this->tbl_reports.".assigned_stores_id) AS stores_address,
						 (SELECT start_date FROM tbl_".$this->tbl_assigned_stores." WHERE tbl_".$this->tbl_assigned_stores.".id = tbl_".$this->tbl_reports.".assigned_stores_id) AS stores_start_from,
						 (SELECT end_date FROM tbl_".$this->tbl_assigned_stores." WHERE tbl_".$this->tbl_assigned_stores.".id = tbl_".$this->tbl_reports.".assigned_stores_id) AS stores_end_from,
						 (SELECT open_time FROM tbl_".$this->tbl_assigned_stores." WHERE tbl_".$this->tbl_assigned_stores.".id = tbl_".$this->tbl_reports.".assigned_stores_id) AS stores_open_time,
						 (SELECT close_time FROM tbl_".$this->tbl_assigned_stores." WHERE tbl_".$this->tbl_assigned_stores.".id = tbl_".$this->tbl_reports.".assigned_stores_id) AS stores_close_time,
						 (SELECT checkin_datetime FROM tbl_".$this->tbl_activities." WHERE tbl_".$this->tbl_activities.".id = tbl_".$this->tbl_reports.".activities_id) AS activities_checkin_time,
						 (SELECT checkin_duration FROM tbl_".$this->tbl_activities." WHERE tbl_".$this->tbl_activities.".id = tbl_".$this->tbl_reports.".activities_id) AS activities_checkin_duration,
						 (SELECT checkout_datetime FROM tbl_".$this->tbl_activities." WHERE tbl_".$this->tbl_activities.".id = tbl_".$this->tbl_reports.".activities_id) AS activities_checkout_time,
						 (SELECT checkout_duration FROM tbl_".$this->tbl_activities." WHERE tbl_".$this->tbl_activities.".id = tbl_".$this->tbl_reports.".activities_id) AS checkout_duration");
		$this->db->where($this->tbl_reports.'.id',$id);
		$query = $this->db->get($this->tbl_reports);
		return $query;
	}

	function getProductDetails($id)
	{
		$this->db->where($this->tbl_report_details.'.reports_id',$id);
		return $this->db->get($this->tbl_report_details);
	}

	function getAssignedStores($id)
	{
		$this->db->select($this->tbl_assigned_stores.".*");
		$this->db->where($this->tbl_assigned_stores.".id", $id);
		$this->db->where($this->tbl_assigned_stores.".is_enabled", 1);
		$this->db->order_by($this->tbl_assigned_stores.".created_date","asc");
		$this->db->limit(1);
		return $this->db->get($this->tbl_assigned_stores);
	}

	function getAssignedProducts($assigned_stores_id)
	{
		$this->db->select($this->tbl_assigned_products.".*,
						 (SELECT name FROM tbl_".$this->tbl_products." WHERE tbl_".$this->tbl_products.".id = tbl_".$this->tbl_assigned_products.".products_id) AS products_name, 
						 (SELECT (SELECT name FROM tbl_".$this->tbl_ref_products." WHERE tbl_".$this->tbl_ref_products.".id = tbl_".$this->tbl_products.".ref_products_id) FROM tbl_".$this->tbl_products." WHERE tbl_".$this->tbl_products.".id = tbl_".$this->tbl_assigned_products.".products_id) AS ref_prodcts_name,
						 (SELECT price FROM tbl_".$this->tbl_products." WHERE tbl_".$this->tbl_products.".id = tbl_".$this->tbl_assigned_products.".products_id) AS price,
						 (SELECT file_image FROM tbl_".$this->tbl_products." WHERE tbl_".$this->tbl_products.".id = tbl_".$this->tbl_assigned_products.".products_id) AS file_image,
						 (SELECT expired_date FROM tbl_".$this->tbl_products." WHERE tbl_".$this->tbl_products.".id = tbl_".$this->tbl_assigned_products.".products_id) AS expired_date");
		$this->db->where($this->tbl_assigned_products.'.assigned_stores_id',$assigned_stores_id);
		$this->db->where($this->tbl_assigned_products.".id", $assigned_stores_id);
		$this->db->where($this->tbl_assigned_products.".is_enabled", 1);
		$this->db->order_by("(SELECT name FROM tbl_".$this->tbl_products." WHERE tbl_".$this->tbl_products.".id = tbl_".$this->tbl_assigned_products.".products_id) ASC", null, FALSE);
		return $this->db->get($this->tbl_assigned_products);
	}

	function getProductDetail($id)
	{
		$this->db->select($this->tbl_products.".*,
						 (SELECT name FROM tbl_".$this->tbl_ref_products." WHERE tbl_".$this->tbl_ref_products.".id = tbl_".$this->tbl_products.".ref_products_id) AS ref_products_name");
		$this->db->where($this->tbl_products.".id", $id);
		$this->db->where("is_enabled", 1);
		return $this->db->get($this->tbl_products);
	}

	function getStoreDetail($id)
	{
		$this->db->select($this->tbl_stores.".*");
		$this->db->where($this->tbl_stores.".id", $id);
		$this->db->where("is_enabled", 1);
		return $this->db->get($this->tbl_stores);
	}
	
	function getMulti($id)
	{
		$this->db->join($this->tbl_activities_multi,$this->tbl_activities_multi.".id=".$this->tbl_activities.".".$this->tbl_activities_multi."_id");
		$this->db->where($this->tbl_activities_multi.".id",$id);
		$query = $this->db->get($this->tbl_activities);
		return $query;
	}
	
	function setUpdate($data_pack,$where_pack)
	{
		$this->db->update($this->tbl_activities,$data_pack,$where_pack);
	}
	
	function setInsert($data_pack)
	{
		$this->db->insert($this->tbl_activities,$data_pack);
		$last_id = $this->db->insert_id();
		return $last_id;
	}
	

	function setDelete($id)
	{
		$status = 0;
		#select first
		$this->db->where('id',$id);
		$this->db->where('is_enabled',1);
		
		$query = $this->db->get($this->tbl_activities);
		
		if($query->num_rows() == 0){
			
			if($status == 0){
				$this->db->where('id',$id);
				$this->db->delete($this->tbl_activities);

				$status = 1;
			}
		}
		return $status;
	}
	
}
?>
