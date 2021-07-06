<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contents extends MX_Controller  {
	
	var $path = "contents";		
	var $alias = "Contents";
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
				  'contents' => $contents,
				  'sidebar'=>$sidebar
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
		$sch2_arr = array(
							  0=>"No Active",
							  1=>"Active"
							  );
		$sch2_select_arr = array($sch2_parm);
		$ref2 = Modules::run('widget/getStaticDropdown',$sch2_arr,$sch2_select_arr,2);
		
		$sch_path = rawurlencode($sch1_parm)."/".rawurlencode($sch2_parm);
		$sch_pack = array(
						"sch1_parm"=>$sch1_parm,
						"sch2_parm"=>$sch2_parm
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
		$jum_record = $this->contents->getTotal($sch_pack);
		$paging = Modules::run("widget/page",$jum_record,$per_page,$path,$uri_segment);
		if(!$paging) $paging = "";
		$display_record = $jum_record > 0 ? "" : "display:none;";
		#end paging
		
		#record
		$query = $this->contents->getList($per_page,$lmt,$sch_pack);
		$list = array();
		if($query->num_rows() > 0){
			foreach($query->result() as $r)
			{
				$no++;
				
				$title = $r->title;
				$title = highlight_phrase($title, $sch1_parm, '<span style="color:#990000">', '</span>');
				$content  = highlight_phrase(strip_tags(word_limiter($r->content,10)), $sch1_parm, '<span style="color:#990000">', '</span>');
				$is_enabled = $r->is_enabled == 1 ? "icon-ok-sign" : "icon-minus-sign";
				$created_date = date("d/m/Y H:i",strtotime($r->created_date));
				
				$list[] = array(
								 'admin_url' => base_url(),
								 'title_link'=>$this->path,
								 "no"=>$no,
								 "id"=>$r->id,
								 "title" =>$title,
								 "link"=>site_url($this->path."/edit/".$r->id),
								 "content"=>$content,
								 "is_enabled"=>$is_enabled,
								 "created_date"=>$created_date
								);
			}
		}	
		#end record
	
		$data = array(
				  'base_url' => base_url(),
				  'admin_url' => base_url(),
				  'paging'=>$paging,
				  'list'=>$list,
				  'jum_record'=>$jum_record,
				  'display_record'=>$display_record,
				  'sch1_val'=>$sch1_val,				  				  
				  'ref2'=>$ref2,
				  'sch_path'=>$sch_path,
				  'per_page'=>$per_page,
				  'pg'=>$go_pg,
				  'title_head'=>ucwords(str_replace("_","",$this->alias)),
				  'title_link'=>$this->path
				  );
		return $this->parser->parse("list.html", $data, TRUE);
	}
	
	function search()
	{
		$sch1 = rawurlencode($this->input->post('sch1'));
		$sch2 = rawurlencode($this->input->post('ref2'));
		$sch2 = is_null($sch2) || $sch2 == "" ? 'null' : $sch2;
		$per_page = rawurlencode($this->input->post('per_page'));
		
		$sch1 = empty($sch1) ? 'null' : $sch1;
		$sch2 = empty($sch2) ? 'null' : $sch2;
		$sch_path = $sch1."/".$sch2;
		
		redirect($this->path."/pages/".$sch_path."/".$per_page);
	}
	
	
	function edit()
	{
		$this->setheader();		
		$id = $this->uri->segment(3);
		$contents = $this->edit_content($id);
		$sidebar = Modules::run('layout/sidebar');
		
		$data = array(
				  'admin_url'=>base_url(),
				  'base_url' => base_url(),
				  'contents'=>$contents,
				  'sidebar'=>$sidebar
				  );
		$this->parser->parse('layout/contents.html', $data);
		
		$this->setfooter();
	}
	
	
	
	function edit_content($id)
	{
		$number = 0;
		$files = $files_icon = "";
		$add_edit = $id == 0 ? "Add" : "Edit";
		$is_files = "";
		if(is_numeric($id)){
			
			#set asset
			$ref2_arr = array(
							  0=>"Not Active",
							  1=>"Active"
							  );
			
			$q = $this->contents->getDetail($id);
			$list = $list_term_option = array();
			if($q->num_rows() > 0){
					$r = $q->row();
				
					$title = $this->session->flashdata("name") ? $this->session->flashdata("name") : $r->name;
					$description = $this->session->flashdata("description") ? $this->session->flashdata("description") : $r->description;
					$created_date = $r->created_date;
					$id = $r->id;


					#image
					if($r->files)
					{
						$files = "
						<a href='".asset_url().'contents/'.$r->files."' rel='facebox'><img width='150' src='".asset_url()."contents/thumbs/".$r->files."' class='thumbnail'></a>
						<br/>
						<a href='".base_url().$this->path."/unlink/".$r->id."/".rawurlencode($r->files)."' class='btn btn-warning'>delete</a><br/>
						<input type='hidden' name='files_old' value='".$r->files."' id='files_old'>";
					}
					#end image

					#ref dropdown multi value					
					$ref2_select_arr[0] = $this->session->flashdata("is_enabled") ? $this->session->flashdata("is_enabled") : $r->is_enabled;
					$ref2 = Modules::run('widget/getStaticDropdown',$ref2_arr,$ref2_select_arr,2);
					#end ref dropdown multi value

					if($id == 1){
						$is_files = "none";
					}
			
			}else{
				
					$title = $this->session->flashdata("name") ? $this->session->flashdata("name") : "";
					$description = $this->session->flashdata("description") ? $this->session->flashdata("description") : "";
					$created_date = "";
					$id = 0;
					
					#ref dropdown multi value
					$ref2_select_arr[0] = $this->session->flashdata("is_enabled") ? $this->session->flashdata("is_enabled") : null;
					$ref2 = Modules::run('widget/getStaticDropdown',$ref2_arr,$ref2_select_arr,2);
					#end ref dropdown multi value

			}
			
			$list[] = array(
									"id"=>$id,
									"name"=>$title,
									"description"=>$description,
									"files"=>$files,
									"created_date"=>$created_date,
									"ref2"=>$ref2,
									"is_files"=>$is_files
									);
	
			#notification
			$err = $this->session->flashdata("err") ? $this->session->flashdata("err") : "";
			$success = $this->session->flashdata("success") ? $this->session->flashdata("success") : "";
			$notif = array();
			$btn_plus = "display:none;";
			if(!empty($success)){
				$btn_plus = "";
				$notif[] = array(
									"notif_title"=>$success,
									"notif_class"=>"alert-success"
									);
			}else if(!empty($err)){
				$notif[] = array(
									"notif_title"=>$err,
									"notif_class"=>"alert-error"
									);
			}
			#end notification
		
			$data = array(
					  'admin_url' => base_url(),
					  'base_url' => base_url(),
					  'notif'=>$notif,
					  'btn_plus'=>$btn_plus,
					  'list'=>$list,
					  'title_head'=>ucwords(str_replace('_',' ',$this->alias)),
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
		$response['status'] = 0;
		$response['link_id'] = 0;
		$response['message'] = "";
		$types = null;

		#image 
		$files = null;
		if($this->input->post("id") != 1){
			$files_old = strip_tags($this->input->post("files_old"));
			$files_name = $_FILES["files"]["name"];
			$files_tmp  = $_FILES["files"]["tmp_name"];

			if($this->input->post("id") == 8){
				$types = "ask";
			}
			
			if(!empty($files_name)){
				if(!empty($files_old)){
					$this->contents->deleteFileUpload($files_old);
				}
				$files = $this->contents->setFileUpload($files_name,$files_tmp,$files_old,$types);
			}else{
				$files = $files_old;
			}
		}
		
		#data
		$id = strip_tags($this->input->post("id"));	
		$tags = '<h1><h2><h3><h4><h5><table><tbody><tr><td><p><a><br><ul><ol><li><strong><b><i><u><strike>';
		$data_pack = array(
						'name'=>ucwords(strip_tags($this->input->post("name"))),
						'description'=>strip_tags($this->input->post("description"),$tags),
						'is_enabled'=>$this->input->post("ref2"),
						'files'=>$files,
						'modified_by'=>$this->session->userdata('adminID'),
						'created_date'=>date("Y-m-d H:i:s",now())
						 );
		$where_pack = array(
						'id'=>$id
						 );
		

		#validation
		$this->form_validation->set_rules('name', 'name', 'required');
		if ($this->form_validation->run($this) == FALSE)
		{
			$this->session->set_flashdata("err",validation_errors());
			$this->session->set_flashdata($data_pack);

			redirect($this->path."/edit/".$id);
		}else{
			if($id > 0)
			{
				$this->contents->setUpdate($data_pack,$where_pack);
				$this->session->set_flashdata("success","Data saved successful");
				$link_id = $id;
			}else{
				$id_term = $this->contents->setInsert($data_pack);
				$last_id = $this->db->insert_id();
				
				$this->session->set_flashdata("success","Data inserted successful");
				$link_id = $last_id;
			}
		}

		$response['linkId'] = $link_id;
		echo json_encode($response);
	}
	
	function delete($id=0)
	{
		$del_status = $this->contents->setDelete($id);
		$response['id'] = $id;
		$response['status'] = $del_status;
		echo $result = json_encode($response);
		exit();
	}
	
	function unlink($id,$files)
	{
		$files = rawurldecode($files);
		$this->contents->unlinkFileUpload($id);
		$this->contents->deleteFileUpload($files);
		redirect($this->path."/edit/".$id);
	}
	
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
