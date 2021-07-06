<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Subscribers extends MX_Controller  {
	
	var $path = "subscribers";
	var $alias = "Subscribers";	
	var $uri_page = 7;
	var $per_page = 25;
	var $price_total;
	 
	function __construct()
	{
		parent::__construct();
		$this->load->model($this->path."/Model_".$this->path, $this->path);
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

		$sch2_parm = rawurldecode($this->uri->segment(4));
		$sch2_parm = $sch2_parm != 'null' && !empty($sch2_parm) ? $sch2_parm : 'null';
		$sch2_val = $sch2_parm != 'null' ? $sch2_parm : '';

		$sch3_parm = rawurldecode($this->uri->segment(5));
		$sch3_parm = $sch3_parm != 'null' && !empty($sch3_parm) ? $sch3_parm : 'null';
		$sch3_val = $sch3_parm != 'null' ? $sch3_parm : '';
		
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
		$jum_record = $this->subscribers->getTotal($sch_pack);
		$paging = Modules::run("widget/page",$jum_record,$per_page,$path,$uri_segment);
		if(!$paging) $paging = "";
		$display_record = $jum_record > 0 ? "" : "display:none;";
		#end paging
		
		#record
		$query = $this->subscribers->getList($per_page,$lmt,$sch_pack);
		$list = array();

		if($query->num_rows() > 0){
			foreach($query->result() as $r)
			{
				$no++;
				$email = $r->email;
				$email = highlight_phrase($email, $sch1_parm, '<span style="color:#990000">', '</span>');
				$ip_address = $r->ip_address;
				$ip_address = highlight_phrase($ip_address, $sch1_parm, '<span style="color:#990000">', '</span>');
				$created_date = date("d/m/Y H:i", strtotime($r->created_date));

				$list[] = array(
								"no"=>$no,
								"id"=>$r->id,
								"email"=>$email,
								"ip_address"=>$ip_address,
								"created_date"=>$created_date,
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
				  'sch2_val' => $sch2_val,
				  'sch3_val' => $sch3_val,
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
		$sch2 = rawurlencode($this->input->post('sch2'));
		$sch3 = rawurlencode($this->input->post('sch3'));
		$per_page = rawurlencode($this->input->post('per_page'));
		
		$sch1 = empty($sch1) ? 'null' : $sch1;
		$sch2 = empty($sch2) ? 'null' : $sch2;
		$sch3 = empty($sch3) ? 'null' : $sch3;
		$sch_path = $sch1."/".$sch2."/".$sch3;
		
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

		$sch_path = rawurlencode($sch1_parm)."/".rawurlencode($sch2_parm)."/".rawurlencode($sch3_parm);
		$sch_pack = array(
						"sch1_parm"=>$sch1_parm,
						"sch2_parm"=>$sch2_parm,
						"sch3_parm"=>$sch3_parm
						 );
		#end search

		#record
		$query = $this->subscribers->getList(null,null,$sch_pack);
		$lists = array();
		$no = 0;
		if($query->num_rows() > 0){
			foreach($query->result() as $r)
			{

				$no++;
				$title = $r->email;
				$created_date = date("d/m/Y H:i", strtotime($r->created_date));
				$date = date("d/m/Y", strtotime($r->activities_date));

				$lists[] = array(
								"no"=>$no,
								"id"=>$r->id,
								"date"=>$date,
								"created_date"=>$created_date,
								"email"=>$title,
								"ip_address"=>$r->ip_address
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


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
