<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class menus extends MX_Controller  {
	
	var $path = "menus";
	var $alias = "Manage Menu";
	var $uri_page = 7;
	var $per_page = 25;
	 
	function __construct()
	{
		parent::__construct();
		$this->load->model("".$this->path."/Model_".$this->path, $this->path);
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
				  'sidebar' => $sidebar,
				  'contents' => $contents,
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
		$ref3 = Modules::run('menus/getRefDropdown',$sch3_parm,3);
		
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
		$jum_record = $this->menus->getTotal($sch_pack);
		$paging = Modules::run("widget/page",$jum_record,$per_page,$path,$uri_segment);
		if(!$paging) $paging = "";
		$display_record = $jum_record > 0 ? "" : "display:none;";
		#end paging
		
		#record
		$query = $this->menus->getList($per_page,$lmt,$sch_pack);
		$list = array();
		if($query->num_rows() > 0){
			foreach($query->result() as $r)
			{
				$no++;
				$zebra = $no % 2 == 0 ? "zebra" : "";
				
				$title = ucwords($r->title);
				$parent_title = ucwords($this->menus->getParentList($r->parent_id));
				$title = highlight_phrase($title, $sch1_parm, '<span style="color:#990000">', '</span>');
				$is_enabled = $r->is_enabled == 1 ? "icon-ok-sign" : "icon-minus-sign";
				$created_date = date("d/m/Y H:i:s",strtotime($r->created_date));
			
				$list[] = array(
								 "no"=>$no,
								 "id"=>$r->id,
								 "link"=>site_url($this->path."/edit/".$r->id),
								 "title" =>$title,
								 "parent" =>$parent_title,
								 "is_enabled"=>$is_enabled,
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
				  'sch1_parm'=>$sch1_parm,
				  'sch1_val'=>$sch1_val,
				  'sch2_parm'=>$sch2_parm,
				  'sch3_parm'=>$sch3_parm,
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
				  'sidebar'=>$sidebar,
				  'contents'=>$contents
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
		
			#set asset
			$ref2_arr = array("No"=>"No","Yes"=>"Yes");
			$ref3_arr = array(0=>"Not Active",1=>"Active");
			
			#record
			$q = $this->menus->getDetail($id);
			$list = $list_term_option = array();
			if($q->num_rows() > 0){
				$r = $q->row();
				
				#ref dropdown multi value
				$ref2_select_arr[0] = $r->divider;
				$ref2 = Modules::run('widget/getStaticDropdown',$ref2_arr,$ref2_select_arr,2);
				#end ref dropdown multi value
				
				#ref dropdown multi value					
				$ref3_select_arr[0] = $r->is_enabled;
				$ref3 = Modules::run('widget/getStaticDropdown',$ref3_arr,$ref3_select_arr,3);
				#end ref dropdown multi value
				
				$title = $this->session->flashdata("title") ? $this->session->flashdata("title") : $r->title;
				$uri = $this->session->flashdata("uri") ? $this->session->flashdata("uri") : $r->uri;
				$ordered = $this->session->flashdata("ordered") ? $this->session->flashdata("ordered") : $r->ordered;
				$parent_id = $r->parent_id;
				$created_date = $r->created_date;
				$id = $r->id;
	
			}else{
				
				#ref dropdown multi value
				$ref2 = Modules::run('widget/getStaticDropdown',$ref2_arr,null,2);
				#end ref dropdown multi value
				
				#ref dropdown multi value
				$ref3 = Modules::run('widget/getStaticDropdown',$ref3_arr,null,3);
				#end ref dropdown multi value
				
				$title = "";
				$uri = "";
				$ordered = "";
				$parent_id = 0;
				$created_date = "";
				$id = 0;
				
			}
			#end record
			
			
			#listing
			
			#ref dropdown multi value
			$ref1 = $this->getRefDropdownParent($id,$parent_id,1);
			#end ref dropdown multi value
			
			$list[] = array(
									"id"=>$id,
									"title"=>$title,
									"ordered"=>$ordered,
									"uri"=>$uri,
									"created_date"=>$created_date,
									"ref2"=>$ref2,
									"ref3"=>$ref3,
									'ref1'=>$ref1
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

		#data
		$uri = $this->input->post("uri") == "#" ? str_replace(" ","",strtolower($title)) : $this->input->post("uri");
		$id = strip_tags($this->input->post("id"));		
		$data_pack = array(
						'title'=>strip_tags($this->input->post("title")),
						'parent_id'=>$this->input->post("ref1"),
						'uri'=>$uri,
						'ordered'=>$this->input->post("ordered"),
						'is_enabled'=>$this->input->post("ref3"),
						'modified_by'=>$this->session->userdata('adminID'),
						'created_date'=>date("Y-m-d :H:i:s",now())
						 );
		$where_pack = array(
						'id'=>$id
						 );
		
		#validation
		$this->load->library('form_validation');
		$this->form_validation->set_rules('title', 'title', 'required');
		$this->form_validation->set_rules('uri', 'uri', 'required');
		if ($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata("err",validation_errors());
			$this->session->set_flashdata($data_pack);
			redirect($this->path."/edit/".$id);
		}else{
			if($id > 0)
			{
				$this->menus->setUpdate($data_pack,$where_pack);
				$this->session->set_flashdata("success","Data saved successful");
				redirect($this->path."/edit/".$id);
			}else{
				
				$q = $this->menus->getMax();
				$r = $q->row();
				$ordered = $r->max_ordered+1;
				
				$id_term = $this->menus->setInsert($data_pack);
				$last_id = $this->db->insert_id();
				
				$this->session->set_flashdata("success","Data inserted successful");
				redirect($this->path."/edit/".$last_id);
			}
		}
	}
	
	
	function ajaxsort()
	{
		$post = $this->input->post('data');
		$order =  $this->input->post('index_order');
		foreach($post as $val)
		{
			$order++;
			$this->menus->ajaxsort($val,$order);
		}
	}
	
	
	function delete($id=0)
	{
		$del_status = $this->menus->setDelete($id);
		$response['id'] = $id;
		$response['status'] = $del_status;
		echo $result = json_encode($response);
		exit();
	}
	
	function unlink($id,$file_image)
	{
		$this->db->where("id",$id);
		$this->db->update(array("file_image"=>""));
		unlink("uploads/".$file_image);
		redirect($this->path."/edit/".$id);
	}
	
	function getRefDropdownParent($id,$parent_id,$name,$type=NULL)
	{
		$q = $this->menus->getMenuList($id);
		$list = array();
		foreach ($q->result() as $val) {
			$selected = $val->id == $parent_id ? $selected = "selected='selected'" : "tidak";	
			$list[]= array(
						'id' => $val->id,
						'title'=>ucwords($val->title),
						"selected"=>$selected
					 );
		}
		$data = array(
				"list"=>$list,
				"name"=>"ref".$name
				);
		return $this->parser->parse("layout/ref_dropdown".$type.".html", $data, TRUE);
	}
	
	function getRefDropdown($id,$name,$type=NULL)
	{
		$q = $this->menus->getList();
		$list = array();
		foreach ($q->result() as $val) {

			$selected = $val->id == $id ? $selected = "selected='selected'" : "";	
			
			$list[]= array(
						'id' => $val->id,
						'title'=>ucwords($val->title),
						"selected"=>$selected
					 );
		}
		$data = array(
				"list"=>$list,
				"name"=>"ref".$name
				);
		return $this->parser->parse("layout/ref_dropdown".$type.".html", $data, TRUE);
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */