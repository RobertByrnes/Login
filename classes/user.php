<?php
/**
* Secure login/registration user class.
*/

require_once('includes/login.db.config.php');
require('Shifty.php');

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
        try {
            $pdo = new PDO(dsn, username, password);
            $this->pdo = $pdo;
            return $this->pdo;
        } catch(PDOException $e) { 
            $this->msg = 'Connection to database failed!';
            return false;
        }
    }

    private static function cipherIn($string) : string
    {
        Shifty::encipher($string, $cipherString='');
        return $cipherString = Shifty::XORCipher($cipherString); 
    }


    private static function cipherOut($cipherString) : string
    {
        $cipherString = Shifty::XORCipher($cipherString);
        Shifty::decipher($cipherString, $string ='');
        return $string;
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
    * @return bool Returns login success.
    */
    public function login($email, $password)
    {
        $email = User::cipherIn($email);
        if(is_null($this->pdo)) {
            $this->msg = 'Connection failed.';
            return false;
        } else {
            $pdo = $this->pdo;
            $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, failures, password, permission FROM users WHERE email = '".$email."'");
            $stmt->execute();
            $user = $stmt->fetch();

            if(password_verify($password, $user['password'])) {
                if($user['failures'] <= $this->permitedAttemps) {
                    $this->user = $user;
                    session_regenerate_id();
                    $_SESSION['user']['id'] = $user['id'];
                    $_SESSION['user']['permission'] = $user['permission'];
                    header('Location: index.php');
                    return true;
                } else {
                    $this->msg = 'This user account is blocked.';
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
            $this->msg = 'All fields are required.';
            return false;
        }

        $email = User::cipherIn($email);
        $first_name = User::cipherIn($first_name);
        $last_name = User::cipherIn($last_name);

        $password = $this->hashPass($password);
        $authCode = $this->hashPass(date('Y-m-d H:i:s').$email);
        $stmt = $pdo->prepare('INSERT INTO users (first_name, last_name, email, `password`, auth_code) VALUES (?, ?, ?, ?, ?)');
        if($stmt->execute([$first_name, $last_name, $email, $password, $authCode])) {
            if($this->sendConfirmationEmail($email, $authCode)) {
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
    private function sendConfirmationEmail($email, $authCode)
    {
        $subject = "Activate your registration";
        $message = ' 
            <!DOCTYPE html> 
            <head> 
                <title>Welcome to envirosample.online</title> 
            </head> 
            <body> 
                <h4>Thanks for joining us!</h4> 
                <table rules="all" cellspacing="0" style="border: 2px;  border-color: #FB4314; width: 100%;"> 
                    <tr style="background: rgb(139, 139, 139);"> 
                        <td>
                            <strong>Email from: </strong>
                        </td>
                        <td>
                            admin@envirosample.online
                        </td> 
                    </tr> 
                    <tr> 
                        <td>
                            <strong>Website: </strong>
                        </td>
                        <td>
                            <a href="http://www.envirosample.online">www.envirosample.online</a>
                        </td> 
                    </tr>
                    <tr>
                        <td><strong>Click here to confirm your account: </strong></td>
                        <td>
                            <a href="login.manager.php?activity=activation.script&authCode='.$authCode.'"><strong>Confirm</strong></a>
                        </td>
                    </tr>
                </table>
            </body> 
            </html>';

        $from = "admin@envirosample.online";

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From:" . $from;
        $email = User::cipherOut($email);
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
    public function emailActivation($email, $authCode)
    {
        $email = User::cipherIn($email);
        $pdo = $this->pdo;
        $stmt = $pdo->prepare('UPDATE users SET confirmed = 1 WHERE email = ? and auth_code = ?');
        $stmt->execute([$email, $authCode]);

        if($stmt->rowCount()>0) {
            $stmt = $pdo->prepare('SELECT id, first_name, last_name, email, failures, permission FROM users WHERE email = ? and confirmed = 1 limit 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            $this->user = $user;
        
            session_start();
            if(!empty($user['email'])) {
            	$_SESSION['user']['id'] = $user['id'];
                $_SESSION['user']['permission'] = $user['permission'];
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
    public function passwordChange($email, $oldPassword, $newPassword)
    {
        $email = User::cipherIn($email);
        if(isset($email) && isset($oldPassword) && isset($newPassword)) {
            $pdo = $this->pdo;
            $stmt = $pdo->prepare('SELECT `password` FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if(password_verify($oldPassword, $user['password'])) {
                $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE email = ?');
                if($stmt->execute([$email, $this->hashPass($newPassword)])) {
                    return true;
                } else {
                    $this->msg = 'Password change failed.';
                    return false;
                }
            } else {
                $this->msg = 'Provide a valid email address and a ensure old passwords match.';
                return false;
            }
        }
    }


    /**
    * Assign a permission level function, default 1.
    * @param int $id User id.
    * @param int $role User role.
    * @return boolean of success.
    */
    public function assignPermission($id, $permission)
    {
        $pdo = $this->pdo;
        if(isset($id) && isset($role)) {
            $stmt = $pdo->prepare('UPDATE users SET permission = ? WHERE id = ?');
            if($stmt->execute([$permission, $role])) {
                return true;
            } else {
                $this->msg = 'Permission assignment failed.';
                return false;
            }
        } else {
            $this->msg = 'Provide a permission level for this user.';
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
            $this->msg = 'Provide valid data.';
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
        $stmt = $pdo->prepare('UPDATE users SET failures = failures + 1 WHERE email = ?');
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
            $this->msg = 'Connection failed!';
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