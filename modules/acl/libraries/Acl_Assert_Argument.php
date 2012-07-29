<?php

/*
 * Argument Assertion - check if certain keys of role and resource are the same
 * 
 * Possible use when you want to check if the resource object has a user_id attribute
 * with the same value of the role object (a user object).
 *
 * The assertion object requires an array of KEY => VALUE pairs, where the KEYs refer
 * to role attributes, and VALUEs to resource attributes.
 *
 * For example new Acl_Assert_Argument(array('primary_key_value'=>'user_id'));
 */
 
class Acl_Assert_Argument implements Acl_Assert_Interface {
	
	protected $arguments;

	public function __construct($arguments)
	{
		$this->arguments = $arguments;
	}
	
	public function assert(Acl $acl, $role = null, $resource = null, $privilege = null)
  {
		foreach($this->arguments as $role_key => $resource_key)
		{
			if($role->$role_key !== $resource->$resource_key)
				return FALSE;
		}
		
		return TRUE;
  }
}