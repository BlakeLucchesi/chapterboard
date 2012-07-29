<?php defined('SYSPATH') or die('No direct script access.');

class Group_Model extends ORM {
  
  protected $has_and_belongs_to_many = array('users', 'calendars');
  
  protected $has_many = array('group_rules', 'invites');
  
  protected $sorting = array('name' => 'ASC');
  
  public function sms_key() {
    return "@$this->sms_key";
  }
  
  /**
   * Override ORM->find_all().
   *
   * Filter results to only groups from the current site.
   */
  public function find_all($limit = NULL, $offset = NULL) {
    if ($site_id = kohana::config('chapterboard.site_id')) {
      $this->where('site_id', $site_id);
    }
    return parent::find_all($limit, $offset);
  }
  
  public function find($id = NULL) {
    if (kohana::config('chapterboard.site_id') > 0) {
      $this->where('site_id', kohana::config('chapterboard.site_id'));
    }
    return parent::find($id);
  }
  
  /**
   * Return only the custom groups created for this site.
   */
  public function custom_groups() {
    return $this->where('static_key', '')->find_all();
  }
  
  /**
   * Return default groups for this site.
   */
  public function default_groups() {
    return $this->where('site_id', kohana::config('chapterboard.site_id'))->where('static_key !=', '')->find_all();
  }

  /**
   * Destroy the group and all associated data.
   */
  public function destroy() {
    if ( ! $this->static_key) {
      $this->db->query("DELETE FROM announcements_groups WHERE group_id = ?", $this->id);
      $this->db->query("DELETE FROM group_rules WHERE group_id = ?", $this->id);
      $this->db->query("DELETE FROM groups_users WHERE group_id = ?", $this->id);
      $this->delete();
      return TRUE;
    }
    return FALSE;
  }
  
  /**
   * Validation.
   */
  public function validate(array &$array, $save = FALSE) {
    $array = Validation::factory($array)
      ->pre_filter('trim')
      ->add_rules('name', 'required', 'standard_text');
    return parent::validate($array, $save);
  }

  /**
   * Validate the form to edit just the sms_key.
   */
  public function validate_sms_key(array &$array, $save = FALSE) {
    $array = Validation::factory($array)
      ->pre_filter('trim')
      ->add_rules('sms_key', 'alpha_numeric', 'length[3,20]');
    return parent::validate($array, $save);
  }
  
  public function before_insert() {
    if ( ! $this->site_id) {
      $this->site_id = kohana::config('chapterboard.site_id');
    }
    if ( ! $this->sms_key) {
      $this->sms_key = $this->generate_sms_key();
    }
  }
  
  public function before_update() {
    if ( ! $this->sms_key) {
      $this->sms_key = $this->generate_sms_key();
    }
  }
  
  /**
   * Generate an sms key based on the group's name.
   */
  public function generate_sms_key() {
    $str = preg_replace('/[^0-9a-zA-Z]/i', '', $this->name);
    return strtolower($str);
  }
  
  /**
   * Valid helper
   */
  public static function valid_group(Validation $array, $field) {
    $group = ORM::factory('group')->where('site_id', kohana::config('chapterboard.site_id'))->where('id', $array[$field])->find();
    if ( ! $group->loaded)
      $array->add_error($field, 'permission_denied');
  }
  
  public function default_groups_select() {
    $results = $this->default_groups();
    foreach ($results as $group) {
      $groups[$group->id] = ucwords(inflector::singular($group->name));
    }
    return $groups;
  }
  
  /**
   * Make sure we load only active users.
   */
  public function __get($column) {
    if ($column == 'users') {
      $this->where('users.status', 1);
    }
    return parent::__get($column);
  }
  
  /**
   * Load values based on the static_key column if the lookup value is
   * a string and not an integer.
   */
  public function unique_key($id = NULL)
  {
    if ( ! empty($id) AND is_string($id) AND ! ctype_digit($id) ) {
      return 'static_key';
    }
    return parent::unique_key($id);
  }
}