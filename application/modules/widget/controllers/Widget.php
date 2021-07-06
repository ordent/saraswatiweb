<?php
class widget extends MX_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model("widget/model_widget", "widget");
	}
	
	public function index(){
	}
	
	
	public function page($jum_record,$per_page,$path,$uri_segment)
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
	
	public function setalias($uri_title)
	{
		$groups_alias = preg_replace("/[^A-Za-z0-9\s*]/","",$uri_title);
		$groups_alias = strtolower(url_title($groups_alias));
		return $groups_alias;
	}
	
	public function cek_uri($uri,$count)
	{
		if(!is_numeric($uri) || ereg("[^A-Za-z0-9]",$uri)){
			redirect(base_url());
		}
	}
	
	
	public function getUrlDate($date)
	{
		$date = str_replace("-","/",date("Y-m-d",strtotime($date)));
  		return $date;
	}
	
	
	public function getTitleMenu($path=NULL)
	{
		$this->db->where("menu_file",$path);
		$query = $this->db->get("ddi_ref_menu");
		return $query;
	}
	
	
	public function publish()
	{
		$table = $this->uri->segment(3);
		$id = $this->uri->segment(4);
		$setval = 'Publish'; 
		
		$q = $this->widget->getList($table,$id);

		if($q->num_rows() > 0)
		{
			$row = $q->row(); 
			$val = $row->publish;
		}
		
		if($val == 'Publish'){ 
			$setval = 'Not Publish'; 
		}
		
		$this->widget->setUpdate($table,$id,$setval,$this->session->userdata('adminID'),'publish');
		
		$status = 0;
		$title = "";
		$q = $this->widget->getList($table,$id);
		if($q->num_rows() > 0)
		{
			$status = 1; 
			$row = $q->row(); 
			$title = $row->publish;
		}
		
		$title = $title == "Publish" ? "<i class=\"icon-ok-sign\"></i>" : "<i class=\"icon-minus-sign\"></i>";
		
		$response['id'] = $id;
		$response['val'] = $title;
		$response['status'] = $status;
		echo $result = json_encode($response);
		
	}
	
	
	public function highlight()
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
		
		$this->widget->setUpdate($table,$id,$setval,$this->session->userdata('adminID'),'highlight');
		
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
	
	
	public function publish_parent()
	{
		$table = $this->uri->segment(3);
		$table_child = $this->uri->segment(4);
		$id = $this->uri->segment(5);
		$setval = 'Publish'; 
		
		#get parent id
		$this->db->where("id",$id);
		$qc = $this->db->get($table_child);
		$rc = $qc->row();
		$id = $rc->user_auth_id;
		
		$q = $this->widget->getList($table,$id);

		if($q->num_rows() > 0)
		{
			$row = $q->row(); 
			$val = $row->publish;
		}
		
		if($val == 'Publish'){ 
			$setval = 'Not Publish'; 
		}
		
		$this->widget->setUpdate($table,$id,$setval,$this->session->userdata('adminID'),'publish');
		
		$status = 0;
		$title = "";
		$q = $this->widget->getList($table,$id);
		if($q->num_rows() > 0)
		{
			$status = 1; 
			$row = $q->row(); 
			$title = $row->publish;
		}
		
		$response['id'] = $this->uri->segment(5);
		$response['val'] = $title;
		$response['status'] = $status;
		echo $result = json_encode($response);
		
	}
	
	
	public function getQueryStaticDropdown($table,$sort=NULL,$wheres=array())
	{
		$q = $this->widget->getDropdown($table,$sort,$wheres);
		return $q;
	}
	
	
	public function getStaticDropdown($array,$array_select,$name,$type=NULL,$disabled=Null){
		
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
							"selected"=>$selected
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
	
	
	public function getStaticLevelDropdown($array,$array_select,$array_disabled,$name,$type=NULL,$disabled=Null){
		
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
	
	
	
	public function sendmail_newsletter($options=array())
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


	public function sendmail($data)
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
		if (!$this->email->send())
		{
		    return false;
		}else{
			return true;
		}
	}
	
	
	public function sendmail_test()
	{				
		$subject = "Test Message";
		$msg_email = "Testing message testing message";
		
		$from_email = $this->widget->getInfoEmail("configs");
		$from_password = $this->widget->getInfoPassword("configs");
		$from_mail_name = $this->widget->getInfoMailName("configs");
		$from_mail_server = $this->widget->getInfoMailServer("configs");
		$from_mail_port = $this->widget->getInfoMailPort("configs");
		
		$config = array(
			'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
			'smtp_crypto' => 'ssl',
			'smtp_host'=>$from_mail_server,
			'smtp_port'=>$from_mail_port,
			'smtp_user'=>$from_email,
			'smtp_pass'=>$from_password,
			'mailtype'=>'html'
        );
	
// 	$config = array(
// 			'protocol'=>'smtp',
// 			'smtp_host'=>'smtp.gmail.com',
// 			'smtp_port'=>'465',
// 			'smtp_user'=>'randikha01@gmail.com',
// 			'smtp_pass'=>'R@nd1kh4D3w4nt0r0!'
// 		);
		
		$this->load->library('email', $config);
		$this->email->from($from_email, 'Testing Mail');
		
		$to = "randikha01@gmail.com";
		$this->email->to($to);
		
		$this->email->subject($subject);
		$this->email->message($msg_email);	

		// echo $msg_email;
		// die();
		$this->email->send();
		echo $this->email->print_debugger(array('headers'));
	}
	
	
	public function newsletter($newsletters=array())
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
