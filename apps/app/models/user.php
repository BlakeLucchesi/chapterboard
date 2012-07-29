<?php defined('SYSPATH') or die('No direct script access.');
 
class User_Model extends A1_User_Model implements Acl_Role_Interface, Acl_Resource_Interface {
  
  protected $belongs_to = array('site');
  protected $has_many = array('topic', 'comment', 'notice', 'file', 'message', 'signups', 'sms_sent', 'sms');
  protected $has_one = array('profile');
  protected $has_and_belongs_to_many = array('messages', 'roles', 'groups');
  
  public $sorting = array('searchname' => 'ASC');
  
  // Define our member types for validation when setting a member type.
  public static $types = array('pledge' => 'New Member', 'active' => 'Active', 'alumni' => 'Alumni');

  public $default_picture_filename = '_member.gif';

  /**
   * Loop through roles to see if the user already has an associated role.
   */
  function has_role($role_key) {
    foreach ($this->get_role_id() as $role) {
      if ($role == $role_key) {
        return TRUE;
      }
    }
    return FALSE;
  }
    
  /**
   * Find a user by user_id and current site_id.
   */
  function site_user($id = NULL) {
    if ( ! is_numeric($id))
      return FALSE;
    $this->where('site_id', kohana::config('chapterboard.site_id'))->where('id', $id)->find();
    return $this->loaded ? $this : FALSE;
  }
  
  function get($user_id, $status = 1, $site_id = NULL) {
    $site_id = is_null($site_id) ? kohana::config('chapterboard.site_id') : $site_id;
    return $this->where('id', $user_id)->where('site_id', $site_id)->where('status', $status)->find();
  }
  
  
  ###########################################
  ### User Level Data Operations
  ###########################################
  
  /**
   * Display the member's full name.
   */
  public function name($link = FALSE) {
    // if ($this->has_role('root')) {
    //   return $this->first_name .' (ChapterBoard Team)';
    // }
    $name = ucwords($this->first_name) .' '. ucwords($this->last_name); 
    if ($link) {
      return html::anchor('profile/'. $this->id, $name);
    }
    return $name;
  }

  public function picture() {
    return $this->picture ? $this->picture : $this->default_picture_filename;
  }

  public function email($link = FALSE) {
    if ($link)
      return html::mailto($this->email);
    return $this->email;
  }
  
  /**
   * Output a formatted version of the user's phone number. 
   * 
   * @param boolean  Whether or not to output the phone as an html5 call link.
   */
  public function phone($call_link = FALSE) {
    if ($call_link) {
      return '<a href="tel:'.$this->profile->phone.'">'. format::phone($this->profile->phone) .'</a>';
    }
    return format::phone($this->profile->phone);
  }
  
  public function birthday($format = NULL) {
    if ($format == 'age') {
      // To find the upcoming age after their birthday just subtract this year from birth year.
      $years = date("Y") - date("Y", strtotime($this->profile->birthday));
      return sprintf('%d years old', $years);
    }
    $format = $format ? $format : 'F jS, Y';
    return date::display($this->profile->birthday, $format, FALSE);
  }
  
  public function balance() {
    $cache = new Cache;
    $total = $cache->get('user:balance:'. $this->id);
    if (is_null($total)) {
      $total = 0;
      foreach (ORM::factory('finance_charge_member')->unpaid($this->id) as $result) {
        $total += $result->amount - $result->payments->sum('amount');
      }
      $cache->set('user:balance:'. $this->id, $total);
    }
    return $total;
  }
  
  public function shirt_size($full = TRUE) {
    $sizes = $full ? Kohana::config('chapterboard.shirt_sizes') : Kohana::config('chapterboard.shirt_sizes_chars');
    return $sizes[$this->profile->shirt_size];
  }
  
  public function initiated_in() {
    return $this->profile->initiation_year;
  }
  
  public function type() {
    if ($this->status && $this->type) {
      return self::$types[$this->type];
    }
    return 'Archived';
  }
  
  /**
   * Manage the display of help text for new users.
   */
  public function help($key, $hide = FALSE) {
    $help = unserialize($this->help);
    if ( ! $help[$key]) {
      if ($hide) {
        $help[$key] = 1;
        $this->help = serialize($help);
        $this->save();
      }
      return message::help($key);
    }
  }
  
  /**
   * Display the users last login time.
   */
  public function last_login($type = 'short', $format = NULL) {
    return $this->last_login ? date::display($this->last_login, $type, $format) : 'Never Logged In';
  }
  
  /**
   * Load profile
   */
  public function profile() {
    return $this->with('profile');
  }
  
  /**
   * Return any notices for the user in question.
   */
  function notices() {
    $a2 = A2::instance();
    $notices = array();
    
    if ($this->messages->count()) {
      $notices['messages'] = array(
        'icon' => 'email.png',
        'message' => html::anchor('messages', 'You have new messages', array('id' => 'message-link'))
      );
    }
    return $notices;
  }
  
  /**
   * Find user by calendar token.
   */
  function find_by_token($token) {
    return $this->where('calendar_token', $token)->find();
  }
  
  /**
   * Calendar token
   */
  function calendar_token($reset = FALSE) {
    if ( ! $this->calendar_token || $reset) {
      $this->calendar_token = sha1($this->id . text::random('alnum', 24));
      $this->save();
    }
    return $this->calendar_token;
  }  
  
  /**
   * Password recovery
   */
  function password_recover($email) {
    $user = $this->where('email', $email)->find();
    if ($user->loaded) {
      $new_password = text::random('distinct', 6);
      $user->password = $new_password;
      $user->save();
      $vars = array('new_password' => $new_password) + $user->as_array();
      email::notify($user->email, 'user_password', $vars);
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  ###########################################
  ### Validation and Callbacks
  ###########################################
  
  /**
   * Validates and optionally saves a new user record from an array.
   *
   * @param  array    values to check
   * @param  boolean  save[Optional] the record when validation succeeds
   * @return boolean
   */
  public function validate_login_update(array &$array, $save = FALSE) {
    // Initialise the validation library and setup some rules
    $array = Validation::factory($array)
      ->pre_filter('trim')
      ->add_rules('password', 'length[6,64]')
      ->add_rules('password_confirm', 'matches[password]')
      ->add_rules('email', 'required', 'email')
      ->add_callbacks('email', array($this, '_update_unique_mail'));
    if (A2::instance()->allowed('user', 'edit_name')) {
      $array->add_rules('first_name', 'required')
            ->add_rules('last_name', 'required');
    }
    return parent::validate($array, $save);
  }
  
  /**
   * Validates and optionally saves a new user record from an array.
   *
   * @param  array    values to check
   * @param  boolean  save[Optional] the record when validation succeeds
   * @return boolean
   */
  public function validate(array &$array, $save = FALSE) {
    // Initialise the validation library and setup some rules
    $array = Validation::factory($array)
      ->pre_filter('trim')
      ->add_rules('first_name', 'required', 'length[2,64]')
      ->add_rules('last_name', 'required', 'length[2,64]')
      ->add_rules('password', 'required', 'length[6,64]')
      ->add_rules('password_confirm', 'required', 'matches[password]')
      ->add_rules('email', 'required', 'email')
      ->add_rules('agreement', 'required', 'numeric')
      ->add_rules('type', 'required', 'standard_text')
      ->add_rules('site_id', 'required', 'digit')
      ->add_callbacks('email', array('User_Model', '_unique_mail'));
    return parent::validate($array, $save);
  }

  /**
   * Verify uniqueness of email addresses.
   */
  public static function _unique_mail(Validation &$array, $field) {
    $user = ORM::factory('user')->where('email', $array[$field])->find();
    if ($user->id) {
      $array->add_error($field, 'unique');
    }
  }
  
  public function _update_unique_mail(Validation &$array, $field) {
    $user = ORM::factory('user')->where('email', $array[$field])->where('id !=', $this->id)->find_all();
    if ($user->count()) {
      $array->add_error($field, 'unique');
    }
  }

  /**
   * Before insert hook.
   */
  public function before_insert() {
    $this->status = 1;
    $this->created = date::to_db();
    $this->updated = date::to_db();
    $this->agreement = date::to_db();
    $this->_update_searchname();
  }
  
  /**
   * After insert hook.
   */
  public function after_insert() {
    $profile = ORM::factory('profile');
    $profile->user_id = $this->id;
    $profile->save();
    $vars = array(
      'name' => $this->first_name,
      'site_name' => $this->site->name()
    );
    email::notify($this->email, 'user_welcome', $vars);
  }

  /**
   * Before update hook.
   */
  public function before_update() {
    $this->updated = date::to_db();
    $this->_update_searchname();
    
    // Delete old caches of profile resizes.
    if ($this->changed['picture']) {
      $old = ORM::factory('user')->where('id', $this->id)->find();
      image::clear_cache($old->picture);
      unlink(Kohana::config('upload.directory').DIRECTORY_SEPARATOR.$old->picture);
    }
  }

  /**
   * New admin
   */
  public function new_admin($vars, $site) {
    $this->first_name = $vars['first_name'];
    $this->last_name = $vars['last_name'];
    $this->password = $vars['password'];
    $this->email = $vars['email'];
    $this->site_id = $site->id;
    $this->type = 'active';
    $this->add(ORM::factory('role', 'admin'));
    $group = ORM::factory('group')->where('site_id', $site->id)->where('static_key', 'active')->find_all()->current();
    $this->add($group);
    $this->save();
    
    $this->profile->phone = preg_replace('/[^0-9]/i', '', $vars['phone']);
    $this->profile->save();
    
    $site->user_id = $this->id;
    $site->save();

    return $this;
  }

  /**
   * Save updated information from the manage members form.
   */
  public function manage_member_save($data) {
    $this->profile->student_id = $data['student_id'];
    $this->profile->scroll_number = $data['scroll_number'];
    $this->profile->position = $data['position'];
    $this->profile->save();
    
    // If type is being changed we need to remove current static group (active, alumni, or pledge).
    $types = Kohana::config('chapterboard.user_types') + array('archive' => 'Archived');
    if ($data['type'] && array_key_exists($data['type'], $types)) {
      foreach ($this->groups as $group) {
        if ($group->static_key) {
          $this->remove($group);
        }
      }

      if ($data['type'] == 'archive') {
        $this->status = 0;
      }
      else {
        $this->status = 1;
        $this->add(ORM::factory('group', $data['type']));
      }
      $this->type = $data['type'];
      $this->save();
      ORM::factory('notification')->sync_user_permissions($this);
    }
    return TRUE;
  }

  ###########################################
  ### Member List Queries
  ###########################################
  
  /**
   * Find users with a specific role.
   *
   * @param int|string|array find users from a site based on role_id or role key.
   * integer role id
   * string  role key
   * array   strings of role keys
   */
  function find_by_role($role_keys) {
    if (is_array($role_keys)) {
      $results = ORM::factory('role')->in('key', $role_keys)->find_all();
      foreach ($results as $result) {
        $roles[] = $result->id;
      }
    }
    else if ( ! is_numeric($role_keys)) {
      $roles[] = $this->db->query("SELECT id FROM roles WHERE `key` = ?", array($role_keys))->current()->id;
    }
    else if (is_numeric($role_keys)) {
      $roles[] = $role_keys;
    }
    return $this->with('profile')->join('roles_users', 'roles_users.user_id', 'users.id', 'INNER')->in('roles_users.role_id', $roles)->where('users.site_id', kohana::config('chapterboard.site_id'))->groupby('roles_users.user_id')->find_all();
  }

  /**
   * Find with leadership.
   */
  public function find_with_leadership($site_id = NULL) {
    $site_id = is_null($site_id) ? kohana::config('chapterboard.site_id') : $site_id;
    return ORM::factory('user')->with('profile')->where('profile.position >', '')->where('site_id', $site_id)->where('status', 1)->find_all();
  }

  /**
   * Find by phone number.
   */
  public function find_by_phone($number) {
    $user = $this->with('profile')->where('profile.phone', $number)->find();
    if ($user->loaded) {
      return $user;
    }
    return FALSE;
  }
  
  /**
   * Find by email.
   */
  public function find_by_email($email) {
    return $this->where('email', $email)->find();
  }
  
  public function find_user_by_chapter($chapter_id, $params = array()) {
    $valid_params = array('name', 'type', 'site_id');
    $params = array_intersect_key($params, array_flip($valid_params));
    $params = array_filter($params);
    $name = text::searchable($params['name']);
    if ( ! $name)
      return $this->find_all(0);
    $this->join('sites', 'users.site_id', 'sites.id', 'LEFT')->where('sites.chapter_id', $chapter_id);
    foreach ($params as $key => $value) {
      if ($key == 'name') {
        $this->like('searchname', $name);
      }
      else {
        $this->where('users.'. $key, $value);
      }
    }
    return $this->find_all(100, 0);
  }
  
  /**
   * Generate a list of upcoming birthdays
   */
  public function upcoming_birthdays() {
    return $this->with('profile')->select('users.*, DAYOFYEAR(`profile`.`birthday`) as days')
      ->custom_where('DAYOFYEAR(CURDATE()) <= DAYOFYEAR(`profile`.birthday) AND DAYOFYEAR(CURDATE() + 8) >= DAYOFYEAR(`profile`.`birthday`)')
      ->where('site_id', kohana::config('chapterboard.site_id'))
      ->where('status', TRUE)
      ->orderby('days', 'ASC')
      ->find_all();
  }
  
  /**
   * List of members for roster
   */
  function roster($type = NULL) {
    $statuses = Kohana::config('chapterboard.user_statuses');
    $this->where('site_id', kohana::config('chapterboard.site_id'));
    if (array_key_exists($type, $statuses)) {
      $this->where('type', $type);
    }
    else {
      $type = 'pending';
      $status = 0;
    }
    $status = array_pop(array_keys($statuses, $type));
    return $this->where('status', $status)->find_all();
  }
  
  /**
   * Provide list of current active users.
   */
  public function active_users_list() {
    $users = $this->where('site_id', kohana::config('chapterboard.site_id'))->orderby('searchname', 'ASC')
      ->where('status', 1)
      ->find_all();
    foreach ($users as $user) {
      $results[$user->id] = $user->name();
    }
    return $results;
  }

  /**
   * Other Members
   */
  public function other_members() {
    $users = $this->select('id, first_name, last_name')
      ->where('status', 1)
      ->where('id!=', kohana::config('chapterboard.user_id'))
      ->where('site_id', kohana::config('chapterboard.site_id'))
      ->find_all();
      
    foreach ($users as $user) {
      $options[$user->id] = $user->first_name .' '. $user->last_name;
    }
    
    return $options;
  }
  
  /**
   * Provides a user search for members page.
   */
  public function search_profile($name = NULL, $type = null, $status = NULL, $site_id = NULL, $extra = array()) {
    $site_id = is_null($site_id) ? kohana::config('chapterboard.site_id') : $site_id;
    $this->with('profile');
    if ($name) {
      $this->like('searchname', text::searchable($name));      
    }
    if ($type && $type != 'all') {
      $this->where('type', $type);
    }
    if ($extra['major']) {
      $this->where('major', $extra['major']);
    }
    if ($extra['department']) {
      $this->where('department', $extra['department']);
    }
    
    switch ($status) {
      case 'approval':
        $status = 0;
        break;
      case 'archive':
        $status = 2;
        break;
      default:
        $status = 1;
    }
    $this->where('status', $status);
    $this->where('site_id', $site_id);
    $this->orderby('searchname', 'ASC');
    return $this->find_all();
  }
  
  /**
   * Provides a simple name search for json response.
   */
  public function search_names($name, $status = 1) {
    $this->like('searchname', text::searchable($name));          
    $this->where('site_id', kohana::config('chapterboard.site_id'));
    $this->where('status', $status);
    foreach ($this->find_all() as $user) {
      $results[] = array(
        'id' => $user->id,
        'name' => $user->name(),
      );
    }
    return $results;
  }

  public function count_types($site_id = NULL) {
    $site_id = is_null($site_id) ? kohana::config('chapterboard.site_id') : $site_id;
    
    // Defaults
    $data = array('pledge' => 0, 'active' => 0, 'alumni' => 0, 'archive' => 0);

    // Status counts
    $results = $this->select('COUNT(id) as count, status')
      ->where('site_id', $site_id)
      ->where('status', 0)
      ->groupby('status')
      ->find_all();
    
    foreach ($results as $result) {
      $data['archive'] = $result->count;
    }
    
    // Type counts
    $results = $this->select('COUNT(id) as count, type')
      ->where('site_id', $site_id)
      ->where('status', 1)
      ->groupby('type')
      ->find_all();
    $count = 0;
    foreach ($results as $result) {
      $data[$result->type] = $result->count;
      $count += $result->count;
    }
    $data['all'] = $count;
    
    // Leadership Count
    $leadership = ORM::factory('user')->with('profile')->where('profile.position >', '')->where('site_id', $site_id)->find_all();
    $data['leadership'] = $leadership->count();
    return $data;
  }

  /**
   * Build a set of member statistics.
   *
   * Return an array of histogram data about the members in the result.
   *
   */
  function get_statistics($members) {
    $data = array();
    foreach ($members as $member) {
      $data['types'][$member->type]++;
      $data['shirt_size'][$member->profile->shirt_size]++;
      $data['year'][$member->profile->school_year]++;
      $data['initiation_year'][text::searchable($member->profile->initiation_year)]['name'] = $member->profile->initiation_year;
      $data['initiation_year'][text::searchable($member->profile->initiation_year)]['count']++;
      if ($member->profile->department) {
        $data['departments'][$member->profile->department]++;
      }
      if ($member->profile->major) {
        $data['majors'][$member->profile->major]++;
      }
    }
    ksort($data['shirt_size']);
    krsort($data['initiation_year']);
    ksort($data['departments']);
    ksort($data['majors']);
    return $data;
  }
    
  public function select_members($user = NULL) {
    $results[$user->id] = 'Me ('. $user->name() .')';
    $results[''] = '----------';
    $members = $this->where('site_id', kohana::config('chapterboard.site_id'))->where('status', 1)->where('id !=', $user->id)->orderby('searchname', 'ASC')->find_all();
    foreach ($members as $member) {
      $results[$member->id] = $member->name();
    }
    return $results;
  }
  
  /**
   * Creates a key/value array from all of the objects available. Uses find_all
   * to find the objects.
   *
   * @param   string  key column
   * @param   string  value column
   * @return  array
   */
  public function select_list($key = NULL, $val = NULL)
  {
    if ($key === NULL) {
      $key = $this->primary_key;
    }
    if ($val === NULL) {
      $val = $this->primary_val;
    }
    // Return a select list from the results
    return $this->select($key, $val)->find_all()->select_list($key, $val);
  }
  
  /**
   * Select list of types
   */
  public function types_list() {
    $types = array('0' => '- All Members -');
    foreach (Kohana::config('chapterboard.user_types') as $key => $type) {
      $types[$key] = ucwords(inflector::plural($type));
    }
    return $types;
  }
  
  /**
   * Update the searchname field.
   */
  private function _update_searchname() {
    $this->searchname = sprintf('%s%s', text::searchable($this->first_name), text::searchable($this->last_name));
  }
  
  public function __get($column) {
    if ($column == 'chapter_id')
      return $this->site->chapter_id; //Kohana::config('chapterboard.chapter_id');
    return parent::__get($column);
  }
  
  /**
   * Return ACL Role Interface roles.
   */  
  public function get_role_id() {
    $roles = array();
    $roles[] = 'user';
    $roles[] = $this->type;
    foreach ($this->roles as $role) {
      $roles[] = $role->key;
    }
    foreach ($this->groups as $group) {
      $roles[] = $group->id;
    }
    if ($this->site->user_id == $this->id) {
      $roles[] = 'admin';
    }
    if ($this->site->type == 'national') {
      $roles[] = 'admin';
      $roles[] = 'national';
    }
    if ($this->id == 3940) {
      $roles[] = 'national';
    }
    return $roles;
  }
  
  /**
   * Return ACL Resource Interface
   */
  public function get_resource_id() {
    return 'user';
  }
  
}
