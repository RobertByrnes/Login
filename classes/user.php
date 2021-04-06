<?php

require __DIR__ . '\..\vendor\autoload.php';

class User extends DataConnection
{
    use Email_Message;

    /**
     * Number of permitted login attempt allowed before a user account is locked.
     *
     *@var const
     */
    const ATTEMPTS = 5;

    /**
     * Object of the logged in user.
     * 
     * @var object
     */

    private object $user;

    /**
     * Variable holding error userMessages to login/registration attempts.
     * 
     * @var string
     */
    public string $userMessage;

    private string $srcEmail;

    /**
    * Class constructor.

    * @param string $dsn DB connection string.
    * @param string $user DB user.
    * @param string $pass DB password.
    * @return bool Returns connection success.
    */
    public function __construct(string $email)
    {
        $this->srcEmail = $email;
        PARENT::__construct();
    }

    /**
    * Return the logged in user.

    * @return user array data
    */
    public function getUser()
    {
        return $this->user;
    }

    /**
    * Login function.

    * @param string $email
    * @param string $password
    * @return bool
    */
    public function login(string $email, string $password) : bool
    {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
		$password = filter_var($password, FILTER_DEFAULT);
        if (is_null($this->dB))
        {
            $this->userMessage = $this->message;
            return FALSE;
        }

        else
        {
            $user = $this->preparedQueryRow("SELECT id, failures, password, permission, confirmed FROM `users` WHERE email=:email", array('email' => $email));

            if (isset($user->password) && password_verify($password, $user->password))
            {
                if ($user->confirmed === 0)
                {
                    $this->userMessage = "This account has not been authenticated.";
                    return FALSE;
                }

                if($user->failures <= SELF::ATTEMPTS)
                {
                    $this->user = $user;
                    (isset($_SESSION)) ? session_regenerate_id() : session_start();                    
                    $_SESSION['user']['id'] = $this->user->id;
                    $_SESSION['user']['permission'] = $this->user->permission;
                    $this->userMessage = 'Login successful.';
                    return TRUE;
                }
                else
                {
                    $this->userMessage = 'This user is blocked.';
                    return FALSE;
                }
            } 
            else
            {
                $this->registerWrongLoginAttempt($email);
                $this->userMessage = 'Invalid login information.';
                return FALSE;
            } 
        }
    }

    /**
    * Register a new user account and send a confirmation email.
    *
    * @param string $email
    * @param string $fName
    * @param string $lName
    * @param string $password
    * @return bool
    */
    public function registration(string $email, string $fName, string $lName, string $password) : bool
    {
        if ($this->checkEmail($email))
        {
            $this->userMessage = 'This email is already taken.';
            return FALSE;
        }

        if (!(isset($email) || !isset($fName) || !isset($lName) || !isset($password) || !filter_var($email, FILTER_VALIDATE_EMAIL)))
        {
            $this->userMessage = 'All fields are required.';
            return FALSE;
        }

        $authCode = md5(date('YmdHis'));
        $userDetails = array(
            'fName'     => $fName,
            'lName'     => $lName,
            'email'     => $email,
            'password'  => password_hash($password, PASSWORD_ARGON2I),
            'code'      => $authCode
        );

        $outcome = $this->preparedInsert("INSERT INTO users (first_name, last_name, email, `password`, auth_code) VALUES (:fName, :lName, :email, :password, :code)", $userDetails);

        if ($outcome)
        {
            if ($this->sendConfirmationEmail($email, $authCode))
            {
                $this->userMessage = 'Adding this user was a success, a confirmation email has been sent.';
                return TRUE;
            }        
            else
            {
                $this->userMessage = 'Adding this user was a success but the email did not send.';
                return FALSE; 
            }
        }
        else
        {
            $this->userMessage = 'Adding new user failed.';
            return FALSE;
        }
    }

    /**
    * Email the confirmation code function.
    *
    * @param string $email
    * @return bool
    */
    private function sendConfirmationEmail(string $email, string $authCode) : bool
    {
        $subject  = "Activate your registration";
        $userMessage  = $this->confirmationEmailTemplate($authCode);
        $headers  = "MIME-Version: 1.0"."\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8"."\r\n";
        $headers .= "From: local.dev.env@gmail.com";
        $email    = $email;
        if (mail($email, $subject, $userMessage, $headers))
        {
            return TRUE;
        }
        
        else
        {
            return FALSE;
        }
    }

    /**
    * Activate a login by a confirmation code and login function.
    *
    * @param string $email
    * @param string $authCode
    * @return bool
    */
    public function emailActivation(string $email, string $authCode) : bool
    {
        $userDetails = array(
            'email'     => $email,
            'auth_code' => $authCode
        );
        $count = $this->preparedInsertGetCount("UPDATE users SET confirmed = 1 WHERE email=:email AND auth_code=:auth_code", $userDetails);

        if ($count>0)
        {
            $this->user = $this->preparedQueryRow("SELECT id, permission FROM users WHERE email=:email and confirmed = 1", array('email' => $email));
            session_start();
            
            if (isset($this->user->id) && !empty($this->user->id))
            {
            	$_SESSION['user']['id'] = $this->user->id;
                $_SESSION['user']['permission'] = $this->user->permission;
                $this->message = 'Account activated successfully.';
                return TRUE;
            }
            
            else
            {
            	$this->userMessage = 'Account activitation failed.';
            	return FALSE;
            }            
        }
        
        else
        {
            $this->userMessage = 'Account activitation failed.';
            return FALSE;
        }
    }

    /**
    * Password change function.
    *
    * @param string $email
    * @param string $oldPassword
    * @param string $newPassword
    * @return bool
    */
    public function passwordChange($email, $oldPassword, $newPassword) : bool
    {
        
		$email = filter_input($username, FILTER_SANITIZE_EMAIL);
		$oldPassword = filter_input($oldPassword, FILTER_DEFAULT);
		$newPassword = filter_input($newPassword, FILTER_DEFAULT);
        if (isset($email) && isset($oldPassword) && isset($newPassword))
        {
            $this->user = $this->preparedQueryRow("SELECT `password` FROM users WHERE email=:email", array('email' => $email));

            if (password_verify($oldPassword, $this->user->password))
            {
                $userUpdate = array(
                    'password'  => password_hash($newPassword, PASSWORD_ARGON2I),
                    'email'     => $email
                );
                $count = $this->preparedInsertGetCount("UPDATE users SET password=:password WHERE email=:email", $userUpdate);
                
                if ($count>0)
                {
                    $this->userMessage = 'Password changed successfully.';
                    return TRUE;
                }                
                else
                {
                    $this->userMessage = 'Password change failed.';
                    return FALSE;
                }
            }           
            else
            {
                $this->userMessage = 'Passwords did not match. Try again ensuring both passwords match.';
                return FALSE;
            }
        }
    }

    /**
    * Assign permission level for a user, default.
    *
    * @param int $id
    * @param int $permission
    * @return bool
    */
    public function assignPermission($id, $permission) : bool
    {
        if (isset($id) && isset($permission))
        {
            $count = $this->preparedInsertGetCount("UPDATE users SET permission = $permission WHERE id = $id");
            
            if ($count > 0)
            {
                $this->userMessage = 'User permission level has been set to '.$permission;
                return TRUE;
            }
            
            else
            {
                $this->userMessage = 'Permission assignment failed.';
                return FALSE;
            }
        }
        
        else
        {
            $this->userMessage = 'A permission level for this user must be provided.';
            return FALSE;
        }
    }

    /**
    * User information change function
    *
    * @param int $id
    * @param string $fName
    * @param string $lName
    * @return bool
    */
    public function userUpdate($id, $fName, $lName)
    {
        if (isset($id) && isset($fName) && isset($lName))
        {
            $userDetails = array(
                'id'        => $id,
                'fName'     => $fName,
                'lName'     => $lName
            );
            $count = $this->preparedInsertGetCount("UPDATE users SET first_name=:fName, last_name=:lName WHERE id=:id", $userDetails);
            
            if($count)
            {
                $this->userMessage = 'User information changed.';
                return TRUE;
            }
            
            else
            {
                $this->userMessage = 'User information change failed.';
                return FALSE;
            }
        }
        
        else
        {
            $this->userMessage = 'Provide valid data.';
            return FALSE;
        }
    }

    /**
    * Check if email is already used function.
    *
    * @param string $email
    * @return bool
    */
    private function checkEmail($email) : bool
    {
        $count = $this->preparedQueryRow("SELECT id FROM users WHERE email=:email", array('email' => $email));

        if ($count > 0)
        {
            $this->userMessage = 'This email address is already in use.';
            return TRUE;
        }
        
        else
        {
            $this->userMessage = 'This email address is available.';
            return FALSE;
        }
    }

    /**
    * Register a wrong login attemp function.
    *
    * @param string $email
    * @return bool
    */
    private function registerWrongLoginAttempt($email) : bool
    {
        $result = $this->preparedInsert("UPDATE users SET failures = failures + 1 WHERE email = '".$email."'");
        if ($result)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    /**
    * Returns the userMessage currently held by an object of this class.
    *
    * @return string
    */
    public function getuserMessage() : string
    {
        return $this->userMessage;
    }

    /**
    * Log the user out and remove the user from session.
    * @return bool
    */
    public function logout() : bool
    {
        $_SESSION['user'] = NULL;
        $this->userMessage = 'User logged out.';
        return TRUE;
    }

    /**
    * Returns an array of all user details
    *
    * @return array
    */
    public function listUsers() : array
    {
        $users = array();
        if (is_null($this->dB))
        {
            $this->userMessage = 'Database connection failed.';
            return $users;
        }
        else
        {
            $users = $this->preparedQueryMany("SELECT id, first_name, last_name, email FROM users WHERE confirmed=1", NULL);
            $this->userMessage = 'User list has been generated.';
            return $users; 
        }
    }
}
