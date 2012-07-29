<?php defined('SYSPATH') or die('No direct script access.');

class Announcement_Model extends ORM implements Acl_Resource_Interface {
  
  protected $belongs_to = array('user', 'site');
  
  protected $has_and_belongs_to_many = array('groups');
  
  protected $sorting = array('created' => 'DESC');
  
  /**
   * Unpublish an announcement by changed the post_until field.
   */
  public function unpublish() {
    $this->post_until = date::to_db('-1 second');
    return $this->save();
  }
  
  /**
   * Model validation
   */
  public function validate(array &$array, $save = FALSE) {
    $array = Validation::factory($array)
      ->pre_filter('trim')
      ->add_rules('title', 'required', 'standard_text')        
      ->add_rules('message', 'required')
      ->add_rules('groups', 'is_array', 'required')
      ->add_callbacks('groups', array($this, '_groups_access'))
      ->add_rules('post_until', array('valid', 'date'), 'required');
    return parent::validate($array, $save);
  }
  
  public function _groups_access(Validation $array, $field) {
    $groups = ORM::factory('group')->where('site_id', kohana::config('chapterboard.site_id'))->find_keyed_array();
    foreach ($array[$field] as $id => $group_id) {
      if ($id != $group_id || ! array_key_exists($id, $groups)) {
        $array->add_error($field, 'access_denied');
      }
    } 
  }
  
  public function before_insert() {
    $this->user_id = kohana::config('chapterboard.user_id');
    $this->site_id = kohana::config('chapterboard.site_id');
    $this->created = date::to_db();
    $this->post_until = date::to_db($this->post_until);
  }
  
  /**
   * After a new announcement has been posted we send out emails.
   */
  public function after_insert() {
    $sent = array();
    foreach ($this->groups as $group) {
      foreach ($group->users as $user) {
        if ($user->status && $user->site_id == $this->site_id && ! $sent[$user->id]) {
          $sent[$user->id] = TRUE;
          email::announcement($user->email, $this->user->email, 'announcement', $this, $this->title);
        }
      }
    }
    log::system('notice', sprintf('Announcement from %s sent to %d users. [%s]', $this->site->name(), count($sent), $this->title));
  }
  
  /**
   * Gather the announcements for this user.  Access control is performed here since
   * it does not make sense to load announcement group permissions for each page load and
   * for all announcements.
   */
  public function find_by_site($site_id = NULL) {
    $site_id = is_null($site_id) ? kohana::config('chapterboard.site_id') : $site_id;
    $items = $this->where('site_id', $site_id)->where('post_until >', date::to_db())->find_all();
    $results = array();
    foreach ($items as $item) {
      if ($item->allowed()) {
        $results[] = $item;
      }
    }
    return $results;
  }
  
  /**
   * Retrieve a single announcement.  Just like in the get() method, access control
   * is manage through this accessor method.
   */
  public function show($id) {
    $this->where('id', $id)->find();
    foreach ($this->groups as $group) {
      if ($this->allowed()) {
        return $this;
      }
    }
    return FALSE;
  }
  
  // Properly determine whether the given user can view the announcement.
  public function allowed() {
    $user = A2::instance()->logged_in();
    // Admin allowed.
    if ($user->has_role('root') || ($user->has_role('national') && $user->chapter_id == $this->site->chapter_id)) {
      return TRUE;
    }
    else if ($user->has_role('admin') && $user->site_id == $this->site_id) {
      return TRUE;
    }
    // Allowed based on user group.
    foreach ($this->groups as $group) {
      if ($user->has_role($group->id)) {
        return TRUE;
      }
    }
    return FALSE;
  }
  
  public function get_resource_id() {
    return 'announcement';
  }
  
}