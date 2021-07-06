<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Devi extends MX_Controller  {
	
	var $path = "devi";
	var $alias = "DEVI";	
	var $uri_page = 6;
	var $per_page = 25;
	 
	function __construct()
	{
		parent::__construct();
		$this->load->model($this->path."/Model_".$this->path, $this->path);
		$this->load->library('form_validation');
	}
	
	public function setheader()
	{
		return Modules::run('layout/setheader');
	}

	public function setfooter()
	{
		return Modules::run('layout/setfooter');
	}
	 
	public function auth()
	{
		return Modules::run('auth/privateAuth');
	}
	
	public function forbiddenAuth()
	{
		return Modules::run('auth/forbiddenAuth');
	}

	function index()
	{
		$this->auth();
		$this->forbiddenAuth();
		$this->grid();
	}


	function grid()
	{
		$this->setheader();
		$sidebar = Modules::run('layout/sidebar');		
		$contents = $this->grid_content();
	
		$data = array(
				  'admin_url'=>base_url(),
				  'sidebar'=>$sidebar,
				  'contents'=>$contents,
				  );
		$this->parser->parse('layout/contents.html', $data);
		
		$this->setfooter();
	}
	
	
	
	function grid_content()
	{	
		
		#search
		$sch1_parm = rawurldecode($this->uri->segment(3));
		$sch1_parm = $sch1_parm != 'null' && !empty($sch1_parm) ? $sch1_parm : 'null';
		$sch1_val = $sch1_parm != 'null' ? $sch1_parm : '';
		
		$sch_path = rawurlencode($sch1_parm);
		$sch_pack = array(
						"sch1_parm"=>$sch1_parm
						 );
		#end search

		#paging
		$get_page = $this->uri->segment(5);
		$uri_segment = $this->uri_page;
		$pg = $this->uri->segment($uri_segment);
		$per_page = !empty($get_page) ? $get_page : $this->per_page;
		$no = $go_pg = !$pg ? 0 : $pg;

		if(!$pg)
		{
			$lmt = 0;
			$pg = 1;
		}else{
			$lmt = $pg;
		}
		
		$path = site_url($this->path."/pages/".$sch_path."/".$per_page);
		$jum_record = $this->devi->getTotal($sch_pack);
		$paging = Modules::run("widget/page",$jum_record,$per_page,$path,$uri_segment);
		if(!$paging) $paging = "";
		$display_record = $jum_record > 0 ? "" : "display:none;";
		#end paging
		
		#record
		$query = $this->devi->getList($per_page,$lmt,$sch_pack);
		$list = array();

		if($query->num_rows() > 0){
			foreach($query->result() as $r)
			{
				$no++;
				$token = $r->token;
				$token = highlight_phrase($token, $sch1_parm, '<span style="color:#990000">', '</span>');
				$created_date  = date("d/m/Y H:i",strtotime($r->created_date));

				$list[] = array(
								"no"=>$no,
								"id"=>$r->id,
								"user_id"=>$r->user_id,
								"token"=>$token,
								"link"=>site_url($this->path."/edit/".$r->id),
								"created_date"=>$created_date
								);
			}
			
		}	
		#end record

		$data = array(
				  'admin_url' => base_url(),
				  'paging'=>$paging,
				  'list'=>$list,
				  'jum_record'=>$jum_record,
				  'display_record'=>$display_record,
				  'sch1_val'=>$sch1_val,
				  'sch_path'=>$sch_path,
				  'per_page'=>$per_page,
				  'pg'=>$go_pg,
				  'title_head'=>ucfirst(str_replace('_',' ',$this->alias)),
				  'title_link'=>$this->path
				  );
		return $this->parser->parse("list.html", $data, TRUE);
	}
	
	function search()
	{
		$sch1 = rawurlencode($this->input->post('sch1'));
		$per_page = rawurlencode($this->input->post('per_page'));
		
		$sch1 = empty($sch1) ? 'null' : $sch1;
		$sch_path = $sch1;
		
		redirect($this->path."/pages/".$sch_path."/".$per_page);
	}
	
	
	function edit()
	{
		$this->setheader();		
		$id = $this->uri->segment(3);
		$sidebar = Modules::run('layout/sidebar');
		$contents = $this->edit_content($id);
		
		$data = array(
				  'admin_url'=>base_url(),
				  'sidebar'=>$sidebar,
				  'contents'=>$contents
				  );
		$this->parser->parse('layout/contents.html', $data);
		
		$this->setfooter();
	}
	
	
	
	function edit_content($id)
	{
		
		if(is_numeric($id)){
			
			$add_edit = $id == 0 ? "Add" : "Edit";

			#set dropdown asset
			$ref2_arr = array(0=>"Not Active",1=>"Active");

			#record
			$q = $this->devi->getDetail($id);
			$list = $list_term_option = array();
			if($q->num_rows() > 0){
					$r = $q->row();
					
					$user_id = $this->session->flashdata("user_id") ? $this->session->flashdata("user_id") : $r->user_id;
					$token = $this->session->flashdata("token") ? $this->session->flashdata("token") : $r->token;
					$link_website = $this->session->flashdata("link_website") ? $this->session->flashdata("link_website") : $r->link_website;
					$link_instagram = $this->session->flashdata("link_instagram") ? $this->session->flashdata("link_instagram") : $r->link_instagram;
					$description = $this->session->flashdata("description") ? $this->session->flashdata("description") : $r->description;
					$created_date = $r->created_date;
					$id = $r->id;

			}else{

					$user_id = $this->session->flashdata("user_id") ? $this->session->flashdata("user_id") : "";
					$token = $this->session->flashdata("token") ? $this->session->flashdata("token") : "";
					$link_website = $this->session->flashdata("link_website") ? $this->session->flashdata("link_website") : "";
					$link_instagram = $this->session->flashdata("link_instagram") ? $this->session->flashdata("link_instagram") : "";
					$description = $this->session->flashdata("description") ? $this->session->flashdata("description") : "";
					$id = 0;

					$created_date = "";
			}
			#end record
			
			#listing
			$list[] = array(
									"id"=>$id,
									"user_id" =>$user_id,
									"token" =>$token,
									"link_website" =>$link_website,
									"link_instagram" =>$link_instagram,
									"description" =>$description,
									"created_date"=>$created_date
									);

	
			#notification
			$err = $this->session->flashdata("err") ? $this->session->flashdata("err") : "";
			$success = $this->session->flashdata("success") ? $this->session->flashdata("success") : "";
			extract(GetNotification($success,$err));
			#end notification
		
			$data = array(
					  'admin_url'=>base_url(),
					  'notif'=>$notif,
					  'btn_plus'=>$btn_plus,
					  'list'=>$list,
					  'title_head'=>ucfirst(str_replace('_',' ',$this->alias)),
				 	  'title_link'=>$this->path,
					  'add_edit'=>$add_edit
					  );
			return $this->parser->parse("edit.html", $data, TRUE);
		}else{
			redirect($this->path);
		}
	}
	
	
	function submit()
	{
		#data
		$id = strip_tags($this->input->post("id"));	
		$tags = '<img><div><table><tbody><tr><td><p><a><br><ul><ol><li><strong><b><i><u><strike><em>';
		$data_pack = array(
						'user_id'=>strip_tags($this->input->post("user_id")),
						'token'=>strip_tags($this->input->post("token")),
						'link_website'=>strip_tags($this->input->post("link_website")),
						'link_instagram'=>strip_tags($this->input->post("link_instagram")),
						'description'=>strip_tags($this->input->post("description"), $tags)						 
						);
		$where_pack = array(
						'id'=>$id
						 );

		if($id > 0){
			$data_pack['modified_by']= $this->session->userdata('adminID');
			$data_pack['modified_date'] = date("Y-m-d H:i:s",now());
		}else{
			$data_pack['created_by']= $this->session->userdata('adminID');
			$data_pack['created_date'] = date("Y-m-d H:i:s",now());
		}	
		

		#validation
		$this->form_validation->set_rules('user_id', 'user_id', 'required');
		$this->form_validation->set_rules('token', 'token', 'required');
		if ($this->form_validation->run($this) == FALSE)
		{
			$this->session->set_flashdata("err",validation_errors());
			$this->session->set_flashdata($data_pack);

			redirect($this->path."/edit/".$id);
		}else{
			if($id > 0)
			{
				$this->devi->setUpdate($data_pack,$where_pack);
				$this->session->set_flashdata("success","Data saved successful");
				redirect($this->path."/edit/".$id);
			}else{
				$id_term = $this->devi->setInsert($data_pack);
				$last_id = $this->db->insert_id();
				
				$this->session->set_flashdata("success","Data inserted successful");
				redirect($this->path."/edit/".$last_id);
			}
		}
	}
	

	function delete($id=0)
	{
		$del_status = $this->devi->setDelete($id);
		$response['id'] = $id;
		$response['status'] = $del_status;
		echo $result = json_encode($response);
		exit();
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
