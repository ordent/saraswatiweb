<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class layout extends MX_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('adminuser_auths/Model_adminuser_auths', 'adminuser_auths');
		$this->load->model('menu_auths/Model_menu_auths', 'menu_auths');
		$this->load->model('configs/model_configs', 'configs');
	}
	
	public function auth()
	{
		return Modules::run('auth/privateAuth');
	}
	
	
	function index(){}
	
	
	function setheader_login()
	{
		$q = $this->configs->getDetail(1);
		$r = $q->row();
		$site_name = $r->meta_title;

		$data = array(
				  'base_url' => base_url(),
				  'site_name'=> $site_name
				  );
		$this->parser->parse('layout/header_login.html', $data);
	}

	function setheader_modal()
	{
		$q = $this->configs->getDetail(1);
		$r = $q->row();
		$site_name = $r->meta_title;

		$data = array(
				  'base_url' => base_url(),
				  'site_name'=> $site_name
				  );
		$this->parser->parse('layout/header_modal.html', $data);
	}
	
	
	function setheader()
	{
		$q = $this->adminuser_auths->getAccount($this->session->userdata("sess_admin")['id']);
		$admin_name =  "Unknown";
		$adminuser_auths_id = "#";
		$adminuser_auths_level_id = "";
		$title_parent = "";
		if($q->num_rows() > 0)
		{
			foreach($q->result() as $row)
			{
				if($row->parent_id > 0){
					$q_parent = $this->adminuser_auths->getParent($row->parent_id);
					$r_parent = $q_parent->row();
					$title_parent = " (".$r_parent->title.") ";
				}
				
				$admin_user = ucfirst($row->name)." - ";
				$admin_name = $admin_user.$row->title.$title_parent;
				$adminuser_auths_level_id = $row->adminuser_levels_id;
				$adminuser_auths_id = $row->id;
			}
		}else{
			$this->auth();
		}

		$q = $this->configs->getDetail(1);
		$r = $q->row();
		$site_name = $r->meta_title;
		
		$data = array(
				  'base_url' => base_url(),
				  'admin_url'=>base_url(),
				  'admin_name'=>$admin_name,
				  'adminuser_auths_id'=>$adminuser_auths_id,
				  'site_name'=>$site_name
				  );
		$this->parser->parse('layout/header.html', $data);
	}
	
	
	function sidebar()
	{	
		$uri2 = $this->uri->segment(1);
		$uri2 = $uri2 == "contents" ? $this->uri->segment(3) : $uri2;
		$adminuser_auths_level_id = $this->session->userdata("sess_admin")['adminuser_levels_id'];
		$q =  $this->menu_auths->getMenu($adminuser_auths_level_id);
		$list_menu = array();
		if($q->num_rows() > 0)
		{
			foreach($q->result() as $r_menu)
			{
				$q_menu1 =  $this->menu_auths->getMenu($adminuser_auths_level_id,$r_menu->menus_id);
				$list_menu1 = array();
				$num_menu1 = $q_menu1->num_rows();
				
				if($num_menu1 > 0)
				{
					foreach($q_menu1->result() as $r_menu1)
					{
							$q_menu2 =  $this->menu_auths->getMenu($adminuser_auths_level_id,$r_menu1->menus_id);
							$list_menu2 = array();
							$num_menu2 = $q_menu2->num_rows();
							if($num_menu2 > 0)
							{
								foreach($q_menu2->result() as $r_menu2)
								{
									
									$num_menu3 = 0;
									
									$q_selectParent2 = $this->menu_auths->getSelectParentMenu($uri2,$r_menu2->menus_id);
									$uri_menu2 = $r_menu2->menus_uri == "#" ? $r_menu2->menus_uri : site_url($r_menu2->menus_uri);
									$actived_menu2 = $uri2 == $r_menu2->menus_uri || $q_selectParent2['status'] > 0 ? "active" : "";

									$dropdown_toogle2 = $num_menu3 > 0 ? "class=\"".$actived_menu2." collapsed\" data-toggle=\"collapse\" href=\"#togglePages".$r_menu2->menus_id."\"" : "class=\"".$actived_menu2."\"";
									$cevron_toogle2 = $num_menu3 > 0 ? "<i class=\"icon-chevron-down pull-right\"></i><i class=\"icon-chevron-up pull-right\"></i>" : "";
									$dropdown_menu2 = $num_menu3 > 0 ? "id=\"togglePages".$r_menu2->menus_id."\" class=\"collapse unstyled\"" : "";
									$list_menu2[] = array(
												"base_url"=>base_url(),
												 "title"=>ucwords($r_menu2->menus_title),
												 "uri"=>$uri_menu2,
												 "actived"=>$actived_menu2,
												 "dropdown_toogle"=>$dropdown_toogle2,
												 "cevron_toogle"=>$cevron_toogle2,
												 "dropdown_menu"=>$dropdown_menu2
												 );
								}
							}
						
						
						$q_selectParent1 = $this->menu_auths->getSelectParentMenu($uri2,$r_menu1->menus_id);
						if($q_selectParent1['status'] > 0){
							$uri2 = $r_menu1->menus_uri;
						}
						$uri_menu1 = $r_menu1->menus_uri == "#" || $num_menu2 > 0 ? $r_menu1->menus_uri : site_url($r_menu1->menus_uri);
						$actived_menu1 = $uri2 == $r_menu1->menus_uri || $q_selectParent1['status'] > 0 ? "active" : "";
						$dropdown_toogle1 = $num_menu2 > 0 ? "class=\"".$actived_menu1." collapsed\" data-toggle=\"collapse\" href=\"#togglePages".$r_menu1->menus_id."\"" : "class=\"".$actived_menu1."\"";
						$cevron_toogle1 = $num_menu2 > 0 ? "<i class=\"icon-chevron-down pull-right\"></i><i class=\"icon-chevron-up pull-right\"></i>" : "";
						$dropdown_menu1 = $num_menu2 > 0 ? "id=\"togglePages".$r_menu1->menus_id."\" class=\"collapse unstyled\"" : "";
						$list_menu1[] = array(
									 "list_menu2"=>$list_menu2,
									 "title"=>ucwords($r_menu1->menus_title),
									 "uri"=>$uri_menu1,
									 "actived"=>$actived_menu1,
									 "dropdown_toogle"=>$dropdown_toogle1,
									 "cevron_toogle"=>$cevron_toogle1,
									 "dropdown_menu"=>$dropdown_menu1
									 );
					}
				}
				
				$q_selectParent = $this->menu_auths->getSelectParentMenu($uri2,$r_menu->menus_id);
				$uri_menu = $r_menu->menus_uri == "#" || $num_menu1 > 0 ? $r_menu->menus_uri : site_url($r_menu->menus_uri);
				$actived_menu = $uri2 == $r_menu->menus_uri || $q_selectParent['status'] > 0 ? "active" : "";
				$q_selectParent['status']." ".$q_selectParent['uri']." ".$r_menu->menus_id."<br/>";
				$dropdown_toogle = $num_menu1 > 0 ? "class=\"".$actived_menu." collapsed\" data-toggle=\"collapse\" href=\"#togglePages".$r_menu->menus_id."\"" : "class=\"".$actived_menu."\"";
				$cevron_toogle = $num_menu1 > 0 ? "<i class=\"icon-chevron-down pull-right\"></i><i class=\"icon-chevron-up pull-right\"></i>" : "";
				$dropdown_menu = $num_menu1 > 0 ? "id=\"togglePages".$r_menu->menus_id."\" class=\"collapse unstyled\"" : "";
				
				
				
				$list_menu[] = array(
									 "list_menu1"=>$list_menu1,
									 "title"=>ucwords($r_menu->menus_title),
									 "icon"=>$r_menu->icon,
									 "uri"=>$uri_menu,
									 "actived"=>$actived_menu,
									 "dropdown_toogle"=>$dropdown_toogle,
									 "cevron_toogle"=>$cevron_toogle,
									 "dropdown_menu"=>$dropdown_menu
									 );
			}

		}
		
		$data = array(
				  'base_url' => base_url(),
				  'admin_url'=>base_url(),
				  'list_menu' => $list_menu
				  );
		return $this->parser->parse('layout/sidebar.html', $data, TRUE);
	}


	function sidebar_static()
	{	
		
		$data = array(
				  'base_url' => base_url(),
				  'admin_url'=>base_url(),
				  );
		return $this->parser->parse('layout/sidebar_static_manager.html', $data, TRUE);
	}
	
	
	function setfooter_value()
	{
		$q = $this->configs->getDetail(1);
		$r = $q->row();
		$site_name = $r->meta_title;

		$year = gmdate("Y");
		$version = "1.8.0";
		return "&copy; ".$year." Copyright by ".$site_name;
	}
	
	
	function setfooter()
	{
		$footer = $this->setfooter_value();
		$data = array(
				  'base_url' => base_url(),
				  'footer'=>$footer
				  );
		$this->parser->parse('layout/footer.html', $data);
	}
	
	
	function setfooterlogin()
	{
		$footer = $this->setfooter_value();
		$data = array(
				  'base_url' => base_url(),
				  'footer'=>$footer
				  );
		$this->parser->parse('layout/footer_login.html', $data);
	}
	
	
	function pre($array)
	{
		echo "<pre>";
		print_r($array);
		echo "</pre>";
	}
	

}

?>
