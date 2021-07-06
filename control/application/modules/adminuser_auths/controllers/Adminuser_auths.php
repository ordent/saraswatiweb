<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Adminuser_auths extends MX_Controller  {
	
	var $path = "adminuser_auths";
	var $alias = "Admin Users";
	var $uri_page = 7;
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
				  'admin_url' => base_url(),
				  'contents' => $contents,
				  'sidebar' => $sidebar
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
		
		$sch2_parm = rawurldecode($this->uri->segment(4));
		$sch2_parm = $sch2_parm != 'null' ? $sch2_parm : 'null';
		$sch2_parm =  $sch2_parm == ""  ? 'null' : $sch2_parm;
		$sch2_select_arr[0] = $sch2_parm;
		$sch2_arr = array(
							0=>"Not Active",
							1=>"Active"
						  );
		$ref2 = Modules::run('widget/getStaticDropdown',$sch2_arr,$sch2_select_arr,2);

		
		$sch3_parm = rawurldecode($this->uri->segment(5));
		$sch3_parm = $sch3_parm != 'null' && !empty($sch3_parm) ? $sch3_parm : 'null';
		
		#ref dropdown no multi value for search
		$q_ref3 = Modules::run('widget/getQueryStaticDropdown','adminuser_levels','title');
		$ref3_arr = array();
		$ref3_select_arr = array();
		$r3 = 0;
		if($q_ref3->num_rows() > 0){
			foreach ($q_ref3->result() as $r_ref3) {
				 $ref3_arr[$r_ref3->id] = $r_ref3->title;
				 $ref3_select_arr[$r3] = $sch3_parm;
				 $r3++;
			}
		}
		$ref3 = Modules::run('widget/getStaticDropdown',$ref3_arr,$ref3_select_arr,3);
		#end ref dropdown no multi value for search
		
		$sch_path = rawurlencode($sch1_parm)."/".rawurlencode($sch2_parm)."/".rawurlencode($sch3_parm);
		$sch_pack = array(
						"sch1_parm"=>$sch1_parm,
						"sch2_parm"=>$sch2_parm,
						"sch3_parm"=>$sch3_parm
						 );
		#end search

		
		#paging
		$get_page = $this->uri->segment(6);
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
		$jum_record = $this->adminuser_auths->getTotal($sch_pack);
		$paging = Modules::run("widget/page",$jum_record,$per_page,$path,$uri_segment);
		if(!$paging) $paging = "";
		$display_record = $jum_record > 0 ? "" : "display:none;";
		#end paging
		
		#record
		$query = $this->adminuser_auths->getList($per_page,$lmt,$sch_pack);
		$list = array();
		if($query->num_rows() > 0){
			foreach($query->result() as $r)
			{
				$no++;
				
				$email = $r->email;
				$email = highlight_phrase($email, $sch1_parm, '<span style="color:#990000">', '</span>');
				$publish = $r->is_enabled == 1 ? "icon-ok-sign" : "icon-minus-sign";
				$created_date = date("d/m/Y H:i:s",strtotime($r->created_date));
			
				$list[] = array(
								 "no"=>$no,
								 "id"=>$r->id,
								 "link"=>site_url($this->path."/edit/".$r->id),
								 "title" =>$email,
								 "level" =>$r->title,
								 "publish"=>$publish,
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
				  'ref2'=>$ref2,
				  'ref3'=>$ref3,
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
		$sch2 = rawurlencode($this->input->post('ref2'));
		$sch3 = rawurlencode($this->input->post('ref3'));
		$per_page = rawurlencode($this->input->post('per_page'));
		
		$sch1 = empty($sch1) ? 'null' : $sch1;
		$sch2 = is_null($sch2) || $sch2 == "" ? 'null' : $sch2;
		$sch3 = empty($sch3) ? 'null' : $sch3;
		$sch_path = $sch1."/".$sch2."/".$sch3;
		
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
				  'contents'=>$contents,
				  'sidebar'=> $sidebar
				  );
		$this->parser->parse('layout/contents.html', $data);
		
		$this->setfooter();
	}
	
	
	
	function edit_content($id)
	{
		$number = 0;
		$file_image = "";
		
		if(is_numeric($id)){
		
			$add_edit = $id == 0 ? "Add" : "Edit";
		
			#set dropdown asset
			$ref3_arr = array(
							   0=>"Not Active",
							   1=>"Active"
							  );
			
			$q = $this->adminuser_auths->getDetail($id);
			$list = $list_term_option = array();
			if($q->num_rows() > 0){
					$r = $q->row();
					
					$email = $this->session->flashdata("email") ? $this->session->flashdata("email") : $r->email;
					$name = $this->session->flashdata("name") ? $this->session->flashdata("name") : $r->name;
					$password = $r->password;
					$created_date = $r->created_date;
					$id = $r->id;
					
					#ref dropdown no multi value
					$wheres = array("field"=>array("id !="),
									"value"=>array(1));
					$q_ref2 = Modules::run('widget/getQueryStaticDropdown','adminuser_levels','title',$wheres);
					$ref2_arr = array();
					foreach ($q_ref2->result() as $r_ref2) {
						 $ref2_arr[$r_ref2->id] = $r_ref2->title;
					}
					
					$q_ref2 = $this->session->flashdata("ref2") ? $this->session->flashdata("ref2") : $q;
					$q_result2 = $this->session->flashdata("ref2") ? $q_ref2 : $q_ref2->result();
					$ref2_select_arr = array();
					$r2 = 0;
					foreach ($q_result2 as $r_ref2) {
						 $ref2_select_arr[$r2] = $this->session->flashdata("ref2") ? $r_ref2 : $r_ref2->adminuser_levels_id;
						 $r2++;
					}

					$ref2 = Modules::run('widget/getStaticDropdown',$ref2_arr,$ref2_select_arr,2);
					#end ref dropdown no multi value
					
					
					#ref dropdown no multi value
					$ref3_select_arr[0] = $r->is_enabled;
					$ref3 = Modules::run('widget/getStaticDropdown',$ref3_arr,$ref3_select_arr,3);
					#end ref dropdown no multi value

			}else{
			
					$email = $this->session->flashdata("email") ? $this->session->flashdata("email") : null;
					$name = $this->session->flashdata("name") ? $this->session->flashdata("name") : null;
					$password = "";
					$created_date = "";
					$id = 0;
				
					#ref dropdown no multi value
					$wheres = array("field"=>array("id !="),
									"value"=>array(1));
					$q_ref2 = Modules::run('widget/getQueryStaticDropdown','adminuser_levels','title',$wheres);
					$ref2_arr = array();
					foreach ($q_ref2->result() as $r_ref2) {
						 $ref2_arr[$r_ref2->id] = $r_ref2->title;
					}
					
					$q_ref2 = $this->session->flashdata("ref2") ? $this->session->flashdata("ref2") : array();

					$ref2_select_arr = array();
					$r2 = 0;
					foreach ($q_ref2 as $r_ref2) {
						 $ref2_select_arr[$r2] = $r_ref2;
						 $r2++;
					}

					$ref2 = Modules::run('widget/getStaticDropdown',$ref2_arr,$ref2_select_arr,2);
					#end ref dropdown no multi value

				
					#ref dropdown no multi value
					$ref3 = Modules::run('widget/getStaticDropdown',$ref3_arr,null,3);
					#end ref dropdown no multi value	
			}
			
			#listing
			$list[] = array(
									"id"=>$id,
									"email"=>$email,
									"name"=>$name,
									"password"=>$password,
									"created_date"=>$created_date,
									"ref2"=>$ref2,
									"ref3"=>$ref3
									);
			
	
			#notification
			$err = $this->session->flashdata("err") ? $this->session->flashdata("err") : "";
			$success = $this->session->flashdata("success") ? $this->session->flashdata("success") : "";
			extract(GetNotification($success,$err));
			#end notification
		
			$data = array(
					  'admin_url' => base_url(),
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
		#password
		$password = $this->input->post("password");
		$password_old = $this->input->post("password_old");
		if(!empty($password)){
			$password = md5($password);
			$key = $this->config->item('encryption_key');
			$password = md5($key.$password);
		}else{
			$password = $password_old;
		}
		
		#data
		$id = strip_tags($this->input->post("id"));	
		$data_pack = array(
						  'email'=>strip_tags($this->input->post("email")),
						  'name'=>strip_tags($this->input->post("name")),
						  'password'=>$password,
						  'adminuser_levels_id'=>$this->input->post("ref2"),
						  'is_enabled'=>$this->input->post("ref3")
						 );
		$where_pack = array(
						'id'=>$id
						 );
		
		if($id > 0){
			$data_pack['modified_by'] = $this->session->userdata('sess_admin')['adminuser_levels_id'];
		}else{
			$data_pack['created_by'] = $this->session->userdata('sess_admin')['adminuser_levels_id'];
			$data_pack['created_date'] = date("Y-m-d H:i:s",now());
		}

		#validation
		$this->form_validation->set_rules('email', 'email', 'required|callback_email_check');
		$this->form_validation->set_rules('ref2', 'admin level', 'required');

		if ($this->form_validation->run($this) == FALSE)
		{
			$this->session->set_flashdata("err",validation_errors());
			$this->session->set_flashdata($data_pack);

			redirect($this->path."/edit/".$id);
		}else{
				
			if($id > 0)
			{
				$this->adminuser_auths->setUpdate($data_pack,$where_pack);
				$this->session->set_flashdata("success","Data saved successful");
				redirect($this->path."/edit/".$id);
			}else{
				$this->adminuser_auths->setInsert($data_pack);
				$last_id = $this->db->insert_id();
				
				$this->session->set_flashdata("success","Data saved successful");
				redirect($this->path."/edit/".$last_id);
			}
		}
	}
	
	
	function email_check($email)
	{
		$count = $this->adminuser_auths->checkUsername($email,$this->input->post('id'));
		if ($count > 0)
		{
			$this->form_validation->set_message('email_check', 'Email was registered');
			return false;
		}else{
			return true;
		}
	}
	
	
	function edit_account()
	{
		$this->setheader();		
		$id = $this->uri->segment(3);
		$sidebar = Modules::run('layout/sidebar');
		$contents = $this->edit_account_content($id);
	
		$data = array(
				  'admin_url' => base_url(),
				  'contents' => $contents,
				  'sidebar'=> $sidebar
				  );
		$this->parser->parse('layout/contents.html', $data);
		
		$this->setfooter();
	}
	
	
	
	function edit_account_content($id)
	{
		$number = 0;
		$file_image = "";
		
		if(is_numeric($id)){
			
			#record
			$q = $this->adminuser_auths->getDetail($id);
			$list =  array();
			if($q->num_rows() > 0){
				$r = $q->row();
				
				$title = $this->session->flashdata("email") ? $this->session->flashdata("email") : $r->email;
				$level = $r->title;
				$password = $r->password;
				$name = $r->name;
				$adminuser_levels_id = $r->adminuser_levels_id;
				$publish = $r->is_enabled;
				$created_date = date("d/m/Y H:i",strtotime($r->created_date));
				$id = $r->id;

			}else{
				
				$title = $this->session->flashdata("email") ? $this->session->flashdata("email") : "";
				$level = "";
				$password = "";
				$name = "";
				$adminuser_levels_id = 0;
				$publish = "";
				$created_date = "";
				$id = 0;
				
			}
			
			#listing
			$list[] = array(
							"id"=>$r->id,
							"title"=>$title,
							"name"=>$name,
							"level_title"=>$level,
							"password"=>$password,
							"publish"=>$publish,
							"ref1"=>$adminuser_levels_id,
							"created_date"=>$created_date
							);

	
			#notification
			$err = $this->session->flashdata("err") ? $this->session->flashdata("err") : "";
			$success = $this->session->flashdata("success") ? $this->session->flashdata("success") : "";
			extract(GetNotification($success,$err));
		
			$data = array(
					  'admin_url' => base_url(),
					  'notif'=>$notif,
					  'list'=>$list,
					  'title_head'=>ucfirst(str_replace('_',' ',$this->alias)),
				 	  'title_link'=>$this->path
					  );
			return $this->parser->parse("account_edit.html", $data, TRUE);
		}else{
			redirect($this->path);
		}
	}
	
	
	function submit_account()
	{
		
		#password
		$password = $this->input->post("password");
		$password_old = $this->input->post("password_old");
		if(!empty($password)){
			$password = md5($password);
			$key = $this->config->item('encryption_key');
			$password = md5($key.$password);
		}else{
			$password = $password_old;
		}

		#data		
		$id = strip_tags($this->input->post("id"));	
		
		$data_pack = array(
						'password'=>$password,
						'name'=>$this->input->post("name"),
						'is_enabled'=>$this->input->post("publish"),
						'adminuser_levels_id'=>$this->input->post("ref1"),
						'adminuser_levels_id'=>$this->input->post("ref1"),
						'modified_by'=>$this->session->userdata('sess_admin')['adminuser_levels_id']
						 );
		$where_pack = array(
						'id'=>$id
						 );

			
		if($id > 0)
		{
			$this->adminuser_auths->setUpdate($data_pack,$where_pack);
			$this->session->set_flashdata("success","Data saved successful");
			redirect($this->path."/edit_account/".$id);
		}else{
			$this->adminuser_auths->setInsert($data_pack);
			$last_id = $this->db->insert_id();
			
			$this->session->set_flashdata("success","Data inserted successful");
			redirect($this->path."/edit_account/".$last_id);
		}
	}
	
	
	function delete($id=0)
	{
		$del_status = $this->adminuser_auths->setDelete($id);
		$response['id'] = $id;
		$response['status'] = $del_status;
		echo $result = json_encode($response);
		exit();
	}

	public function do_check_email($email=NULL)
	{
		$email = !is_null($email) ? $email : $this->input->post('email');
		$count = $this->adminuser_auths->checkEmailExists($email);	
		if ($count > 0)
		{
			$status = 'false';
		}else{
			$status = 'true';
		}
		echo $status;
	}

	public function do_check_email_exists($email=NULL)
	{
		$email = !is_null($email) ? $email : $this->input->post('email');
		$count = $this->adminuser_auths->checkEmailExists($email);	
		if ($count > 0)
		{
			$status = 'true';
		}else{
			$status = 'false';
		}
		echo $status;
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
