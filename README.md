# Login Application
This application, including classes and examples with PHP 7.4.9 request handler provides a secure login/user
management system easily adapted to any code situation.

PHP Tested: 5.6.19, 7.0.11, 7.4.9.

The use case here is to include the this as a module in any project, configure index.php to test for $_SESSION['user'] - if this is not set then redirect to login.manager.php which will handle everything from there.  When an existing user is detected in session then a redirect will occur to index.php allowing the user to access the desired content.  What a user may access is set with the use of user permissions from the Class User. 

# Shifty Class
The Shifty class handles the encryption and decryption of user information.  Shifty is a static class using strict data types. Everything stored in DB in this application goes through this class.  The primary method of enryption used for username and email data is a dual method - a substitution cypher followed up with an XOR encrpytion.  This is reversed in retrieval of data from the DB, there is much more to this class not used in this application but could easily be added.
See the class file Shifty.php for more information.

# User Class
This class contains function for user registration, activation, login and more.  This class when called from the login.manager.php script handles validation and database entry/retreival.  The example use case given here (see Smarty framework templates) demonstrated the use of registration with email activation and login only.  Other options provided by the class 'User' - set permission level, get user, change password, edit user information, logout etc. See class file User.php for more information.


# CONTENTS

	1. ALL METHODS - Class User (all functions annotated in user.php)
		1.1. User::__construct()
		1.2. User::cipherIn()
		1.3. User::cipherOut()
		1.4. User::getUser()
		1.5. User::login()
		1.6. User::registration()
		1.7. User::sendConfirmationEmail()
		1.8. User::emailActivation()
		1.9. User::passwordChange()
		1.10. User::assignPermission()
		1.11. User::userUpdate()
		1.12. User::checkEmail()
		1.13. User::registerWrongLoginAttempt()
		1.14. User::hashPass()
		1.15. User::printMsg()
		1.16. User::logout()
		1.17. User::listUsers()

	2. ALL METHODS - Class Shifty (all functions annotated in Shifty.php)
		2.1. Shifty::encipher()
		2.2. Shift::decipher()
		2.3. Shifty::cipher()
		2.4. Shifty::XORCipher()
		2.5. Shifty::lockUserDetail()
		2.6. Shifty::freeUserDetail()
		2.7. Shifty::hashGen()
		2.8. Shifty::benchmarkServer()
		2.9. Shifty::hashWithKnownCost()
		2.10. SHifty::verifyByComparison()

# Example Usage
- Create a repository on your local machine called login.  Nagivate to login/login.manager.php
- Use the login.sql file to create a database table and set up database credentials in login.db.config.php file.
- NOTE: login.config.php contains setup for autoloading and handling live use as well as local testing environments.