<?php
require_once('./vendor/autoload.php');
require_once './libraries/xml2json.php';

defined('BASEPATH') or exit('No direct script access allowed');


class Auth extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('LoginModel');
		$this->load->library('session');
	}

	public function index()
	{
		$this->load->view('login');
	}

	public function login_user()
	{
		$log = new LoginModel;
		$log->login_user();
	}
	public function logout_user()
	{
		session_destroy();
		redirect(base_url('login'));
	}
}
