<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class configs extends MX_Controller  {
	 
	var $path = "configs";
	var $alias = "Configs";
	var $uri_page = 4;
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
		$this->edit();
	}
	
	function edit()
	{
		$this->setheader();		
		$id = 1;
		$sidebar = Modules::run('layout/sidebar');
		$contents = $this->edit_content($id);
		$data = array(
				  'admin_url'=>base_url(),
				  'contents'=>$contents,
				  'sidebar'=>$sidebar
				  );
		$this->parser->parse('layout/contents.html', $data);
		
		$this->setfooter();
	}
	
	
	
	function edit_content($id)
	{
		$number = 0;
		$file_image = "";
		$add_edit = $id == 0 ? "Add" : "Edit";

		if(is_numeric($id)){
			
			#data
			$q = $this->configs->getDetail($id);
			$list = $list_term_option = array();
			if($q->num_rows() > 0){
				$r = $q->row();
				
				$meta_title = $this->session->flashdata("meta_title") ? $this->session->flashdata("meta_title") : $r->meta_title;
				$meta_keyword = $this->session->flashdata("meta_keyword") ? $this->session->flashdata("meta_keyword") : $r->meta_keyword;
				$meta_description = $this->session->flashdata("meta_description") ? $this->session->flashdata("meta_description") : $r->meta_description;
				$meta_author = $this->session->flashdata("meta_author") ? $this->session->flashdata("meta_author") : $r->meta_author;
				$email = $this->session->flashdata("email") ? $this->session->flashdata("email") : $r->email;
				$password = $this->session->flashdata("password") ? $this->session->flashdata("password") : $r->password;
				$created_date = $this->session->flashdata("created_date") ? $this->session->flashdata("created_date") : $r->created_date;
				$id = $r->id;					
			}else{	
				$meta_title = "";
				$meta_keyword = "";
				$meta_description = "";
				$meta_author = "";
				$email = "";
				$password = "";
				$created_date = "";
				$id = 0;
			}
			
			#listing
			$list[] = array(
									"id"=>$id,
									"meta_title"=>$meta_title,
									"meta_keyword"=>$meta_keyword,
									"meta_description"=>$meta_description,
									"meta_author"=>$meta_author,
									"email"=>$email,
									"password"=>$password,
									"created_date"=>$created_date
									);

	
			#notification
			$err = $this->session->flashdata("err") ? $this->session->flashdata("err") : "";
			$success = $this->session->flashdata("success") ? $this->session->flashdata("success") : "";
			extract(GetNotification($success,$err));

		
			$data = array(
					  'admin_url'=>base_url(),
					  'notif'=>$notif,
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
		$id = $this->input->post("id");
		$data_pack = array(
						'meta_title'=>strip_tags($this->input->post("meta_title")),
						'meta_keyword'=>strip_tags($this->input->post("meta_keyword")),
						'meta_description'=>strip_tags($this->input->post("meta_description")),
						'meta_author'=>strip_tags($this->input->post("meta_author")),
						'email'=>$this->input->post("email"),
						'password'=>$this->input->post("password"),
						'publish_auth'=>"",
						'modified_by'=>$this->session->userdata('adminID'),
						'created_date'=>date("Y-m-d H:i:s",now())
						 );
		$where_pack = array(
						'id'=>$id
						 );
		
		
		#validation
		if($this->input->post("password") != ""){
			$this->form_validation->set_rules('email', 'email', 'required|valid_email');
		}else if(!empty($email)){
			$this->form_validation->set_rules('password', 'password', 'required');
		}
		
		$this->form_validation->set_rules('meta_title', 'meta title', 'required');
		$this->form_validation->set_rules('meta_keyword', 'meta keyword', 'required');
		$this->form_validation->set_rules('meta_description', 'meta description', 'required');
		$this->form_validation->set_rules('meta_author', 'meta author', 'required');
		if ($this->form_validation->run($this) == FALSE)
		{
			$this->session->set_flashdata('err',validation_errors());
			$this->session->set_flashdata($data_pack);
			
			redirect($this->path);
		}else{
			if($id > 0)
			{
				#update
				$this->configs->setUpdate($data_pack,$where_pack);
				$this->session->set_flashdata("success","Data saved successful");
				redirect($this->path);
			}else{
				#insert
				$id_term = $this->configs->setInsert($data_pack);
				$last_id = $this->db->insert_id();
				
				$this->session->set_flashdata("success","Data inserted successful");
				redirect($this->path);
			}
		}
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
