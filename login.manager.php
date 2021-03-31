<?php
session_start();
require_once('includes/login.config.php');
require_once('includes/login.db.config.php');


/**
* Class LoginManager, validates and routes user requests to Class User. 
* 
* @author Robert Byrnes
* @created 01/01/2021
**/
Class LoginManager
{
	/**
	 * Object of the Class User.
	 * 
	 * @var object
	 */
	private User $user;

	/**
	 * Class constructor.
	 */
	public function __construct()
	{
		$this->user = new User;
		$this->loginHandler($_REQUEST);
	}

	/**
	 * Checks request for required inputs, then routes to member functions.
	 *
	 * @param array $request
	 * @return void
	 */
	private function loginHandler(array $request) : void
	{
		(isset($request['email'])) ? $email = $request['email']: $email = null;
		(isset($request['first_name'])) ? $fName = $request['first_name'] : $firstName = null;
		(isset($request['last_name'])) ? $lName = $request['last_name'] : $lastName = null;
		(isset($request['password'])) ? $password = $request['password'] : $password = null;
		(isset($request['authCode'])) ? $templateData['authCode'] = $request['authCode'] : $authCode = null;
		(isset($request['activity'])) ? $activity = $request['activity'] : $activity = null;
		$templateData = [];
		
		switch ($activity)
		{
			case 'register': 			$this->registerNewUser($email, $fName, $lName, $password); 	break;
			case 'activation.script': 	$page='activation'; 										break;
			case 'activate': 			$this->activateNewUser($email, $authCode); 					break;
			case 'login': 				$this->login($email, $password); 							break;
			case 'logout': 				$this->logout(); 											break;
			case 'password.script': 	$page='passwordChange'; 									break;
			case 'change.password': 	$this->passwordChange($email, $password); 					break;
			case 'success': 			header('Location: index.php'); 								break;
			default:
				$templateData=[];
				$page='loginRegister';
		}
		$this->displayPage($templateData, $page);
	}

	/**
	 * Pass $templateData array and $page name to Smarty.
	 *
	 * @param array $templateData
	 * @param string $page
	 * @return boolean
	 */
	private function displayPage(array $templateData, string $page) : bool
    {
		if (preg_match('/wamp64|repositories/i', $_SERVER['DOCUMENT_ROOT']))
		{
			(!isset($templateData['debug'])) ? $templateData['debug'] = 1 : $templateData['debug'] = 0;
		}

        global $smarty;
        $smarty->assign('templateData', $templateData);
        $smarty->display('header.tpl');
        $smarty->display($page.'.tpl');
        $smarty->display('footer.tpl');    
        return true;
	}
	
	/**
	 * Sanitizes inputs, then passes them to Class User to register a new user.
	 *
	 * @param string $email
	 * @param string $fName
	 * @param string $lName
	 * @param string $password
	 * @return void
	 */
	private function registerNewUser($email, $fName, $lName, $password) : void
	{
		$email = filter_var($email, FILTER_SANITIZE_EMAIL);
		$fName = filter_var($fName, FILTER_SANITIZE_STRING);
		$lName = filter_var($lName, FILTER_SANITIZE_STRING);
		$password = filter_var($password, FILTER_DEFAULT);
		$confirmation = 'A confirmation email has been sent.';
		($this->user->registration($email, $fName, $lName, $password)) ? print $confirmation : $this->user->printMsg();
	}

	/**
	 * Sanitizes email and authorisation code and then passes them to Class User
	 * to activate this user account.
	 *
	 * @param string $email
	 * @param string $authCode
	 * @return boolean
	 */
	private function activateNewUser($email, $authCode) : bool
	{
		$email = filter_var($email, FILTER_SANITIZE_EMAIL);
		$code = filter_var($authCode, FILTER_DEFAULT);
		
		if ($this->user->emailActivation($email, $code))
		{
			print 'Account activitation successful, login to continue.';
			return true;
		}

		else
		{
			$this->user->printMsg();
			return false;
		}
	}


	/**
	 * Sanitizes inputs, then passes them to Class User to log this user in.
	 * The user is then redirected to the home page.
	 *
	 * @param string $email
	 * @param string $password
	 * @return boolean
	 */
	private function login($email, $password) : bool
	{
		$email = filter_var($email, FILTER_SANITIZE_EMAIL);
		$password = filter_var($password, FILTER_DEFAULT);

		if ($this->user->login($email, $password))
		{
			header('Location: /index.php');
		}

		else
		{
			$this->user->printMsg();
			return false;
		}
	}

	/**
	 * Passes control to Class User to log this user out, then
	 * redirects the user to the login page.
	 *
	 * @return void
	 */
	private function logout()
	{
		$this->user->logout();
		header('location: login.manager.php');
		return true;
	}

	/**
	 * Sanitizes inputs, passing them to Class User to change this users
	 * password.
	 *
	 * @param string $email
	 * @param string $oldPassword
	 * @param string $newPassword
	 * @return void
	 */
	private function passwordChange($email, $oldPassword, $newPassword)
	{
		$email = filter_input($username, FILTER_SANITIZE_EMAIL);
		$oldPassword = filter_input($oldPassword, FILTER_DEFAULT);
		$newPassword = filter_input($newPassword, FILTER_DEFAULT);
		
		if ($this->user->passwordChange($email, $oldPassword, $newPassword))
		{
			$this->displayPage($templateData, 'userpage');
		}
		
		else
		{
			$this->user->printMsg();
			return false;
		}
	}
}
new LoginManager;