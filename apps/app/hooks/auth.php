<?php defined('SYSPATH') or die('No direct script access.');

class auth {
  
  function __construct() {
    Event::add('system.ready', array('auth', 'setup_globals'));
    Event::add('system.ready', array('auth', 'setup_permissions'));
  }

  /**
   * Setup global variables based on a user's session.
   *
   * It's important to note that during cron/background jobs, none of these
   * system variables get set.  In those situations you will likely set the system
   * variables on your own and then call auth::setup_permissions();
   */
  public static function setup_globals() {
    $auth = A1::instance();
    $session = Session::instance();    
    if ($user = $auth->get_user()) {

      Kohana::config_set('chapterboard.user_id', $user->id);
      
      // If we are teleported into a site setup those vars, otherwise use the user's site values.
      if ($site_id = $session->get('site_id')) {
        $site = ORM::factory('site', $site_id);
        $type = $site->type;
        Kohana::config_set('chapterboard.site_id', $site->id);
        Kohana::config_set('chapterboard.school_id', $site->school_id);
        Kohana::config_set('chapterboard.chapter_id', $site->chapter_id);
        Kohana::config_set('locale.site_timezone', $site->timezone);
        Kohana::config_set('locale.language', array($site->locale(), strtoupper($site->locale())));
      }
      else {
        $type = $user->site->type;
        Kohana::config_set('chapterboard.site_id', $user->site->id);
        Kohana::config_set('chapterboard.school_id', $user->site->school_id);
        Kohana::config_set('chapterboard.chapter_id', $user->site->chapter_id);
        Kohana::config_set('locale.site_timezone', $user->site->timezone);
        Kohana::config_set('locale.language', array($user->site->locale(), strtoupper($user->site->locale())));
      }
      Kohana::config_set('core.override_path', $type .'/');
    }
  }

  /**
   *  Reloads permissions based on the currently set site_id.
   *
   *  We load all of the dynamic ACL settings as configured by
   *  the site administrator from the group_rules table and merge 
   *  them with the static data that we have defined in the config/a2 file.
   *
   *  Use this class method to reload permissions when doing background jobs.
   */
  public static function setup_permissions() {
    $acl = A2::instance('a2', TRUE);
    
    // Define the available roles. We call site specific roles groups, 
    // wheras roles are used for hard coded administrative tasks.
    $groups = ORM::factory('group')->where('site_id', kohana::config('chapterboard.site_id'))->find_all();
    foreach ($groups as $group) {
      $acl->add_role($group->id);
    }

    $rules = ORM::factory('group_rule')->where('site_id', kohana::config('chapterboard.site_id'))->find_all();
    foreach ($rules as $rule) {
      $resource_ids = empty($rule->resource_ids) ? NULL : unserialize($rule->resource_ids);
      if ($resource_ids) {
        $acl->allow($rule->group_id, $rule->resource, $rule->privilege, new Acl_Assert_Static_Resource_Id(array('id' => $resource_ids)));
      }
      else {
        $acl->allow($rule->group_id, $rule->resource, $rule->privilege);
      }
    }
  }
}

new auth;