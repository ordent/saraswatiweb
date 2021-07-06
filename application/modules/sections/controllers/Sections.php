<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Sections extends MX_Controller {

	var $path = 'sections';

	function __construct()
	{
		parent::__construct();
		
		$this->asset_url = base_url()."control/";
		$this->load->model('sections/model_'.$this->path,$this->path);
	}
	
	public function header()
	{
		return Modules::run('layout/setheader');
	}

	public function navigation()
	{
		return Modules::run('layout/setnavigation');
	}

	public function footer()
	{
		return Modules::run('layout/setfooter');
	} 
	
	public function index()
	{
		$this->grid();
	}


	public function grid()
	{
		$this->header();
		$this->navigation();
		$contents = $this->grid_content();
	
		$data = array(
				  'base_url' => base_url(),
				  'contents' => $contents,
				  );
		$this->parser->parse('layout/contents.html', $data);
		
		$this->footer();
	}
	
	public function grid_content()
	{
		$data = array(
				  'base_url' => base_url(),
				  'asset_url' => asset_url(),
				  'homepage'=>$this->homepage(),
				  'about'=>$this->about(),
				  'whatwedo'=>$this->whatwedo(),
				  //'devi'=>$this->devi(),
				  'programs'=>$this->programs(),
				  'sub_programs'=>$this->sub_programs(),
				  'blog'=>$this->blog(),
				  'team'=>$this->team(),
				  'partner'=>$this->partner(),
				  'contact'=>$this->contact()
				  );
		return $this->parser->parse('sections.html', $data, TRUE);
	}


	private function homepage()
	{

		$data = array(
				  'base_url' => base_url(),
				  'asset_url' => asset_url()
				  );
		return $this->parser->parse('homepage.html', $data, TRUE);
	}

	private function about()
	{

		$q = $this->sections->getAbout();
		$about = array();
		if($q->num_rows() > 0){
			$r = $q->row_array();
			$about[] = $r;
			$about[0]['image_url']  = !empty($r['file_image']) ? asset_url()."abouts/".$r['file_image'] : '';
		}

		$data = array(
				  'base_url' => base_url(),
				  'asset_url' => asset_url(),
				  'about'=>$about
				  );
		return $this->parser->parse('about.html', $data, TRUE);
	}

	private function whatwedo()
	{
		$qs = $this->sections->getWhatWeDo('strategic');
		$strategic = array();
		if($qs->num_rows() > 0){
			$r = $qs->row_array();
			$strategic[] = $r;
			$strategic[0]['link']  = site_url("whatwedo/detail/".$r['id']."/".url_title(strtolower($r['name'])));
			$strategic[0]['image']  = !empty($r['file_image']) ? '<img style="width: 35%;" src="'.asset_url()."what-we-do/".$r['icon_big'].'" alt="'.$r['name'].'" />' : '';
			$strategic[0]['image_url']  = !empty($r['icon_big']) ? asset_url()."what-we-do/".$r['icon_big'] : '';
			$strategic[0]['image_hover_url']  = !empty($r['icon_big_hover']) ? asset_url()."what-we-do/".$r['icon_big_hover'] : '';
		}

		$qo = $this->sections->getWhatWeDo('online');
		$online = array();
		if($qo->num_rows() > 0){
			$r = $qo->row_array();
			$online[] = $r;
			$online[0]['link']  = site_url("whatwedo/detail/".$r['id']."/".url_title(strtolower($r['name'])));
			$online[0]['image']  = !empty($r['icon_big']) ? '<img src="'.asset_url()."what-we-do/".$r['icon_big'].'" alt="'.$r['name'].'" />' : '';
			$online[0]['image_url']  = !empty($r['icon_big']) ? asset_url()."what-we-do/".$r['icon_big'] : '';
			$online[0]['image_hover_url']  = !empty($r['icon_big_hover']) ? asset_url()."what-we-do/".$r['icon_big_hover'] : '';
		}

		$qm = $this->sections->getWhatWeDo('monitoring');
		$monitoring = array();
		if($qm->num_rows() > 0){
			$r = $qm->row_array();
			$monitoring[] = $r;
			$monitoring[0]['link']  = site_url("whatwedo/detail/".$r['id']."/".url_title(strtolower($r['name'])));
			$monitoring[0]['image']  = !empty($r['icon_big']) ? '<img class="top-50" src="'.asset_url()."what-we-do/".$r['icon_big'].'" alt="'.$r['name'].'" />' : '';
			$monitoring[0]['image_url']  = !empty($r['icon_big']) ? asset_url()."what-we-do/".$r['icon_big'] : '';
			$monitoring[0]['image_hover_url']  = !empty($r['icon_big_hover']) ? asset_url()."what-we-do/".$r['icon_big_hover'] : '';
		}

		$qr = $this->sections->getWhatWeDo('research');
		$research = array();
		if($qr->num_rows() > 0){
			$r = $qr->row_array();
			$research[] = $r;
			$research[0]['link']  = site_url("whatwedo/detail/".$r['id']."/".url_title(strtolower($r['name'])));
			$research[0]['image']  = !empty($r['icon_big']) ? '<img src="'.asset_url()."what-we-do/".$r['icon_big'].'" alt="'.$r['name'].'" />' : '';
			$research[0]['image_url']  = !empty($r['icon_big']) ? asset_url()."what-we-do/".$r['icon_big'] : '';
			$research[0]['image_hover_url']  = !empty($r['icon_big_hover']) ? asset_url()."what-we-do/".$r['icon_big_hover'] : '';
		}	

		$qk = $this->sections->getWhatWeDo('knowledge');
		$knowledge = array();
		if($qk->num_rows() > 0){
			$r = $qk->row_array();
			$knowledge[] = $r;
			$knowledge[0]['link']  = site_url("whatwedo/detail/".$r['id']."/".url_title(strtolower($r['name'])));
			$knowledge[0]['image']  = !empty($r['icon_big']) ? '<img class="top-50" src="'.asset_url()."what-we-do/".$r['icon_big'].'" alt="'.$r['name'].'" />' : '';
			$knowledge[0]['image_url']  = !empty($r['icon_big']) ? asset_url()."what-we-do/".$r['icon_big'] : '';
			$knowledge[0]['image_hover_url']  = !empty($r['icon_big_hover']) ? asset_url()."what-we-do/".$r['icon_big_hover'] : '';
		}

		$qt = $this->sections->getWhatWeDo('training');
		$training = array();
		if($qt->num_rows() > 0){
			$r = $qt->row_array();
			$training[] = $r;
			$training[0]['link']  = site_url("whatwedo/detail/".$r['id']."/".url_title(strtolower($r['name'])));
			$training[0]['image']  = !empty($r['icon_big']) ? '<img src="'.asset_url()."what-we-do/".$r['icon_big'].'" alt="'.$r['name'].'" />' : '';
			$training[0]['image_url']  = !empty($r['icon_big']) ? asset_url()."what-we-do/".$r['icon_big'] : '';
			$training[0]['image_hover_url']  = !empty($r['icon_big_hover']) ? asset_url()."what-we-do/".$r['icon_big_hover'] : '';
		}

		$qp = $this->sections->getWhatWeDo('partnerships');
		$partnerships = array();
		if($qp->num_rows() > 0){
			$r = $qp->row_array();
			$partnerships[] = $r;
			$partnerships[0]['link']  = site_url("whatwedo/detail/".$r['id']."/".url_title(strtolower($r['name'])));
			$partnerships[0]['image']  = !empty($r['icon_big']) ? '<img src="'.asset_url()."what-we-do/".$r['icon_big'].'" alt="'.$r['name'].'" />' : '';
			$partnerships[0]['image_url']  = !empty($r['icon_big']) ? asset_url()."what-we-do/".$r['icon_big'] : '';
			$partnerships[0]['image_hover_url']  = !empty($r['icon_big_hover']) ? asset_url()."what-we-do/".$r['icon_big_hover'] : '';
		}

		$data = array(
				  'base_url' => base_url(),
				  'asset_url' => asset_url(),
				  'strategic' => $strategic,
				  'monitoring' => $monitoring,
				  'online' => $online,
				  'research' => $research,
				  'knowledge' => $knowledge,
				  'training' => $training,
				  'partnerships' => $partnerships
				  );
		return $this->parser->parse('whatwedo.html', $data, TRUE);
	}

	private function programs()
	{
		$q_programs = $this->sections->getPrograms();
		$programs = array();
		$name = "";
		if($q_programs->num_rows() > 0){
			$r_programs = $q_programs->row_array();
			$name = $r_programs['name'];
		}

		$q = $this->sections->getSubPrograms();
		$sub_programs = array();
		if($q->num_rows() > 0){
			foreach($q->result_array() as $key=>$r){
				$sub_programs[$key] = $r;
				$sub_programs[$key]['image_url']  = !empty($r['file_logo']) ? '<a href="#'.$r["slug"].'" class="scroll-to-link" onclick="javscript:window.location.href='.base_url().'#'.$r["slug"].'"><img src="'.asset_url()."programs/".$r['file_logo'].'" alt="'.$r['name'].'" /></a>' : '';
			}
		}

		$programs[] = array(
						"name"=>$name,
						"sub_programs"=>$sub_programs
						);

		$data = array(
				  'base_url' => base_url(),
				  'asset_url' => asset_url(),
				  'programs' => $programs
				  );

		return $this->parser->parse('programs.html', $data, TRUE);
	}

	private function sub_programs()
	{

		$q = $this->sections->getSubPrograms();
		$sub_programs = array();
		if($q->num_rows() > 0){
			foreach($q->result_array() as $key=>$r){
				$sub_programs[$key] = $r;
				$sub_programs[$key]['program_photos'] = array();
				$sub_programs[$key]['background'] = !empty($r['background_url']) ? 'background-image: url("'.$r['background_url'].'")' : '';
				$sub_programs[$key]['background_url']  = !empty($r['file_image']) ? asset_url()."programs/".$r['file_image'] : '';
				$sub_programs[$key]['logo_url']  = !empty($r['file_logo']) ? '<img src="'.asset_url()."programs/".$r['file_logo'].'" alt="'.$r['name'].'" /></a>' : '';
				$sub_programs[$key]['link_website']  = !empty($r['link_website']) ?  $r['link_website'] : "#";
				$sub_programs[$key]['link_instagram']  = !empty($r['link_instagram']) ?  $r['link_instagram'] : "#";

				$q_photos = $this->sections->getProgramPhotos($r['id']);
				$photos = array();
				if($q_photos->num_rows() > 0){
					foreach($q_photos->result_array() as $key_photo=>$r){
						$photos[$key_photo] = $r;
						$photos[$key_photo]['image_url']  = !empty($r['file_image']) ? '<img src="'.asset_url()."devi/".$r['file_image'].'" alt="'.$r['name'].'" />' : '';
					}
					$sub_programs[$key]['program_photos'] = $photos;
				}

			}
		}

		$data = array(
				  'base_url' => base_url(),
				  'asset_url' => asset_url(),
				  'sub_programs' => $sub_programs
				  );

		return $this->parser->parse('sub_programs.html', $data, TRUE);
	}

	public function devi()
	{
		$q = $this->sections->getDevi();
		$devi = array();
		if($q->num_rows() > 0){
			$devi[] = $q->row_array();
			$devi[0]['base_url'] = base_url();

			$q_photos = $this->sections->getDeviPhotos();
			$photos = array();
			if($q_photos->num_rows() > 0){
				foreach($q_photos->result_array() as $key=>$r){
					$photos[$key] = $r;
					$photos[$key]['image_url']  = !empty($r['file_image']) ? '<img src="'.asset_url()."devi/".$r['file_image'].'" alt="'.$r['name'].'" />' : '';
				}
			}

			$devi[0]['devi_photos'] = $photos;
		}

		$data = array(
				  'base_url' => base_url(),
				  'asset_url' => asset_url(),
				  'devi' => $devi
				  );
		return $this->parser->parse('devi.html', $data, TRUE);
	}

	private function blog()
	{

		$q = $this->sections->getBlog();
		$blog = array();
		$n = 1;
		$m = 0;
		foreach($q->result_array() as $key=>$r){

			if(! isset($blog[$m])){
				$blog[$m] = null;
			}

			$blog[$m]['blog'][$key] = $r;
			$blog[$m]['blog'][$key]['link'] = site_url("blog/detail/".date("Y/m/d",strtotime($r['created_date']))."/".$r['id']."/".url_title(strtolower($r['name'])));
			$blog[$m]['blog'][$key]['preview'] = word_limiter($r['preview'],25); 
			$blog[$m]['blog'][$key]['image_url']  = !empty($r['file_thumb']) ? '<img src="'.asset_url()."blogs/thumbs/".$r['file_thumb'].'" alt="'.$r['name'].'" />' : '';
			
			if($n % 3 == 0){ 
				$m++;	
			}

			$n++;
		}

		$data = array(
				  'base_url' => base_url(),
				  'asset_url' => asset_url(),
				  'blog_list' => $blog
				  );
		return $this->parser->parse('blog.html', $data, TRUE);
	}

	private function team()
	{
		$q = $this->sections->getTeam();
		$team = array();
		if($q->num_rows() > 0){
			foreach($q->result_array() as $key=>$r){
				$team[$key] = $r;
				$team[$key]['image_url']  = !empty($r['file_image']) ? '<img class="team-item" src="'.asset_url()."teams/".$r['file_image'].'" alt="'.$r['name'].'" />' : '';
			}
		}

		$data = array(
				  'base_url' => base_url(),
				  'asset_url' => asset_url(),
				  'team' => $team
				  );
		return $this->parser->parse('team.html', $data, TRUE);
	}

	private function partner()
	{
		$q = $this->sections->getPartner();
		$partner = array();
		if($q->num_rows() > 0){
			foreach($q->result_array() as $key=>$r){
				$partner[$key] = $r;
				$link = !empty($r['link']) ? $r['link'] : "#";
				$partner[$key]['image_url']  = !empty($r['file_image']) ? '<a target="_blank" href="'.$link.'"><img src="'.asset_url()."partners/".$r['file_image'].'" alt="'.$r['name'].'" /></a>' : '';
			}
		}

		$data = array(
				  'base_url' => base_url(),
				  'asset_url' => asset_url(),
				  'partner' => $partner
				  );
		return $this->parser->parse('partner.html', $data, TRUE);
	}

	public function contact()
	{

		$q = $this->sections->getContact();
		$contact = array();
		if($q->num_rows() > 0){
			$contact[] = $q->row_array();
		}

		$data = array(
				  'base_url' => base_url(),
				  'asset_url' => asset_url(),
				  'contact' => $contact,
				  'socials' => $this->social(),
				  'year'=>date('Y')
				  );
		return $this->parser->parse('sections/contact.html', $data, TRUE);
	}

	public function contact_less()
	{
		$data = array(
				  'base_url' => base_url(),
				  'asset_url' => asset_url(),
				  'socials' => $this->social(),
				  'year'=>date('Y')
				  );
		return $this->parser->parse('sections/contact-less.html', $data, TRUE);
	}

	public function social()
	{
		$q = $this->sections->getSocial();
		$list = array();
		if($q->num_rows() > 0){
			foreach($q->result() as $r){
				$title =  $r->name;
				$list[] = array(
								"base_url"=>asset_url(),
								"icon"=>$r->file_image,
								"class"=>$r->class,
								"link"=>$r->link,
								''
								 );
			}
		}

		return $list;
	}

}

?>
