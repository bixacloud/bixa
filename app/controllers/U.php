<?php 

class U extends CI_Controller
{
	function __construct()
	{
	    
		parent::__construct();
		$this->load->model('user');
		$this->load->model('ticket');
		$this->load->model('account');
		$this->load->model(['gogetssl' => 'ssl']);
		$this->load->model(['acme' => 'acme']);
		$this->load->model('mofh');
		$this->load->model('oauth');
		$this->load->model('vistapanel');
		$this->load->model(['sitepro' => 'sp']);
		$this->load->library(['form_validation' => 'fv']);
		$this->load->model(['recaptcha' => 'grc']);
		$this->load->model('cloudflare');
        $this->load->model('ads');
        $this->load->helper('ads');
		if(!$this->base->is_active())
		{
			redirect('500');
		}
		
		 if($this->user->is_logged()) {
        // Skip redirect to verify if logging out
        if($this->input->get('action') !== 'logout') {
            if(!$this->user->is_active()) {
                redirect('verify');
            }
        }
    }
		if(!get_cookie('theme'))
		{
			set_cookie('theme', 'light', 30*86400);
		}
		if ($this->input->post('language')) {
    $lang = $this->input->post('language');
    set_cookie('lang', $lang, 3600*24*30); // Lưu cookie ngôn ngữ trong 30 ngày
    redirect($this->uri->uri_string()); // Tải lại trang hiện tại
}
	}

	function index()
	{
		$this->login();
	}

	function register()
	{
		if(!$this->user->is_logged())
		{
			if($this->input->post('register'))
			{
				$this->fv->set_rules('name', $this->base->text('your_name', 'label'), ['trim', 'required', 'valid_name']);
				$this->fv->set_rules('email', $this->base->text('email_address', 'label'), ['trim', 'required', 'valid_email']);
				$this->fv->set_rules('password', $this->base->text('password', 'label'), ['trim', 'required']);
				$this->fv->set_rules('password1', $this->base->text('confirm_password', 'label'), ['trim', 'required','matches[password]']);
				if($this->grc->is_active())
				{
					if($this->grc->get_type() == "google")
					{
						$this->fv->set_rules('g-recaptcha-response', $this->base->text('recaptcha', 'label'), ['trim', 'required']);
					}
					elseif($this->grc->get_type() == "crypto")
					{
						$this->fv->set_rules('CRLT-captcha-token', $this->base->text('recaptcha', 'label'), ['trim', 'required']);
					}
					elseif($this->grc->get_type() == "human")
					{
						$this->fv->set_rules('h-captcha-response', $this->base->text('recaptcha', 'label'), ['trim', 'required']);
					}
					elseif($this->grc->get_type() == "turnstile")
					{
						$this->fv->set_rules('cf-turnstile-response', $this->base->text('recaptcha', 'label'), ['trim', 'required']);
					}
					if($this->fv->run() === true)
					{
						$name = $this->input->post('name');
						$email = $this->input->post('email');
						$password = $this->input->post('password');
						if($this->grc->get_type() == "google")
						{
							$token = $this->input->post('g-recaptcha-response');
							$type = "google";
						}
						elseif($this->grc->get_type() == "crypto")
						{
							$token = $this->input->post('CRLT-captcha-token');
							$type = "crypto";
						}
						elseif($this->grc->get_type() == "turnstile")
						{
							$token = $this->input->post('cf-turnstile-response');
							$type = "turnstile";
						}
						else
						{
							$token = $this->input->post('h-captcha-response');
							$type = "human";
						}
						if($this->grc->is_valid($token, $type))
						{
							if(!$this->user->is_register($email))
							{
								$res = $this->user->register($name, $email, $password);
								if($res)
								{
									$this->session->set_flashdata('msg', json_encode([1, $this->base->text('register_msg', 'success')]));
									redirect('login');
								}
								else
								{
									$this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
									redirect('register');
								}
							}
							else
							{
								$this->session->set_flashdata('msg', json_encode([0, $this->base->text('user_exists', 'success')]));
								redirect('register');
							}
						}
						else
						{
							$this->session->set_flashdata('msg', json_encode([0, $this->base->text('captcha_error', 'error')]));
							redirect('register');
						}
					}
					else
					{
						if(validation_errors() !== '')
						{
							$this->session->set_flashdata('msg', json_encode([0, validation_errors()]));
						}
						else
						{
							$this->session->set_flashdata('msg', json_encode([0, $this->base->text('required_fields', 'error')]));
						}
						redirect('register');
					}
				}
				else
				{
					if($this->fv->run() === true)
					{
						$name = $this->input->post('name');
						$email = $this->input->post('email');
						$password = $this->input->post('password');
						if(!$this->user->is_register($email))
						{
							$res = $this->user->register($name, $email, $password);
							if($res)
							{
								$this->session->set_flashdata('msg', json_encode([1, $this->base->text('register_msg', 'success')]));
									redirect('login');
							}
							else
							{
								$this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
							}
							redirect('register');
						}
						else
						{
							$this->session->set_flashdata('msg', json_encode([0, $this->base->text('user_exists', 'success')]));
							redirect('register');
						}
					}
					else
					{
						if(validation_errors() !== '')
						{
							$this->session->set_flashdata('msg', json_encode([0, validation_errors()]));
						}
						else
						{
							$this->session->set_flashdata('msg', json_encode([0, $this->base->text('required_fields', 'error')]));
						}
						redirect('register');
					}
				}
			}
			else
			{
				$data['title'] = 'register';
				$this->load->view($this->base->get_template().'/form/includes/user/header.php', $data);
				$this->load->view($this->base->get_template().'/form/user/register.php');
				$this->load->view($this->base->get_template().'/form/includes/user/footer.php');
			}
		}
		else
		{
			redirect('user');
		}
	}

	function login()
{
   // Kiểm tra chưa đăng nhập
   if(!$this->user->is_logged())
   {
       // Xử lý form submit
       if($this->input->post('login'))
       {
           // Set validation rules
           $this->fv->set_rules('email', $this->base->text('email_address', 'label'), ['trim', 'required', 'valid_email']);
           $this->fv->set_rules('password', $this->base->text('password', 'label'), ['trim', 'required']);
           
           // Nếu có captcha
           if($this->grc->is_active())
           {
               // Kiểm tra loại captcha và thêm rule tương ứng
               if($this->grc->get_type() == "google")
               {
                   $this->fv->set_rules('g-recaptcha-response', $this->base->text('recaptcha', 'label'), ['trim', 'required']);
               }
               elseif($this->grc->get_type() == "crypto") 
               {
                   $this->fv->set_rules('CRLT-captcha-token', $this->base->text('recaptcha', 'label'), ['trim', 'required']);
               }
               elseif($this->grc->get_type() == "human")
               {
                   $this->fv->set_rules('h-captcha-response', $this->base->text('recaptcha', 'label'), ['trim', 'required']);
               }
               elseif($this->grc->get_type() == "turnstile")
               {
                   $this->fv->set_rules('cf-turnstile-response', $this->base->text('recaptcha', 'label'), ['trim', 'required']);
               }

               // Kiểm tra validation
               if($this->fv->run() === true)
               {
                   $email = $this->input->post('email');
                   $password = $this->input->post('password'); 
                   $checkbox = $this->input->post('checkbox');

                   // Lấy token captcha theo loại
                   if($this->grc->get_type() == "google")
                   {
                       $token = $this->input->post('g-recaptcha-response');
                       $type = "google";
                   }
                   elseif($this->grc->get_type() == "crypto")
                   {
                       $token = $this->input->post('CRLT-captcha-token');
                       $type = "crypto";
                   }
                   elseif($this->grc->get_type() == "turnstile")
                   {
                       $token = $this->input->post('cf-turnstile-response');
                       $type = "turnstile";
                   }
                   else
                   {
                       $token = $this->input->post('h-captcha-response');
                       $type = "human";
                   }

                   // Validate captcha
                   if($this->grc->is_valid($token, $type))
                   {
                       // Set cookie duration
                       $days = !$checkbox ? 1 : 30;

                       // Login
                       $res = $this->user->login($email, $password, $days);

                       if(!is_bool($res))
                       {
                           $this->session->unset_userdata('msg');
                           $this->session->set_flashdata('msg', json_encode([0, $this->base->text('oauth_msg', 'error')]));
                           redirect('user');
                           return;
                       }
                       elseif($res)
                       {
                           $this->session->unset_userdata('msg');
                           $this->session->set_flashdata('msg', json_encode([1, $this->base->text('login_msg', 'success')]));
                           redirect('user');
                           return;
                       }
                       else
                       {
                           $this->session->unset_userdata('msg');
                           $this->session->set_flashdata('msg', json_encode([0, $this->base->text('invalid_email_pass', 'error')]));
                           redirect('login');
                           return;
                       }
                   }
                   else
                   {
                       $this->session->unset_userdata('msg');
                       $this->session->set_flashdata('msg', json_encode([0, $this->base->text('captcha_error', 'error')]));
                       redirect('login');
                       return;
                   }
               }
               else
               {
                   // Validation errors
                   $this->session->unset_userdata('msg');
                   if(validation_errors() !== '')
                   {
                       $this->session->set_flashdata('msg', json_encode([0, validation_errors()]));
                   }
                   else
                   {
                       $this->session->set_flashdata('msg', json_encode([0, $this->base->text('required_fields', 'error')]));
                   }
                   redirect('login');
                   return;
               }
           }
           // Không có captcha
           else
           {
               if($this->fv->run() === true)
               {
                   $email = $this->input->post('email');
                   $password = $this->input->post('password');
                   $checkbox = $this->input->post('checkbox');
                   $days = !$checkbox ? 1 : 30;

                   $res = $this->user->login($email, $password, $days);
                   if($res)
                   {
                       $this->session->unset_userdata('msg');
                       $this->session->set_flashdata('msg', json_encode([1, $this->base->text('login_msg', 'success')]));
                       redirect('user');
                       return;
                   }
                   else
                   {
                       $this->session->unset_userdata('msg');
                       $this->session->set_flashdata('msg', json_encode([0, $this->base->text('invalid_email_pass', 'error')]));
                       redirect('login');
                       return;
                   }
               }
               else
               {
                   $this->session->unset_userdata('msg');
                   if(validation_errors() !== '')
                   {
                       $this->session->set_flashdata('msg', json_encode([0, validation_errors()]));
                   }
                   else
                   {
                       $this->session->set_flashdata('msg', json_encode([0, $this->base->text('required_fields', 'error')]));
                   }
                   redirect('login');
                   return;
               }
           }
       }
       // Load view if not POST
       else
       {
           $data['title'] = 'login';
           $this->session->unset_userdata('msg');
           $this->load->view($this->base->get_template().'/form/includes/user/header.php', $data);
           $this->load->view($this->base->get_template().'/form/user/login.php');
           $this->load->view($this->base->get_template().'/form/includes/user/footer.php');
       }
   }
   // Redirect if already logged in
   else
   {
       redirect('user');
   }
}

	function forget()
	{
		if(!$this->user->is_logged())
		{
			if($this->input->post('forget'))
			{
				$this->fv->set_rules('email', $this->base->text('email_address', 'label'), ['trim', 'required', 'valid_email']);
				if($this->fv->run() === true)
				{
					$email = $this->input->post('email');
					$data = $this->user->reset_password($email);
					$this->session->set_flashdata('msg', json_encode([1, $this->base->text('forget_msg', 'success')]));
					redirect('login');
				}
				else
				{
					if(validation_errors() !== '')
					{
						$this->session->set_flashdata('msg', json_encode([0, validation_errors()]));
					}
					else
					{
						$this->session->set_flashdata('msg', json_encode([0, $this->base->text('required_fields', 'error')]));
					}
					redirect('forget');
				}
			}
			else
			{
				$data['title'] = 'forget_password';
				$this->load->view($this->base->get_template().'/form/includes/user/header.php', $data);
				$this->load->view($this->base->get_template().'/form/user/forget.php');
				$this->load->view($this->base->get_template().'/form/includes/user/footer.php');
			}
		}
		else
		{
			redirect('user');
		}
	}

	function reset_password($token = '')
{
    if(!$this->user->is_logged())
    {
        // Validate token và lấy email
        $email = $this->user->validate_reset_token($token);
        
        if($email === false)
        {
            $this->session->set_flashdata('msg', json_encode([0, $this->base->text('reset_token_expired', 'error')]));
            redirect('login');
        }

        if($this->input->post('reset'))
        {
            $this->fv->set_rules('password', $this->base->text('password', 'label'), ['trim', 'required']);
            $this->fv->set_rules('password1', $this->base->text('confirm_password', 'label'), ['trim', 'required', 'matches[password]']);
            
            if($this->fv->run() === true)
            {
                $password = $this->input->post('password');
                $res = $this->user->reset_user_password($password, $email);
                
                if($res !== false)
                {
                    // Xóa session data sau khi reset thành công
                    $this->session->unset_userdata('reset_data');
                    
                    $this->session->set_flashdata('msg', json_encode([1, $this->base->text('reset_msg', 'success')]));
                    redirect('login');
                }
                else
                {
                    $this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
                    redirect('login');
                }
            }
            else
            {
                if(validation_errors() !== '')
                {
                    $this->session->set_flashdata('msg', json_encode([0, validation_errors()]));
                }
                else
                {
                    $this->session->set_flashdata('msg', json_encode([0, $this->base->text('required_fields', 'error')]));
                }
                redirect('login');
            }
        }
        else
        {
            $data['title'] = 'reset_password';
            $data['token'] = $token;
            
            $this->load->view($this->base->get_template().'/form/includes/user/header.php', $data);
            $this->load->view($this->base->get_template().'/form/user/reset_password.php');
            $this->load->view($this->base->get_template().'/form/includes/user/footer.php');
        }
    }
    else
    {
        redirect('user');
    }
}

	function logout($status = 1, $msg = '') {
    if($this->user->logout()) {
        $this->session->sess_destroy();
        if($msg !== '') {
            $this->session->set_flashdata('msg', json_encode([$status, $msg]));
        } else {
            $this->session->set_flashdata('msg', json_encode([1, $this->base->text('logout_msg', 'success')]));
        }
        redirect('login');
    } else {
        $this->session->set_flashdata('msg', json_encode([0, $this->base->text('login_to_continue', 'error')]));
        redirect('login'); 
    }
}

	function settings()
	{
		if($this->user->is_logged())
		{
			if($this->input->post('update_theme'))
			{
				set_cookie('theme', $this->input->post('theme'), 30 * 86400);
				set_cookie('lang', $this->input->post('language'), 30 * 86400);
				$this->session->set_flashdata('msg', json_encode([1, $this->base->text('theme_msg', 'success')]));
				redirect('settings');
			}
			elseif($this->input->get('enable_oauth'))
			{
				if ($this->user->enable_oauth()) {
				  redirect('https://github.com/login/oauth/authorize?client_id='.$this->oauth->get_client('github').'&scope=user,email');
				}
				$this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
				redirect('settings');
			}
			elseif($this->input->post('update_name'))
			{
				$this->fv->set_rules('name', $this->base->text('your_name', 'label'), ['trim', 'required', 'valid_name']);
				if($this->fv->run() === true)
				{
					$name = $this->input->post('name');
					$res = $this->user->set_name($name);
					if($res !== false)
					{
						$this->session->set_flashdata('msg', json_encode([1, $this->base->text('name_msg', 'success')]));
						redirect('settings');
					}
					else
					{
						$this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
						redirect('settings');
					}
				}
				else
				{
					$this->session->set_flashdata('msg', json_encode([0, validation_errors()]));
					redirect('settings');
				}
			}
			elseif($this->input->post('update_password'))
			{
				$this->fv->set_rules('password', $this->base->text('new_password', 'label'), ['trim', 'required']);
				$this->fv->set_rules('password1', $this->base->text('confirm_password', 'label'), ['trim', 'required', 'matches[password]']);
				$this->fv->set_rules('old_password', $this->base->text('old_password', 'label'), ['trim', 'required']);
				if($this->fv->run() === true)
				{
					$password = $this->input->post('password');
					$old_password = $this->input->post('old_password');
					$res = $this->user->set_password($old_password, $password);
					if($res !== false)
					{
						$this->logout(1, $this->base->text('user_pass_msg', 'success'));
					}
					else
					{
						$this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
						redirect('settings');
					}
				}
				else
				{
					$this->session->set_flashdata('msg', json_encode([0, validation_errors()]));
					redirect('settings');
				}
			}
			else
			{
				$data['title'] = 'settings';

				$this->load->view($this->base->get_template().'/page/includes/user/header', $data);
				$this->load->view($this->base->get_template().'/page/includes/user/navbar');
				$this->load->view($this->base->get_template().'/page/user/settings');
				$this->load->view($this->base->get_template().'/page/includes/user/footer');
			}

		}
		else
		{
			redirect('login');
		}
	}

	function dashboard()
{
    if($this->user->is_logged())
    {
        $data['title'] = 'dashboard';
        $data['active'] = 'home';
        
        // Get counts for dashboard cards
        $data['account_count'] = count($this->account->get_user_accounts());
        $data['ssl_count'] = count($this->acme->get_ssl_list());
        $data['ticket_count'] = count($this->ticket->get_user_tickets()); 
        
        // Get latest 5 accounts for account list
        $data['accounts'] = array_slice($this->account->get_user_accounts(), 0, 5);

        // Get announcements - you may want to create an announcements model/table
        $data['announcements'] = [
            [
                'message' => 'Now you can create web hosting accounts with in a few steps.'
            ],
            [
                'message' => 'Now you can create Self Signed SSL and GoGetSSL in a few clicks.'
            ],
            [
                'message' => 'Now it is easy to contact with our support staff through Support Center.'
            ]
        ];

        $this->load->view($this->base->get_template().'/page/includes/user/header', $data);
        $this->load->view($this->base->get_template().'/page/includes/user/navbar'); 
        $this->load->view($this->base->get_template().'/page/user/dashboard');
        $this->load->view($this->base->get_template().'/page/includes/user/footer');
    }
    else
    {
        redirect('login');
    }
}

	function tickets()
	{
		if($this->user->is_logged())
		{
			$data['title'] = 'tickets';
			$data['active'] = 'ticket';
			$data['list'] = $this->ticket->get_user_tickets();

			$this->load->view($this->base->get_template().'/page/includes/user/header', $data);
			$this->load->view($this->base->get_template().'/page/includes/user/navbar');
			$this->load->view($this->base->get_template().'/page/user/tickets');
			$this->load->view($this->base->get_template().'/page/includes/user/footer');
		}
		else
		{
			redirect('login');
		}
	}

	function create_ticket()
	{
		if($this->user->is_logged())
		{
			if($this->input->post('create'))
			{
				$this->fv->set_rules('subject', $this->base->text('subject', 'label'), ['trim', 'required']);
				$this->fv->set_rules('content', $this->base->text('content', 'label'), ['trim', 'required']);
				if($this->grc->is_active())
				{
					if($this->grc->get_type() == "google")
					{
						$this->fv->set_rules('g-recaptcha-response', $this->base->text('recaptcha', 'label'), ['trim', 'required']);
					}
					elseif($this->grc->get_type() == "crypto")
					{
						$this->fv->set_rules('CRLT-captcha-token', $this->base->text('recaptcha', 'label'), ['trim', 'required']);
					}
					elseif($this->grc->get_type() == "human")
					{
						$this->fv->set_rules('h-captcha-response', $this->base->text('recaptcha', 'label'), ['trim', 'required']);
					}
					elseif($this->grc->get_type() == "turnstile")
					{
						$this->fv->set_rules('cf-turnstile-response', $this->base->text('recaptcha', 'label'), ['trim', 'required']);
					}
					if($this->fv->run() === true)
					{
						$subject = $this->input->post('subject');
						$content = $this->input->post('content');
						if($this->grc->get_type() == "google")
						{
							$token = $this->input->post('g-recaptcha-response');
							$type = "google";
						}
						elseif($this->grc->get_type() == "crypto")
						{
							$token = $this->input->post('CRLT-captcha-token');
							$type = "crypto";
						}
						elseif($this->grc->get_type() == "turnstile")
						{
							$token = $this->input->post('cf-turnstile-response');
							$type = "turnstile";
						}
						else
						{
							$token = $this->input->post('h-captcha-response');
							$type = "human";
						}
						if($this->grc->is_valid($token, $type))
						{
							$res = $this->ticket->create_ticket($subject, $content);
							if($res)
							{
								$this->session->set_flashdata('msg', json_encode([1, $this->base->text('ticket_msg', 'success')]));
								redirect('ticket/list');
							}
							else
							{
								$this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
								redirect('ticket/create');
							}
						}
						else
						{
							$this->session->set_flashdata('msg', json_encode([0, $this->base->text('captcha_error', 'error')]));
							redirect('ticket/create');
						}
					}
					else
					{
						if(validation_errors() !== '')
						{
							$this->session->set_flashdata('msg', json_encode([0, validation_errors()]));
						}
						else
						{
							$this->session->set_flashdata('msg', json_encode([0, $this->base->text('required_fields', 'error')]));
						}
						redirect('ticket/create');
					}
				}
				else
				{
					if($this->fv->run() === true)
					{
						$subject = $this->input->post('subject');
						$content = $this->input->post('content');
						$res = $this->ticket->create_ticket($subject, $content);
						if($res)
						{
							$this->session->set_flashdata('msg', json_encode([1, $this->base->text('ticket_msg', 'success')]));
							redirect('ticket/list');
						}
						else
						{
							$this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
							redirect('ticket/create');
						}
					}
					else
					{
						if(validation_errors() !== '')
						{
								$this->session->set_flashdata('msg', json_encode([0, validation_errors()]));
						}
						else
						{
							$this->session->set_flashdata('msg', json_encode([0, $this->base->text('required_fields', 'error')]));
						}
						redirect('ticket/create');
					}
				}
			}
			else
			{
				$data['title'] = 'create_ticket';
				$data['active'] = 'ticket';

				$this->load->view($this->base->get_template().'/page/includes/user/header', $data);
				$this->load->view($this->base->get_template().'/page/includes/user/navbar');
				$this->load->view($this->base->get_template().'/page/user/create_ticket');
				$this->load->view($this->base->get_template().'/page/includes/user/footer');
			}
		}
		else
		{
			redirect('login');
		}
	}

	function view_ticket($id)
	{
		if($this->user->is_logged()){
			$id = $this->security->xss_clean($id);
			if($this->input->get('close'))
			{
				if($this->ticket->view_user_ticket($id))
				{
					$res = $this->ticket->change_ticket_status($id, 'closed');
					if($res)
					{
						$this->session->set_flashdata('msg', json_encode([1, $this->base->text('ticket_closed_msg', 'success')]));
						redirect("ticket/view/$id");
					}
					else
					{
						$this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
						redirect("ticket/view/$id");
					}
				}
				else
				{
					redirect('ticket/list');
				}
			}
			elseif($this->input->get('open'))
			{
				if($this->ticket->view_user_ticket($id))
				{
					$res = $this->ticket->change_ticket_status($id, 'open');
					if($res)
					{
						$this->session->set_flashdata('msg', json_encode([1, $this->base->text('ticket_opened_msg', 'success')]));
						redirect("ticket/view/$id");
					}
					else
					{
						$this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
						redirect("ticket/view/$id");
					}
				}
				else
				{
					redirect('ticket/list');
				}
			}
			elseif($this->input->post('reply'))
			{
				if($this->ticket->view_user_ticket($id))
				{
					$this->fv->set_rules('content', $this->base->text('content', 'label'), ['trim', 'required']);
					if($this->grc->is_active())
					{
						if($this->grc->get_type() == "google")
					{
						$this->fv->set_rules('g-recaptcha-response', $this->base->text('recaptcha', 'label'), ['trim', 'required']);
					}
					elseif($this->grc->get_type() == "crypto")
					{
						$this->fv->set_rules('CRLT-captcha-token', $this->base->text('recaptcha', 'label'), ['trim', 'required']);
					}
					elseif($this->grc->get_type() == "human")
					{
						$this->fv->set_rules('h-captcha-response', $this->base->text('recaptcha', 'label'), ['trim', 'required']);
					}
					elseif($this->grc->get_type() == "turnstile")
					{
						$this->fv->set_rules('cf-turnstile-response', $this->base->text('recaptcha', 'label'), ['trim', 'required']);
					}
						if($this->fv->run() === true)
						{
							$content = $this->input->post('content');
							if($this->grc->get_type() == "google")
							{
								$token = $this->input->post('g-recaptcha-response');
								$type = "google";
							}
							elseif($this->grc->get_type() == "crypto")
							{
								$token = $this->input->post('CRLT-captcha-token');
								$type = "crypto";
							}
							elseif($this->grc->get_type() == "turnstile")
							{
								$token = $this->input->post('cf-turnstile-response');
								$type = "turnstile";
							}
							else
							{
								$token = $this->input->post('h-captcha-response');
								$type = "human";
							}
							if($this->grc->is_valid($token, $type))
							{
								$res = $this->ticket->add_reply($id, $content, $this->user->get_key(), 'customer');
								if($res)
								{
									$this->session->set_flashdata('msg', json_encode([1, $this->base->text('reply_msg', 'success')]));
									redirect("ticket/view/$id");
								}
								else
								{
									$this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
									redirect("ticket/view/$id");
								}
							}
							else
							{
								$this->session->set_flashdata('msg', json_encode([0, $this->base->text('captcha_error', 'error')]));
								redirect("ticket/view/$id");
							}
						}
						else
						{
							if(validation_errors() !== '')
							{
								$this->session->set_flashdata('msg', json_encode([0, validation_errors()]));
							}
							else
							{
								$this->session->set_flashdata('msg', json_encode([0, $this->base->text('required_fields', 'error')]));
							}
							redirect("ticket/view/$id");
						}
					}
					else
					{
						if($this->fv->run() === true)
						{
							$content = $this->input->post('content');
							$res = $this->ticket->add_reply($id, $content, $this->user->get_key(), 'customer');
							if($res)
							{
								$this->session->set_flashdata('msg', json_encode([1, $this->base->text('reply_msg', 'success')]));
								redirect("ticket/view/$id");
							}
							else
							{
								$this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
								redirect("ticket/view/$id");
							}
						}
						else
						{
							if(validation_errors() !== '')
							{
									$this->session->set_flashdata('msg', json_encode([0, validation_errors()]));
							}
							else
							{
								$this->session->set_flashdata('msg', json_encode([0, $this->base->text('required_fields', 'error')]));
							}
							redirect("ticket/view/$id");
						}
					}
				}
				else
				{
					redirect("ticket/list");
				}
			}
			else
			{
				$data['title'] = 'view_ticket';
                $data['id'] = $id;
				$data['active'] = 'ticket';
				$data['ticket'] = $this->ticket->view_user_ticket($id);
				if($data['ticket'] !== false)
				{
                    $count = $this->input->get('page') ?? 0;
					$data['replies'] = $this->ticket->get_ticket_reply($id, $count);

					$this->load->view($this->base->get_template().'/page/includes/user/header', $data);
					$this->load->view($this->base->get_template().'/page/includes/user/navbar');
					$this->load->view($this->base->get_template().'/page/user/view_ticket');
					$this->load->view($this->base->get_template().'/page/includes/user/footer');
				}
				else
				{
					redirect('404');
				}
			}
		}
		else
		{
			redirect('login');
		}
	}

	function accounts()
	{
		if($this->user->is_logged())
		{
			$data['title'] = 'accounts';
			$data['active'] = 'account';
			$data['list'] = $this->account->get_user_accounts();
			
			$this->load->view($this->base->get_template().'/page/includes/user/header', $data);
			$this->load->view($this->base->get_template().'/page/includes/user/navbar');
			$this->load->view($this->base->get_template().'/page/user/accounts');
			$this->load->view($this->base->get_template().'/page/includes/user/footer');
		}
		else
		{
			redirect('login');
		}
	}

function create_account()
{
   if(!$this->user->is_logged())
   {
       redirect('login');
       return;
   }

   $count = $this->account->get_active_accounts($this->user->get_key());
   if($count >= 3)
   {
       $this->session->set_flashdata('msg', json_encode([0, $this->base->text('account_limit', 'error')]));
       redirect('account/list');
       return;
   }

   // Clear domain nếu cancel
   if($this->input->get('cancel')) {
       $this->session->unset_userdata('domain');
       redirect('account/create');
       return;
   }

   // Handle check domain
   if($this->input->post('check_subdomain'))
   {
       $this->fv->set_rules('domain', $this->base->text('domain_name', 'label'), ['trim', 'required']);
       $this->fv->set_rules('ext', 'Extension', ['trim', 'required']);

       if($this->fv->run() !== false)
       {
           $domain = $this->input->post('domain');
           $ext = $this->input->post('ext');
           $subdomain = $domain.$ext;

           $res = $this->mofh->check_availablity($subdomain);

           if($res === true)
           {
               $this->session->set_userdata('domain', strtolower($subdomain));
               $this->session->set_flashdata('msg', json_encode([1, $this->base->text('domain_selected_msg', 'success')]));
           }
           elseif($res === false)
           {
               $this->session->set_flashdata('msg', json_encode([0, $this->base->text('domain_not_available', 'error')]));
           }
           else
           {
               $this->session->set_flashdata('msg', json_encode([0, $res]));
           }
           redirect('account/create');
           return;
       }
       else
       {
           $this->session->set_flashdata('msg', json_encode([0, $this->base->text('fill_domain_field', 'error')]));
           redirect('account/create');
           return;
       }
   }

   // Handle create account 
   if($this->input->post('create'))
   {
       if(!$this->session->userdata('domain')) {
           $this->session->set_flashdata('msg', json_encode([0, 'Please check domain availability first']));
           redirect('account/create');
           return;
       }

       $this->fv->set_rules('label', $this->base->text('label', 'label'), ['trim', 'required']);

       if($this->grc->is_active())
       {
           if($this->grc->get_type() == "google")
           {
               $this->fv->set_rules('g-recaptcha-response', $this->base->text('recaptcha', 'label'), ['trim', 'required']);
           }
           elseif($this->grc->get_type() == "crypto")
           {
               $this->fv->set_rules('CRLT-captcha-token', $this->base->text('recaptcha', 'label'), ['trim', 'required']);
           }
           elseif($this->grc->get_type() == "human")
           {
               $this->fv->set_rules('h-captcha-response', $this->base->text('recaptcha', 'label'), ['trim', 'required']);
           }
           elseif($this->grc->get_type() == "turnstile")
           {
               $this->fv->set_rules('cf-turnstile-response', $this->base->text('recaptcha', 'label'), ['trim', 'required']);
           }
       }

       if($this->fv->run() === true)
       {
           // Validate captcha
           if($this->grc->is_active())
           {
               $token = null;
               $type = null;

               if($this->grc->get_type() == "google")
               {
                   $token = $this->input->post('g-recaptcha-response');
                   $type = "google";
               }
               elseif($this->grc->get_type() == "crypto")
               {
                   $token = $this->input->post('CRLT-captcha-token');
                   $type = "crypto";
               }
               elseif($this->grc->get_type() == "turnstile")
               {
                   $token = $this->input->post('cf-turnstile-response');
                   $type = "turnstile";
               }
               else
               {
                   $token = $this->input->post('h-captcha-response');
                   $type = "human";
               }

               if(!$this->grc->is_valid($token, $type))
               {
                   $this->session->set_flashdata('msg', json_encode([0, $this->base->text('captcha_error', 'error')]));
                   redirect('account/create');
                   return;
               }
           }

           $label = $this->input->post('label');
           $password = $this->input->post('password');
           $domain = $this->session->userdata('domain');

           $res = $this->mofh->create_account($label, $domain, $password);

           if($res === true)
           {
               $this->session->unset_userdata('domain');
               $this->session->set_flashdata('msg', json_encode([1, $this->base->text('account_msg', 'success')]));
               redirect('account/list');
               return;
           }
           elseif(is_string($res))
           {
               $this->session->set_flashdata('msg', json_encode([0, $res]));
           }
           else
           {
               $this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
           }
           redirect('account/create');
           return;
       }
       else
       {
           $this->session->set_flashdata('msg', json_encode([0, validation_errors() ?: $this->base->text('required_fields', 'error')]));
           redirect('account/create');
           return;
       }
   }

   // Load view
   $data['title'] = 'create_account';
   $data['active'] = 'account';

   $this->load->view($this->base->get_template().'/page/includes/user/header', $data);
   $this->load->view($this->base->get_template().'/page/includes/user/navbar');
   $this->load->view($this->base->get_template().'/page/user/create_account');
   $this->load->view($this->base->get_template().'/page/includes/user/footer');
}



function get_account_stats($id = null)
{
    ob_start();
    ob_clean();
    
    try {
        // Validate input 
        if(!$id) {
            throw new Exception('Account ID is required');
        }

        if(!$this->user->is_logged()) {
            throw new Exception('Not logged in');
        }

        // Get account info
        $account = $this->account->get_user_account($id);
        if(!$account) {
            throw new Exception('Account not found');
        }

        if($account['account_status'] !== 'active') {
            throw new Exception('Account is not active');
        }

        // Lấy thống kê thật từ API vistapanel
        require_once APPPATH . 'vendor/vistapanel/api-client.php';
        $api = new VistapanelApi();
        $api->setCpanelUrl("https://cpanel.byethost.com");

        if($api->login($account['account_username'], $account['account_password'])) {
            $stats = $api->getUserStats();
            
            // Format lại dữ liệu cho phù hợp
            $formattedStats = [
                'disk' => [
                    'used' => (float)str_replace(['MB', 'GB'], '', $stats['Disk Space Used:']),
                    'total' => (float)str_replace(['MB', 'GB'], '', $stats['Disk Quota:']),
                    'unit' => 'MB',
                    'percent' => 0 
                ],
                'bandwidth' => [
                    'used' => (float)str_replace(['MB', 'GB'], '', $stats['Bandwidth used:']),
                    'total' => 'Unlimited',
                    'unit' => 'MB',
                    'percent' => 0
                ],
                'inodes' => [
                    'used' => (int)$stats['Inodes Used:'],
                    'total' => 59400, // Maximum inodes allowed
                    'percent' => 0
                ]
            ];

            // Tính phần trăm
            if($formattedStats['disk']['total'] > 0) {
                $formattedStats['disk']['percent'] = 
                    ($formattedStats['disk']['used'] / $formattedStats['disk']['total']) * 100;
            }

            if($formattedStats['inodes']['total'] > 0) {
                $formattedStats['inodes']['percent'] = 
                    ($formattedStats['inodes']['used'] / $formattedStats['inodes']['total']) * 100;
            }

            $api->logout();

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $formattedStats
            ]);
            exit;
        } else {
            throw new Exception('Failed to connect to server');
        }

    } catch(Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
        exit;
    }
}

function view_account($id, $action = null, $path = null)
{
    if($this->user->is_logged())
    {
        $id = $this->security->xss_clean($id);
        
        // Handle cpanel login
        if($action === 'cpanel') {
            $res = $this->account->get_user_account($id);
            if($res !== false && $res['account_status'] === 'active')
            {
                $data['username'] = $res['account_username'];
                $data['password'] = $res['account_password'];
                $this->load->view($this->base->get_template().'/page/user/cpanel_login', $data);
            }
            else
            {
                $this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
                redirect("account/view/$id");
            }
            return;
        }

        if($action === 'softaculous') {
    $res = $this->account->get_user_account($id);
    if($res !== false && $res['account_status'] === 'active')
    {
        $softaculous_url = $this->mofh->get_softaculous_link(
            $res['account_username'],
            $res['account_password']
        );

        if($softaculous_url) {
            redirect($softaculous_url);
        } else {
            // Fallback to form login if direct link fails
            $data['username'] = $res['account_username'];
            $data['password'] = $res['account_password'];
            $this->load->view($this->base->get_template().'/page/user/softaculous_login', $data);
        }
    }
    else
    {
        $this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
        redirect("account/view/$id");
    }
    return;
}

        // Handle file manager login
        if($action === 'filemanager') {
            $res = $this->account->get_user_account($id);
            if($res !== false && $res['account_status'] === 'active')
            {
                $data['username'] = $res['account_username'];
                $data['password'] = $res['account_password'];
                $data['dir'] = $path ? '/' . $path . '/htdocs/' : '/htdocs/';
                $this->load->view($this->base->get_template().'/page/user/filemanager_login', $data);
            }
            else
            {
                $this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
                redirect("account/view/$id");
            }
            return;
        }

        // Handle reactivation request
        if($this->input->get('reactivate'))
        {
            $count = $this->account->get_active_accounts($this->user->get_key());
            if($count > 2)
            {
                $this->session->set_flashdata('msg', json_encode([0, $this->base->text('cant_reactivate', 'error')]));
                redirect("account/view/$id");
            }
            else
            {
                $res = $this->account->get_user_account($id);
                if($res !== false)
                {
                    if($res['account_status'] === 'suspended' OR $res['account_status'] === 'deactivated')
                    {
                        $res = $this->mofh->reactivate_account($res['account_key']);
                        if(!is_bool($res))
                        {
                            $this->session->set_flashdata('msg', json_encode([0, $res]));
                            redirect("account/view/$id");
                        }
                        elseif($res !== false)
                        {
                            $this->session->set_flashdata('msg', json_encode([1, $this->base->text('account_reactivated_msg', 'success')]));
                            redirect("account/view/$id");
                        }
                        else
                        {
                            $this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
                            redirect("account/view/$id");
                        }
                    }
                    else
                    {
                        $this->session->set_flashdata('msg', json_encode([0, $this->base->text('reactivation_error', 'error')]));
                        redirect("account/view/$id");
                    }
                }
                else
                {
                    $this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
                    redirect("account/view/$id");
                }
            }
        }
        
        // Normal account view
        $data['title'] = 'view_account';
        $data['active'] = 'account';
        $data['id'] = $id;
        $data['data'] = $this->account->get_user_account($id);
        if($data['data'] !== false && $data['data']['account_status'] === 'active') {
            try {
                $domain = $data['data']['account_domain'];
                $ip = gethostbyname($domain);
                $data['server_ip'] = ($ip !== $domain) ? $ip : false;
            } catch(Exception $e) {
                $data['server_ip'] = false;
            }
        }
        if($data['data'] !== false)
        {
            $this->load->view($this->base->get_template().'/page/includes/user/header', $data);
            $this->load->view($this->base->get_template().'/page/includes/user/navbar');
            $this->load->view($this->base->get_template().'/page/user/view_account');
            $this->load->view($this->base->get_template().'/page/includes/user/footer');
        }
        else
        {
            redirect('404');
        }
    }
    else
    {
        redirect('login');
    }
}

	function account_settings($id)
	{
		if($this->user->is_logged())
		{
			$id = $this->security->xss_clean($id);
			if($this->input->post('update_label'))
			{
				$res = $this->account->get_user_account($id);
				if($res !== false)
				{
					$res = $this->account->set_label($id, $this->input->post('label'));
					if($res !== false)
					{
						$this->session->set_flashdata('msg', json_encode([1, $this->base->text('label_updated_msg', 'success')]));
						redirect("account/settings/$id");
					}
						else
					{
						$this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
						redirect("account/settings/$id");
					}
				}
				else
				{
					$this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
					redirect("account/settings/$id");
				}
			}
			elseif($this->input->post('update_password'))
			{
				$res = $this->account->get_user_account($id);
				if($res !== false)
				{
					if(strlen($this->input->post('password')) > 4 AND strlen($this->input->post('old_password')) > 4)
					{
						$res = $this->account->change_account_password($id, $this->input->post('password'), $this->input->post('old_password'));
						if(!is_bool($res))
						{
							$this->session->set_flashdata('msg', json_encode([0, $res]));
							redirect("account/settings/$id");
						}
						elseif($res !== false)
						{
							$this->session->set_flashdata('msg', json_encode([1, $this->base->text('account_password_msg', 'success')]));
							redirect("account/view/$id");
						}
						else
						{
							$this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
							redirect("account/settings/$id");
						}
					}
					else
					{
						$this->session->set_flashdata('msg', json_encode([0, 'Unable to delete account.']));
							redirect("account/settings/$id");
					}
				}
				else
				{
					$this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
					redirect("account/settings/$id");
				}
			} 
			elseif($this->input->post('deactivate'))
			{
				$res = $this->account->get_user_account($id);
				if($res !== false)
				{
					if($res['account_status'] === 'active')
					{
						$res = $this->mofh->deactivate_account($res['account_key'], $this->input->post('reason'));
						if(!is_bool($res)){
							$this->session->set_flashdata('msg', json_encode([0, $res]));
							redirect("account/settings/$id");
						}
						elseif($res !== false)
						{
							$this->session->set_flashdata('msg', json_encode([1, $this->base->text('account_deactivated_msg', 'success')]));
							redirect("account/list");
						}
						else
						{
							$this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
							redirect("account/settings/$id");
						}
					}
					else
					{
						$this->session->set_flashdata('msg', json_encode([0, $this->base->text('deactivation_error', 'error')]));
							redirect("account/settings/$id");
					}
				}
				else
				{
					$this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
					redirect("account/settings/$id");
				}
			}
			else
			{
				$data['title'] = 'account_settings';
				$data['active'] = 'account';
				$data['id'] = $id;
				$data['data'] = $this->account->get_user_account($id);
				if($data['data'] !== false)
				{
					$this->load->view($this->base->get_template().'/page/includes/user/header', $data);
					$this->load->view($this->base->get_template().'/page/includes/user/navbar');
					$this->load->view($this->base->get_template().'/page/user/account_settings');
					$this->load->view($this->base->get_template().'/page/includes/user/footer');
				}
				else
				{
					redirect('404');
				}
			}
		}
		else
		{
			redirect('login');
		}
	}

	function domain_checker($domain = false)
	{
		$domain = $this->security->xss_clean($domain);
		$domain = strtolower($domain);
		if($this->user->is_logged())
		{
			$data['title'] = 'domain_checker';
			$data['active'] = 'domain';
			if($domain !== false)
			{
				$data['data'] = $this->mofh->get_domain_user($domain);
			}
			else
			{
				$data['data'] = false;
			}
			$data['domain'] = $domain;

			$this->load->view($this->base->get_template().'/page/includes/user/header', $data);
			$this->load->view($this->base->get_template().'/page/includes/user/navbar');
			$this->load->view($this->base->get_template().'/page/user/domain_checker');
			$this->load->view($this->base->get_template().'/page/includes/user/footer');
		}
		else
		{
			redirect('login');
		}
	}

	function ssl()
	{
		if($this->user->is_logged())
		{
			if($this->ssl->is_active() || $this->acme->is_active())
			{
				$data['title'] = 'ssl';
				$data['active'] = 'ssl';
				$data['list'] = $this->acme->get_ssl_list();
				
				$this->load->view($this->base->get_template().'/page/includes/user/header', $data);
				$this->load->view($this->base->get_template().'/page/includes/user/navbar');
				$this->load->view($this->base->get_template().'/page/user/ssl');
				$this->load->view($this->base->get_template().'/page/includes/user/footer');
			}
			else
			{
				redirect('user');
			}
		}
		else
		{
			redirect('login');
		}
	}

	function create_ssl()
	{
		if($this->user->is_logged())
		{
			if($this->input->post('create'))
			{
				//$this->fv->set_rules('type', $this->base->text('ssl_type', 'label'), ['trim', 'required']);
				$this->fv->set_rules('type', 'SSL Type', ['trim', 'required']);
				$this->fv->set_rules('domain', $this->base->text('domain_name', 'label'), ['trim', 'required']);
				if($this->grc->is_active())
				{
					if($this->grc->get_type() == "google")
					{
						$this->fv->set_rules('g-recaptcha-response', $this->base->text('recaptcha', 'label'), ['trim', 'required']);
					}
					elseif($this->grc->get_type() == "crypto")
					{
						$this->fv->set_rules('CRLT-captcha-token', $this->base->text('recaptcha', 'label'), ['trim', 'required']);
					}
					elseif($this->grc->get_type() == "human")
					{
						$this->fv->set_rules('h-captcha-response', $this->base->text('recaptcha', 'label'), ['trim', 'required']);
					}
					elseif($this->grc->get_type() == "turnstile")
					{
						$this->fv->set_rules('cf-turnstile-response', $this->base->text('recaptcha', 'label'), ['trim', 'required']);
					}
					if($this->fv->run() === true)
					{
						$domain = $this->input->post('domain');
						if($this->grc->get_type() == "google")
						{
							$token = $this->input->post('g-recaptcha-response');
							$type = "google";
						}
						elseif($this->grc->get_type() == "crypto")
						{
							$token = $this->input->post('CRLT-captcha-token');
							$type = "crypto";
						}
						elseif($this->grc->get_type() == "turnstile")
						{
							$token = $this->input->post('cf-turnstile-response');
							$type = "turnstile";
						}
						else
						{
							$token = $this->input->post('h-captcha-response');
							$type = "human";
						}
						if($this->grc->is_valid($token, $type))
						{
							$type = $this->input->post('type');
							if ($type == 'gogetssl') {
								$res = $this->ssl->create_ssl($domain);
							} else {
								$res = $this->acme->initilize($type);
								if (!is_bool($res))
								{
									$this->session->set_flashdata('msg', json_encode([0, $res]));
									redirect('ssl/list');
								} elseif(is_bool($res) AND $res == false)
								{
									$this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
									redirect('u/create_ssl');
								}

								$res = $this->acme->create_ssl($domain, $type);
							}
							if(!is_bool($res))
							{
								$this->session->set_flashdata('msg', json_encode([0, $res]));
								redirect('ssl/list');
							}
							elseif(is_bool($res) AND $res == true)
							{
								$this->session->set_flashdata('msg', json_encode([1, $this->base->text('ssl_created_msg', 'success')]));
								redirect('ssl/list');
							}
							else
							{
								$this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
								redirect('u/create_ssl');
							}
						}
						else
						{
							$this->session->set_flashdata('msg', json_encode([0, $this->base->text('captcha_error', 'error')]));
							redirect('u/create_ssl');
						}
					}
					else
					{
						if(validation_errors() !== '')
						{
							$this->session->set_flashdata('msg', json_encode([0, validation_errors()]));
						}
						else
						{
							$this->session->set_flashdata('msg', json_encode([0, $this->base->text('required_fields', 'error')]));
						}
						redirect('u/create_ssl');
					}
				}
				else
				{
					if($this->fv->run() === true)
					{
						$domain = $this->input->post('domain');
						$type = $this->input->post('type');
						if ($type == 'gogetssl') {
							$res = $this->ssl->create_ssl($domain);
						} else {
							$res = $this->acme->initilize($type);
							if (!is_bool($res))
							{
								$this->session->set_flashdata('msg', json_encode([0, $res]));
								redirect('ssl/list');
							} elseif(is_bool($res) AND $res == false)
							{
								$this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
								redirect('u/create_ssl');
							}

							$res = $this->acme->create_ssl($domain, $type);
						}
						if(!is_bool($res))
						{
							$this->session->set_flashdata('msg', json_encode([0, $res]));
							redirect('ssl/list');
						}
						if(is_bool($res) AND $res == true)
						{
							$this->session->set_flashdata('msg', json_encode([1, $this->base->text('ssl_created_msg', 'success')]));
							redirect('ssl/list');
						}
						else
						{
							$this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
							redirect('u/create_ssl');
						}
					}
					else
					{
						if(validation_errors() !== '')
						{
								$this->session->set_flashdata('msg', json_encode([0, validation_errors()]));
						}
						else
						{
							$this->session->set_flashdata('msg', json_encode([0, $this->base->text('required_fields', 'error')]));
						}
						redirect('u/create_ssl');
					}
				}
			}
			else
			{
				if($this->ssl->is_active() || $this->acme->is_active())
				{
					$data['title'] = 'create_ssl';
					$data['active'] = 'ssl';
					$data['acme_active'] = $this->acme->is_active();

					$this->load->view($this->base->get_template().'/page/includes/user/header', $data);
					$this->load->view($this->base->get_template().'/page/includes/user/navbar');
					$this->load->view($this->base->get_template().'/page/user/create_ssl');
					$this->load->view($this->base->get_template().'/page/includes/user/footer');
				}
				else
				{
					redirect('user');
				}
			}
		}
		else
		{
			redirect('login');
		}
	}

	function view_ssl($id)
	{
		if($this->user->is_logged())
		{
			$id = $this->security->xss_clean($id);
			if($this->input->get('delete'))
			{
				$this->db->where(['ssl_key' => $id]);
				$res = $this->db->delete('is_ssl');
				if($res !== false)
				{
					$this->session->set_flashdata('msg', json_encode([1, $this->base->text('ssl_deleted_msg', 'success')]));
					redirect("ssl/list");
				}
				else
				{
					$this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
					redirect("ssl/view/$id");
				}
			}
			elseif($this->input->get('cancel'))
			{
				$ssl_type = $this->ssl->get_ssl_type($id);
				if ($ssl_type == 'gogetssl') {
					$res = $this->ssl->cancel_ssl($id, 'Some Reason');
				} else {
					$res = $this->acme->initilize($ssl_type);
					if(!is_bool($res))
					{
						$this->session->set_flashdata('msg', json_encode([0, $res]));
						redirect("ssl/view/$id");
					}
					elseif(is_bool($res) AND $res == true)
					{
						$this->session->set_flashdata('msg', json_encode([1, $this->base->text('ssl_cancelled_msg', 'success')]));
						redirect("ssl/view/$id");
					}
					else
					{
						$this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
						redirect("ssl/view/$id");
					}
					$res = $this->acme->cancel_ssl($id, 'Some Reason');
				}
				if(!is_bool($res))
				{
					$this->session->set_flashdata('msg', json_encode([0, $res]));
					redirect("ssl/view/$id");
				}
				elseif(is_bool($res) AND $res == true)
				{
					$this->session->set_flashdata('msg', json_encode([1, $this->base->text('ssl_cancelled_msg', 'success')]));
					redirect("ssl/view/$id");
				}
				else
				{
					$this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
					redirect("ssl/view/$id");
				}
			}
			elseif($this->input->get('validate'))
			{
				$ssl_type = $this->ssl->get_ssl_type($id);
				$res = $this->acme->initilize($ssl_type);
				if(!is_bool($res))
				{
					$this->session->set_flashdata('msg', json_encode([0, $res]));
					redirect("ssl/view/$id");
				}
				elseif(is_bool($res) AND $res == true)
				{
				}
				else
				{
					$this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
					redirect("ssl/view/$id");
				}

				$res = $this->acme->validateOrder($id);
				if(!is_bool($res))
				{
					$this->session->set_flashdata('msg', json_encode([0, $res]));
					redirect("ssl/view/$id");
				}
				elseif(is_bool($res) AND $res == true)
				{
					$this->session->set_flashdata('msg', json_encode([1, $this->base->text('ssl_validated_msg', 'success')]));
					redirect("ssl/view/$id");
				}
				else
				{
					$this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
					redirect("ssl/view/$id");
				}
			}
			else
			{
				if($this->ssl->is_active() || $this->acme->is_active())
				{
					$data['title'] = 'view_ssl';
					$data['active'] = 'ssl';
					$data['id'] = $id;
					$ssl_type = $this->ssl->get_ssl_type($id);
					if ($ssl_type == 'gogetssl') {
						$data['data'] = $this->ssl->get_ssl_info($id);
					} else {
						$data['data'] = $this->acme->get_ssl_info($id);
					}
					if($data['data'] !== false)
					{
						$this->load->view($this->base->get_template().'/page/includes/user/header', $data);
						$this->load->view($this->base->get_template().'/page/includes/user/navbar');
						$this->load->view($this->base->get_template().'/page/user/view_ssl');
						$this->load->view($this->base->get_template().'/page/includes/user/footer');
					}
					else
					{
						redirect('404');
					}
				}
				elseif ($data['data'] == False)
				{
					$this->session->set_flashdata('msg', json_encode([0, $this->base->text('error_occured', 'error')]));
					redirect("ssl/list");
				} else
				{
					$this->session->set_flashdata('msg', json_encode([0, $data['data']]));
					redirect("ssl/list");
				}
			}
		}
		else
		{
			redirect('login');
		}
	}

	function upgrade()
	{
		if($this->user->is_logged())
		{
$this->load->view($this->base->get_template().'/page/includes/user/header', $data);						
$this->load->view($this->base->get_template().'/page/includes/user/navbar');			
$this->load->view($this->base->get_template().'/page/user/upgrade');									
$this->load->view($this->base->get_template().'/page/includes/user/footer');
		}
		else
		{
			redirect('login');
		}
	}

	function dns_lookup()
	{
		if($this->user->is_logged())
		{
			$data['title'] = 'dns_lookup';
			$data['type'] = [
				'A' => DNS_A,
				'AAAA' => DNS_AAAA,
				'CNAME' => DNS_CNAME,
				'TXT' => DNS_TXT,
				'MX' => DNS_MX,
				'NS' => DNS_NS,
				'CAA' => DNS_CAA,
				'SOA' => DNS_SOA
			];
			$data['fields'] = [
				'A' => [
					'host' => $this->base->text('host', 'table'),
					'type' => $this->base->text('type', 'table'),
					'ttl' => $this->base->text('ttl', 'table'),
					'ip' => $this->base->text('ip', 'table')
				],
				'AAAA' => [
					'host' => $this->base->text('host', 'table'),
					'type' => $this->base->text('type', 'table'),
					'ttl' => $this->base->text('ttl', 'table'),
					'ip' => $this->base->text('ip', 'table')
				],
				'CNAME' => [
					'host' => $this->base->text('host', 'table'),
					'type' => $this->base->text('type', 'table'),
					'ttl' => $this->base->text('ttl', 'table'),
					'target' => $this->base->text('target', 'table')
				],
				'TXT' => [
					'host' => $this->base->text('host', 'table'),
					'type' => $this->base->text('type', 'table'),
					'ttl' => $this->base->text('ttl', 'table'),
					'txt' => $this->base->text('content', 'table')
				],
				'MX' => [
					'host' => $this->base->text('host', 'table'),
					'type' => $this->base->text('type', 'table'),
					'ttl' => $this->base->text('ttl', 'table'),
					'pri' => $this->base->text('pri', 'table'),
					'target' => $this->base->text('target', 'table')
				],
				'NS' => [
					'host' => $this->base->text('host', 'table'),
					'type' => $this->base->text('type', 'table'),
					'ttl' => $this->base->text('ttl', 'table'),
					'target' => $this->base->text('target', 'table')
				],
			];

			$this->load->view($this->base->get_template().'/page/includes/user/header', $data);
			$this->load->view($this->base->get_template().'/page/includes/user/navbar');
			$this->load->view($this->base->get_template().'/page/user/dns_lookup');
			$this->load->view($this->base->get_template().'/page/includes/user/footer');
		}
		else
		{
			redirect('login');
		}
	}

	function whois_lookup()
	{
		if($this->user->is_logged())
		{
			$data['title'] = 'whois_lookup';
			$this->load->helper('whois');

			$this->load->view($this->base->get_template().'/page/includes/user/header', $data);
			$this->load->view($this->base->get_template().'/page/includes/user/navbar');
			$this->load->view($this->base->get_template().'/page/user/whois_lookup');
			$this->load->view($this->base->get_template().'/page/includes/user/footer');
		}
		else
		{
			redirect('login');
		}
	}
	
	// Thêm các function mới
function html_tools()
{
    if($this->user->is_logged())
    {
        $data['title'] = 'HTML Tools';
        $data['active'] = 'tools';

        $this->load->view($this->base->get_template().'/page/includes/user/header', $data);
        $this->load->view($this->base->get_template().'/page/includes/user/navbar');
        $this->load->view($this->base->get_template().'/page/user/html_tools');
        $this->load->view($this->base->get_template().'/page/includes/user/footer');
    }
    else
    {
        redirect('login');
    }
}

function base64_tools()
{
    if($this->user->is_logged())
    {
        $data['title'] = 'Base64 Tools';
        $data['active'] = 'tools';

        $this->load->view($this->base->get_template().'/page/includes/user/header', $data);
        $this->load->view($this->base->get_template().'/page/includes/user/navbar');
        $this->load->view($this->base->get_template().'/page/user/base64_tools');
        $this->load->view($this->base->get_template().'/page/includes/user/footer');
    }
    else
    {
        redirect('login');
    }
}
function wordpress_tools()
{
    if($this->user->is_logged())
    {
        $data['title'] = 'WordPress Tools';
        $data['active'] = 'tools';

        $this->load->view($this->base->get_template().'/page/includes/user/header', $data);
        $this->load->view($this->base->get_template().'/page/includes/user/navbar');
        $this->load->view($this->base->get_template().'/page/user/wordpress_tools');
        $this->load->view($this->base->get_template().'/page/includes/user/footer');
    }
    else
    {
        redirect('login');
    }
}
// Add these methods to your U controller

public function cloudflare_settings()
{
    if(!$this->user->is_logged()) {
        redirect('login');
        return;
    }

    if($this->input->post('update_api')) {
        $this->fv->set_rules('cf_email', 'Cloudflare Email', ['trim', 'required', 'valid_email']);
        $this->fv->set_rules('cf_key', 'API Key', ['trim', 'required']);

        if($this->fv->run() === true) {
            $email = $this->input->post('cf_email');
            $key = $this->input->post('cf_key');

            if($this->cloudflare->validate_api_key($email, $key)) {
                if($this->user->set_cloudflare_api($email, $key)) {
                    $this->session->set_flashdata('msg', json_encode([1, 'Cloudflare API settings updated successfully']));
                } else {
                    $this->session->set_flashdata('msg', json_encode([0, 'Error saving API settings']));
                }
            } else {
                $this->session->set_flashdata('msg', json_encode([0, 'Invalid Cloudflare API credentials']));
            }
        } else {
            $this->session->set_flashdata('msg', json_encode([0, validation_errors()]));
        }
        redirect('cloudflare/settings');
        return;
    }

    $data['title'] = 'Cloudflare Settings';
    $data['active'] = 'cloudflare';
    $data['cf_api'] = $this->user->get_cloudflare_api();
    
    $this->load->view($this->base->get_template().'/page/includes/user/header', $data);
    $this->load->view($this->base->get_template().'/page/includes/user/navbar');
    $this->load->view($this->base->get_template().'/page/user/cloudflare_settings');
    $this->load->view($this->base->get_template().'/page/includes/user/footer');
}

public function cloudflare_zones()
{
    if(!$this->user->is_logged()) {
        redirect('login');
        return;
    }

    if(!$this->user->has_cloudflare_api()) {
        $this->session->set_flashdata('msg', json_encode([0, 'Please configure Cloudflare API settings first']));
        redirect('cloudflare/settings');
        return;
    }

    $data['title'] = 'Cloudflare Zones';
    $data['active'] = 'cloudflare';
    $data['zones'] = $this->cloudflare->list_zones();
        log_message('debug', 'CF Zones Response: ' . json_encode($zones));


    $this->load->view($this->base->get_template().'/page/includes/user/header', $data);
    $this->load->view($this->base->get_template().'/page/includes/user/navbar');
    $this->load->view($this->base->get_template().'/page/user/cloudflare_zones');
    $this->load->view($this->base->get_template().'/page/includes/user/footer');
}

public function manage_dns($domain = null) {
    if(!$this->user->is_logged()) {
        redirect('login');
        return;
    }

    log_message('debug', '--- Request Data ---');
    log_message('debug', 'POST data: ' . json_encode($_POST));
    log_message('debug', 'GET data: ' . json_encode($_GET));

    // Get zone ID
    $zone_id = $this->cloudflare->get_zone_id($domain);
    if(!$zone_id) {
        $this->session->set_flashdata('msg', json_encode([0, 'Domain not found']));
        redirect('cloudflare/zones');
        return;
    }

    // Handle form submissions
    if($this->input->post('update_record')) {
        log_message('debug', 'Processing update record request');
        $this->_handle_update_dns_record($zone_id, $domain);
        return;
    }

    if($this->input->post('add_record')) {
        log_message('debug', 'Processing add record request');
        $this->_handle_add_dns_record($zone_id, $domain); 
        return;
    }

    if($this->input->post('delete_record')) {
        log_message('debug', 'Processing delete record request');
        $this->_handle_delete_dns_record($zone_id, $domain);
        return;
    }

    // Get DNS records
    $records = $this->cloudflare->list_dns_records($zone_id);
    
    // Get hosting accounts for IP suggestions
    $server_ips = [];
    $accounts = $this->account->get_user_accounts();
    
    foreach($accounts as $account) {
        if($account['account_status'] === 'active') {
            try {
                $ip = gethostbyname($account['account_domain']);
                if($ip && $ip !== $account['account_domain']) {
                    $server_ips[] = [
                        'name' => $account['account_label'],
                        'domain' => $account['account_domain'],
                        'ip' => $ip
                    ];
                }
            } catch(Exception $e) {
                log_message('error', 'Error getting IP for ' . $account['account_domain'] . ': ' . $e->getMessage());
            }
        }
    }

    $data = [
        'title' => 'Manage DNS',
        'active' => 'cloudflare',
        'domain' => $domain,
        'zone_id' => $zone_id,
        'records' => $records,
        'server_ips' => $server_ips
    ];

    $this->load->view($this->base->get_template().'/page/includes/user/header', $data);
    $this->load->view($this->base->get_template().'/page/includes/user/navbar');
    $this->load->view($this->base->get_template().'/page/user/manage_dns', $data);
    $this->load->view($this->base->get_template().'/page/includes/user/footer');
}
private function _handle_add_dns_record($zone_id, $domain) {
    try {
        $type = $this->input->post('type');
        $name = $this->input->post('name');
        $content = $this->input->post('content');
        $ttl = (int)$this->input->post('ttl');
        $proxied = $this->input->post('proxied') ? true : false;
        $priority = $this->input->post('priority') ? (int)$this->input->post('priority') : null;

        // Format name
        if($name === '@') {
            $name = $domain;
        } else {
            $name = $name . '.' . $domain;
        }

        $result = $this->cloudflare->add_dns_record(
            $zone_id,
            $type,
            $name,
            $content,
            $ttl,
            $proxied,
            $priority
        );

        if($result) {
            $this->session->set_flashdata('msg', json_encode([1, 'DNS record added successfully']));
        } else {
            $this->session->set_flashdata('msg', json_encode([0, 'Failed to add DNS record']));
        }

    } catch(Exception $e) {
        $this->session->set_flashdata('msg', json_encode([0, $e->getMessage()]));
    }

    redirect("manage_dns/$domain");
}

  private function _handle_delete_dns_record($zone_id, $domain) {
    // Debug
    log_message('debug', '--- DELETE DNS RECORD START ---');
    log_message('debug', 'POST data: ' . json_encode($_POST));

    $record_id = $this->input->post('record_id');
    if(!$record_id) {
        $this->session->set_flashdata('msg', json_encode([0, 'Record ID is required']));
        redirect("manage_dns/$domain");
        return;
    }

    $result = $this->cloudflare->delete_dns_record($zone_id, $record_id);

    if($result) {
        $this->session->set_flashdata('msg', json_encode([1, 'DNS record deleted successfully']));
    } else {
        $this->session->set_flashdata('msg', json_encode([0, 'Failed to delete DNS record']));
    }

    log_message('debug', '--- DELETE DNS RECORD END ---');
    redirect("manage_dns/$domain"); 
}
private function _validate_dns_record($type, $content)
{
    switch(strtoupper($type)) {
        case 'A':
            return filter_var($content, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
            
        case 'AAAA':
            return filter_var($content, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
            
        case 'CNAME':
        case 'NS':
            // Basic domain format validation
            return preg_match('/^(?:[-A-Za-z0-9]+\.)+[A-Za-z]{2,}$/', $content);
            
        case 'MX':
            // Priority weight + domain format
            return preg_match('/^\d+\s+(?:[-A-Za-z0-9]+\.)+[A-Za-z]{2,}$/', $content);
            
        case 'TXT':
            // Allow any content for TXT records
            return true;
            
        default:
            return false;
    }
}

public function check_domain_cloudflare($domain = null) 
{
    if(!$this->user->is_logged()) {
        redirect('login');
        return;
    }

    if(!$domain) {
        $this->session->set_flashdata('msg', json_encode([0, 'Domain name is required']));
        redirect('cloudflare/zones');
        return;
    }

    $zone_info = $this->cloudflare->get_zone_info($domain);
    
    $data['title'] = 'Domain Check';
    $data['active'] = 'cloudflare';
    $data['domain'] = $domain;
    $data['zone_info'] = $zone_info;

    $this->load->view($this->base->get_template().'/page/includes/user/header', $data);
    $this->load->view($this->base->get_template().'/page/includes/user/navbar');
    $this->load->view($this->base->get_template().'/page/user/check_domain_cloudflare');
    $this->load->view($this->base->get_template().'/page/includes/user/footer');
}
  public function get_stats_data($id = null) {
    if(!$this->user->is_logged()) {
        echo json_encode(['error' => 'Not logged in']);
        return;
    }

    header('Content-Type: application/json');
    
    try {
        $account = $this->account->get_user_account($id);
        if(!$account || $account['account_status'] !== 'active') {
            throw new Exception('Invalid account');
        }

        $stats = $this->vistapanel->get_usage_stats(
            $account['account_username'],
            $account['account_password']
        );

        if($stats === false) {
            throw new Exception('Failed to fetch stats');
        }

        echo json_encode([
            'success' => true,
            'data' => $stats
        ]);
    } catch(Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}
}

