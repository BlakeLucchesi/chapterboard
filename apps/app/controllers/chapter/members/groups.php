<?php defined('SYSPATH') or die('No direct script access.');

class Groups_Controller extends Members_Controller {

  /**
   * Auth.
   */
  public function _pre_controller() {
    if ( ! A2::instance()->allowed('user', 'manage'))
      Event::run('system.403');
  }

  /**
   * Show a list of all the groups
   */
  public function index() {
    $this->title = 'Groups';
    $this->groups = ORM::factory('group')->custom_groups();
    
    if ($post = $this->input->post()) {
      $this->group = ORM::factory('group');
      if ($this->group->validate($post, TRUE)) {
        message::add(TRUE, Kohana::lang('form_group.success'));
        url::redirect('members/groups/'. $this->group->id);
      }
      else {
        message::add(FALSE, Kohana::lang('form_group.errors'));
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_group');
      }
    }
  }
  
  /**
   * Show the details of a group.
   */
  public function show($id) {
    Router::$routed_uri = 'members/groups';
    javascript::add('jquery/jquery.autocomplete.js');
    javascript::add('jquery/jquery.bgiframe.min.js');
    javascript::add('scripts/groups.js');
    
    $arg = Router::$arguments[0];
    javascript::add("Kohana.groups_url = \"/members/groups/members/$arg\";", 'inline');
    $this->group = ORM::factory('group', $id);
    if ( ! $this->group->loaded)
      Event::run('system.404');

    $this->title = sprintf('Group: %s', $this->group->name);
    $this->users = $this->group->users;
    $this->group->reload_columns(TRUE);
  }
  
  /**
   * Show a partial with a list of members.
   */
  public function members($group_id) {
    $this->group = ORM::factory('group', $group_id);
    if ($name = $this->input->post('name')) {
      $this->user = ORM::factory('user')->where('site_id', $this->site->id)->where('searchname', text::searchable($name))->where('status', 1)->find();
      if ($this->user->loaded) {
        $this->user->add($this->group);
        $this->user->save();
      }
    }
    $this->group->reload();
    $this->users = $this->group->users;
    print View::factory('members/groups/members')->render();
    die();
  }
  
  /**
   * Remove a member from a group.
   */
  public function remove($group_id, $user_id) {
    $this->group = ORM::factory('group', $group_id);
    $this->user = ORM::factory('user', $user_id);
    
    if ( ! $this->group->loaded || ! $this->user->loaded)
      Event::run('system.404');
    if ($this->user->site_id != $this->site->id || $this->group->site_id != $this->site->id)
      Event::run('system.403');
      
    $this->user->remove($this->group);
    $this->user->save();
    url::redirect('members/groups/'. $this->group->id);
  }
  
  /**
   * Delete group.
   */
  public function destroy($id) {
    $this->group = ORM::factory('group', $id);
    if ( ! $this->group->loaded)
      Event::run('system.404');
    if ( ! $this->group->site_id == $this->site->id)
      Event::run('system.403');
    
    if ( ! $this->group->static_key) {
      message::add(TRUE, 'Group %s removed succesfully.', $this->group->name);
      $this->group->destroy();
      url::redirect('members/groups');
    }
    else {
      message::add(FALSE, 'You cannot delete a static group');
    }
  }

}