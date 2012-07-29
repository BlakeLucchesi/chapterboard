<?php

/* ------------------------------------------------------
 * New to ACL? Read the Zend documentation:
 *   http://framework.zend.com/manual/en/zend.acl.html
 * All their examples work with this lib
 * ------------------------------------------------------
 *
 * This is a Kohana port of the Zend_ACL library, with a few changes.
 *
 * Things that are different from Zend_ACL:
 * 1) Your ACL definition is saved using the string identifiers of the roles/resources,
 *    NOT the objects. This way, if you serialize the ACL, you won't end up with a 
 *    unneccesary large serialization string. You don't have to supply objects when
 *    adding roles/resources. EG a $acl->add_role('user') is fine.
 * 2) If you have defined assertions in your rules, the assert methods will have access
 *    to the arguments you provided in the ->allow($role,$resource,$privilege) call.
 *    So, if you provide a User_Model as $role, the assert method will receive this object,
 *    and not the role_id of this object. This way, assertions become way more powerful.
 * 3) Not all methods are implemented, because they weren't needed by me at the time.
 *    However, the essential methods (the core of ACL) are implemented, so the missing methods
 *    can be implemented easily when needed.
 * 4) The methods are underscored instead of camelCased, so add_role, add_resource and is_allowed.
 *
 * Ported to Kohana & modified by Wouter - see Kohana Forum.
 *
 * Based on Zend_Acl:
 *
 * @category   Zend
 * @package    Zend_Acl
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Acl.php 9417 2008-05-08 16:28:31Z darby $
 */

class Acl_Core {
	
	protected $command = array();

	protected $_roles = array();
	protected $_resources = array();

  protected $_rules = array();
	/* the $_rules array is structured in a way like this:
			array(
				'allResources' => array(
					'allRoles' => array(
		            'allPrivileges' => array(
		                'allow'  => FALSE,
		                'assert' => null
		                ),
		            'byPrivilegeId' => array()
		            ),
			      'byRoleId' => array()
			      ),
		    'byResourceId' => array()
		   );
  */

	// add role
	public function add_role($role,$parents = NULL)
	{
		if($parents !== NULL AND ! is_array($parents))
		{
			$parents = array($parents);
		}
		
		$this->_roles[$role] = array(
			'children'	=> array(),
			'parents'		=> $parents
		);
		
		if(!empty($parents))
		{
			foreach($parents as $parent)
			{
				$this->_roles[$parent]['children'][] = $role;
			}
		}
	}
	
	// check if role exists in ACL
	public function has_role($roles)
	{
    foreach ($roles as $role) {
      $return = array_key_exists($role, $this->_roles);
      if ($return)
        return TRUE;
    }
    return FALSE;
	}
	
	// add resource
	public function add_resource($resource,$parent = NULL)
	{
		$this->_resources[$resource] = array(
			'children'	=> array(),
			'parent'		=> $parent
		);
		
		if($parent !== NULL)
		{
			$this->_resources[$parent]['children'][] = $resource;
		}
	}
	
	// check if resource exists in ACL
	public function has_resource($resource)
	{
		return $resource !== NULL AND isset($this->_resources[$resource]);
	}
	
	// add an allow rule
	public function allow($roles = NULL, $resources = NULL, $privileges = NULL, Acl_Assert_Interface $assertion = NULL)
	{
		$this->add_rule(TRUE,$roles,$resources,$privileges,$assertion);
	}

	// add an deny rule
	public function deny($roles = NULL, $resources = NULL, $privileges = NULL, Acl_Assert_Interface $assertion = NULL)
	{
		$this->add_rule(FALSE,$roles,$resources,$privileges,$assertion);
	}
	
	// internal add rule method
	private function add_rule($allow,$roles,$resources,$privileges,$assertion)
	{
		// Normalize arguments (build arrays with IDs as string)
		
			//privileges
		if($privileges !== NULL AND !is_array($privileges)) 
		{
			$privileges = array($privileges);
		}
			//resources
		if($resources !== NULL)
		{
			if(!is_array($resources)) 
			{
				$resources = array($resources);
			}
			foreach($resources as &$resource)
			{
				if($resource instanceof Acl_Resource_Interface)
				{
					$resource = $resource->get_resource_id();
				}
				else
				{
					$resource = (string) $resource;
				}
			}
		}
			//roles
		if($roles !== NULL)
		{
			if(!is_array($roles)) 
			{
				$roles = array($roles);
			}
			foreach($roles as &$role)
			{
				if($role instanceof Acl_Role_Interface)
				{
					$role = $role->get_role_id();
				}
				else
				{
					$role= (string) $role;
				}
			}
		}

		// start building rule, from bottom to top
		$rule = array(
			'allow'	 => $allow,
			'assert' => $assertion
		);
		
		$rule = $privileges === NULL ? array('allPrivileges' => $rule) : array('byPrivilegeId'=> array_fill_keys($privileges,$rule) );
		
		$rule = $roles === NULL ? array('allRoles' => $rule) : array('byRoleId' => array_fill_keys($roles,$rule) );
		
		$rule = $resources === NULL ? array('allResources' => $rule) : array('byResourceId' => array_fill_keys($resources,$rule) );
		
		// using arr::merge, this appends numeric keys, but replaces associative keys
		$this->_rules = arr::merge($this->_rules,$rule);
	}
	
	public function is_allowed($role = NULL, $resource = NULL, $privilege = NULL)
	{
		// save command data (in case of assertion, then the original objects are used)
		$this->command = array
		(
			'role' => $role,
			'resource' => $resource,
			'privilege'=> $privilege
		);
		
		// normalize role & resource to a string value (or NULL)
		$role = $role !== NULL ? ($role instanceof Acl_Role_Interface ? $role->get_role_id() : (string) $role) : NULL;
		$resource = $resource !== NULL ? ($resource instanceof Acl_Resource_Interface ? $resource->get_resource_id() : (string) $resource) : NULL;
		
		//echo $role,'-',$resource,'-',$privilege,'<br>';
		
		// make role array
		$role = is_array($role) ? $role : array($role);
		
		// role unknown
		if($role !== NULL AND !$this->has_role($role))
			return FALSE;
			
		// resource unknown
		if($resource !== NULL AND !$this->has_resource($resource))
			return FALSE;
		
		do
		{
			//echo 'running for resource: ' . $resource .  '<br>';
								
			// try to find rule
			if( ($rule = $this->_find_match_role($resource,$role,$privilege) ) )
			{
				return $rule['allow'];
			}
		}
		// go level up in resources tree (child resources inherit rules from parent)
		while($resource !== NULL AND ($resource = $this->_resources[$resource]['parent']) );

		return FALSE;
	}

	/*
	 * Try to find a matching rule based for supplied role and its parents (if any)
	 *
	 * @param string $resource  resource id
	 * @param array  $roles     array of role ids
	 * @param string $privilege privilege
	 * @return array|boolean a matching rule on success, false otherwise.
	 */
	private function _find_match_role($resource,$roles,$privilege)
	{
		foreach($roles as $role)
		{
			// find match for this role		
			if( ($rule = $this->_find_match($this->_rules,$resource,$role,$privilege) ) )
			{
				$return[] = $rule;
			}
			
			// try parents of role (starting at last added parent role)
			if($role !== NULL AND !empty($this->_roles[$role]['parents']))
			{
				$return[] = $this->_find_match_role($resource,array_reverse($this->_roles[$role]['parents']),$privilege);
			}
			
		}
		$return = array_filter($return);
		return empty($return) ? FALSE : array_pop($return);
	}
	
	/*
	 * Try to find a matching rule based on the specific arguments
	 *
	 * @param array  $attach    the (remaining) rules array
	 * @param string $resource  resource id
	 * @param string $role      role id
	 * @param string $privilege privilege
	 * @return array|boolean a matching rule on success, false otherwise.
	 */
	private function _find_match(& $attach,$resource,$role,$privilege)
	{
		//echo $resource,' ',$role,' ',$privilege,'<br>';
		
		// resource level
		if($resource !== FALSE)
		{
			if( isset($attach['byResourceId'][$resource]) AND ($rule = $this->_find_match($attach['byResourceId'][$resource],FALSE,$role,$privilege) ) )
			{
				return $rule;
			}
			elseif(isset($attach['allResources']))
			{
				$attach =& $attach['allResources'];
			}
			else
			{
				return FALSE;
			}
		}
		
		// role level
		if($role !== FALSE)
		{
			if( isset($attach['byRoleId'][$role]) AND ($rule = $this->_find_match($attach['byRoleId'][$role],FALSE,FALSE,$privilege) ) )
			{	
				return $rule;
			}
			elseif(isset($attach['allRoles']))
			{
				$attach =& $attach['allRoles'];
			}
			else
			{
				return FALSE;
			}
		}
		
		if($privilege === NULL)
		{
			$specificDeny = FALSE;
			
			if( isset($attach['byPrivilegeId']) )
			{
				foreach($attach['byPrivilegeId'] as $rule)
				{
					if($this->_rule_runnable($rule,FALSE))
					{
						$specificDeny = $rule;
						break;
					}
				}
			}
			
			if( !empty($attach['allPrivileges']) AND $this->_rule_runnable($attach['allPrivileges']) )
			{
				if($attach['allPrivileges']['allow'] AND $specificDeny !== FALSE)
				{
					return $specificDeny;
				}
				else
				{
					return $attach['allPrivileges'];
				}
			}
			else
			{
				return $specificDeny;
				
				/*if($specificDeny !== FALSE)
				{
					return $specificDeny;
				}
				else
				{
					return FALSE;
				}*/
			}
		}
		else
		{
			if( empty($attach['byPrivilegeId']) OR ! isset ($attach['byPrivilegeId'][$privilege]) )
			{
				if( !empty($attach['allPrivileges']) AND $this->_rule_runnable($attach['allPrivileges']) )
				{
					return $attach['allPrivileges'];
				}
				else
				{
					return FALSE;
				}
			}
			elseif( isset($attach['byPrivilegeId'][$privilege]) AND $this->_rule_runnable($attach['byPrivilegeId'][$privilege]) )
			{
				return $attach['byPrivilegeId'][$privilege];
			}
			else
			{
				return FALSE;
			}
		}

		// never reached
		return FALSE;

	}
	
	/*
	 * Verifies if rule can be applied to specified arguments
	 *
	 * @param  array   $rule  the rule
	 * @param  boolean $allow verify if rule is allowing/denying
	 * @return boolean rule can be applied to arguments
	 */
	private function _rule_runnable($rule,$allow = NULL)	
	{
		if($allow !== NULL)
		{
			if($rule['allow'] !== $allow)
				return FALSE;
		}
		
		if(isset($rule['assert']))
		{
			return $rule['assert']->assert($this,$this->command['role'],$this->command['resource'],$this->command['privilege']);
		}
		
		return TRUE;
	}
	
	public function __sleep()
	{
		return array('_roles','_resources','_rules'); // no need to save the current command ($this->command)
	}
}	