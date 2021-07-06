<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class General extends MX_Controller  {
	
	var $path = "general";
	 
	function __construct()
	{
		parent::__construct();
		$this->load->model($this->path."/Model_".$this->path, $this->path);
	}

	public function do_get_ref_rules_regulation_subs($ref_id=NULL)
	{
		$q = $this->general->getRefRulesRegulationSubs($ref_id);
		$lists = array();
		if($q->num_rows() > 0){
			$lists = $q->result_array();
			foreach($lists as $key=>$r){
				$lists[$key]['name'] =  ucwords($r['name']);
			}
		}
		
		$data = array(
					  'admin_url' => base_url(),
					  'lists'=>$lists
					  );
		return $this->parser->parse("dropdown.html", $data);
	}

	public function do_get_ref_products($ref_id=NULL)
	{
		$q = $this->general->getRefproducts($ref_id);
		$lists = array();
		if($q->num_rows() > 0){
			$lists = $q->result_array();
			foreach($lists as $key=>$r){
				$lists[$key]['name'] =  ucwords($r['name']);
			}
		}
		
		$data = array(
					  'admin_url' => base_url(),
					  'lists'=>$lists
					  );
		return $this->parser->parse("dropdown.html", $data);
	}

	public function do_get_projects($ref_id=NULL)
	{
		$q = $this->general->getProjects($ref_id);
		$lists = array();
		if($q->num_rows() > 0){
			$lists = $q->result_array();
			foreach($lists as $key=>$r){
				$lists[$key]['name'] =  ucwords($r['name']);
			}
		}
		
		$data = array(
					  'admin_url' => base_url(),
					  'lists'=>$lists
					  );
		return $this->parser->parse("dropdown.html", $data);
	}

	public function do_get_products($ref_id=NULL)
	{
		$q = $this->general->getProducts($ref_id);
		$lists = array();
		if($q->num_rows() > 0){
			$lists = $q->result_array();
			foreach($lists as $key=>$r){
				$lists[$key]['name'] =  ucwords($r['name']);
			}
		}
		
		$data = array(
					  'admin_url' => base_url(),
					  'lists'=>$lists
					  );
		return $this->parser->parse("dropdown.html", $data);
	}

	public function do_get_stores($ref_id=NULL)
	{
		$q = $this->general->getAssignedStores($ref_id);
		$lists = array();
		if($q->num_rows() > 0){
			$lists = $q->result_array();
			foreach($lists as $key=>$r){
				$lists[$key]['id'] = $r['stores_id'];
				$lists[$key]['name'] =  ucwords($r['name']);
			}
		}
		
		$data = array(
					  'admin_url' => base_url(),
					  'lists'=>$lists
					  );
		return $this->parser->parse("dropdown.html", $data);
	}

	public function do_get_stores_lists($ref_id=NULL)
	{
		$q = $this->general->getStores($ref_id);
		$lists = array();
		if($q->num_rows() > 0){
			$lists = $q->result_array();
			foreach($lists as $key=>$r){
				$lists[$key]['id'] = $r['id'];
				$lists[$key]['name'] =  ucwords($r['name']);
			}
		}
		
		$data = array(
					  'admin_url' => base_url(),
					  'lists'=>$lists
					  );
		return $this->parser->parse("dropdown.html", $data);
	}

	public function do_get_ref_cities($ref_provinces_id=NULL)
	{
		$q = $this->general->getCities($ref_provinces_id);
		$lists = array();
		if($q->num_rows() > 0){
			$lists = $q->result_array();
			foreach($lists as $key=>$r){
				$lists[$key]['name'] =  ucwords(strtolower($r['name']));
			}
		}
		
		$data = array(
					  'admin_url' => base_url(),
					  'lists'=>$lists
					  );
		return $this->parser->parse("dropdown.html", $data);
	}

	public function do_get_stocks($ref_id=NULL)
	{
		$q = $this->general->getStocks($ref_id);
		$lists = array();
		if($q->num_rows() > 0){
			$lists = $q->result_array();
			foreach($lists as $key=>$r){
				$lists[$key]['id'] = $r['id'];
				$lists[$key]['name'] =  ucwords(strtolower($r['name']));
			}
		}
		
		$data = array(
					  'admin_url' => base_url(),
					  'lists'=>$lists
					  );
		return $this->parser->parse("dropdown.html", $data);
	}

	public function do_check_email($getEmail=NULL)
	{
		$email = !is_null($getEmail) ? $getEmail : $this->input->post('email');
		$count = $this->general->checkEmailExists($email, $this->input->post('table'), $this->input->post('id'));	
		if ($count > 0)
		{
			$status = 'false';
		}else{
			$status = 'true';
		}
		
		if($getEmail){
			return $status;
		}else{
			echo $status;
		}
	}

	public function do_get_available_stock($id)
	{
		$used_stock = $this->general->getTotalUsedStock($id);
		if(empty($used_stock)){
			$used_stock = 0;
		}
		return $used_stock;
	}

	public function set_calculate_available_qty($qty, $used_qty)
	{
		 return (int)$qty - (int)$used_qty;
	}

}
