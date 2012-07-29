<?php
/**
 * Type of hash to use for passwords. Any algorithm supported by the hash function
 * can be used here. Note that the length of your password is determined by the
 * hash type + the number of salt characters.
 * @see http://php.net/hash
 * @see http://php.net/hash_algos
 */
$config['hash_method'] = 'sha1';

/**
 * Defines the hash offsets to insert the salt at. The password hash length
 * will be increased by the total number of offsets.
 */
$config['salt_pattern'] = '3, 6, 9, 10, 11, 20, 22, 25, 28, 30';

/**
 * Set the auto-login (remember me) cookie lifetime, in seconds. The default
 * lifetime is two weeks.
 */
$config['lifetime'] = 1209600;

/**
 * User model
 */
$config['user_model'] = 'user';

/**
 * Set the session key that will be used to store the current user.
 */
$config['session_key'] = 'a1_user';
 
/**
 * Table column names
 */
$config['columns']   = array(
	'username'	=> 'username',    //username
	'password'	=> 'password',    //password
	'token'    	=> 'token',  			//token
	'last_login'=> 'last_login',  //last login (optional)
	'logins'    => 'logins' 			//login count (optional)
);
