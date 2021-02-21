<?php

/**
* @author Robert Byrnes
* @created 01/01/2021
**/

require_once('includes/login.config.php');
require('includes/login.db.config.php');

new LoginManager;

Class LoginManager {

	private $user;

	public function __construct()
	{
		session_start();
		$this->user = new User;
		$this->loginHandler($_REQUEST);

		return $this->user;
	}

	
	private function loginHandler($request)
	{
		$templateData = array();
		if (isset($request['activity'])) {
			$activity = $request['activity'];
		} else {
			$activity = null;
		}
			switch ($activity) {
				case 'register':
					if (isset($request['email']) && isset($request['first_name']) && isset($request['last_name']) && isset($request['password'])) {
						$this->register($email = $request['email'], $first_name = $request['first_name'], $last_name = $request['last_name'], $password = $request['password']); 
					}
					break;
				case 'activation.script':
					$templateData['authCode'] = $request['authCode'];
					$this->displayPage($templateData, $page='activationform');
					break;
				case 'activate':
					if (isset($request['email']) && isset($request['authCode'])) {
						$this->activateNew($request['email'], $request['authCode']);
					}
					break;
				case 'login':
					if (isset($request['email']) && isset($request['password'])) {
						if($this->login($request['email'], $request['password'])) {
							header('Location: /index.php');
						}
					}
					break;
				case 'logout':
					$this->logout();
					break;
				case 'password.script':
						$this->displayPage($templateData, $page='password.change');
					break;
				case 'change.password':
					if (isset($request['email']) && isset($request['password'])) {
						$this->passwordChange($request['email'], $request['password']);
						$this->displayPage($templateData, $page='userpage');
					}
					break;
				case 'success':
					header('Location: index.php');
					break;
				default:
					$this->displayPage($templateData=array(), $page='login.registerform');
					break;
			}
	}


	private function displayPage($templateData, $page)
    {
		if (preg_match('/wamp64|repositories/i', $_SERVER['DOCUMENT_ROOT'])){
			if(!isset($templateData['debug'])) {
				$templateData['debug'] = 1;
			}
		} else {
			if(!isset($templateData['debug'])) {
				$templateData['debug'] = 0;
			}
		}
        global $smarty;
        $smarty->assign('templateData', $templateData);
        $smarty->display('header.tpl');
        $smarty->display($page.'.tpl');
        $smarty->display('footer.tpl');
            
        return true;
	}
	

	private function register($email, $first_name, $last_name, $password)
	{
		$email = filter_var($email, FILTER_SANITIZE_EMAIL);
		$first_name = filter_var($first_name, FILTER_SANITIZE_STRING);
		$last_name = filter_var($last_name, FILTER_SANITIZE_STRING);
		$password = filter_var($password, FILTER_DEFAULT);
	
		if($this->user->registration($email, $first_name, $last_name, $password)) {
			print 'A confirmation mail has been sent, please confirm your account registration.';
			return true;
		} else {
			$this->user->printMsg();
			return false;
		}
	}


	private function activateNew($email, $authCode)
	{
		$email = filter_var($email, FILTER_SANITIZE_EMAIL);
		$code = filter_var($authCode, FILTER_DEFAULT);
		
		if($this->user->emailActivation($email, $code)) {
			print 'Account activitation successful, login to continue.';
			return true;
		} else {
			$this->user->printMsg();
			return false;
		}
	}


	private function login($email, $password)
	{
		$email = filter_var($email, FILTER_SANITIZE_EMAIL);
		$password = filter_var($password, FILTER_DEFAULT);
		if($this->user->login($email, $password)) {
			return true;
		} else {
			$this->user->printMsg();
			return false;
		}
	}


	private function logout()
	{
		$this->user->logout();
		header('location: login.manager.php');

		return true;
	}


	private function passwordChange($email, $oldPassword, $newPassword)
	{
		$email = filter_input($username, FILTER_SANITIZE_EMAIL);
		$oldPassword = filter_input($oldPassword, FILTER_DEFAULT);
		$newPassword = filter_input($newPassword, FILTER_DEFAULT);

		if($this->user->passwordChange($email, $oldPassword, $newPassword)) {
			return true;
		} else {
			$this->user->printMsg();
			return false;
		}
	}
}