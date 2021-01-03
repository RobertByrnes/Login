<?php
/**
* Secure login/registration user class.
*/

require_once('includes/db.config.php');

class User {
    /** @var object $pdo Copy of PDO connection */
    private $pdo;
    /** @var object of the logged in user */
    private $user;
    /** @var string error msg */
    private $msg;
    /** @var int number of permitted wrong login attemps */
    private $permitedAttemps = 5;

    /**
    * Connection init function
    * @param string $dsn DB connection string.
    * @param string $user DB user.
    * @param string $pass DB password.
    *
    * @return bool Returns connection success.
    */
    public function __construct()
    {
        if(session_status() !== PHP_SESSION_ACTIVE) {
            try {
                $pdo = new PDO(dsn, username, password);
                $this->pdo = $pdo;
                return $this->pdo;
            } catch(PDOException $e) { 
                $this->msg = 'Connection did not work out!';
                return false;
            }
        } else {
            $this->msg = 'Session did not start.';
            return false;
        }
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
    * Login function
    * @param string $email User email.
    * @param string $password User password.
    *
    * @return bool Returns login success.
    */
    public function login($email, $password)
    {
        if(is_null($this->pdo)) {
            $this->msg = 'Connection did not work out!';
            return false;
        } else {
            $pdo = $this->pdo;
            $stmt = $pdo->prepare('SELECT id, first_name, last_name, email, failures, `password`, user_role FROM users WHERE email = ? and confirmed = 1 limit 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if(password_verify($password, $user['password'])) {
                if($user['wrong_logins'] <= $this->permitedAttemps) {
                    $this->user = $user;
                    session_regenerate_id();
                    $_SESSION['user']['id'] = $user['id'];
                    $_SESSION['user']['first_name'] = $user['first_name'];
                    $_SESSION['user']['last_name'] = $user['last_name'];
                    $_SESSION['user']['email'] = $user['email'];
                    $_SESSION['user']['user_role'] = $user['user_role'];
                    return true;
                } else {
                    $this->msg = 'This user account is blocked, please contact our support department.';
                    return false;
                }
            } else {
                $this->registerWrongLoginAttemp($email);
                $this->msg = 'Invalid login information or the account is not activated.';
                return false;
            } 
        }
    }

    /**
    * Register a new user account function
    * @param string $email User email.
    * @param string $first_name User first name.
    * @param string $last_name User last name.
    * @param string $password User password.
    * @return boolean of success.
    */
    public function registration($email, $first_name, $last_name, $password)
    {
        $pdo = $this->pdo;
        if($this->checkEmail($email)) {
            $this->msg = 'This email is already taken.';
            return false;
        }
        if(!(isset($email) && isset($first_name) && isset($last_name) && isset($password) && filter_var($email, FILTER_VALIDATE_EMAIL))) {
            $this->msg = 'Inesrt all valid requered fields.';
            return false;
        }

        $password = $this->hashPass($password);
        $confCode = $this->hashPass(date('Y-m-d H:i:s').$email);
        $stmt = $pdo->prepare('INSERT INTO users (first_name, last_name, email, `password`, confirm_code) VALUES (?, ?, ?, ?, ?)');
        if($stmt->execute([$first_name,$last_name,$email,$password,$confCode])) {
            if($this->sendConfirmationEmail($email)) {
                return true;
            } else {
                $this->msg = 'confirmation email sending has failed.';
                return false; 
            }
        } else {
            $this->msg = 'Adding new user failed.';
            return false;
        }
    }

    /**
    * Email the confirmation code function
    * @param string $email User email.
    * @return boolean of success.
    */
    private function sendConfirmationEmail($email)
    {
        $pdo = $this->pdo;
        $stmt = $pdo->prepare('SELECT confirm_code FROM users WHERE email = ? limit 1');
        $stmt->execute([$email]);
        $code = $stmt->fetch();

        $subject = 'Confirm your registration';
        $message = 'Please confirm you registration by pasting this code in the confirmation box: '.$code['confirm_code'];
        // $headers = 'X-Mailer: PHP/' . phpversion();
        // Use below for local testing
        $headers = 'From: local.dev.env@gmail.com' . "\r\n" . 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/html; charset=utf-8';

        if(mail($email, $subject, $message, $headers)) {
            return true;
        } else {
            return false;
        }
    }

    /**
    * Activate a login by a confirmation code and login function
    * @param string $email User email.
    * @param string $confCode Confirmation code.
    * @return boolean of success.
    */
    public function emailActivation($email, $confCode)
    {
        $pdo = $this->pdo;
        $stmt = $pdo->prepare('UPDATE users SET confirmed = 1 WHERE email = ? and confirm_code = ?');
        $stmt->execute([$email,$confCode]);
        if($stmt->rowCount()>0) {
            $stmt = $pdo->prepare('SELECT id, first_name, last_name, email, wrong_logins, user_role FROM users WHERE email = ? and confirmed = 1 limit 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            $this->user = $user;
            session_regenerate_id();
            if(!empty($user['email'])) {
            	$_SESSION['user']['id'] = $user['id'];
	            $_SESSION['user']['first_name'] = $user['first_name'];
	            $_SESSION['user']['last_name'] = $user['last_name'];
	            $_SESSION['user']['email'] = $user['email'];
	            $_SESSION['user']['user_role'] = $user['user_role'];
	            return true;
            } else {
            	$this->msg = 'Account activitation failed.';
            	return false;
            }            
        } else {
            $this->msg = 'Account activitation failed.';
            return false;
        }
    }

    /**
    * Password change function
    * @param int $id User id.
    * @param string $password New password.
    * @return boolean of success.
    */
    public function passwordChange($id, $password)
    {
        $pdo = $this->pdo;
        if(isset($id) && isset($password)) {
            $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
            if($stmt->execute([$id,$this->hashPass($password)])) {
                return true;
            } else {
                $this->msg = 'Password change failed.';
                return false;
            }
        } else {
            $this->msg = 'Provide an ID and a password.';
            return false;
        }
    }


    /**
    * Assign a role function
    * @param int $id User id.
    * @param int $role User role.
    * @return boolean of success.
    */
    public function assignRole($id, $role)
    {
        $pdo = $this->pdo;
        if(isset($id) && isset($role)) {
            $stmt = $pdo->prepare('UPDATE users SET role = ? WHERE id = ?');
            if($stmt->execute([$id,$role])) {
                return true;
            } else {
                $this->msg = 'Role assign failed.';
                return false;
            }
        } else {
            $this->msg = 'Provide a role for this user.';
            return false;
        }
    }



    /**
    * User information change function
    * @param int $id User id.
    * @param string $first_name User first name.
    * @param string $last_name User last name.
    * @return boolean of success.
    */
    public function userUpdate($id, $first_name, $last_name)
    {
        $pdo = $this->pdo;
        if(isset($id) && isset($first_name) && isset($last_name)) {
            $stmt = $pdo->prepare('UPDATE users SET first_name = ?, last_name = ? WHERE id = ?');
            if($stmt->execute([$id,$first_name,$last_name])) {
                return true;
            } else {
                $this->msg = 'User information change failed.';
                return false;
            }
        } else {
            $this->msg = 'Provide a valid data.';
            return false;
        }
    }

    /**
    * Check if email is already used function
    * @param string $email User email.
    * @return boolean of success.
    */
    private function checkEmail($email)
    {
        $pdo = $this->pdo;
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? limit 1');
        $stmt->execute([$email]);
        if($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }


    /**
    * Register a wrong login attemp function
    * @param string $email User email.
    * @return void.
    */
    private function registerWrongLoginAttemp($email)
    {
        $pdo = $this->pdo;
        $stmt = $pdo->prepare('UPDATE users SET wrong_logins = wrong_logins + 1 WHERE email = ?');
        $stmt->execute([$email]);
    }

    /**
    * Password hash function
    * @param string $password User password.
    * @return string $password Hashed password.
    */
    private function hashPass($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
    * Print error msg function
    * @return void.
    */
    public function printMsg()
    {
        print $this->msg;
    }

    /**
    * Logout the user and remove it from the session.
    *
    * @return true
    */
    public function logout()
    {
        $_SESSION['user'] = null;
        session_regenerate_id();
        return true;
    }



    /**
    * List users function
    *
    * @return array Returns list of users.
    */
    public function listUsers()
    {
        if(is_null($this->pdo)) {
            $this->msg = 'Connection did not work out!';
            return [];
        } else {
            $pdo = $this->pdo;
            $stmt = $pdo->prepare('SELECT id, first_name, last_name, email FROM users WHERE confirmed = 1');
            $stmt->execute();
            $result = $stmt->fetchAll(); 
            return $result; 
        }
    }
}