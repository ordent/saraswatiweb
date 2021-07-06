<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class login extends MX_Controller  {

	 
	function __construct()
	{
		parent::__construct();
		$this->load->model('login/model_login','login');
		$this->load->model('configs/model_configs','configs');
		$this->lang->load('elemen_layout', 'indonesia');
		$this->load->library('form_validation');
	}
	
	function index()
	{
		Modules::run('auth/publicAuth');
		$this->grid();
	}
	
	
	function grid($err=NULL)
	{
		Modules::run('layout/setheader_login');
		$contents = $this->grid_content($err);
	
		$data = array(
				  'base_url' => base_url(),
				  'contents' => $contents,
				  );
		$this->parser->parse('contents_login.html', $data);
		
		Modules::run('layout/setfooterlogin');
	}
	
	
	
	function grid_content($err=NULL)
	{

		$notification = !empty($err) || !is_null($err) ? "<span>".$err."</span>" : "";
		$err_display = !empty($err) || !is_null($err) ? "display:''" : "display:none;";
		
		$q = $this->configs->getDetail(1);
		$r = $q->row();
		$site_name = $r->meta_title;

		$data = array(
				  'base_url' => base_url(),
				  'site_name'=>$site_name,
				  'notification'=>$notification ,
				  'err_display'=>$err_display
				  );
		return $this->parser->parse('login.html', $data, TRUE);
	}
	
	
	function submit()
	{
		
		$post_email = strip_tags($this->input->post('email'));
		$post_password = strip_tags($this->input->post('password'));

		if(empty($post_email) && empty($post_password))
		{	
			$err = $this->lang->line('login_err_required');
			
			$response['status'] = 1;
			$response['message'] = $err;
			echo json_encode($response);
			exit;

		}else{

			$this->form_validation->set_rules('email', 'email', 'required|valid_email');

			if ($this->form_validation->run($this) == FALSE)
			{
				$err = $this->lang->line('login_err_valid_email');

				$response['status'] = 2;
				$response['message'] = $err;
				echo json_encode($response);
				exit;
			}

			$post_password = md5($post_password);
			$key = $this->config->item('encryption_key');
			$post_password = md5($key.$post_password);
			$query = $this->login->cekUserLogin($post_email,$post_password);
			$jum = $query->num_rows();
			
			if($jum > 0){
				
				$user_data = array("sess_admin"=>$query->row_array());
				$this->session->set_userdata($user_data);
				
				$response['status'] = 0;
				$response['message'] = "";
				echo json_encode($response);
				exit;

			}else{
				$err = $this->lang->line('login_err_user');
				
				$response['status'] = 3;
				$response['message'] = $err;
				echo json_encode($response);
				exit;
			}
		}
	}
	

	function logout()
	{
	  $this->login->setLoginDate($this->session->userdata('sess_admin')['id']);
	  $this->session->sess_destroy();
	  redirect('login');
	}
	
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */