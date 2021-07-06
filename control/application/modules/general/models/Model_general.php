<?php
class Model_general extends CI_Model {

	var $table_rules_regulation_subs = "ref_rules_regulation_subs";

	var $table_ref_products = "ref_products";
	var $table_assigned_stores = "assigned_stores";
	var $table_stores = "stores";
	var $table_projects = "projects";
	var $table_products = "products";

	var $tbl_cities = "ref_cities";
	var $tbl_subcategories = "ref_subcategories";
	var $tbl_customers = "customers";

	var $tbl_customer_saldo_histories = "customer_saldo_histories";
	var $tbl_customer_reward_histories = "customer_reward_histories";
	var $tbl_customer_saldo_topups = "customer_saldo_topups";

	var $tbl_stocks = "stocks";
	var $tbl_purchases = "purchases";

	var $tbl_carts = "carts";
	var $tbl_cart_items = "cart_items";
	var $tbl_cart_item_details = "cart_item_details";

	var $tbl_payments = "payments";

	function __construct()
	{
		parent::__construct();
	}

	function getRefRulesRegulationSubs($ref_id)
	{
		$this->db->where('ref_rules_regulations_id',$ref_id);
		$query = $this->db->get($this->table_rules_regulation_subs);
		return $query;
	}

	function getProjects($ref_id)
	{
		$this->db->where('brands_id',$ref_id);
		$query = $this->db->get($this->table_projects);
		return $query;
	}

	function getProducts($ref_id)
	{
		$this->db->where('brands_id',$ref_id);
		$query = $this->db->get($this->table_products);
		return $query;
	}

	function getAssignedStores($ref_id)
	{
		$this->db->select($this->table_assigned_stores.".*,
						 (SELECT name FROM tbl_".$this->table_stores." WHERE tbl_".$this->table_stores.".id = tbl_".$this->table_assigned_stores.".stores_id) AS name");
		$this->db->where('projects_id',$ref_id);
		$query = $this->db->get($this->table_assigned_stores);
		return $query;
	}

	function getStores($ref_id)
	{
		$this->db->select($this->table_stores.".*");
		$this->db->where('brands_id',$ref_id);
		$query = $this->db->get($this->table_stores);
		return $query;
	}

	function getRefproducts($ref_id)
	{
		$this->db->where('brands_id',$ref_id);
		$query = $this->db->get($this->table_ref_products);
		return $query;
	}

	function getCities($ref_provinces_id)
	{
		$this->db->where('ref_provinces_id',$ref_provinces_id);
		$this->db->where('types',2);
		$query = $this->db->get($this->tbl_cities);
		return $query;
	}

	function getSubcategories($ref_id)
	{
		$query = $this->db->get($this->tbl_subcategories);
		return $query;
	}

	function getStocks($ref_id)
	{
		$this->db->where('stores_id',$ref_id);
		$this->db->where('is_enabled',1);
		$query = $this->db->get($this->tbl_stocks);
		return $query;
	}

	function checkEmailExists($email,$tbl,$id=null)
	{
		if(!empty($id)){
			$this->db->where('id !=',$id);
		}
		$this->db->where('email',$email);
		$query = $this->db->get($tbl);
		return $query->num_rows();
	}

	function checkCardExists($card_number,$id=null)
	{
		if(!empty($id)){
			$this->db->where('id !=',$id);
		}
		$this->db->where('card_number',$card_number);
		$query = $this->db->get($this->tbl_customers);
		return $query->num_rows();
	}

	function getReferences($tbl,$id,$ref_id=null,$ref_tbl=null)
	{
		$tbl = "ref_".$tbl;

		if(!empty($ref_tbl) && !empty($ref_id)){
			$this->db->where($tbl.".".$ref_tbl."_id",$id);
		}

		$this->db->where($tbl.".id",$id);
		$q = $this->db->get($tbl);
		$ref_name = "-";
		if($q->num_rows() > 0){
			$r =  $q->row_array();
			$ref_name = $r['name'];
		}

		return $ref_name;
	}

	function getItemTarget($tbl=NULL, $store=NULL, $category=NULL, $subcategory=NULL, $items=NULL)
	{

		if(!empty($store)){
			$this->db->where("stores_id",$store);
		}

		if(!empty($category)){
			$this->db->where("ref_categories_id",$category);
		}


		if(!empty($subcategory)){
			$this->db->where("ref_subcategories_id",$subcategory);
		}

		if(!empty($items)){
			$this->db->where_in('id', $items);
		}

		$this->db->order_by("name", "desc");
		$q = $this->db->get($tbl);
		return $q;

	}

	function getTotalSaldoSpent($id,$date=null)
	{
		$this->db->select_sum($this->tbl_customer_saldo_histories.'.saldo','total_saldo');
		$this->db->where($this->tbl_customer_saldo_histories.'.types','spent');
    	$this->db->where($this->tbl_customer_saldo_histories.'.customers_id', $id);
    	$this->db->where($this->tbl_customer_saldo_histories.'.is_enabled', 1);

		if($date){
    		$this->db->where($this->tbl_customer_saldo_histories.'.created_date <=', date("Y-m-d H:i",strtotime($date)));
    	}

		$q_earn = $this->db->get($this->tbl_customer_saldo_histories);

		$spent = 0;
		if($q_earn->num_rows() > 0){
			$spent = $q_earn->row_array()['total_saldo'];
		}

		return $spent;
	}

	function getTotalSaldoRefund($id,$date=null)
	{
		$this->db->select_sum($this->tbl_customer_saldo_histories.'.saldo','total_saldo');
		$this->db->where($this->tbl_customer_saldo_histories.'.types','refund');
    	$this->db->where($this->tbl_customer_saldo_histories.'.customers_id', $id);
    	$this->db->where($this->tbl_customer_saldo_histories.'.is_enabled', 1);

    	if($date){
    		$this->db->where($this->tbl_customer_saldo_histories.'.created_date <=', date("Y-m-d H:i",strtotime($date)));
    	}

		$q_earn = $this->db->get($this->tbl_customer_saldo_histories);

		$earn = 0;
		if($q_earn->num_rows() > 0){
			$earn = $q_earn->row_array()['total_saldo'];
		}

		return $earn;
	}

	function getTotalSaldoEarn($id,$date=null)
	{
		$this->db->select_sum($this->tbl_customer_saldo_histories.'.saldo','total_saldo');
		$this->db->where($this->tbl_customer_saldo_histories.'.types','earn');
    	$this->db->where($this->tbl_customer_saldo_histories.'.customers_id', $id);
    	$this->db->where($this->tbl_customer_saldo_histories.'.is_enabled', 1);

    	if($date){
    		$this->db->where($this->tbl_customer_saldo_histories.'.created_date <=', date("Y-m-d H:i",strtotime($date)));
    	}

		$q_earn = $this->db->get($this->tbl_customer_saldo_histories);

		$earn = 0;
		if($q_earn->num_rows() > 0){
			$earn = $q_earn->row_array()['total_saldo'];
		}

		return $earn;
	}

	function getTotalRewardRefund($id,$date=null)
	{
		$this->db->select_sum($this->tbl_customer_reward_histories.'.reward_poin','total_reward_poin');
		$this->db->where($this->tbl_customer_reward_histories.'.types','refund');
    	$this->db->where($this->tbl_customer_reward_histories.'.customers_id', $id);
    	$this->db->where($this->tbl_customer_reward_histories.'.is_enabled', 1);

    	if($date){
    		$this->db->where($this->tbl_customer_reward_histories.'.created_date <=', date("Y-m-d H:i",strtotime($date)));
    	}

		$q_earn = $this->db->get($this->tbl_customer_reward_histories);

		$earn = 0;
		if($q_earn->num_rows() > 0){
			$earn = $q_earn->row_array()['total_reward_poin'];
		}

		return $earn;
	}

	function getTotalRewardSpent($id,$date=null)
	{
		$this->db->select_sum($this->tbl_customer_reward_histories.'.reward_poin','total_reward_poin');
		$this->db->where($this->tbl_customer_reward_histories.'.types','spent');
    	$this->db->where($this->tbl_customer_reward_histories.'.customers_id', $id);
    	$this->db->where($this->tbl_customer_reward_histories.'.is_enabled', 1);

    	if($date){
    		$this->db->where($this->tbl_customer_reward_histories.'.created_date <=', date("Y-m-d H:i",strtotime($date)));
    	}

		$q_earn = $this->db->get($this->tbl_customer_reward_histories);

		$spent = 0;
		if($q_earn->num_rows() > 0){
			$spent = $q_earn->row_array()['total_reward_poin'];
		}

		return $spent;
	}

	function getTotalRewardEarn($id,$date=null)
	{
		$this->db->select_sum($this->tbl_customer_reward_histories.'.reward_poin','total_reward_poin');
		$this->db->where($this->tbl_customer_reward_histories.'.types','earn');
    	$this->db->where($this->tbl_customer_reward_histories.'.customers_id', $id);
    	$this->db->where($this->tbl_customer_reward_histories.'.is_enabled', 1);

    	if($date){
    		$this->db->where($this->tbl_customer_reward_histories.'.created_date <=', date("Y-m-d H:i",strtotime($date)));
    	}

		$q_earn = $this->db->get($this->tbl_customer_reward_histories);

		$earn = 0;
		if($q_earn->num_rows() > 0){
			$earn = $q_earn->row_array()['total_reward_poin'];
		}

		return $earn;
	}

	function getLatestTopups($id)
	{
		$this->db->select_max('id');
    	$this->db->where('customers_id', $id);
		$q = $this->db->get($this->tbl_customer_saldo_topups);
		return $q;
	}

	function getTopups($id)
	{
		$this->db->select('discount_type, discount_value');
    	$this->db->where('id', $id);
		$q = $this->db->get($this->tbl_customer_saldo_topups);
		return $q;
	}

	function getTotalPurchasesStock($ref_id)
	{
		$this->db->select_sum($this->tbl_purchases.'.qty','total_qty');
		$this->db->where($this->tbl_purchases.'.stocks_id',$ref_id);
		$this->db->where($this->tbl_purchases.'.is_enabled',1);
		$q = $this->db->get($this->tbl_purchases);
		$total_purchases_qty = 0;
		if($q->num_rows() > 0){
			$total_purchases_qty = $q->row_array()['total_qty'];
		}

		return $total_purchases_qty;
	}

	function getTotalUsedStock($ref_id)
	{
		$this->db->select_sum($this->tbl_cart_item_details.'.qty','total_qty');
		$this->db->where($this->tbl_cart_item_details.'.ref_id',$ref_id);
		$this->db->where(
						"(SELECT (SELECT canceled_date FROM tbl_".$this->tbl_carts." WHERE tbl_".$this->tbl_carts.".id = tbl_".$this->tbl_cart_items.".carts_id) FROM tbl_".$this->tbl_cart_items." WHERE tbl_".$this->tbl_cart_items.".id = tbl_".$this->tbl_cart_item_details.".cart_items_id) IS NULL",null,false
						);
		$this->db->where(
						"(SELECT (SELECT paid_date FROM tbl_".$this->tbl_carts." WHERE tbl_".$this->tbl_carts.".id = tbl_".$this->tbl_cart_items.".carts_id) FROM tbl_".$this->tbl_cart_items." WHERE tbl_".$this->tbl_cart_items.".id = tbl_".$this->tbl_cart_item_details.".cart_items_id) IS NOT NULL",null,false
						);
		$q_cart_item_details = $this->db->get($this->tbl_cart_item_details);

		$total_used_qty = 0;
		if($q_cart_item_details->num_rows() > 0){
			$total_used_qty = $q_cart_item_details->row_array()['total_qty'];
		}

		return $total_used_qty;
	}
}
