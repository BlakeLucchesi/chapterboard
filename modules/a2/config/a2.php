<?php

/*
 * The Authentication library to use
 * Make sure that the library supports:
 * 1) A get_user method that returns FALSE when no user is logged in
 *	  and a user object that implements Acl_Role_Interface when a user is logged in
 * 2) A static instance method to instantiate a Authentication object
 *
 * array(CLASS_NAME,array $arguments)
 */
$config['a1'] = array('A1'); // For Kohana's AUTH, simply use array('AUTH');

/*
 * The ACL Roles (String IDs are fine, use of ACL_Role_Interface objects also possible)
 * Use: ROLE => PARENT(S) (make sure parent is defined as role itself before you use it as a parent)
 */
$config['roles'] = array
(
	// ADD YOUR OWN ROLES HERE
	'user'	=>	'guest'
);

/*
 * The name of the guest role 
 * Used when no user is logged in.
 */
$config['guest_role'] = 'guest';

/*
 * The ACL Resources (String IDs are fine, use of ACL_Resource_Interface objects also possible)
 * Use: ROLE => PARENT (make sure parent is defined as resource itself before you use it as a parent)
 */
$config['resources'] = array
(
	// ADD YOUR OWN RESOURCES HERE
	'blog'	=>	NULL
);

/*
 * The ACL Rules (Again, string IDs are fine, use of ACL_Role/Resource_Interface objects also possible)
 * Split in allow rules and deny rules, one sub-array per rule:
     array( ROLES, RESOURCES, PRIVILEGES, ASSERTION)
 */
$config['rules'] = array
(
	'allow' => array
	(
		// ADD YOUR OWN ALLOW RULES HERE
		array('guest','blog','read')
	),
	'deny' => array
	(
		// ADD YOUR OWN DENY RULES HERE
	)
);