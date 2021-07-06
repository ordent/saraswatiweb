<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Layout extends MX_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model("layout/Model_layout", "layout");
	}
	
	
	public function setheader()
	{	
		#get meta
		extract($this->set_meta_tag($meta));

		$data = array(
				  'base_url' => base_url(),
				  'meta_title'=>$meta_title,
			      'meta_description'=>$meta_description,
				  'meta_author'=>$meta_author,
				  'meta_keyword'=>$meta_keyword,
				  'meta_image'=>$meta_image,
				  'meta_url'=>$this->input->server('PHP_SELF'),
				  );
		$this->parser->parse('layout/header.html', $data);
	}

	public function setnavigation($meta=array())
	{		
		#menu
		$uri = $this->uri->segment(1);
		$active = "active";
		$m1 = empty($uri) ? $active : "";

		$act1 = '';
		$act2 = '';
		$act3 = $this->uri->segment(1) && $this->uri->segment(1) == 'whatwedo' ? 'act' : '';
		$act4 = '';
		$act5 = $this->uri->segment(1) && $this->uri->segment(1) == 'blog' ? 'act' : '';
		$act6 = '';
		$act7 = '';

		$header_detail = $this->uri->segment(1) ? "header-detail" : '';

		$q = $this->layout->getWhatWeDoDropdown();
		$dropdown = array();
		if($q->num_rows() > 0){
			foreach($q->result_array() as $key=>$r){
				$dropdown[$key] = $r;
				$dropdown[$key]['link']  = site_url("whatwedo/detail/".$r['id']."/".url_title(strtolower($r['name'])));
				$dropdown[$key]['image_url']  = !empty($r['icon_small']) ? '<img src="'.asset_url()."what-we-do/".$r['icon_small'].'" alt="'.$r['name'].'" />' : '';
			}
		}

		$q_menu_programs = $this->layout->getPrograms();
		$menu_programs = "";
		if($q_menu_programs->num_rows() > 0){
			$r_menu_programs = $q_menu_programs->row_array();
			$q_menu_program_link = $this->layout->getProgramLinks();
			$menu_program_link = "";
			if($q_menu_program_link->num_rows() > 0){
				$r_menu_program_link = $q_menu_program_link->row_array();
				$menu_program_link = $r_menu_program_link['slug'];
			}
			$menu_programs = $r_menu_programs['name'];
		}
		
		$data = array(
				  'base_url' => base_url(),
				  'act1'=>$act1,
				  'act2'=>$act2,
				  'act3'=>$act3,
				  'act4'=>$act4,
				  'act5'=>$act5,
				  'act6'=>$act6,
				  'act7'=>$act7,
				  'header_detail'=>$header_detail,
				  'dropdown'=>$dropdown,
				  'menu_programs'=>$menu_programs,
				  'menu_program_link'=> $menu_program_link
				  );
		$this->parser->parse('layout/navigation.html', $data);
	}
	
	
	public function setfooter()
	{	
		
		$year = gmdate("Y");	
		$socials = Modules::run('sections/social');	
		$data = array(
				  'base_url' => base_url(),
				  'year'=>$year,
				  'socials'=>$socials,
				  'modal_about'=>Modules::run('sections/modal', 'about'),
				  'modal_tnc'=>Modules::run('sections/modal', 'term-condition'),
				  'modal_contact'=>Modules::run('sections/modal', 'contact'),
				  );
		$this->parser->parse('layout/footer.html', $data);
	}

	public function set_meta_tag($meta=array())
	{
		#set meta
		$meta_title = "";
		$meta_keyword = "";
		$meta_description = "";
		$meta_author = "";
		$meta_image = "";
		$q = $this->layout->getMeta();
		if($q->num_rows() > 0){
			$r = $q->row();
			$meta_title = isset($meta['meta_title']) ? $r->meta_title." - ".$meta['meta_title'] : $r->meta_title;
			$meta_keyword = $r->meta_keyword;
			$meta_description = isset($meta['meta_description']) ? $meta['meta_description'] : $r->meta_description;
			$meta_author = $r->meta_author;
			$meta_image = isset($meta['meta_image']) ? $meta['meta_image'] : '';
		}
		
		return compact('meta_title','meta_keyword','meta_author','meta_description','meta_image');
	}
	

}

?>
