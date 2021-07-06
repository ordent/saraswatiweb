<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Whatwedo extends MX_Controller {

	var $path = "whatwedo";

	function __construct()
	{
		parent::__construct();
		$this->load->model($this->path."/Model_".$this->path, $this->path);
	}
	
	public function header()
	{
		return Modules::run('layout/setheader');
	}

	public function navigation()
	{
		return Modules::run('layout/setnavigation');
	}

	public function contact()
	{
		return Modules::run('sections/contact_less');
	}

	public function footer()
	{
		return Modules::run('layout/setfooter');
	} 
	
	public function index(){
		$this->detail();
	}

	public function detail()
	{
		$this->header();
		$this->navigation();
		$contents = $this->detail_content();
	
		$data = array(
				  'base_url' => base_url(),
				  'contents' => $contents
				  );
		$this->parser->parse('layout/contents.html', $data);
		
		$this->footer();
	}

	public function detail_content()
	{
		if(!$this->uri->segment(3)) redirect('custom404');

		$q = $this->whatwedo->getPhotos($this->uri->segment(3));
		$photos = array();
		if($q->num_rows() > 0){
			foreach($q->result_array() as $key=>$r){
				$photos[$key] = $r;
				$photos[$key]['image_url']  = !empty($r['file_image']) ? '<img src="'.asset_url()."what-we-do/".$r['file_image'].'" alt="'.$r['name'].'" />' : '';
			}
		}

		$q = $this->whatwedo->getQuotes($this->uri->segment(3));
		$quotes = array();
		if($q->num_rows() > 0){
			foreach($q->result_array() as $key=>$r){
				$quotes[$key] = $r;
			}
		}

		$q = $this->whatwedo->getDetail($this->uri->segment(3));
		$list = [];
		if($q->num_rows() > 0){
				$list[] = $q->row_array();
				$list[0]['image_url']  = !empty($list[0]['file_image']) ? '<img src="'.asset_url()."what-we-do/".$list[0]['file_image'].'" alt="">' : "";
				$list[0]['photos']  = $photos;
				$list[0]['quotes']  = $quotes;
		}

		$data = array(
				  'base_url' => base_url(),
				  'detail' => $list,
				  'photos' => $photos,
				  'quotes' => $quotes,
				  'contact' => $this->contact()
				  );
		return $this->parser->parse('detail.html', $data, TRUE);
	}

}

?>
