<?php
class Model_dashboard extends CI_Model {

	var $table_partners = "partners";
	var $table_visits = "visits";

	function __construct()
	{
		parent::__construct();
		$this->load->helper('date');
	}
	
	function getTotalVisit()
	{
		$this->db->where($this->table_visits.'.status','open');
		$query = $this->db->count_all_results($this->table_visits);
		return $query;
	}

	function getTotalVisitPRO()
	{
		$now = date("Y-m-d");
		$this->db->where($this->table_visits.'.status','open');
		$this->db->where('DATE(tbl_'.$this->table_visits.'.visit_date) >=', $now);
		$this->db->where('DATE(tbl_'.$this->table_visits.'.visit_date) <=', $now);
		$query = $this->db->count_all_results($this->table_visits);
		return $query;
	}

	function getTotalVisitBackend()
	{
		$this->db->where($this->table_visits.'.taked_by', $this->session->userdata('sess_admin')['id']);
		$this->db->where($this->table_visits.'.status','open');
		$query = $this->db->count_all_results($this->table_visits);
		return $query;
	}

	function getTotalLayanan($sch_pack)
	{
		if($sch_pack){
			if($sch_pack['sch1_parm'] != 'null' && !empty($sch_pack['sch1_parm']))
			{
				$this->db->where('YEAR(tbl_'.$this->table_visits.'.visit_date)', $sch_pack['sch1_parm']);
			}
			if($sch_pack['sch2_parm'] != 'null' && !empty($sch_pack['sch2_parm']))
			{
				$this->db->where('MONTH(tbl_'.$this->table_visits.'.visit_date)', $sch_pack['sch2_parm']);
			}
		}

		$this->db->where($this->table_visits.'.type_of_service','layanan');	
		$query = $this->db->count_all_results($this->table_visits);
		return $query;
	}

	function getTotalPagu($sch_pack)
	{
		if($sch_pack){
			if($sch_pack['sch1_parm'] != 'null' && !empty($sch_pack['sch1_parm']))
			{
				$this->db->where('YEAR(tbl_'.$this->table_visits.'.visit_date)', $sch_pack['sch1_parm']);
			}
			if($sch_pack['sch2_parm'] != 'null' && !empty($sch_pack['sch2_parm']))
			{
				$this->db->where('MONTH(tbl_'.$this->table_visits.'.visit_date)', $sch_pack['sch2_parm']);
			}
		}

		$this->db->select_sum('budget');	
		$this->db->where($this->table_visits.".status <>", 'booking');
		$this->db->where($this->table_visits.".deleted_date IS NULL");
		$r = $this->db->get($this->table_visits)->row_array();
		return !is_null($r['budget']) ? $r['budget'] : 0;
	}

	function getTotalNonLayanan($sch_pack)
	{
		if($sch_pack){
			if($sch_pack['sch1_parm'] != 'null' && !empty($sch_pack['sch1_parm']))
			{
				$this->db->where('YEAR(tbl_'.$this->table_visits.'.visit_date)', $sch_pack['sch1_parm']);
			}

			if($sch_pack['sch2_parm'] != 'null' && !empty($sch_pack['sch2_parm']))
			{
				$this->db->where('MONTH(tbl_'.$this->table_visits.'.visit_date)', $sch_pack['sch2_parm']);
			}
		}
		$this->db->where($this->table_visits.'.type_of_service','non-layanan');
		$query = $this->db->count_all_results($this->table_visits);
		return $query;
	}

	function getTotalAnswer($sch_pack)
	{
		if($sch_pack){
			if($sch_pack['sch1_parm'] != 'null' && !empty($sch_pack['sch1_parm']))
			{
				$this->db->where('YEAR(tbl_'.$this->table_visits.'.visit_date)', $sch_pack['sch1_parm']);
			}
			if($sch_pack['sch2_parm'] != 'null' && !empty($sch_pack['sch2_parm']))
			{
				$this->db->where('MONTH(tbl_'.$this->table_visits.'.visit_date)', $sch_pack['sch2_parm']);
			}
		}

		$this->db->where($this->table_visits.'.answer IS NOT NULL');	
		$query = $this->db->count_all_results($this->table_visits);
		return $query;
	}

	function getTotalEmptyAnswer($sch_pack)
	{
		if($sch_pack){
			if($sch_pack['sch1_parm'] != 'null' && !empty($sch_pack['sch1_parm']))
			{
				$this->db->where('YEAR(tbl_'.$this->table_visits.'.visit_date)', $sch_pack['sch1_parm']);
			}
			if($sch_pack['sch2_parm'] != 'null' && !empty($sch_pack['sch2_parm']))
			{
				$this->db->where('MONTH(tbl_'.$this->table_visits.'.visit_date)', $sch_pack['sch2_parm']);
			}
		}

		$this->db->where($this->table_visits.'.answer IS NULL');
		$this->db->or_where($this->table_visits.'.answer','');
		$this->db->where($this->table_visits.'.status <>','closed');	
		$query = $this->db->count_all_results($this->table_visits);
		return $query;
	}

	function getLastLogin()
	{
		if(!$this->session->userdata('sess_admin'))
			return "-";

		if(is_null($this->session->userdata('sess_admin')['login_date']))
			return "-";

		return $last_login = date("d M, Y H:i", strtotime($this->session->userdata('sess_admin')['login_date']));
	}

	function getLastVisit()
	{
		$q = $this->getVisits(1,'desc');
		$last_visit = "-";
		if($q->num_rows() > 0){
			$r = $q->row_array();
			$last_visit = date("d M, Y H:i", strtotime($r['modified_date']));
		}
		return $last_visit;
	}

	function getLastVisitPRO()
	{
		$q = $this->getVisitsPRO(1,'desc');
		$last_visit = "-";
		if($q->num_rows() > 0){
			$r = $q->row_array();
			$last_visit = date("d M, Y H:i", strtotime($r['modified_date']));
		}
		return $last_visit;
	}

	function getLastVisitBackend()
	{
		$q = $this->getVisitsbackend(1,'desc');
		$last_visit = "-";
		if($q->num_rows() > 0){
			$r = $q->row_array();
			$last_visit = date("d M, Y H:i", strtotime($r['modified_date']));
		}
		return $last_visit;
	}

	function getLatestVisitBackend()
	{
		$q = $this->getVisitsBackend();
		return $q;
	}


	function getLatestVisit()
	{
		$q = $this->getVisits();
		return $q;
	}

	function getLatestVisitPRO()
	{
		$q = $this->getVisitsPRO();
		return $q;
	}

	function getVisits($limit=3,$order='asc'){
		$this->db->where($this->table_visits.'.status <>', 'booking');
		$this->db->order_by($this->table_visits.'.visit_date',$order);
		$this->db->limit($limit);
		$q = $this->db->get($this->table_visits);
		return $q;
	}

	function getVisitsPRO($limit=3,$order='asc'){
		$this->db->where($this->table_visits.'.status <>', 'booking');
		$this->db->where('DATE(tbl_'.$this->table_visits.'.visit_date)', date("Y-m-d",now()));
		$this->db->order_by($this->table_visits.'.visit_date',$order);
		$this->db->limit($limit);
		$q = $this->db->get($this->table_visits);
		return $q;
	}

	function getVisitsBackend($limit=3,$order='asc'){
		$this->db->where($this->table_visits.'.status <>', 'booking');
		$this->db->where($this->table_visits.'.taked_by', $this->session->userdata('sess_admin')['id']);
		$this->db->order_by($this->table_visits.'.visit_date',$order);
		$this->db->limit($limit);
		$q = $this->db->get($this->table_visits);
		return $q;
	}
	


}
?>