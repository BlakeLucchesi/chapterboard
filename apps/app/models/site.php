<?php defined('SYSPATH') or die('No direct script access.');

class Site_Model extends ORM implements Acl_Resource_Interface {
  
  protected $has_many = array('users', 'forums', 'calendars', 'site_payments', 'sms', 'service_hours', 'deposit_accounts');

  protected $belongs_to = array('school', 'chapter', 'user');

  # Psuedo property used in validate_account_setup().
  protected $confirm_token, $password, $password_confirm;

  public function find_by_slug($slug) {
    return $this->where('slug_lower', strtolower($slug))->find();
  }

  /**
   * Instance Methods
   */
  public function name() {
    switch ($this->id) {
      case 1:
        return 'ChapterBoard Admin';
      case 29:
        return 'ChapterBoard Demo';
      default:
        if ($this->type == 'national') {
          return sprintf('%s %s', $this->chapter->name, $this->chapter_name);
        }
        else {
          return sprintf('%s at %s', $this->chapter->name, $this->school->name);
        }
    }
  }
  
  public function slug() {
    return $this->slug_lower;
  }
  
  public function chapter_name() {
    if ($this->type == 'chapter') {
      return sprintf('%s (%s)', $this->chapter_name, $this->school->name);
    }
    else {
      return $this->chapter_name;
    }
  }
  
  public function fundraising_enabled() {
    return $this->fundraising_enabled;
  }
  
  public function collections_enabled() {
    return $this->collections_enabled;
  }
  
  public function bank_on_file() {
    return (bool) $this->db->query("SELECT id FROM deposit_accounts WHERE site_id = ? AND status = 1", array($this->id))->current();
  }
  
  public function fee_credit() {
    return $this->fee_credit ? number_format($this->fee_credit, 2) .'%' : '0.00%';
  }
  
  public function fee_echeck() {
    return $this->fee_echeck ? number_format($this->fee_echeck, 2) .'%' : '0.00%';
  }
  
  public function deposit_accounts() {
    return ORM::factory('deposit_account')->where('status', 1)->where('site_id', $this->id)->find_all();
  }
  
  
  /**
   * Setup account to have fundraising enabled.
   */
  public function fundraising_setup() {
    $this->fundraising_enabled = TRUE;
    $this->save();
  }
  /**
   * Enable finances for the site and update all existing
   * charges to point to this deposit account.
   */
  public function collections_setup() {
    $this->collections_enabled = TRUE;
    $this->save();
    
    $deposit_account_id = $this->deposit_accounts->current()->id;
    // If this is their first deposit account, make sure all existing charges
    // in the system point to this deposit account.
    if (ORM::factory('deposit_account')->where('site_id', $this->id)->find_all()->count() == 1) {
      $this->db->query("UPDATE finance_charges SET deposit_account_id = ? WHERE site_id = ?", array($deposit_account_id, $this->id));
    }
  }
  

  public function locale() {
    if ($this->id == 29 || $this->chapter_id == 0)
      return 'sorority';
    return $this->chapter->type ? $this->chapter->type : 'en_US';
  }

  public function is_national() {
    return (bool) ($this->type == 'national');
  }
  
  public function is_chapter() {
    return (bool) ($this->type == 'chapter');
  }
    
  /**
   * Yearly fees and renewals.
   */
  public function status() {
    if ($this->status) {
      return $this->renewal_date > date::now() ? 'Current' : 'Past Due';
    }
    else {
      return 'Suspended';
    }
  }

  public function renewal_notice() {
    $difference = date::timespan(strtotime($this->renewal_date), date::now(NULL, 'U'), 'days');
    if ($difference < 20) {
      return Kohana::lang('chapterboard.site.renewal', array($difference));
    }
  }
  
  /**
   * Custom Aggregation Queries
   */
  public function users_by_chapter($chapter_id) {
    return $this->select(" 
      (SELECT COUNT(*) FROM users WHERE status = 1 AND type = 'active' AND site_id = sites.id) as actives,
      (SELECT COUNT(*) FROM users WHERE status = 1 AND type = 'alumni' AND site_id = sites.id) as alumni,
      (SELECT COUNT(*) FROM users WHERE status = 1 AND type = 'pledge' AND site_id = sites.id) as pledges, sites.*")
      ->join('users', 'users.site_id', 'sites.id', 'LEFT')
      ->orderby('chapter_name', 'ASC')
      ->where('chapter_id', $chapter_id)
      ->where('sites.type', 'chapter')
      ->groupby('sites.id')
      ->find_all();
  }
  
  public function finances_by_chapter($chapter_id) {
    return $this->select("(SELECT SUM(finance_charge_members.amount)) as balance, (SELECT COUNT(*)) as past_due, sites.*")
      ->join('finance_charge_members', 'finance_charge_members.site_id', 'sites.id', 'LEFT')
      // ->join('finance_payments', 'finance_payments.site_id', 'sites.id', 'LEFT')
      ->orderby('chapter_name', 'ASC')
      ->where('finance_charge_members.paid', FALSE)
      ->where('chapter_id', $chapter_id)
      ->where('sites.type', 'chapter')
      ->groupby('sites.id')
      ->find_all();
  }
  
  /**
   * Validation.
   */
  public function validate_account_setup(array &$array, $save = FALSE) {
    $array['slug'] = str_replace(' ', '-', $array['slug']);
    $array['slug'] = preg_replace('/[^0-9a-zA-Z-]/i', '', $array['slug']);
    $array = Validation::factory($array)
      ->add_rules('confirm_token', 'required')
      ->add_rules('password', 'required', 'length[6,24]')
      ->add_rules('password_confirm', 'required', 'matches[password]')
      ->add_rules('timezone', 'required')
      ->add_rules('slug', 'required', 'standard_text', 'length[3,50]')
      ->add_callbacks('confirm_token', array($this, '_valid_token'))
      ->add_callbacks('slug', array($this, '_unique_slug'))
      ->add_callbacks('slug', array($this, '_unreserved_slug'));
      return parent::validate($array, $save);
  }
  
  public function validate_account_update(array &$array, $save = FALSE) {
    $array['slug'] = str_replace(' ', '-', $array['slug']);
    $array['slug'] = preg_replace('/[^0-9a-zA-Z-]/i', '', $array['slug']);
    $array = Validation::factory($array)
    ->pre_filter('trim')
    ->add_rules('user_id', 'required', 'numeric')
    ->add_rules('slug', 'required', 'standard_text', 'length[3,50]')
    ->add_callbacks('user_id', array($this, '_valid_user'))
    ->add_callbacks('slug', array($this, '_unique_slug'))
    ->add_callbacks('slug', array($this, '_unreserved_slug'));
    return parent::validate($array, $save);
  }
  
  /**
   * Validate the token entered.
   */
  public function _valid_token(Validation $array, $field) {
    $signup = ORM::factory('site_signup')->where('confirmed', 0)->where('confirm_token', strtoupper($array[$field]))->find();
    if ( ! $signup->loaded) {
      $array->add_error($field, 'invalid');
    }
  }
    
  /**
   * Make sure that the user_id is active and belongs to this site.
   */
  public function _valid_user(Validation $array, $field) {
    $user = ORM::factory('user')->where('site_id', kohana::config('chapterboard.site_id'))->where('status', 1)->find();
    if ( ! $user->loaded) {
      $array->add_error($field, 'invalid_user');
    }
  }
  
  public function _unique_slug(Validation $array, $field) {
    $site = ORM::factory('site')->find_by_slug($array[$field]);
    if ($site->loaded && $site->id != kohana::config('chapterboard.site_id')) {
      $array->add_error($field, 'unique');
    }
  }
  
  public function _unreserved_slug(Validation $array, $field) {
    $reserved_words = array('about', 'admin', 'chapter', 'finance', 'help', 'faq', 'contact', 'terms', 'tos', 'privacy', 'privacy-policy', 'cb', 'chapterboard', 'payrally', 'pr', 'pay', 'rally', 'team', 'new', 'jobs', 'careers', 'contact', 'shop', 'buy', 'checkout', 'dashboard', 'home', 'ducks', 'blake', 'alex', 'teemu', 'edison', 'leonidas', 'nidas', 'config', 'work');
    if (in_array($array[$field], $reserved_words)) {
      $array->add_error($field, 'reserved');
    }
  }
  
  public function before_update() {
    $this->updated = date::to_db();
    $this->slug_lower = strtolower($this->slug);
  }

  /**
   * Site setup
   *
   * Create the site record.  The after_insert method sets up all other tasks after
   * the site record is saved.
   */
  public function setup($vars) {
    // Setup the chapter and school.
    $school = ORM::factory('school')->where('name', $vars['school'])->find();
    if ( ! $school->loaded) {
      $school = ORM::factory('school');
      $school->name = $vars['school'];
      $school->save();
    }
    $this->school_id = $school->id;
    
    $chapter = ORM::factory('chapter')->where('name', $vars['chapter'])->find();
    if ( ! $chapter->loaded) {
      $chapter = ORM::factory('chapter');
      $chapter->name = $vars['chapter'];
      $chapter->save();
    }
    $this->chapter_id = $chapter->id;

    $this->status = 1;
    $this->renewal_date = date::to_db('+30 days');
    $this->created = date::to_db();
    $this->timezone = $vars['timezone'];
    $this->slug = $vars['slug'];
    $this->slug_lower = strtolower($this->slug);
    $this->fee_annual = 0;
    $this->fee_credit = 4.00;
    $this->fee_echeck = 4.00;
    return $this->save();
  }

  /**
   * Callback :after_insert
   *
   * Use this function to perform any site setup tasks.
   */
  public function after_insert() {
    
    // Setup default forums.
    $forums = array(
      array(
        'title' => 'Chapter Lounge',
        'description' => 'A place for active members to post off-topic discussions.',
      ),
      array(
        'title' => 'Alumni Discussion',
        'description' => 'Discussion board for chapter alumni.',
      ),
      array(
        'title' => 'New Member Discussion',
        'description' => 'Discussion board for the Pledge class.',
      ),
      array(
        'title' => 'Executive Board Discussion',
        'description' => 'Discussion board for Executive Board members.',
      ),
      array(
        'title' => 'Chapter Announcements',
        'description' => 'All chapter and executive board announcements.',
      ),
      array(
        'title' => 'Meeting Minutes and File Archives',
        'description' => 'Past meeting minutes and chapter documents.'
      ),
      array(
        'title' => 'Continuing Education',
        'description' => 'Information, files and links related to Continuing Education.'
      ),
    );
    
    foreach ($forums as $forum) {
      $orm = ORM::factory('forum');
      $orm->site_id = $this->id;
      $orm->title = $forum['title'];
      $orm->description = $forum['description'];
      $orm->status = 1;
      $orm->save();
      $forum_ids[] = $orm->id;
    }
  
    // Add some default calendars.
    $calendars = array(
      'Athletics',
      'Meetings',
      'New Member',
      'Community Service',
      'Social',
    );
    
    foreach ($calendars as $calendar) {
      $orm = ORM::factory('calendar');
      $orm->site_id = $this->id;
      $orm->title = $calendar;
      $orm->status = 1;
      $orm->save();
      $cal_ids[] = $orm->id;
    }
    
    // Actives group
    $active = ORM::factory('group');
    $active->site_id = $this->id;
    $active->static_key = 'active';
    $active->name = 'Active';
    $active->save();
    
    $forums = array($forum_ids[0], $forum_ids[4], $forum_ids[5], $forum_ids[6]);
    $values = array(
      'resource' => 'forum',
      'site_id' => $this->id,
      'group_id' => $active->id,
      'privilege' => 'view',
      'resource_ids' => serialize($forums),
    );
    ORM::factory('group_rule')->insert($values);

    // Alumni group
    $alumni = ORM::factory('group');
    $alumni->site_id = $this->id;
    $alumni->static_key = 'alumni';
    $alumni->name = 'Alumni';
    $alumni->save();
    
    $forums = array($forum_ids[1], $forum_ids[6]);
    $values = array(
      'resource' => 'forum',
      'site_id' => $this->id,
      'group_id' => $alumni->id,
      'privilege' => 'view',
      'resource_ids' => serialize($forums),
    );
    ORM::factory('group_rule')->insert($values);
    
    
    // Pledges
    $pledge = ORM::factory('group');
    $pledge->site_id = $this->id;
    $pledge->static_key = 'pledge';
    $pledge->sms_key = 'newmembers';
    $pledge->name = 'New Members';
    $pledge->save();
    
    $forums = array($forum_ids[2], $forum_ids[6]);
    $values = array(
      'resource' => 'forum',
      'site_id' => $this->id,
      'group_id' => $pledge->id,
      'privilege' => 'view',
      'resource_ids' => serialize($forums),
    );
    ORM::factory('group_rule')->insert($values);

    // Exec group
    $exec = ORM::factory('group');
    $exec->site_id = $this->id;
    $exec->name = 'Executive Board';
    $exec->save();
    
    $forums = array($forum_ids[2], $forum_ids[3]);
    $values = array(
      'resource' => 'forum',
      'site_id' => $this->id,
      'group_id' => $exec->id,
      'privilege' => 'view',
      'resource_ids' => serialize($forums),
    );
    ORM::factory('group_rule')->insert($values);
    
    
    // Add access to all calendars for actives, alumni and pledges
    $values = array(
      'resource' => 'calendar',
      'site_id' => $this->id,
      'group_id' => $active->id,
      'privilege' => 'view',
      'resource_ids' => serialize($cal_ids),
    );
    ORM::factory('group_rule')->insert($values);
    
    $values = array(
      'resource' => 'calendar',
      'site_id' => $this->id,
      'group_id' => $alumni->id,
      'privilege' => 'view',
      'resource_ids' => serialize($cal_ids),
    );
    ORM::factory('group_rule')->insert($values);
    
    $values = array(
      'resource' => 'calendar',
      'site_id' => $this->id,
      'group_id' => $pledge->id,
      'privilege' => 'view',
      'resource_ids' => serialize($cal_ids),
    );
    ORM::factory('group_rule')->insert($values);
    
  }
  
  /**
   * Select List
   */
	public function select_list($key = NULL, $val = NULL) {
    $sites = $this
      ->with('school')->with('chapter')
      ->where('sites.status !=', '3')
      ->orderby('chapter:name', 'ASC')->orderby('school:name', 'ASC')
      ->find_all();
    foreach ($sites as $site) {
      $results[$site->id] = $site->name();
    }
    return $results;
	}
	
	public function chapter_select($chapter_id) {
	  $sites = $this->with('school')->where('sites.type', 'chapter')->where('chapter_id', $chapter_id)->orderby('chapter_name', 'ASC')->find_all();
	  foreach ($sites as $site) {
	    $results[$site->id] = $site->chapter_name();
	  }
	  return $results;
	}
  
  /**
   * A2 Resource ID
   */
  public function get_resource_id() {
    return 'site';
  }
  
}