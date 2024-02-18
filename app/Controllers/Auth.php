<?php

namespace App\Controllers;

use App\Libraries\GoogleOAuth2;

class Auth extends \IonAuth\Controllers\Auth
{
	protected $validationListTemplate = 'list';
	protected $db;
	protected $request;
	protected $client;
	protected $createURL;

	public function __construct()
	{
		require_once ROOTPATH . "vendor/autoload.php";
		helper(['form', 'url']);

		// Load Google Libraries
		$google = new GoogleOAuth2;

		$this->ionAuth    		= new \IonAuth\Libraries\IonAuth();
		$this->validation 		= \Config\Services::validation();
		$this->session       	= \Config\Services::session();
		$this->db 				= \Config\Database::connect();
		$this->request 			= \Config\Services::request();
		$this->configIonAuth 	= config('IonAuth');
		$this->client 			= $google->init();
		
		// Google Button URL
		$this->createURL = $this->client->createAuthUrl();

		// IonAuth Error Templates
		if (!empty($this->configIonAuth->templates['errors']['list'])) {
			$this->validationListTemplate = $this->configIonAuth->templates['errors']['list'];
		}
	}

	// Google OAuth Login
	public function oauth()
	{
		// Get code & validate token
		$code = $this->request->getVar('code');
		$token = $this->client->fetchAccessTokenWithAuthCode($code);
		
		if (!isset($token['error'])) {
			$this->client->setAccessToken($token['access_token']);
			$this->session->set("AccessToken", $token['access_token']);

			// Init Google Service
			$googleService = new \Google_Service_Oauth2($this->client);
			$userProfile = $googleService->userinfo->get();

			// Check email if exist
			$emailCheck = $this->ionAuth->emailCheck($userProfile['email']);
			if ($emailCheck) {
				$users = $this->db->table('users')->getWhere(['email' => $userProfile['email']])->getRowArray();

				// set data into session
				$this->session->set("LoggedUserData", $users);

				// Update Last Login
				$this->ionAuth->updateLastLogin($users['id']);
			} else {
				// retrieve google data
				$data = [
					'google_id' => $userProfile['id'],
					'username' => $userProfile['name'],
					'first_name' => $userProfile['givenName'],
					'last_name' => $userProfile['familyName'],
					'email' => $userProfile['email'],
					'profile_img' => $userProfile['picture'],
					'phone' => $userProfile['phone'],
					'ip_address' => \Config\Services::request()->getIPAddress(),
					'last_login' => time(),
					'created_on' => time(),
					'active' => 1,
				];

				// set data into session
				$this->session->set("LoggedUserData", $data);

				// Insert Data
				$this->ionAuth->register($userProfile['name'], "Metranet123@", $userProfile['email'], $data);
			}

			// Successfull Login
			$this->session->setFlashdata('message', ['type' => 'success', 'text' => 'Logged In Successfully']);
			return redirect()->to(base_url() . "/profile");
		} else {
			// Unsuccessfull Login
			$this->session->setFlashdata('message', ['type' => 'danger', 'text' => '<p>' . $token['error_description'] . '</p>']);
			return redirect()->to(base_url());
		}
	}

	public function index()
	{
		if (!$this->ionAuth->loggedIn()) {
			$this->session->setFlashdata('message', $this->ionAuth->messages());

			// redirect them to the login page
			return redirect()->to(base_url());
		} else if (!$this->ionAuth->isAdmin()) // remove this elseif if you want to enable this for non-admins
		{
			// redirect them to the profile page because they must be an administrator to view this
			//show_error('You must be an administrator to view this page.');
			throw new \Exception('You must be an administrator to view this page.');
		} else {
			$data['message'] = $this->validation->getErrors() ? $this->validation->listErrors($this->validationListTemplate) : $this->session->getFlashdata('message');

			return redirect()->to(base_url() . "/profile")->withCookies();
		}
	}

	public function login()
	{
		// validate form input
		$this->validation->setRule('identity', str_replace(':', '', lang('Auth.login_identity_label')), 'required');
		$this->validation->setRule('password', str_replace(':', '', lang('Auth.login_password_label')), 'required');

		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			// check to see if the user is logging in
			// check for "remember me"
			$remember = (bool)$this->request->getVar('remember');

			if ($this->ionAuth->login($this->request->getVar('identity'), $this->request->getVar('password'), $remember)) {
				// save users into data
				$users = $this->db->table('users')->getWhere(['email' => $this->request->getVar('identity')])->getRowArray();

				// set data into session
				$this->session->set("LoggedUserData", $users);

				//if the login is successful
				$this->session->setFlashdata('message', ['type' => 'success', 'text' => 'Logged In Successfully']);

				//redirect them back to the profile page
				return redirect()->to(base_url() . "/profile")->withCookies();
			} else {
				// if the login was un-successful
				$this->session->setFlashdata('message', $this->ionAuth->errors($this->validationListTemplate));

				// use redirects instead of loading views for compatibility with MY_Controller libraries
				return redirect()->to(base_url())->withInput();
			}
		} else {
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
					'autocomplete' => 'on'
					// 'required' => 'required'
				],
				'message' => $this->validation->getErrors() ? $this->validation->listErrors($this->validationListTemplate) : $this->session->getFlashdata('message'),
				'googleBtn' => $this->createURL,
			];
		}
		return view('auth/login', $data);
	}

	/**
	 * Log the user out
	 *
	 * @return \CodeIgniter\HTTP\RedirectResponse
	 */
	public function logout()
	{
		$this->data['title'] = 'Logout';

		// log the user out
		$this->ionAuth->logout();

		// redirect them to the login page
		$this->session->setFlashdata('message', ['type' => 'success', 'text' => $this->ionAuth->messages()]);
		return redirect()->to(base_url())->withCookies();
	}
}
