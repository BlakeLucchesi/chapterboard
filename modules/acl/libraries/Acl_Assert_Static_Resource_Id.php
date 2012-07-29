<?php defined('SYSPATH') or die('No direct script access.');

class Acl_Assert_Static_Resource_Id implements Acl_Assert_Interface {
  
  protected $arguments;

	public function __construct($arguments)
	{
		$this->arguments = $arguments;
	}
	
	public function assert(Acl $acl, $role = null, $resource = null, $privilege = null)
  {
    $return = array();
		foreach($this->arguments as $resource_key => $allowed_values)	{
		  $allowed_values = is_array($allowed_values) ? $allowed_values : array($allowed_values);
      // var_dump($allowed_values, $resource->$resource_key);
      $return[] = in_array($resource->$resource_key, $allowed_values) ? TRUE : FALSE;
		}
    // var_dump($return);
		$return = array_filter($return);
		return empty($return) ? FALSE : TRUE;
  }
  
}