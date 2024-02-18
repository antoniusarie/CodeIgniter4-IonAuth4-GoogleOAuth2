<?php

namespace App\Controllers;

use App\Models\IonAuthModel;
use App\Models\UserModel;

class Auth extends \IonAuth\Controllers\Auth
{
	/**
	 * If you want to customize the views,
	 *  - copy the ion-auth/Views/auth folder to your Views folder,
	 *  - remove comment
	 */
	protected $viewsFolder = 'auth';
	private $googleModel = NULL;
	private $userModel = NULL;
	private $googleClient = NULL;

	public function index()
	{
		$this->session = \Config\Services::session();

		if (!$this->ionAuth->loggedIn()) {
			$this->session->setFlashdata('message', ['type' => 'danger', 'text' => $this->ionAuth->messages()]);

			// redirect them to the login page
			return redirect()->to(base_url());
		} else if (!$this->ionAuth->isAdmin()) // remove this elseif if you want to enable this for non-admins
		{
			// redirect them to the home page because they must be an administrator to view this
			//show_error('You must be an administrator to view this page.');
			throw new \Exception('You must be an administrator to view this page.');
		} else {
			$this->session->setFlashdata('message', ['type' => 'success', 'text' => $this->ionAuth->messages()]);
			return redirect()->to(base_url() . "/home")->withCookies();
		}
	}

	public function login()
	{
		require_once ROOTPATH . "vendor/autoload.php";
		$this->googleModel = new IonAuthModel();
		$this->googleClient = new \Google_Client();
		$this->validation = \Config\Services::validation();
		$this->session = \Config\Services::session();
		$this->googleClient->setClientId("176941853300-dgui2r7ueqtkocbdblcf10at492fl0ua.apps.googleusercontent.com");
		$this->googleClient->setClientSecret("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");
		$this->googleClient->setRedirectUri(base_url("oauth"));
		$this->googleClient->addScope("email");
		$this->googleClient->addScope("profile");

		$data = [
			'identity' => [
				'class' => 'form-control form-control-lg fs-5 mt-1',
				'name'  => 'identity',
				'id'    => 'identity',
				'type'  => 'text',
				'placeholder' => 'Email',
				'value' => set_value('identity'),
				// 'required' => 'required'
			],
			'password' => [
				'class' => 'form-control form-control-lg fs-5 mt-1',
				'name' => 'password',
				'id'   => 'password',
				'type' => 'password',
				'placeholder' => 'Password',				
				'autocomplete' => 'on',
				// 'required' => 'required'
			],
		];

		// validate form input
		$this->validation->setRule('identity', str_replace(':', '', lang('Auth.login_identity_label')), 'required');
		$this->validation->setRule('password', str_replace(':', '', lang('Auth.login_password_label')), 'required');

		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			// check to see if the user is logging in
			// check for "remember me"
			$remember = (bool)$this->request->getVar('remember');

			if ($this->ionAuth->login($this->request->getVar('identity'), $this->request->getVar('password'), $remember)) {
				//if the login is successful
				$this->session->setFlashdata('message', ['type' => 'success', 'text' => $this->ionAuth->messages()]);
				
				//redirect them back to the home page
				return redirect()->to(base_url() . "/home")->withCookies();
			} else {
				$this->session->setFlashdata('message', ['type' => 'danger', 'text' => $this->ionAuth->errors()]);

				// use redirects instead of loading views for compatibility with MY_Controller libraries
				return redirect()->back()->withInput();
			}
		} else {
			$data['message'] = $this->validation->getErrors() ? $this->validation->listErrors($this->validationListTemplate) : $this->session->getFlashdata('message');
			
			// if the login was un-successful
			$this->session->setFlashdata('message', $this->ionAuth->errors($this->validationListTemplate));

			$data['googleBtn'] = $this->googleClient->createAuthUrl();
			// redirect them back to the login page			
			return view('auth/login', $data);
		}
	}

	public function logout()
	{
		// log the user out
		$this->ionAuth->logout();

		session()->remove('LoggedUserData');
		session()->remove('AccessToken');

		if (!(session()->get('LoggedUserData') && session()->get('AccessToken'))) {
			$this->session->setFlashdata('message', ['type' => 'success', 'text' => $this->ionAuth->messages()]);

			// redirect them to the login page
			return redirect()->to(base_url());
		} else {
			$this->session->setFlashdata('message', ['type' => 'danger', 'text' => 'Failed to Logout, Please Try Again']);
			return redirect()->to(base_url() . "/home");
		}
	}

	public function oauth()
	{
		require_once ROOTPATH . "vendor/autoload.php";
		$this->userModel = new UserModel();
		$this->googleModel = new IonAuthModel();
		$this->googleClient = new \Google_Client();		
		$this->validation = \Config\Services::validation();
		$this->session = \Config\Services::session();
		$this->googleClient->setClientId("176941853300-dgui2r7ueqtkocbdblcf10at492fl0ua.apps.googleusercontent.com");
		$this->googleClient->setClientSecret("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");
		$this->googleClient->setRedirectUri(base_url("oauth"));
		$this->googleClient->addScope("email");
		$this->googleClient->addScope("profile");

		$token = $this->googleClient->fetchAccessTokenWithAuthCode($this->request->getVar('code'));
		if (!isset($token['error'])) {
			$this->googleClient->setAccessToken($token['access_token']);
			session()->set("AccessToken", $token['access_token']);

			$googleService = new \Google_Service_Oauth2($this->googleClient);
			$googleProfile = $googleService->userinfo->get();
			// var_dump($googleProfile); exit();

			$userdata = array();
			// $password = $this->ionAuth->hashPassword("Metranet123@");
			$ipAddress = \Config\Services::request()->getIPAddress();

			if ($this->googleModel->isAlreadyRegister($googleProfile['id'])) {
				// get data from users by GoogleID
				$userProfile = $this->userModel->getById($googleProfile['id']);

				$userdata = [
					'id' => $userProfile['id'],
					'username' => $googleProfile['givenName'],
					'first_name' => $googleProfile['givenName'],
					'last_name' => $googleProfile['familyName'],
					'email' => $googleProfile['email'],
					'profile_img' => $googleProfile['picture'],
					'updated_on' => time()
				];

				//User Already Login and want to Login Again
				$this->googleModel->updateUserData($userdata, $googleProfile['id']);
			} else {
				$userdata = [
					'google_id' => $googleProfile['id'],
					'username' => $googleProfile['givenName'],
					'first_name' => $googleProfile['givenName'],
					'last_name' => $googleProfile['familyName'],
					'email' => $googleProfile['email'],
					'profile_img' => $googleProfile['picture'],
					'ip_address' => $ipAddress,
					'created_on' => time(),
					'active' => 1,
				];
				//new User want to Login
				$this->googleModel->insertUserData($userdata);
			}

			$this->session->setFlashdata('message', "Succesfuly Login");
			session()->set("LoggedUserData", $userdata);

			//Successfull Login
			return redirect()->to(base_url() . "/home");
		} else {
			$this->session->setFlashdata('message', "Something went Wrong");
			return redirect()->to(base_url());
		}
	}

	public function users()
	{
		if (!$this->ionAuth->loggedIn()) {
			// redirect them to the login page
			return redirect()->to(base_url() . '/login');
		} else if (!$this->ionAuth->isAdmin()) // remove this elseif if you want to enable this for non-admins
		{
			// redirect them to the home page because they must be an administrator to view this
			//show_error('You must be an administrator to view this page.');
			throw new \Exception('You must be an administrator to view this page.');
		} else {
			$this->data['title'] = lang('Auth.index_heading');

			// set the flash data error message if there is one
			$this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors($this->validationListTemplate) : $this->session->getFlashdata('message');
			//list the users
			$this->data['users'] = $this->ionAuth->users()->result();
			foreach ($this->data['users'] as $k => $user) {
				$this->data['users'][$k]->groups = $this->ionAuth->getUsersGroups($user->id)->getResult();
			}
			return $this->renderPage($this->viewsFolder . DIRECTORY_SEPARATOR . 'users', $this->data);
		}
	}
}
