<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menu_auths extends MX_Controller  {
	
	var $path = "menu_auths";
	var $alias = "Admin Privileges";
	var $uri_page = 6;
	var $per_page = 25;
	 
	function __construct()
	{
		parent::__construct();
		$this->load->model($this->path."/Model_".$this->path, $this->path);
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
		$sch2_parm = $sch2_parm != 'null' && !empty($sch2_parm) ? $sch2_parm : 'null';
		
		#ref dropdown no multi value for search
		$q_ref2 = Modules::run('widget/getQueryStaticDropdown','adminuser_levels','title');
		$ref2_arr = array();
		foreach ($q_ref2->result() as $r_ref2) {
			 $ref2_arr[$r_ref2->id] = $r_ref2->title;
		}
					
		$q_result2 = $q_ref2->result();
		$ref2_select_arr = array();
		$r2 = 0;
		foreach ($q_result2 as $r_ref2) {
			$ref2_select_arr[$r2] = $sch2_parm;
			$r2++;
		}

		$ref2 = Modules::run('widget/getStaticDropdown',$ref2_arr,$ref2_select_arr,2);
		#end ref dropdown no multi value for search
		
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
		$jum_record = $this->menu_auths->getTotal($sch_pack);
		$paging = Modules::run("widget/page",$jum_record,$per_page,$path,$uri_segment);
		if(!$paging) $paging = "";
		$display_record = $jum_record > 0 ? "" : "display:none;";
		#end paging
		
		#record
		$query = $this->menu_auths->getList($per_page,$lmt,$sch_pack);
		$list = array();
		if($query->num_rows() > 0){
			foreach($query->result() as $r)
			{
				$no++;
				
				$title = ucwords($r->menus_title);
				$title = highlight_phrase($title, $sch1_parm, '<span style="color:#990000">', '</span>');
				$created_date = date("d/m/Y H:i:s",strtotime($r->created_date));
			
				$list[] = array(
								 "no"=>$no,
								 "id"=>$r->id,
								 "title" =>$title,
								 "level" =>$r->title,
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
		$sch2 = empty($sch2) ? 'null' : $sch2;
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
			
			$q = $this->menu_auths->getDetail($this->path,$id);
			$list = $list_term_option = array();
			if($q->num_rows() > 0){
				foreach($q->result() as $r){
					
					$menu_level_id = $r->menu_level_id;
					$menus_id = $r->menus_id;
					$created_date = $r->created_date;
					$id = $r->id;
				}
			}else{
				
				$menu_level_id = "";
				$menus_id = 1;
				$created_date = "";
				$id = 0;
			}
			
			
			#ref dropdown multi value
			$ref1 = Modules::run('menu/getRefDropdown',$menus_id,1,"_multiple");
			#end ref dropdown multi value
			
			#ref dropdown no multi value
			$sch_pack = array(
						"sch1_parm"=>null,
						"sch2_parm"=>1
						 );
			$ref2 = Modules::run('adminuser_levels/getRefDropdown',$menu_level_id,2,null,$sch_pack);
			#end ref dropdown no multi value
			
			$list[] = array(
									"id"=>$id,
									"ref1"=>$ref1,
									"ref2"=>$ref2,
									"created_date"=>$created_date
									);
			
			#notification
			$err = $this->session->flashdata("err") ? $this->session->flashdata("err") : "";
			$success = $this->session->flashdata("success") ? $this->session->flashdata("success") : "";
			extract(GetNotification($success,$err));
			#end notification
			
		
			$data = array(
					  'admin_url' => base_url(),
					  'list'=>$list,
					  'title_head'=>ucfirst(str_replace('_',' ',$this->alias)),
				 	  'title_link'=>$this->path,
					  'notif'=>$notif,
					  'btn_plus'=>$btn_plus,
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
		$ref1 = $this->input->post("ref1");
		$ref2 = $this->input->post("ref2");
		$id = strip_tags($this->input->post("id"));		
		$where_pack = array(
						'id'=>$id
						 );
		
		#validation
		$this->load->library('form_validation');
		$this->form_validation->set_rules('ref1', 'select menu', 'required');
		$this->form_validation->set_rules('ref2', 'select admin level', 'required');
		if ($this->form_validation->run($this) == FALSE)
		{
			$this->session->set_flashdata("err",validation_errors());
			$this->session->set_flashdata($data_pack);
			redirect($this->path."/edit/".$id);
		}else{
				foreach($ref1 as $ref1_val){
					$data_pack = array();
					if($this->menu_auths->cekInsert($ref1_val,$ref2) == 0){
						
						$data_pack = array(
							'menus_id'=>$ref1_val,
							'adminuser_levels_id'=>$ref2,
							'modified_by'=>$this->session->userdata('sess_admin')['id'],
							'created_date'=>date("Y-m-d :H:i:s",now())
						 );
						$this->menu_auths->setInsert($data_pack);
					}
				}
				
				$this->session->set_flashdata("success","Data inserted successful");
				redirect($this->path."/edit/0");
		}
	}
	
	function delete($id=0)
	{
		$del_status = $this->menu_auths->setDelete($id);
		$response['id'] = $id;
		$response['status'] = $del_status;
		echo $result = json_encode($response);
		exit();
	}
	

	function ajaxRequest1($id)
	{

		$name = 1;
		$type = "multiple";
		$refDropdown = $selected = "";
		
		$sch_pack = array(
						"sch1_parm"=>null,
						"sch2_parm"=>$id
						 );
		
		#select menu id
		$query = $this->menu_auths->getList(null,null,$sch_pack);
		foreach($query->result() as $row)
		{
			$this->db->where_not_in('id',$row->menus_id);
		}	
		$query_menu = $this->db->get('menus');
		

		$list=array();
		foreach($query_menu->result() as $row_menu)
		{

			$selected = $row_menu->id == 1 ? "selected='selected'" : "";

			$list[]= array(
									'id' =>$row_menu->id,
									'title'=>ucwords($row_menu->title),
									"selected"=>$selected
								 );
		}
		
		$data = array(
					"list"=>$list,
					"name"=>"ref".$name
					);
		$refDropdown = $this->parser->parse("layout/ref_dropdown_".$type.".html", $data,TRUE);

		$data = array(
					  'base_url' => base_url(),
					  'ref'.$name=>$refDropdown
					  );
		echo $this->parser->parse("ajax.html", $data, TRUE);

	}

	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */