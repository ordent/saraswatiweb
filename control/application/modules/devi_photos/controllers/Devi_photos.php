<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Devi_photos extends MX_Controller  {
	
	var $path = "devi_photos";
	var $alias = "Devi - Photos";	
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
		$jum_record = $this->devi_photos->getTotal($sch_pack);
		$paging = Modules::run("widget/page",$jum_record,$per_page,$path,$uri_segment);
		if(!$paging) $paging = "";
		$display_record = $jum_record > 0 ? "" : "display:none;";
		#end paging
		
		#record
		$query = $this->devi_photos->getList($per_page,$lmt,$sch_pack);
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
		$sch_path = $sch1."/".$sch24;
		
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
			$q = $this->devi_photos->getDetail($id);
			$list = $list_term_option = array();
			if($q->num_rows() > 0){
					$r = $q->row();

					#image
					$file_image = "";
					if($r->file_image)
					{
						$file_image = "
						<a href='".asset_url()."devi/".$r->file_image."' rel='facebox'><img src='".asset_url()."devi/thumbs/".$r->file_image."' class='thumbnail'></a>
						<br/>
						<a href='".base_url().$this->path."/unlink/".$r->id."/".rawurlencode($r->file_image)."' class='btn btn-info'>delete</a><br/>
						<input type='hidden' name='file_image_old' value='".$r->file_image."' id='file_image_old'>";
					}
					#end image
					
					$title = $this->session->flashdata("name") ? $this->session->flashdata("name") : $r->name;
					$link = $this->session->flashdata("link") ? $this->session->flashdata("link") : $r->link;
					$created_date = $r->created_date;
					$id = $r->id;
					
					#ref dropdown no multi value
					$ref2_select_arr[0] = $this->session->flashdata("is_enabled") ? $this->session->flashdata("is_enabled") : $r->is_enabled;
					$ref2 = Modules::run('widget/getStaticDropdown',$ref2_arr,$ref2_select_arr,2);
					#end ref dropdown no multi value

			}else{

					$file_image = "";

					$title = $this->session->flashdata("name") ? $this->session->flashdata("name") : "";
					$link = $this->session->flashdata("link") ? $this->session->flashdata("link") : "";
					$id = 0;
					
					#ref dropdown no multi value
					$ref2_select_arr[0] = $this->session->flashdata("is_enabled") ? $this->session->flashdata("is_enabled") : null;
					$ref2 = Modules::run('widget/getStaticDropdown',$ref2_arr,$ref2_select_arr,2);
					#end ref dropdown no multi value

					$created_date = "";
			}
			#end record
			
			#listing
			$list[] = array(
									"id"=>$id,
									"name" =>$title,
									"link" => $link,
									"ref2"=>$ref2,
									'file_image'=>$file_image,
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

		#image 
		$file_image_old = strip_tags($this->input->post("file_image_old"));
		$file_image_title = $_FILES["file_image"]["name"];
		$file_image_tmp  = $_FILES["file_image"]["tmp_name"];
		
		if(!empty($file_image_title)){
			if(!empty($file_image_old)){
				$this->devi_photos->deleteFileUpload($file_image_old);
			}
			$file_image = $this->devi_photos->setFileUpload($file_image_title,$file_image_tmp,$file_image_old);
		}else{
			$file_image = $file_image_old;
		}

		#data
		$id = strip_tags($this->input->post("id"));	
		$data_pack = array(
						'name'=>strip_tags($this->input->post("name")),
						'link'=>strip_tags($this->input->post("link")),
						'file_image'=>$file_image,
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
		if ($this->form_validation->run($this) == FALSE)
		{
			$this->session->set_flashdata("err",validation_errors());
			$this->session->set_flashdata($data_pack);

			redirect($this->path."/edit/".$id);
		}else{
			if($id > 0)
			{
				$this->devi_photos->setUpdate($data_pack,$where_pack);
				$this->session->set_flashdata("success","Data saved successful");
				$link_id = $id;
			}else{
				$id_term = $this->devi_photos->setInsert($data_pack);
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
		$product = $this->devi_photos->getDetail($id);
		$row = $product->row();
		$file_image = $row->file_image;
		$del_images = $this->devi_photos->setPictureDelete($id,$file_image);
		$del_status = $this->devi_photos->setDelete($id);
		$response['id'] = $id;
		$response['status'] = $del_status;
		echo $result = json_encode($response);
		exit();
	}

	function unlink($id,$file_image)
	{
		$file_image = rawurldecode($file_image);
		$this->devi_photos->setPictureDelete($id, $file_image);
		redirect($this->path."/edit/".$id);
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
