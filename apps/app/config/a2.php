<?php

/*
 * The ACL Roles (String IDs are fine, use of ACL_Role_Interface objects also possible)
 * Use: ROLE => PARENT(S) (make sure parent is defined as role itself before you use it as a parent)
 */
$config['roles'] = array(
    // Add Member type roles
    'guest',
    'user'	=>	'guest',
    'pledge' => 'user',
    'active' => 'user',	
    'alumni' => 'user',

    // Add Admin roles.
    'admin',
    'calendar',
    'files',
    'finance',
    'forum',
    'recruitment',
    'service',
    'sms',
    'national' => 'admin',
    'root' => 'admin',
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
$config['resources'] = array(
    // ADD YOUR OWN RESOURCES HERE
  'activity'       => NULL,
  'album'          => NULL,
  'announcement'   => NULL,
  'budget'         => NULL,
  'calendar'       => NULL,
  'comment'        => NULL,
  'course'         => NULL,
  'dashboard'      => NULL,
  'campaign'       => NULL,
  'event'          => NULL,
  'file'           => NULL,
  'finance'        => NULL,
  'finance_charge' => NULL,
  'folder'         => NULL,
  'forum'          => NULL,
  'recruit'        => NULL,
  'service_event'  => NULL,
  'service_hour'   => NULL,
  'site'           => NULL,
  'sms'            => NULL,
  'topic'          => NULL,
  'user'           => NULL,
);

/*
 * The ACL Rules (Again, string IDs are fine, use of ACL_Role/Resource_Interface objects also possible)
 * Split in allow rules and deny rules, one sub-array per rule:
     array( ROLES, RESOURCES, PRIVILEGES, ASSERTION)
 */
$config['rules'] = array(
    'allow' => array(
    // ADD YOUR OWN ALLOW RULES HERE

    // Allow switching to a different site_id.
    array('national', 'site', 'teleport', array('Acl_Assert_OR_Argument', array('chapter_id' => 'chapter_id'))),
    array('root'),

    array('admin', 'site', 'admin', array('Acl_Assert_OR_Argument', array('site_id' => 'id'))),
    array('admin', 'forum', array('admin', 'view', 'edit'), array('Acl_Assert_OR_Argument', array('site_id' => 'id'))),
    array('admin', 'user', array('admin', 'edit'), array('Acl_Assert_OR_Argument', array('site_id' => 'site_id'))), // Admin users can edit users that belong to the same site as them.

    // Calendar Administration
    array(array('admin', 'calendar'), 'calendar', 'manage'),
    array(array('admin', 'calendar'), 'calendar', array('view', 'edit', 'delete', 'admin'), array('Acl_Assert_Argument', array('site_id' => 'site_id'))),
    array(array('admin', 'calendar'), 'calendar', array('view'), array('Acl_Assert_OR_Argument', array('chapter_id' => array('site', 'chapter_id')))),

    // Event Administration (The view is set by our dynamic permissions in hooks/acl).
    array(array('admin', 'calendar'), 'event', array('edit', 'delete'), array('Acl_Assert_OR_Argument', array('site_id' => array('calendar', 'site_id')))),
    array(array('admin', 'calendar'), 'event', 'add'),
    array('user', 'event', array('edit', 'delete'), array('Acl_Assert_OR_Argument', array('id' => 'user_id'))),

    // Forum Administration
    array(array('admin', 'forum'), 'forum', 'manage'),
    array(array('admin', 'forum'), 'forum', array('view', 'edit', 'delete', 'admin'), array('Acl_Assert_OR_Argument', array('site_id' => 'site_id'))),
    array(array('national'), 'forum', array('view'), array('Acl_Assert_OR_Argument', array('chapter_id' => array('site', 'chapter_id')))),

    array('user', 'topic', array('edit', 'delete'), array('Acl_Assert_OR_Argument', array('id' => 'user_id'))), // Users can edit and delete their own topics.
    array(array('admin', 'forum'), 'topic', 'edit', array('Acl_Assert_OR_Argument', array('site_id' => array('forum', 'site_id')))),

    // Comments - Note that for posting a comment we check if the user has access to view the parent object.
    // Additionally administrator access to deleting comments depends on whether the user is an administrater
    // of the parent object.
    array('user', 'comment', array('edit', 'delete'), array('Acl_Assert_OR_Argument', array('id' => 'user_id'))),

    // Announcements
    array('admin', 'announcement', 'manage'),
    array('user', 'announcement', 'view', array('Acl_Assert_OR_Argument', array('site_id' => 'site_id'))),
    array('national', 'announcement', 'view', array('Acl_Assert_OR_Argument', array('chapter_id' => array('site', 'chapter_id')))),

    // Finances
    // finance.manage: Base permissions to grant access to admin areas for finances section.
    // finance.view budgets: Allows users to view their chapter budgets
    // finance.manage budgets: Allows a user to manage budget categories and record transactions
    // finance_charge.add: Add charges to members.
    // finance_charge.edit: Edit a charge.
    array(array('admin', 'finance', 'national'), 'finance', 'manage'),
    array(array('admin', 'finance'), 'user', 'finances', array('Acl_Assert_OR_Argument', array('site_id' => 'site_id'))),
    array('national', 'user', 'finances', array('Acl_Assert_OR_Argument', array('chapter_id' => 'chapter_id'))),
    array('user', 'user', 'finances', array('Acl_Assert_OR_Argument', array('id' => 'id'))),
    array(array('admin', 'finance'), 'finance_charge', array('add', 'edit', 'delete')),
    array(array('admin', 'finance'), 'campaign', array('manage')),
    array(array('admin', 'finance'), 'campaign', array('edit'), array('Acl_Assert_OR_Argument', array('site_id' => 'site_id'))),

    // Budgets
    array(array('admin', 'finance'), 'budget', 'manage'),
    array(array('admin', 'finance'), 'budget', array('view', 'edit'), array('Acl_Assert_OR_Argument', array('site_id' => 'site_id'))),
    array('national', 'budget', array('view', 'edit'), array('Acl_Assert_OR_Argument', array('chapter_id' => array('site', 'chapter_id')))),

    // User Profile Viewing and Editting
    array('user', 'user', 'view', array('Acl_Assert_OR_Argument', array('site_id' => 'site_id'))), // Users can view other users from their own site.
    array('user', 'user', 'edit', array('Acl_Assert_OR_Argument', array('id' => 'id'))), // Users can only edit their own accounts.
    array('national', 'user', array('view', 'edit'), array('Acl_Assert_OR_Argument', array('chapter_id' => array('site', 'chapter_id')))), // Nationals can view/edit users of their chapter.
    array('admin', 'user', 'edit', array('Acl_Assert_OR_Argument', array('site_id' => 'site_id'))), // Admins can edit users from their own site.
    array('admin', 'user', 'manage'),

    // Recruitment
    array(array('admin', 'recruitment'), 'recruit', 'manage'),
    array(array('admin', 'recruitment'), 'recruit', array('view', 'edit', 'delete', 'admin'), array('Acl_Assert_OR_Argument', array('site_id' => 'site_id'))),
    array('national', 'recruit', array('view'), array('Acl_Assert_OR_Argument', array('chapter_id' => array('site', 'chapter_id')))),

    array(array('active', 'alumni'), 'recruit', 'view', array('Acl_Assert_OR_Argument', array('site_id' => 'site_id'))),
    array(array('active', 'alumni'), 'recruit', array('edit', 'delete'), array('Acl_Assert_OR_Argument', array('id' => 'user_id'))),
    array(array('active', 'alumni'), 'recruit', array('access', 'add')),

    // Community Service
    array(array('admin', 'service'), array('service_event', 'service_hour'), 'admin'),
    array(array('admin', 'service'), 'service_event', array('edit', 'delete'), array('Acl_Assert_OR_Argument', array('site_id' => 'site_id'))),
    array('user', array('service_hour', 'service_event'), 'add'),

    array(array('admin', 'service'), 'service_hour', array('edit', 'delete'), array('Acl_Assert_OR_Argument', array('site_id' => array('event', 'site_id')))),
    array('user', 'service_hour', array('edit', 'delete'), array('Acl_Assert_OR_Argument', array('id' => 'user_id'))),

    // SMS - Users with sms access can view sms stats and send sms messages.
    array(array('admin', 'sms'), 'sms', array('manage', 'send')),

    // File - File management.
    array(array('admin', 'files'), array('file'), array('manage')),
    array(array('admin', 'files'), array('folder', 'file'), array('view', 'edit', 'delete'), array('Acl_Assert_OR_Argument', array('site_id' => 'site_id'))),

    // Allow site admins to edit/delete only their own courses.
    array('admin', 'course', array('edit', 'delete'), array('Acl_Assert_OR_Argument', array('site_id' => 'site_id'))),
    array('user', 'course', array('edit', 'delete'), array('Acl_Assert_OR_Argument', array('id' => 'user_id'))),

    // Photos, allow site admin to delete file.
    array(array('admin'), 'album', array('edit', 'delete'), array('Acl_Assert_OR_Argument', array('site_id' => 'site_id'))),
    array(array('user'), 'album', array('edit', 'delete'), array('Acl_Assert_OR_Argument', array('id' => 'user_id'))),
    array('admin', 'file', array('edit', 'delete'), array('Acl_Assert_OR_Argument', array('site_id' => 'site_id'))),
    array('user', 'file', array('edit', 'delete'), array('Acl_Assert_OR_Argument', array('id' => 'user_id'))),
    ),
    'deny' => array(
        // ADD YOUR OWN DENY RULES HERE
    )
);

/**
 * Routes which are allowed for both anon and logged in users.
 */
$config['public_routes'] = array(
  'donate/index',
  'donate/form'
);

/**
 * Routes which are only available to non-logged in users.
 */
$config['public_only_routes'] = array(
  'login/login',
  'login/register',
  'login/reset',
  'login/setup',
);