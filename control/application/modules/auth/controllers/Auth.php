<?php
class Auth extends MX_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('adminuser_auths/Model_adminuser_auths', 'adminuser_auths');
		$this->load->model('menu_auths/Model_menu_auths', 'menu_auths');
	}
	
	function index(){}
	
	function publicAuth()
	{
		
		if($this->session->userdata("sess_admin")){
			redirect("index",'refresh');
		}
	
	}
	
	function displayAuth()
	{
		$display = "display:none;";
		if($this->session->userdata("sess_admin")){
			$display = "";
		}
		return $display;
	}
	
	function privateAuth()
	{
		if(!$this->session->userdata("sess_admin")){
			
			redirect("login",'refresh');
		}
	
	}
	
	
	function forbiddenAuth()
	{
		$uri2 = $this->uri->segment(1);
		$q = $this->adminuser_auths->getAccount($this->session->userdata("sess_admin")['id']);
		$adminuser_auths_level_id = "";
		if($q->num_rows() > 0)
		{
			$row = $q->row_array(); 
			$adminuser_auths_level_id = $row['adminuser_levels_id'];
		}
		$q = $this->menu_auths->getMenuFromUri($uri2);

		var_dump(in_array($uri2, $this->static_menu()));


		if($q->num_rows() > 0){
			$row = $q->row(); 
			$menu_id = $row->id;
			$q =  $this->menu_auths->getMenuPermission($adminuser_auths_level_id,$menu_id);
			if($q->num_rows() == 0){
				redirect("index");
			}
		}else if(!in_array($uri2, $this->static_menu())){
			redirect("index");
		}

	}

	function static_menu()
	{
		return array("carts","checkout","payment","confirmation","saldo_rewards","transactions","empty");
	}
	
}

?>
