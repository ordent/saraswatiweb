<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Forgot extends MX_Controller  {

	protected $password;

	function __construct()
	{
		parent::__construct();
		$this->load->model('adminuser_auths/Model_adminuser_auths','adminuser_auths');
		$this->load->library('form_validation');
	}
	
	public function index()
	{
		Modules::run('auth/publicAuth');
		$this->grid();
	}	
	
	private function grid($err=NULL)
	{
		Modules::run('layout/setheader_modal');
	
		$data = array(
				  'base_url' => base_url(),
				  'body'=>$this->form(),
				  );
		$this->parser->parse('container.html',$data);
		
	}


	private function form($err=NULL)
	{
	
		$data = array(
				  'base_url' => base_url(),
				  );
		return $this->parser->parse('form.html',$data,TRUE);
		
	}
	
	
	
	public function submit()
	{
		
		if(!$this->input->post()){
			redirect("custom404");
		}

		$response['status'] = 0;

		#validation
		$this->form_validation->set_rules('email', 'email', 'required|email');

		if ($this->form_validation->run($this) == FALSE)
		{
			$response['status'] = 1;
			$response['message'] = validation_errors();
			echo json_encode($response);
			exit;		
		}


		#check email exists
		$email = Modules::run('adminuser_auths/do_check_email',$this->input->post('email'));
		if($email == 'true'){
			$response['status'] = 3;
			$response['message'] = "Email tidak terdaftar, coba yang lain";
			echo json_encode($response);
			exit;
		}

		#register
		$registered_user = $this->generate_new_password();

		if(!$registered_user){
			$response['status'] = 4;
			$response['message'] = "Sistem gagal memproses data ke database, silahkan coba lagi";
			echo json_encode($response);
			exit;
		}

		#send email
		
		$email_generated = $this->generate_email();
		if(!$email_generated){
			$response['status'] = 5;
			$response['message'] = "Sistem gagal memproses email, silahkan coba lag";
			echo json_encode($response);
			exit;
		}	

		#redirect to success page
		echo json_encode($response);
	}

	private function generate_new_password()
	{

		// update password 
		$key = $this->config->item('encryption_key');
		$password = GenerateRandomString(6);
		$this->password = $password;
		$password = md5($password);
		$password = md5($key.$password);

		// update data
		$data_update_pack = array(
						'password'=>$password,
						'modified_by'=>2,
						 );
		$data_where = array(
						"email"=>$this->input->post('email')
						   );

		if(!$this->adminuser_auths->setUpdate($data_update_pack, $data_where)){
			return false;
		}

		return true;
	} 

	private function generate_email()
	{	



		$q = $this->adminuser_auths->getDetailByEmail($this->input->post('email'));

		if($q->num_rows() == 0){
			return false;
		}

		$r = $q->row_array();
		$name = !empty($r['name']) ? ucwords($r['name']) : "Admin";

		#generate email
		$target = array('[url]',
						'[web_url]',
						'[adminuser_levels]',
						'[name]',
						'[email]',
						'[password]',
						'[current_date]'
						);
		$replace = array(base_url(),
						web_url(),
						$r['adminuser_levels'],
						$name,
						$r['email'],
						$this->password,
						date('Y', now())
						 );

		$data = array('base_url'=>base_url());
		$content = $this->parser->parse("email.html", [], TRUE);
		$content = str_replace($target,$replace,$content);

		$data_pack['subject'] = 'Backoffice Pelayanan LKPP - Permintaan Password Baru';
		$data_pack['email'] = strip_tags($this->input->post("email"));
		$data_pack['content'] = $content;
		$data_pack['attachment'] = array();


		#send email
		if(Modules::run('widget/sendmail', $data_pack)){
			return true;
		}else{
			return false;
		}
	}

	public function success($err=NULL)
	{
		$data = array(
				  'base_url' => base_url(),
				  );
		return $this->parser->parse('success.html',$data);
		
	}
	
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */