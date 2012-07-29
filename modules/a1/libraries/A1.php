<?php
/**
 * User AUTHENTICATION library. Handles user login and logout, as well as secure
 * password hashing.
 *
 * Based on Kohana's AUTH library and Fred Wu's AuthLite library:
 *
 * @package    Auth
 * @author     Kohana Team
 * @copyright  (c) 2007 Kohana Team
 * @license    http://kohanaphp.com/license.html
 *
 * @package    Layerful
 * @subpackage  Modules
 * @author    Layerful Team <http://layerful.org/>
 * @author    Fred Wu <fred@beyondcoding.com>
 * @copyright  BeyondCoding
 * @license    http://layerful.org/license MIT
 * @since    0.3.0
 */
class A1_Core {

	protected $config_name;
	protected $session;
	protected $config;
	protected $user_model;
	protected $columns;	
	protected $error = NULL;
	protected $user;

	/**
	 * Create an instance of A1.
	 *
	 * @return  object
	 */
	public static function factory($config_name = 'a1')
	{
		return new A1($config_name);
	}

	/**
	 * Return a static instance of A1.
	 *
	 * @return  object
	 */
	public static function instance($config_name = 'a1')
	{
		static $instance;

		// Load the A1 instance
		empty($instance[$config_name]) and $instance[$config_name] = new A1($config_name);

		return $instance[$config_name];
	}

	/**
	 * Loads Session and configuration options.
	 *
	 * @return  void
	 */
	public function __construct($config_name = 'a1')
	{
		$this->config_name     = $config_name;
		$this->session 				 = Session::instance();
		$this->config          = Kohana::config($config_name);
		$this->user_model      = $this->config['user_model'];
		$this->columns 				 = $this->config['columns'];
		
		// Clean up the salt pattern and split it into an array
		if ($this->config['salt_pattern']) {
		  $this->config['salt_pattern'] = preg_split('/,\s*/', $this->config['salt_pattern']);
		}
		
		Kohana::log('debug', 'A1 Library loaded');
	}

	/**
	 * Check if there is an active session.
	 *
	 * @return  boolean
	 */
	public function logged_in()
	{
	  if ($this->user->loaded) return $this->user;

		// Get the user from the session
		$session_key = $this->session->get($this->config['session_key']);
    $user = ($session_key instanceof ORM) ? $session_key : ORM::factory($this->user_model, $session_key);
		$status = $user->loaded ? true : false;
		
		// Get the user from the cookie
		if ($status === FALSE AND $this->config['lifetime'])
		{
			if ( ($token = cookie::get("a1_{$this->config_name}_autologin")) )
			{
				$token = explode('.',$token);
			
				if (count($token) === 2 AND is_string($token[0]) AND is_numeric($token[1]))
				{
					// Search user on user ID and token. Because user ID is primary key, this is much faster than
					// searching on just the token.
					$user = ORM::factory($this->user_model)->where($this->columns['token'],$token[0])->find($token[1]);
					
					if ($user->loaded)
					{
						$status = true;
						$this->complete_login($user, TRUE);
					}
				}
			}
		}
		$this->user = $user;
		return $status === TRUE ? $user : FALSE;
	}
	
	public function get_user()
	{
		return $this->logged_in();
	}

	public function complete_login($user, $remember = FALSE)
	{
		if ($remember === TRUE AND $this->config['lifetime'])
		{
			// Create token
			$token = text::random('alnum', 32);
			
			$user->{$this->columns['token']} = $token;

			// TODO: find a better way to store used_id in cookie
			cookie::set("a1_{$this->config_name}_autologin", $token . '.' . $user->primary_key_value, $this->config['lifetime']);
		}

		if(isset($this->columns['last_login']))
		{
			$user->{$this->columns['last_login']} = $this->config['time_function'] ? call_user_func($this->config['time_function']) : time();
		}
		
		if(isset($this->columns['logins']))
		{
			$user->{$this->columns['logins']}++;
		}

		$user->save();

		// Regenerate session (prevents session fixation attacks)
		$this->session->regenerate();
		
		$this->session->set($this->config['session_key'], $user->id);
	}

  /**
	 * Attempt to log in a user by using an ORM object and plain-text password.
	 *
	 * @param   string   username to log in
	 * @param   string   password to check against
	 * @param   boolean  enable auto-login
	 * @return  boolean
	 */
	public function login($username, $password, $remember = FALSE)
	{
		if (empty($password)) {
      $this->error = 'password.empty';
		  return FALSE;
		}

		$user = is_object($username) ? $username : ORM::factory($this->user_model)->where($this->columns['username'], $username)->find();
		
    if ( ! $user->loaded) {
      $this->error = 'password.invalid';
      return FALSE;
    }
    
		if ( ! $user->status) {
      $this->error = 'user.blocked';
      return FALSE;
		}

		if ( ! $user->site->status) {
		  $this->error = 'site.blocked';
		  return FALSE;
		}
		
		$salt = $this->find_salt($user->{$this->columns['password']});

		if($this->hash_password($password, $salt) === $user->{$this->columns['password']})
		{
			$this->complete_login($user, $remember);
						
			return TRUE;
		}
		
		$this->error = 'password.invalid';
		
		return FALSE;
	}
	
	/**
	 * Get the language translated error string.
	 *
	 * @return string
	 */
	public function get_error() {
	  return Kohana::lang('a1_error.'. $this->error);
	}

	/**
	 * Log out a user by removing the related session variables.
	 *
	 * @param   boolean  completely destroy the session
	 * @return  boolean
	 */
	public function logout($destroy = FALSE)
	{
		if (cookie::get("a1_{$this->config_name}_autologin"))
		{
			cookie::delete("a1_{$this->config_name}_autologin");
		}
		
		// Remove remember token.
		$user = ORM::factory($this->user_model, $this->session->get($this->config['session_key']));
		if ($user->loaded) {
      $user->{$this->columns['token']} = NULL;
      $user->save();		  
		}
    
		if ($destroy === TRUE)
		{
			// Destroy the session completely
			$this->session->destroy();
		}
		else
		{
			// Remove the user from the session
			$this->session->delete($this->config['session_key']);

			// Regenerate session_id
			$this->session->regenerate();
		}

		return ! $this->logged_in(NULL);
	}

	/**
	 * Creates a hashed password from a plaintext password, inserting salt
	 * based on the configured salt pattern.
	 *
	 * @param   string  plaintext password
	 * @return  string  hashed password string
	 */
	public function hash_password($password, $salt = FALSE)
	{
		if ($salt === FALSE)
		{
      return $this->hash($password);
			// Create a salt seed, same length as the number of offsets in the pattern
      // $salt = substr($this->hash(uniqid(NULL, TRUE)), 0, count($this->config['salt_pattern']));
		}

		// Password hash that the salt will be inserted into
		$hash = $this->hash($salt.$password);

		// Change salt to an array
		$salt = str_split($salt, 1);

		// Returned password
    $password = '';

		// Used to calculate the length of splits
		$last_offset = 0;

		foreach ($this->config['salt_pattern'] as $offset)
		{
			// Split a new part of the hash off
			$part = substr($hash, 0, $offset - $last_offset);

			// Cut the current part out of the hash
			$hash = substr($hash, $offset - $last_offset);

			// Add the part to the password, appending the salt character
			$password .= $part.array_shift($salt);

			// Set the last offset to the current offset
			$last_offset = $offset;
		}
    
		// Return the password, with the remaining hash appended
		return $password.$hash;
	}

	/**
	 * Perform a hash, using the configured method.
	 *
	 * @param   string  string to hash
	 * @return  string
	 */
	public function hash($str)
	{
		return hash($this->config['hash_method'], $str);
	}

	/**
	 * Finds the salt from a password, based on the configured salt pattern.
	 *
	 * @param   string  hashed password
	 * @return  string
	 */
	public function find_salt($password)
	{
	  if ( ! $this->config['salt_pattern'])
	    return '';

		$salt = '';

		foreach ($this->config['salt_pattern'] as $i => $offset)
		{
			// Find salt characters, take a good long look...
			$salt .= substr($password, $offset + $i, 1);
		}

		return $salt;
	}

} // End A1