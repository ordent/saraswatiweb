<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Blog extends MX_Controller {

	var $path = "blog";

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
		$this->grid();
	}

	public function grid()
	{

		$this->header();
		$this->navigation();
		$contents = $this->grid_content();
	
		$data = array(
				  'base_url' => base_url(),
				  'contents' => $contents
				  );
		$this->parser->parse('layout/contents.html', $data);
		
		$this->footer();
	}

	public function grid_content()
	{

		#search
		$sch1_parm = rawurldecode($this->uri->segment(3));
		$sch1_parm = $sch1_parm != 'null' && !empty($sch1_parm) ? $sch1_parm : 'null';
		$sch1_val = $sch1_parm != 'null' ? $sch1_parm : '';

		$sch2_parm = rawurldecode($this->uri->segment(4));
		$sch2_parm = $sch2_parm != 'null' && !empty($sch2_parm) ? $sch2_parm : 'null';
		$sch2_val = $sch2_parm != 'null' ? $sch2_parm : '';

		$sch_path = rawurlencode($sch1_parm)."/".rawurlencode($sch2_parm);

		$sch_pack = array(
						"sch1_parm"=>$sch1_parm,
						"sch2_parm"=>$sch2_parm
						 );

		//categories
		$qc = $this->blog->getCategories();
		$categories = [];
		if($qc->num_rows() > 0){
			foreach($qc->result_array() as $key=>$r){
				$categories[$key] = $r;
				if($r['id'] == $sch2_parm){
					$categories[$key]['selected'] = "selected='selected'";
				}else{
					$categories[$key]['selected'] = "";
				}
			}
		}

		//get hightlight
		$qh = $this->blog->getHighlight();
		$highlight = array();
		if($qh->num_rows() > 0){
			$r = $qh->row_array();
			$highlight[] = $r;
			$highlight[0]['link'] = site_url("blog/detail/".date("Y/m/d",strtotime($r['created_date']))."/".$r['id']."/".url_title(strtolower($r['name'])));
			$highlight[0]['preview'] = word_limiter($r['preview'],8); 
			$highlight[0]['image_url']  = !empty($r['file_thumb']) ? '<img src="'.asset_url()."blogs/".$r['file_thumb'].'" alt="'.$r['name'].'" />' : '';
		}

		$q = $this->blog->getList($sch_pack);
		$list = [];
		if($q->num_rows() > 0){
			foreach($q->result_array() as $key=>$r){

				$list[$key] = $r;
				$list[$key]['link'] = site_url("blog/detail/".date("Y/m/d",strtotime($r['created_date']))."/".$r['id']."/".url_title(strtolower($r['name'])));
				$list[$key]['preview'] = word_limiter($r['preview'],8); 
				$list[$key]['image_url']  = !empty($r['file_thumb']) ? '<img src="'.asset_url()."blogs/thumbs/".$r['file_thumb'].'" alt="'.$r['name'].'" />' : '';

			}
		}

		$data = array(
				  'base_url' => base_url(),
				  'categories' => $categories,
				  'highlight' => $highlight,
				  'list' => $list,
				  'sch1_val'=>$sch1_val,
				  'contact' => $this->contact()
				  );
		return $this->parser->parse('list.html', $data, TRUE);
	}

	function search()
	{
		$sch1 = rawurlencode($this->input->post('sch1'));
		$sch2 = rawurlencode($this->input->post('sch2'));
		
		$sch1 = empty($sch1) ? 'null' : $sch1;
		$sch2 = is_null($sch2) || $sch2 == "" ? 'null' : $sch2;
		$sch_path = $sch1."/".$sch2;
		
		redirect($this->path."/grid/".$sch_path);
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

		if(!$this->uri->segment(6)) redirect('custom404');

		$q = $this->blog->getDetail($this->uri->segment(6));
		$list = array();
		if($q->num_rows() > 0){
				$r = $q->row_array();
				$list[] = $r;
				$list[0]['blog_url'] = site_url("blog");
 				$list[0]['date'] = date('F d, Y', strtotime($list[0]['created_date']));
				$list[0]['image_url']  = !empty($r['file_image']) ? '<img src="'.asset_url()."blogs/".$r['file_image'].'" alt="'.$r['name'].'" />' : '';

				if(!empty($r['file_doc'])){
					$list[0]['download'] = "<a href='".asset_url()."blogs/docs/".$r['file_doc']."' class='btn-download'><i class='fa fa-download'></i> Download Full Report</a>";
				}else{
					$list[0]['download'] = "";
				}

		}

		$data = array(
				  'base_url' => base_url(),
				  'detail' => $list,
				  'contact' => $this->contact()
				  );
		return $this->parser->parse('detail.html', $data, TRUE);
	}

}

?>
