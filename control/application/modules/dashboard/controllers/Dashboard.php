<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends MX_Controller  {

	var $path = "dashboard";
	 
	function __construct()
	{
		parent::__construct();
		$this->load->model('configs/Model_configs', 'configs');
		$this->load->model('dashboard/Model_'.$this->path, $this->path);
		$this->load->model('general/Model_general', 'general');
	}

	function index()
	{
		
		Modules::run('auth/privateAuth');
		Modules::run('auth/forbiddenAuth');
		$this->grid();
	}
	
	function grid($err=NULL)
	{
		Modules::run('layout/setheader');
		$sidebar = Modules::run('layout/sidebar');
		$contents = $this->grid_content($err);
	
		$data = array(
				  'admin_url' => base_url(),
				  'sidebar'=>$sidebar,
				  'contents' => $contents
				  );
		$this->parser->parse('layout/contents.html', $data);
		
		Modules::run('layout/setfooter');
	}
	
	function grid_content($err=NULL)
	{
		$q = $this->configs->getDetail(1);
		$r = $q->row();
		$site_name = $r->meta_title;
		$template = "";

		$brands_id = $this->session->userdata("sess_admin")['brands_id'];
		$template = "";
		if($brands_id > 0){
			$template = "_fo";
		}

		if($this->session->userdata("sess_admin")['adminuser_levels_id'] == 13){
			$template = "_fo";
		}
		
		$data['admin_url'] = base_url();
		$data['site_name']=$site_name;
		$data['title_link']=$this->path;
		$data['account_name'] = $this->session->userdata("sess_admin")['name'];

		return $this->parser->parse('dashboard'.$template.'.html', $data, TRUE);
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */