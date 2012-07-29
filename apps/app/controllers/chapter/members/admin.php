<?php defined('SYSPATH') or die('No direct script access.');

class Admin_Controller extends Members_Controller {
  
  function index($type = 'active') {
    if ( ! A2::instance()->allowed('user', 'manage'))
      Event::run('system.403');

    Router::$routed_uri = 'members/admin'; // Highlight the active tab.
    javascript::add('scripts/members.js');
    css::add('styles/members.css');
    $this->title = 'Manage Members';
    $this->view = 'members/admin/index';
    
    // Setup options for the current list of members.
    $this->status_options = array('' => ' ', 'active' => 'Active', 'alumni' => 'Alumni', 'pledge' => 'New Member', 'archive' => 'Archived');
    unset($this->status_options[$type]);

    // Filter by member type.
    switch ($type) {
      case 'active':
      case 'alumni':
      case 'pledge':
        $this->status = 1;
        $this->type = $type;
        break;
      case 'archive':
        $this->status = 0;
        break;
    }
    
    /**
     * Set member status.
     */
    if ($post = $this->input->post()) {
      $this->form = $post;
      // Allowed types.
      $types = Kohana::config('chapterboard.user_types') + array('archive' => 'Archived');
      foreach ($post as $user_id => $data) {
        // Make sure its a form field with user data.
        if (is_numeric($user_id)) {
          $user = ORM::factory('user', $user_id);
          if ($user->site_id == $this->site->id && $user->manage_member_save($data)) {
            $success = TRUE;
            if (array_key_exists($data['type'], $types)) {
              $type = $data['type'] == 'pledge' ? 'a new' : 'an '. $data['type'];
              message::add(TRUE, '%s is now %s member', $user->name(), $type);
            }
          }
          else {
            message::add(FALSE, 'Error changing %s\'s member type.  Please try again.', $user->name());
          }
        }
      }
      if ($success) {
        message::add(TRUE, 'Changes saved successfully.');
      }
    }

    if ($type == 'leadership') {
      $this->members = ORM::factory('user')->find_with_leadership();
      $this->type = 'leadership';
    }
    else {
      $this->members = ORM::factory('user')->search_profile($this->name, $this->type, $this->status);
    }

    foreach ($this->members as $member) {
      $this->form[$member->id]['student_id'] = $member->profile->student_id;
      $this->form[$member->id]['position'] = $member->profile->position;
      $this->form[$member->id]['scroll_number'] = $member->profile->scroll_number;
    }
    
    $this->groups = ORM::factory('group')->default_groups();
    $this->type_count = ORM::factory('user')->count_types();
    $this->types = Kohana::config('chapterboard.user_types');
    $this->type = $this->type ? $this->type: 'archived';
  }
  
  /**
   * Redirect back to index for undefined methods.
   */
  function __call($method, $args) {
    return $this->index($method);
  }
  
}