<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports extends MX_Controller  {
	
	var $path = "reports";
	var $alias = "Reports";	
	var $uri_page = 10;
	var $per_page = 25;
	var $price_total;
	 
	function __construct()
	{
		parent::__construct();
		$this->load->model($this->path."/Model_".$this->path, $this->path);
		$this->load->model("assigned_stores/Model_assigned_stores", "assigned_stores");
		$this->load->library('form_validation');
		$this->load->helper('to_excel');
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

		$sch3_parm = rawurldecode($this->uri->segment(5));
		$sch3_parm = $sch3_parm != 'null' && !empty($sch3_parm) ? $sch3_parm : 'null';

		if($this->session->userdata('sess_admin')['brands_id'] != "" || $this->session->userdata('sess_admin')['brands_id'] != null){
			$where3Data = array("field"=>array("is_enabled","id"),
									"value"=>array(1, $this->session->userdata('sess_admin')['brands_id']));
		}else{
			$where3Data = array("field"=>array("is_enabled"),
									"value"=>array(1));
		}
		
		$wheres3 = $where3Data;

		#ref dropdown no multi value for search
		$q_ref3 = Modules::run('widget/getQueryStaticDropdown','brands','name', $wheres3);
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

		$sch4_parm = rawurldecode($this->uri->segment(6));
		$sch4_parm = $sch4_parm != 'null' && !empty($sch4_parm) ? $sch4_parm : 'null';

		$wheres4 = array("field"=>array("is_enabled", "brands_id"),
									"value"=>array(1,$sch3_parm));
		
		#ref dropdown no multi value for search
		$q_ref4 = Modules::run('widget/getQueryStaticDropdown','projects','name', $wheres4);
		$ref4_arr = array();
		$ref4_select_arr = array();
		$r4 = 0;
		if($q_ref4->num_rows() > 0){
			foreach ($q_ref4->result() as $r_ref4) {
				 $ref4_arr[$r_ref4->id] = $r_ref4->name;
				 $ref4_select_arr[$r4] = $sch4_parm;
				 $r4++;
			}
		}
		$ref4 = Modules::run('widget/getStaticDropdown',$ref4_arr,$ref4_select_arr,4);
		#end ref dropdown no multi value for search

		$sch2_parm = rawurldecode($this->uri->segment(4));
		$sch2_parm = $sch2_parm != 'null' && !empty($sch2_parm) ? $sch2_parm : 'null';

		$wheres2 = array("field"=>array("is_enabled", "projects_id"),
									"value"=>array(1,$sch4_parm));

		#ref dropdown no multi value for search
		$q_ref2 = Modules::run('widget/getQueryStaticDropdown','assigned_stores','id', $wheres2);
		$ref2_arr = array();
		$ref2_select_arr = array();
		$r2 = 0;
		if($q_ref2->num_rows() > 0){
			foreach ($q_ref2->result() as $r_ref2) {
				 $ref2_arr[$r_ref2->stores_id] = $this->assigned_stores->getStoresName($r_ref2->stores_id);
				 $ref2_select_arr[$r2] = $sch2_parm;
				 $r2++;
			}
		}
		$ref2 = Modules::run('widget/getStaticDropdown',$ref2_arr,$ref2_select_arr,2);
		#end ref dropdown no multi value for search

		$sch5_parm = rawurldecode($this->uri->segment(7));
		$sch5_parm = $sch5_parm != 'null' && !empty($sch5_parm) ? $sch5_parm : 'null';
		$sch5_val = $sch5_parm != 'null' ? $sch5_parm : '';

		$sch6_parm = rawurldecode($this->uri->segment(8));
		$sch6_parm = $sch6_parm != 'null' && !empty($sch6_parm) ? $sch6_parm : 'null';
		$sch6_val = $sch6_parm != 'null' ? $sch6_parm : '';
		
		$sch_path = rawurlencode($sch1_parm)."/".rawurlencode($sch2_parm)."/".rawurlencode($sch3_parm)."/".rawurlencode($sch4_parm)."/".rawurlencode($sch5_parm)."/".rawurlencode($sch6_parm);
		$sch_pack = array(
						"sch1_parm"=>$sch1_parm,
						"sch2_parm"=>$sch2_parm,
						"sch3_parm"=>$sch3_parm,
						"sch4_parm"=>$sch4_parm,
						"sch5_parm"=>$sch5_parm,
						"sch6_parm"=>$sch6_parm
						 );
		#end search

		#paging
		$get_page = $this->uri->segment(9);
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
		$jum_record = $this->reports->getTotal($sch_pack);
		$paging = Modules::run("widget/page",$jum_record,$per_page,$path,$uri_segment);
		if(!$paging) $paging = "";
		$display_record = $jum_record > 0 ? "" : "display:none;";
		#end paging
		
		#record
		$query = $this->reports->getList($per_page,$lmt,$sch_pack);
		$list = array();
		$total_sales = 0;

		$q_total_sales = $this->reports->getTotalSales($sch_pack);
		if($q_total_sales->num_rows() > 0){
			$r_total = $q_total_sales->row();
			$total_sales = $r_total->total_sales; //$this->calculate_price_total($r_total->total_qty, $r_total->total_prices);
		}

		if($query->num_rows() > 0){
			foreach($query->result() as $r)
			{
				$no++;
				$title = $r->employees_name;
				$title = highlight_phrase($title, $sch6_parm, '<span style="color:#990000">', '</span>');
				$created_date = date("d/m/Y H:i", strtotime($r->created_date));
				$date = date("d/m/Y", strtotime($r->activities_date));

				#get product detail
                // $q_details = $this->reports->getProductDetails($r->id);

                // $this->price_total = 0;
                // $qty = $this->count_qty($q_details);
                // $price_total_label = 0;
                // if($q_details->num_rows() > 0){
                //    $details = $q_details->result_array();
                //    $this->populate_products($details);
                //    $price_total_label = number_format($this->price_total);
                // }

				$products_price = $r->products_price;
				$products_subtotal = $this->calculate_price_total($r->qty, $products_price);
                $products_subtotal = !empty($products_subtotal) ? number_format($products_subtotal) : 0;
                $products_price = !empty($products_price) ? number_format($products_price) : 0;

				$list[] = array(
								"no"=>$no,
								"id"=>$r->id,
								"date"=>$date,
								"created_date"=>$created_date,
								"employees_name"=>$title,
								"projects_name"=>$r->projects_name,
								"brands_name"=>$r->brands_name,
								"stores_name"=>$r->stores_name,
								'products_name'=>$r->products_name,
								"products_price"=>$products_price,
								"products_subtotal"=>$products_subtotal,
								"qty"=>$r->qty,
								//"price_total_label"=>$price_total_label,
								"link"=>site_url($this->path."/edit/".$r->id)
								);
			}
			
		}	
		#end record

		$data = array(
				  'admin_url' => base_url(),
				  'paging'=>$paging,
				  'list'=>$list,
				  'total_sales'=>!empty($total_sales) ? number_format($total_sales) : 0,
				  'jum_record'=>$jum_record,
				  'display_record'=>$display_record,
				  'sch1_val'=>$sch1_val,
				  'ref2' => $ref2,
				  'ref3' => $ref3,
				  'ref4' => $ref4,
				  'sch5_val' => $sch5_val,
				  'sch6_val' => $sch6_val,
				  'sch_path'=>$sch_path,
				  'per_page'=>$per_page,
				  'pg'=>$go_pg,
				  'title_head'=>ucfirst(str_replace('_',' ',$this->alias)),
				  'title_link'=>$this->path
				  );
		return $this->parser->parse("list.html", $data, TRUE);
	}

	function calculate_price_total($qty=0, $price=0)
	{
		return (int)$qty * (double)$price;
	}
	
	function search()
	{
		$sch1 = rawurlencode($this->input->post('sch1'));
		$sch2 = rawurlencode($this->input->post('ref2'));
		$sch3 = rawurlencode($this->input->post('ref3'));
		$sch4 = rawurlencode($this->input->post('ref4'));
		$sch5 = rawurlencode($this->input->post('sch5'));
		$sch6 = rawurlencode($this->input->post('sch6'));
		$per_page = rawurlencode($this->input->post('per_page'));
		
		$sch1 = empty($sch1) ? 'null' : $sch1;
		$sch2 = empty($sch2) ? 'null' : $sch2;
		$sch3 = empty($sch3) ? 'null' : $sch3;
		$sch4 = empty($sch4) ? 'null' : $sch4;
		$sch5 = empty($sch5) ? 'null' : $sch5;
		$sch6 = empty($sch6) ? 'null' : $sch6;
		$sch_path = $sch1."/".$sch2."/".$sch3."/".$sch4."/".$sch5."/".$sch6;
		
		redirect($this->path."/pages/".$sch_path."/".$per_page);
	}

	public function export()
	{	
		#search
		$sch1_parm = rawurldecode($this->uri->segment(3));
		$sch1_parm = $sch1_parm != 'null' && !empty($sch1_parm) ? $sch1_parm : 'null';
		$sch1_val = $sch1_parm != 'null' ? $sch1_parm : '';

		$sch2_parm = rawurldecode($this->uri->segment(4));
		$sch2_parm = $sch2_parm != 'null' && !empty($sch2_parm) ? $sch2_parm : 'null';
		$sch2_val = $sch2_parm != 'null' ? $sch2_parm : '';

		$sch3_parm = rawurldecode($this->uri->segment(5));
		$sch3_parm = $sch3_parm != 'null' && !empty($sch3_parm) ? $sch3_parm : 'null';
		$sch3_val = $sch3_parm != 'null' ? $sch3_parm : '';

		$sch4_parm = rawurldecode($this->uri->segment(6));
		$sch4_parm = $sch4_parm != 'null' && !empty($sch4_parm) ? $sch4_parm : 'null';
		$sch4_val = $sch4_parm != 'null' ? $sch4_parm : '';

		$sch5_parm = rawurldecode($this->uri->segment(7));
		$sch5_parm = $sch5_parm != 'null' && !empty($sch5_parm) ? $sch5_parm : 'null';
		$sch5_val = $sch5_parm != 'null' ? $sch5_parm : '';

		$sch6_parm = rawurldecode($this->uri->segment(8));
		$sch6_parm = $sch6_parm != 'null' && !empty($sch6_parm) ? $sch6_parm : 'null';
		$sch6_val = $sch6_parm != 'null' ? $sch6_parm : '';

		$sch_path = rawurlencode($sch1_parm)."/".rawurlencode($sch2_parm)."/".rawurlencode($sch3_parm)."/".rawurlencode($sch4_parm)."/".rawurlencode($sch5_parm)."/".rawurlencode($sch6_parm);
		$sch_pack = array(
						"sch1_parm"=>$sch1_parm,
						"sch2_parm"=>$sch2_parm,
						"sch3_parm"=>$sch3_parm,
						"sch4_parm"=>$sch4_parm,
						"sch5_parm"=>$sch5_parm,
						"sch6_parm"=>$sch6_parm
						 );
		#end search

		#record
		$query = $this->reports->getList(null,null,$sch_pack);
		$lists = array();
		$no = 0;
		if($query->num_rows() > 0){
			foreach($query->result() as $r)
			{

				$no++;
				$title = $r->employees_name;
				$created_date = date("d/m/Y H:i", strtotime($r->created_date));
				$date = date("d/m/Y", strtotime($r->activities_date));

				#get product detail
                // $q_details = $this->reports->getProductDetails($r->id);

                // $this->price_total = 0;
                // $qty = $this->count_qty($q_details);
                // $price_total_label = 0;
                // if($q_details->num_rows() > 0){
                //    $details = $q_details->result_array();
                //    $this->populate_products($details);
                //    $price_total_label = number_format($this->price_total);
                // }

				$products_price = $r->products_price;
				$products_subtotal = $this->calculate_price_total($r->qty, $products_price);
                $products_subtotal = !empty($products_subtotal) ? number_format($products_subtotal) : 0;
                $products_price = !empty($products_price) ? number_format($products_price) : 0;

				$lists[] = array(
								"no"=>$no,
								"id"=>$r->id,
								"date"=>$date,
								"created_date"=>$created_date,
								"employees_name"=>$title,
								"projects_name"=>$r->projects_name,
								"brands_name"=>$r->brands_name,
								"stores_name"=>$r->stores_name,
								'products_name'=>$r->products_name,
								"products_price"=>$products_price,
								"products_subtotal"=>$products_subtotal,
								"qty"=>$r->qty
								);
			}
			
		}	
		#end record

		$data = array(
				  'admin_url' => base_url(),
				  'lists'=>$lists
				  );
		$contents = $this->parser->parse("export.html", $data, TRUE);
		to_excel($contents, $this->path."_".date("YmdHi"));

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
			
			$add_edit = $id == 0 ? "Add" : "View Detail";

			#set dropdown asset

			#record
			$q = $this->reports->getDetail($id);
			$list = array();
			if($q->num_rows() > 0){
					$r = $q->row();

					#listing
					$title = $r->employees_name;
					$activities_date = !empty($r->activities_date) ? date("d/m/Y", strtotime($r->activities_date)) : "";
					$reports_datetime = !empty($r->reports_datetime) ? date("d/m/Y H:i", strtotime($r->reports_datetime)) : "";

					#get product detail
	                $q_details = $this->reports->getProductDetails($r->id);

	                $this->price_total = 0;
	                $total_qty = $this->count_qty($q_details);
	                $price_total_label = 0;
	                if($q_details->num_rows() > 0){
	                   $details = $q_details->result_array();
	                   $products = $this->populate_products($details);
	                   $price_total_label = number_format($this->price_total);
	                }

					$list[] = array(
									"id"=>$r->id,
									"employees_name"=>$title,
									"projects_name"=>$r->projects_name,
									"brands_name"=>$r->brands_name,
									"stores_name"=>$r->stores_name,
									"products"=>$products,
									"qty_total"=>$total_qty,
									"price_total_label"=>$price_total_label,
									"date"=>$activities_date,
									"created_date"=>$reports_datetime
									);
			

			}
			#end record
			
			$data = array(
					  'admin_url'=>base_url(),
					  'list'=>$list,
					  'title_head'=>ucfirst(str_replace('_',' ',$this->alias)),
				 	  'title_link'=>$this->path,
					  'add_edit'=>$add_edit,
					  'id'=>$id
					  );
			return $this->parser->parse("detail.html", $data, TRUE);
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
				$this->reports->deleteFileUpload($file_image_old);
			}
			$file_image = $this->reports->setFileUpload($file_image_title,$file_image_tmp,$file_image_old);
		}else{
			$file_image = $file_image_old;
		}

		#data
		$id = strip_tags($this->input->post("id"));	
		$tags = '<img><div><table><tbody><tr><td><p><a><br><ul><ol><li><strong><b><i><u><strike><em>';
		$data_pack = array(
						'name'=>strip_tags($this->input->post("name")),
						'address'=>strip_tags($this->input->post("address"),$tags),
						'phone'=>strip_tags($this->input->post("phone")),
						'lat'=>strip_tags($this->input->post("lat")),
						'lon'=>strip_tags($this->input->post("lon")),
						'zoom'=>strip_tags($this->input->post("zoom")),
						'map'=>strip_tags($this->input->post("map")),
						'file_image'=>$file_image,
						"brands_id"=>strip_tags($this->input->post("ref3")),
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

			//redirect($this->path."/edit/".$id);
			$link_id = $id;
		}else{
			if($id > 0)
			{
				$this->reports->setUpdate($data_pack,$where_pack);
				$this->session->set_flashdata("success","Data saved successful");
				//redirect($this->path."/edit/".$id);
				$link_id = $id;
			}else{
				$id_term = $this->reports->setInsert($data_pack);
				$last_id = $this->db->insert_id();
				
				$this->session->set_flashdata("success","Data inserted successful");
				//redirect($this->path."/edit/".$last_id);
				$link_id = $last_id;
			}
		}

		#redirect to success page
		$response['linkId'] = $link_id;
		echo json_encode($response);
	}	

	function delete($id=0)
	{
		$product = $this->reports->getDetail($id);
		$row = $product->row();
		$file_image = $row->file_image;
		$del_images = $this->reports->setPictureDelete($id,$file_image);
		$del_status = $this->reports->setDelete($id);
		$response['id'] = $id;
		$response['status'] = $del_status;
		echo $result = json_encode($response);
		exit();
	}

	function unlink($id,$file_image)
	{
		$file_image = rawurldecode($file_image);
		$this->reports->setPictureDelete($id, $file_image);
		redirect($this->path."/edit/".$id);
	}
	
	function count_qty($q)
    {
        $num = 0;
        if($q->num_rows() > 0){
           foreach($q->result_array() as $r){
                $num += $r['qty'];
           }
        }

        return $num;
    }

    function populate_products($details)
    {
        foreach($details as $k=>$detail){
            if(!isset($detail['products_id'])){
                return $details;
            }

            #get data details
            $q_prodcts = $this->reports->getProductDetail($detail['products_id']);
            if($q_prodcts->num_rows() == 0){
               return $details;
            }

            $details[$k] = $q_prodcts->row_array(); 
            $details[$k]['no'] = $k+1;
            $details[$k]['products_name'] = $details[$k]['name'];
            $details[$k]['products_description'] = $details[$k]['description'];
            $price_total = $this->calculate_total($details[$k]['price'], $detail['qty']);
            $details[$k]['price'] = (double)$details[$k]['price'];
            $details[$k]['price_label'] = number_format((double)$details[$k]['price']);
            $this->price_total += $price_total;

            $details[$k]['qty'] = $detail['qty'];
            $details[$k]['file_url'] = !empty($details[$k]['file_image']) ? asset_url()."products/thumbs/".$details[$k]['file_image'] : "";
            $details[$k]['products_image'] = !empty($details[$k]['file_image']) ? "<img src='".$details[$k]['file_url']."' width='50'/>" : "";
            $details[$k]['file_detail_url'] = !empty($details[$k]['file_image']) ? asset_url()."products/".$details[$k]['file_image'] : "";
            $details[$k]['price_subtotal'] = $price_total;
            $details[$k]['price_subtotal_label'] = number_format($price_total);

            unset($details[$k]['name'],$details[$k]['description'],$details[$k]['brands_id']);
          
        }
        return $details;
    }

    function calculate_total($price, $qty)
    {
        $price_total = 0;
        $price_total += $price * $qty;
        return $price_total;
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
