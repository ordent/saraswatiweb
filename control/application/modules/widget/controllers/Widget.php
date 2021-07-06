<?php
class widget extends MX_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model("widget/model_widget", "widget");
	}
	
	function index(){
	}
	
	
	function page($jum_record,$per_page,$path,$uri_segment)
	{
		$link = "";
		
		$config['base_url'] = $path;
		$config['total_rows'] = $jum_record;
		$config['per_page'] = $per_page;
		$config['num_links'] = 3;
		$config['uri_segment'] = $uri_segment;
		$config['next_link'] = '&raquo;';
		$config['prev_link'] = '&laquo;';
		$config['next_tag_open'] = "<span class='next'>";
		$config['next_tag_close'] = "</span>";
		$config['prev_tag_open'] = "<span class='prev'>";
		$config['prev_tag_close'] = "</span>";
		$this->pagination->initialize($config);
		$link = $this->pagination->create_links();
		return $link;
	}
	
	function setalias($uri_title)
	{
		$groups_alias = preg_replace("/[^A-Za-z0-9\s*]/","",$uri_title);
		$groups_alias = strtolower(url_title($groups_alias));
		return $groups_alias;
	}
	
	function cek_uri($uri,$count)
	{
		if(!is_numeric($uri) || ereg("[^A-Za-z0-9]",$uri)){
			redirect(base_url());
		}
	}
	
	
	function getUrlDate($date)
	{
		$date = str_replace("-","/",date("Y-m-d",strtotime($date)));
  		return $date;
	}
	
	
	function getTitleMenu($path=NULL)
	{
		$this->db->where("menu_file",$path);
		$query = $this->db->get("ddi_ref_menu");
		return $query;
	}
	
	
	function publish()
	{
		$table = $this->uri->segment(3);
		$id = $this->uri->segment(4);
		$setval = 1; 
		
		$q = $this->widget->getList($table,$id);

		if($q->num_rows() > 0)
		{
			$row = $q->row(); 
			$val = $row->is_enabled;
		}
		
		if($val == 1){ 
			$setval = 0; 
		}
		
		$this->widget->setUpdate($table,$id,$setval,$this->session->userdata('sess_admin')['id'],'is_enabled');
		
		$status = 0;
		$title = "";
		$q = $this->widget->getList($table,$id);
		if($q->num_rows() > 0)
		{
			$status = 1; 
			$row = $q->row(); 
			$title = $row->is_enabled;
		}
		
		$title = $title == 1 ? "<i class=\"icon-ok-sign\"></i>" : "<i class=\"icon-minus-sign\"></i>";
		
		$response['id'] = $id;
		$response['val'] = $title;
		$response['status'] = $status;
		echo $result = json_encode($response);
		
	}
	
	
	function highlight()
	{
		$table = $this->uri->segment(3);
		$id = $this->uri->segment(4);
		$setval = 'Yes'; 
		
		$q = $this->widget->getList($table,$id);

		if($q->num_rows() > 0)
		{
			$row = $q->row(); 
			$val = $row->highlight;
		}
		
		if($val == 'Yes'){ 
			$setval = 'No'; 
		}
		
		$this->widget->setUpdate($table,$id,$setval,$this->session->userdata('sess_admin')['id'],'highlight');
		
		$status = 0;
		$title = "";
		$q = $this->widget->getList($table,$id);
		if($q->num_rows() > 0)
		{
			$status = 1; 
			$row = $q->row(); 
			$title = $row->highlight;
		}
		
		$title = $title == "Yes" ? "<i class=\"icon-star\"></i>" : "";
		
		$response['id'] = $id;
		$response['val'] = $title;
		$response['status'] = $status;
		echo $result = json_encode($response);
		
	}
	
	
	function publish_parent()
	{
		$table = $this->uri->segment(3);
		$table_child = $this->uri->segment(4);
		$id = $this->uri->segment(5);
		$setval = 1; 
		
		#get parent id
		$this->db->where("id",$id);
		$qc = $this->db->get($table_child);
		$rc = $qc->row();
		$id = $rc->user_auth_id;
		
		$q = $this->widget->getList($table,$id);

		if($q->num_rows() > 0)
		{
			$row = $q->row(); 
			$val = $row->is_enabled;
		}
		
		if($val == 1){ 
			$setval = 0; 
		}
		
		$this->widget->setUpdate($table,$id,$setval,$this->session->userdata('sess_admin')['id'],'publish');
		
		$status = 0;
		$title = "";
		$q = $this->widget->getList($table,$id);
		if($q->num_rows() > 0)
		{
			$status = 1; 
			$row = $q->row(); 
			$title = $row->is_enabled;
		}
		
		$response['id'] = $this->uri->segment(5);
		$response['val'] = $title;
		$response['status'] = $status;
		echo $result = json_encode($response);
		
	}
	
	
	function getQueryStaticDropdown($table,$sort=NULL,$wheres=array(),$subquery=array())
	{
		$q = $this->widget->getDropdown($table,$sort,$wheres,$subquery);
		return $q;
	}
	
	
	function getStaticDropdown($array,$array_select,$name,$type=NULL,$disabled=Null){
		
		if(!empty($disabled) && !is_null($disabled)){
			$disabled = $disabled."='".$disabled."'";
		}
		
		$list = array();
		if($array){
			foreach($array as $key=>$val){
			   
			   $selected = $select_id = "";
			   if($array_select){
					foreach($array_select as $id){
						$key == $id ? $select_id = $id : "";
					}
				}
			
				$selected = $key == $select_id && $select_id <> 'null' ? $selected = "selected='selected'" : "";
				$checked = $key == $select_id ? $checked = "checked='checked'" : "";	
				$list[]= array(
							'id' => $key,
							"name"=>"ref".$name,
							'title'=>ucwords($val),
							"selected"=>$selected,
							"checked"=>$checked,
							"disabled"=>$disabled
						 );
			}
		}

		$data = array(
				"list"=>$list,
				"name"=>"ref".$name,
				"disabled"=>$disabled
				);
		return $this->parser->parse("layout/ref_dropdown".$type.".html", $data, TRUE);
	}
	
	
	function getStaticLevelDropdown($array,$array_select,$array_disabled,$name,$type=NULL,$disabled=Null){
		
		if(!empty($disabled) && !is_null($disabled)){
			$disabled = "disabled='disabled'";
		}
		
		$list = array();
		if($array){
			foreach($array as $key=>$val){
			   
			   $selected = $select_id = "";
			   if($array_select){
					foreach($array_select as $id){
						$key == $id ? $select_id = $id : "";
					}
				}
				$selected = $key == $select_id ? $selected = "selected='selected'" : "";	
				$list[]= array(
							'id' => $key,
							'title'=>$val,
							"selected"=>$selected,
							"disabled"=>$array_disabled[$key]
						 );
			}
		}

		$data = array(
				"list"=>$list,
				"name"=>"ref".$name,
				"disabled"=>$disabled
				);
		return $this->parser->parse("layout/level_ref_dropdown".$type.".html", $data, TRUE);
	}
	
	
	
	function sendmail_newsletter($options=array())
	{
				
		$subject = $options['subject'];			
		
		$from_email = $this->widget->getInfoEmail("configs");
		$from_password = $this->widget->getInfoPassword("configs");
		$from_mail_name = $this->widget->getInfoMailName("configs");
		$from_mail_server = $this->widget->getInfoMailServer("configs");
		$from_mail_port = $this->widget->getInfoMailPort("configs");
		
		$config = Array(
			'protocol'=>'smtp',
			'smtp_host'=>$from_mail_server,
			'smtp_port'=>$from_mail_port,
			'smtp_user'=>$from_email,
			'smtp_pass'=>$from_password,
			'mailtype'=>'html' 
		);
		
		$this->load->helper('path');
		$this->load->library('email', $config);
		
		$this->email->clear();
		
		$this->email->from($from_email, $from_mail_name);
		$this->email->to($options['subscribers']);
		
		$this->email->subject($subject);
		$this->email->message($options['messages']);

		return $options['messages'];
		die();
		#$this->email->send();				
	}


	function sendmail($data)
	{

		$from_email = $this->widget->getInfoEmail("configs");
		$from_password = $this->widget->getInfoPassword("configs");
		$from_mail_name = $this->widget->getInfoMailName("configs");
		$from_mail_server = $this->widget->getInfoMailServer("configs");
		$from_mail_port = $this->widget->getInfoMailPort("configs");
		
		$config = Array(
			'protocol'=>'smtp',
			'smtp_host'=>$from_mail_server,
			'smtp_port'=>$from_mail_port,
			'smtp_user'=>$from_email,
			'smtp_pass'=>$from_password,
			'mailtype'=>'html' 
		);
		
		$this->load->helper('path');
		$this->load->library('email', $config);
		$this->email->from($from_email, $from_mail_name);
		$this->email->to($data['email']);
		
		$this->email->subject($data['subject']);
		$this->email->message($data['content']);	
		
		if(count($data['attachment']) > 0){
			foreach($data['attachment'] as $attachment){
				$this->email->attach($attachment['path'] . $attachment['file']);
			}
		}
		
		return true;
		return $this->email->send();
	}
	
	
	function sendmail_test()
	{				
		$subject = "Test Message";
		$msg_email = "Testing message";
		
		$from_email = $this->widget->getInfoEmail("configs");
		$from_password = $this->widget->getInfoPassword("configs");
		$from_mail_name = $this->widget->getInfoMailName("configs");
		$from_mail_server = $this->widget->getInfoMailServer("configs");
		$from_mail_port = $this->widget->getInfoMailPort("configs");
		
		$config = Array(
			'protocol'=>'smtp',
			'smtp_host'=>$from_mail_server,
			'smtp_port'=>$from_mail_port,
			'smtp_user'=>$from_email,
			'smtp_pass'=>$from_password,
			'mailtype'=>'html' 
		);
		
		$this->load->library('email', $config);
		$this->email->from($from_email, 'Testing Mail');
		
		$to = "randikha01@gmail.com";
		$this->email->to($to);
		
		$this->email->subject($subject);
		$this->email->message($msg_email);	

		// echo $msg_email;
		// die();
		$this->email->send();
		$this->email->print_debugger();
	}
	
	
	function newsletter($newsletters=array())
	{ 
		
		$target = array(
						'#URL#',
						'#CURRDATE#'
						);
		$replace = array(
						 base_url(),
						 date("Y")
						 );
		$data = array(
				"base_url"=>base_url(),
				"newsletters"=>$newsletters
				);
		$content = $this->parser->parse("widget/newsletter.html", $data,true);
		return $content = str_replace($target,$replace,$content);
	}
	
}

?>
