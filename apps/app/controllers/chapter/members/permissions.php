<?php defined('SYSPATH') or die('No direct script access.');

class Permissions_Controller extends Members_Controller {
  
  /**
   * Configure administrators and permissions
   */
  public function index() {
    if ( ! A2::instance()->allowed($this->site, 'admin'))
      Event::run('system.403');

    if (isset($_GET['action'])) {
      $this->_permissions_modify();
    }
    $this->title = 'Configure Permissions';
    javascript::add('scripts/jquery.form.js');
    javascript::add('scripts/permissions.js');

    // Members for select list.
    #TODO FIX THE MEMBERS DROP DOWN LIST 
    $this->members = ORM::factory('user')->active_users_list();

    $roles = ORM::factory('role')->where('id !=', '1')->find_all();
    foreach ($roles as $role) {
      $users = ORM::factory('user')->find_by_role($role->id);
      $this->content .= View::factory('members/permissions/settings-role')->set('role', $role)->set('users', $users)->render();
    }
  }
  
  /**
   * Helper function to perform permission modifications.
   */
  private function _permissions_modify($action = 'add', $role_key = NULL, $user_id = NULL) {
    if ( ! A2::instance()->allowed($this->site, 'admin'))
      Event::run('system.403');

    $action = isset($action, $actions) ? $action : $_GET['action'];
    $role_key = isset($role_key) ? $role_key : $_GET['role_key'];
    $user_id = isset($user_id) ? $user_id : $_GET['user_id'];
    $allowed_actions = array('add', 'remove');

    if (valid::digit($user_id) && valid::standard_text($role_key) && in_array($action, $allowed_actions)) {
      $role = ORM::factory('role', $role_key);
      $target = 'form.'. $role->key;
      $message = '';
      $user = ORM::factory('user')->site_user($user_id);

      if ( ! $user) {
        $status = FALSE;
        $message = '<div class="notice">Error finding user. Please make sure this member exists.</div>';
      }
      
      if ($action == 'add') {
        if ($user->has_role($role->key)) {
          $status = FALSE;
          $message = '<div class="notice">* '. $user->name() .' already has this permission.</div>';
        }
        else {
          $user->add($role);
          $user->save();
          ORM::factory('notification')->sync_user_permissions($user);
          $status = TRUE;
          $message = View::factory('members/permissions/settings-role-member')->set('member', $user)->set('role', $role)->render();
        }
      }
      else if ($action == 'remove') {
        // Make sure the user is not removing themselves from the admin role.
        if ($user->id == $this->user->id AND $role->key == 'admin') {
          $status = FALSE;
          $message = 'You cannot remove yourself from the admin permission.';
        }
        else if ($user->has_role($role->key)) {
          $user->remove($role);
          $user->save();
          ORM::factory('notification')->sync_user_permissions($user);
          $status = TRUE;
        }
        else {
          $status = FALSE;
        }
      }
    }
    else {
      $status = FALSE;
      $message = 'Error. Please submit valid data.';
    }

    // Determine response type.
    if (request::is_ajax()) {
      response::json($status, $message, $target);
    }
    else {
      message::add($status, $message);
    }
  }
  
}