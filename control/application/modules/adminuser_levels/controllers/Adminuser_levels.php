<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Adminuser_levels extends MX_Controller  {
	
	var $path = "adminuser_levels";
	var $alias = "Admin Level";
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
		$jum_record = $this->adminuser_levels->getTotal($sch_pack);
		$paging = Modules::run("widget/page",$jum_record,$per_page,$path,$uri_segment);
		if(!$paging) $paging = "";
		$display_record = $jum_record > 0 ? "" : "display:none;";
		#end paging
		
		$query = $this->adminuser_levels->getList($per_page,$lmt,$sch_pack);
		$list = array();
		if($query->num_rows() > 0){
			foreach($query->result() as $r)
			{
				$no++;			
				
				$title = $r->title;
				$title = highlight_phrase($title, $sch1_parm, '<span style="color:#990000">', '</span>');
				$is_enabled = $r->is_enabled == 1 ? "icon-ok-sign" : "icon-minus-sign";
				$created_date = date("d/m/Y H:i:s",strtotime($r->created_date));
			
				$list[] = array(
								 "no"=>$no,
								 "id"=>$r->id,
								 "title"=>$title,
								 "link"=>site_url($this->path."/edit/".$r->id),
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
				  'sch1_val'=>$sch1_val,
				  'sch1_parm'=>$sch1_parm,
				  'sch2_parm'=>$sch2_parm,
				  'ref2'=>$ref2,
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
		$per_page = rawurlencode($this->input->post('per_page'));

		$sch1 = empty($sch1) ? 'null' : $sch1;
		$sch2 = is_null($sch2) || $sch2 == "" ? 'null' : $sch2;
		$sch_path = $sch1."/".$sch2;
		
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
				  'sidebar'=>$sidebar
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
			$ref2_arr = array(
								0=>"Not Active",
								1=>"Active"
							  );
			
			#record
			$q = $this->adminuser_levels->getDetail($id);
			$list = $list_term_option = array();
			if($q->num_rows() > 0){
				$r = $q->row();

				$title = $this->session->flashdata("ref_title") ? $this->session->flashdata("ref_title") : $r->title;
				$created_date = $r->created_date;
				$id = $r->id;
				
				#ref dropdown no multi value
				$ref2_select_arr[0] = $this->session->flashdata("is_enabled") ? $this->session->flashdata("is_enabled") : $r->is_enabled;
				$ref2 = Modules::run('widget/getStaticDropdown',$ref2_arr,$ref2_select_arr,2);
				#end ref dropdown no multi value

			}else{
				
				$title = $this->session->flashdata("ref_title") ? $this->session->flashdata("ref_title") : "";
				$created_date = "";
				$id = 0;
				
				#ref dropdown no multi value
				$ref2_select_arr[0] = $this->session->flashdata("is_enabled") ? $this->session->flashdata("is_enabled") : null;
				$ref2 = Modules::run('widget/getStaticDropdown',$ref2_arr,$ref2_select_arr,2);
				#end ref dropdown no multi value				


			}
			#end record
			
			#listing
			$list[] = array(
							"id"=>$id,
							"title"=>$title,
							"created_date"=>$created_date,
							"ref2"=>$ref2
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
		$id = strip_tags($this->input->post("id"));		
		$data_pack = array(
						'title'=>strip_tags($this->input->post("title")),
						'is_enabled'=>$this->input->post("ref2"),
						'modified_by'=>$this->session->userdata('sess_admin')['id'],
						'created_date'=>date("Y-m-d H:i:s",now())
						 );
		$where_pack = array(
						'id'=>$id
						 );
		

		#validation
		$this->form_validation->set_rules('title', 'title', 'required');
		if ($this->form_validation->run($this) == FALSE)
		{
			$this->session->set_flashdata("err",validation_errors());
			$this->session->set_flashdata($data_pack);

			redirect($this->path."/edit/".$id);
		}else{
			if($id > 0)
			{
				$this->adminuser_levels->setUpdate($data_pack,$where_pack);
				$this->session->set_flashdata("success","Data saved successful");
				redirect($this->path."/edit/".$id);
			}else{
				$id_term = $this->adminuser_levels->setInsert($data_pack);
				$last_id = $this->db->insert_id();
				
				$this->session->set_flashdata("success","Data inserted successful");
				redirect($this->path."/edit/".$last_id);
			}
		}
	}
	
	
	function delete($id=0)
	{
		$del_status = $this->adminuser_levels->setDelete($id);
		$response['id'] = $id;
		$response['status'] = $del_status;
		echo $result = json_encode($response);
		exit();
	}

	
	function getRefDropdown($id,$name,$type=NULL,$sch_pack)
	{
		$q = $this->adminuser_levels->getList(null,null,$sch_pack,'dropdown');
		$list = array();
		
		foreach ($q->result() as $val) {
			$selected = $val->id == $id ? $selected = "selected='selected'" : "";	
			$qchild = $this->adminuser_levels->getChild($val->id);
			$title_parent = "";
			if($qchild->num_rows() == 0){
				if($val->parent_id > 0){
					$q_parent = $this->adminuser_levels->getParent($val->parent_id);
					$r_parent = $q_parent->row();
					$title_parent = $r_parent->title." - ";
				}
				$title = $title_parent.$val->title;
			}
			if($qchild->num_rows() == 0){
					$list[]= array(
						'id' => $val->id,
						'title'=>$title,
						"selected"=>$selected
					 );
			}
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
