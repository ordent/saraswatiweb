<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class What_we_do_quotes extends MX_Controller  {
	
	var $path = "what_we_do_quotes";
	var $alias = "What We Do - Quotes";	
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
	
		$sch2_parm = rawurldecode($this->uri->segment(4));
		$sch2_parm = $sch2_parm != 'null' ? $sch2_parm : 'null';
		$sch2_parm =  $sch2_parm == ""  ? 'null' : $sch2_parm;
		$sch2_arr = array(
							  0=>"No Active",
							  1=>"Active"
							  );
		$sch2_select_arr = array($sch2_parm);
		$ref2 = Modules::run('widget/getStaticDropdown',$sch2_arr,$sch2_select_arr,2);

		$sch3_parm = rawurldecode($this->uri->segment(5));
		$sch3_parm = $sch3_parm != 'null' && !empty($sch3_parm) ? $sch3_parm : 'null';

		$wheres3 = array("field"=>array("is_enabled"),
									"value"=>array(1));
		
		#ref dropdown no multi value for search
		$q_ref3 = Modules::run('widget/getQueryStaticDropdown','what_we_dos','name', $wheres3);
		$ref3_arr = array();
		$ref3_select_arr = array();
		$r3 = 0;
		if($q_ref3->num_rows() > 0){
			foreach ($q_ref3->result() as $r_ref3) {
				 $ref3_arr[$r_ref3->id] = $r_ref3->name;
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
						"sch3_parm"=>$sch3_parm,
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
		$jum_record = $this->what_we_do_quotes->getTotal($sch_pack);
		$paging = Modules::run("widget/page",$jum_record,$per_page,$path,$uri_segment);
		if(!$paging) $paging = "";
		$display_record = $jum_record > 0 ? "" : "display:none;";
		#end paging
		
		#record
		$query = $this->what_we_do_quotes->getList($per_page,$lmt,$sch_pack);
		$list = array();

		if($query->num_rows() > 0){
			foreach($query->result() as $r)
			{
				$no++;
				$title = $r->name;
				$title = highlight_phrase($title, $sch1_parm, '<span style="color:#990000">', '</span>');
				$is_enabled = $r->is_enabled == 1 ? "icon-ok-sign" : "icon-minus-sign";
				$created_date  = date("d/m/Y H:i",strtotime($r->created_date));

				$list[] = array(
								"no"=>$no,
								"id"=>$r->id,
								"name"=>$title,
								"ref_what_we_dos_name"=>$r->ref_what_we_dos_name,
								"description"=>word_limiter($r->description,10),
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
				  'ref2' => $ref2,
				  'ref3' => $ref3,
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
		
		if(is_numeric($id)){
			
			$add_edit = $id == 0 ? "Add" : "Edit";

			#set dropdown asset
			$ref2_arr = array(0=>"Not Active",1=>"Active");
			$ref4_arr = array(0=>"Open",1=>"Running",2=>"Closed");

			#record
			$q = $this->what_we_do_quotes->getDetail($id);
			$list = $list_term_option = array();
			if($q->num_rows() > 0){
					$r = $q->row();
					
					$title = $this->session->flashdata("name") ? $this->session->flashdata("name") : $r->name;
					$description = $this->session->flashdata("description") ? $this->session->flashdata("description") : $r->description;
					$created_date = $r->created_date;
					$id = $r->id;
					
					#ref dropdown no multi value
					$ref2_select_arr[0] = $this->session->flashdata("is_enabled") ? $this->session->flashdata("is_enabled") : $r->is_enabled;
					$ref2 = Modules::run('widget/getStaticDropdown',$ref2_arr,$ref2_select_arr,2);
					#end ref dropdown no multi value

					#ref dropdown no multi value
					$wheres3 = array("field"=>array("is_enabled"),
									"value"=>array(1));
					$q_ref3 = Modules::run('widget/getQueryStaticDropdown','what_we_dos','name',$wheres3);
					$ref3_arr = array();
					foreach ($q_ref3->result() as $r_ref3) {
						 $ref3_arr[$r_ref3->id] = $r_ref3->name;
					}
					
					$q_ref3 = $this->session->flashdata("ref3") ? $this->session->flashdata("ref3") : $q;
					$q_result3 = $this->session->flashdata("ref3") ? $q_ref3 : $q_ref3->result();
					$ref3_select_arr = array();
					$r3 = 0;
					foreach ($q_result3 as $r_ref3) {
						 $ref3_select_arr[$r3] = $this->session->flashdata("ref3") ? $r_ref3 : $r_ref3->what_we_dos_id;
						 $r3++;
					}

					$ref3 = Modules::run('widget/getStaticDropdown',$ref3_arr,$ref3_select_arr,3);
					#end ref dropdown no multi value

			}else{

					$title = $this->session->flashdata("name") ? $this->session->flashdata("name") : "";
					$description = $this->session->flashdata("description") ? $this->session->flashdata("description") : "";
					$id = 0;
					
					#ref dropdown no multi value
					$ref2_select_arr[0] = $this->session->flashdata("is_enabled") ? $this->session->flashdata("is_enabled") : null;
					$ref2 = Modules::run('widget/getStaticDropdown',$ref2_arr,$ref2_select_arr,2);
					#end ref dropdown no multi value

					#ref dropdown no multi value
					$wheres3 = array("field"=>array("is_enabled"),
									"value"=>array(1));
					$q_ref3 = Modules::run('widget/getQueryStaticDropdown','what_we_dos','name',$wheres3);
					$ref3_arr = array();
					foreach ($q_ref3->result() as $r_ref3) {
						 $ref3_arr[$r_ref3->id] = $r_ref3->name;
					}
					
					$q_ref3 = $this->session->flashdata("ref3") ? $this->session->flashdata("ref3") : array();

					$ref3_select_arr = array();
					$r3 = 0;
					foreach ($q_ref3 as $r_ref3) {
						 $ref3_select_arr[$r3] = $r_ref3;
						 $r3++;
					}

					$ref3 = Modules::run('widget/getStaticDropdown',$ref3_arr,$ref3_select_arr,3);
					#end ref dropdown no multi value

					$created_date = "";
			}
			#end record
			
			#listing
			$list[] = array(
									"id"=>$id,
									"name" =>$title,
									"description"=>$description,
									"ref2"=>$ref2,
									"ref3"=>$ref3,
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
		$response['status'] = 0;
		$response['link_id'] = 0;
		$response['message'] = "";

		#data
		$id = strip_tags($this->input->post("id"));	
		$tags = '<img><div><table><tbody><tr><td><p><a><br><ul><ol><li><strong><b><i><u><strike><em>';
		$data_pack = array(
						'name'=>strip_tags($this->input->post("name")),
						'description'=>strip_tags($this->input->post("description"),$tags),
						"what_we_dos_id"=>strip_tags($this->input->post("ref3")),
						'is_enabled'=>$this->input->post("ref2")
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
		$this->form_validation->set_rules('name', 'name', 'required');
		$this->form_validation->set_rules('description', 'description', 'required');
		if ($this->form_validation->run($this) == FALSE)
		{
			$this->session->set_flashdata("err",validation_errors());
			$this->session->set_flashdata($data_pack);

			redirect($this->path."/edit/".$id);
		}else{
			if($id > 0)
			{
				$this->what_we_do_quotes->setUpdate($data_pack,$where_pack);
				$this->session->set_flashdata("success","Data saved successful");
				$link_id = $id;
			}else{
				$id_term = $this->what_we_do_quotes->setInsert($data_pack);
				$last_id = $this->db->insert_id();
				
				$this->session->set_flashdata("success","Data inserted successful");
				$link_id = $last_id;
			}
		}

		#redirect to success page
		$response['linkId'] = $link_id;
		echo json_encode($response);

	}
	

	function delete($id=0)
	{
		$product = $this->what_we_do_quotes->getDetail($id);
		$row = $product->row();
		$del_status = $this->what_we_do_quotes->setDelete($id);
		$response['id'] = $id;
		$response['status'] = $del_status;
		echo $result = json_encode($response);
		exit();
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
