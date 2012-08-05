<?php

/**
 * User AUTHORIZATION library. 
 * 
 * - Authentication vs Authorization -
 * "However, more precise usage describes authentication as the process of verifying a claim made by a 
 *  subject that it should be treated as acting on behalf of a given principal (person, computer, smart 
 *  card etc.), while authorization is the process of verifying that an authenticated subject has the 
 *  authority to perform a certain operation. Authentication, therefore, must precede authorization."
 * [http://en.wikipedia.org/wiki/Authentication#Authentication_vs._authorization]
 *
 * This library offers advanced user authorization using a user defined Authentication library, and an
 * improved version of Zend's ACL for Kohana.
 *
 * The Access Control List (roles,resources,rules) and the desired Authentication library are stored in a
 * config file. Usage in your code (controller/libraries/models) are as follow:
 
 		if(A2::instance()->allowed('blog','read')) // simple acl usage
 			// do
 		else
 			// don't 
 
 		if(A2::instance()->allowed($blog,'delete')) // advanced acl usage, using the improved assertions
 			// do
 		else
 			// don't
 *
 */

class A2_Core extends Acl_Core {

	public $a1; 				 // the Authentication library (used to retrieve user)
	protected $guest_role; // name of the guest role (used when no user is logged in)

	/**
	 * Create an instance of A2.
	 *
	 * @return  object
	 */
	public static function factory($config_name = 'a2')
	{
		return new A2($config_name);
	}

	/**
	 * Return a static instance of A2.
	 *
	 * @return  object
	 */
	public static function instance($config_name = 'a2', $reset = FALSE)
	{
		static $instance;

		// Load the A2 instance
    if ($reset) {
      $instance[$config_name] = new A2($config_name);
    }
    else {
		  empty($instance[$config_name]) and $instance[$config_name] = new A2($config_name);
    }

		return $instance[$config_name];
	}

	/**
	 * Build default A2 from config.
	 *
	 * @return  void
	 */
	public function __construct($config_name = 'a2')
	{
		// Read config
		$config          = Kohana::config($config_name);
		// var_dump($config);die();
  	$this->guest_role = $config['guest_role'];
  	$this->a1         = A1::instance();

		// Add Guest Role as role
		if(!array_key_exists($this->guest_role,$config['roles']))
		{
			$this->add_role($this->guest_role);
		}

		// Add roles
		foreach($config['roles'] as $role => $parent)
		{
			$this->add_role($role,$parent);
		}

		// Add resources
		if(!empty($config['resources']))
		{
			foreach($config['resources'] as $resource => $parent)
			{
				$this->add_resource($resource,$parent);
			}
		}

		// Add rules
		foreach(array('allow','deny') as $method)
		{
			if(!empty($config['rules'][$method]))
			{
				foreach($config['rules'][$method] as $rule)
				{
					if( ($num = 4 - count($rule)) )
					{
						$rule += array_fill(count($rule),$num, NULL);
					}
					
					// create assert object
					if($rule[3] !== NULL)
						$rule[3] = isset($rule[3][1]) ? new $rule[3][0]($rule[3][1]) : new $rule[3][0];
					
					$this->$method($rule[0],$rule[1],$rule[2],$rule[3]);
				}
			}
		}
	}
	
	public function allowed($resource = NULL, $privilige = NULL)
	{
		// retrieve user
		$role = ($user = $this->a1->get_user()) ? $user : $this->guest_role;
		
		return $this->is_allowed($role,$resource,$privilige);
	}
	
	// Alias of the logged_in method
	public function logged_in() 
	{
		return $this->a1->logged_in();
	}

	// Alias of the get_user method
	public function get_user()
	{
		return $this->a1->get_user();
	}
} // End A2 lib