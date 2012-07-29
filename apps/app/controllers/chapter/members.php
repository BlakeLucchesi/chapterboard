<?php defined('SYSPATH') or die('No direct script access.');

class Members_Controller extends Private_Controller {
  
  public $secondary = 'menu/members';

  function index($type = 'active') {
    Router::$routed_uri = 'members'; // Highlight the active tab.
    css::add('styles/members.css');

    $this->title = 'Chapter Roster';
    $this->view = 'members/index';
    
    // Flag whether the current user can manage members.
    $this->admin = (bool) A2::instance()->allowed('user', 'manage');
    
    if (request::is_ajax()) {
      $response = array();
      if ($type == 'leadership') {
        $this->members = ORM::factory('user')->where('profile.position >', '')->search_profile(null, 'all');
      }
      else {
        $this->members = ORM::factory('user')->search_profile(null, 'all');
      }
      foreach ($this->members as $user) {
        $response[] = (object) array(
          'id' => $user->id,
          'name' => $user->name(),
          'phone' => $user->profile->phone,
          'phone_formatted' => $user->phone() ? $user->phone() : "Unavailable",
          'email' => $user->email,
          'picture' => theme::image('small', $user->picture(), array(), TRUE),
          'type' => $user->type(),
          'position' => $user->profile->position,
          'birthday_formatted' => $user->profile->birthday_formatted(),
          'address' => $user->profile->address_printable() ? $user->profile->address_printable() : 'Unavailable',
          'school_year' => $user->profile->school_year ? $user->profile->school_year : 'Unavailable',
          'emergency_contacts' => array(
            array(
              'name' => $user->profile->emergency1_name,
              'phone_formatted' => format::phone($user->profile->emergency1_phone),
              'phone' => format::phone($user->profile->emergency1_phone, '334')
            ),
            array(
              'name' => $user->profile->emergency2_name,
              'phone_formatted' => format::phone($user->profile->emergency2_phone),
              'phone' => format::phone($user->profile->emergency2_phone, '334'),
            ),
          ),
        );
      }
      response::json(TRUE, NULL, $response);
    }
    
    // Filter by member type.
    $this->type = $type ? $type : 'active';
    
    // Search members.
    $this->name = $this->form['name'] = $this->input->get('name');
    
    if ($this->type == 'leadership') {
      $this->members = ORM::factory('user')->find_with_leadership();
    }
    else {
      $this->members = ORM::factory('user')->search_profile($this->name, $this->type, NULL, NULL, $_GET);
    }
    $this->groups = ORM::factory('group')->default_groups();
    $this->stats = ORM::factory('user')->get_statistics($this->members);
    $this->type_count = ORM::factory('user')->count_types();
    $this->types = Kohana::config('chapterboard.user_types');
    $this->sizes = Kohana::config('chapterboard.shirt_sizes');
  }
  
  function show($id) {
    $this->member = ORM::factory('user', $id);
    if (request::is_ajax()) {
      $response = (object) array();
      if ($this->member->loaded && A2::instance()->allowed($this->member, 'view')) {
        $response = (object) array(
          'id' => $this->member->id,
          'name' => $this->member->name(),
          'phone' => $this->member->profile->phone,
          'phone_formatted' => $this->member->phone() ? $this->member->phone() : "Unavailable",
          'email' => $this->member->email,
          'picture' => theme::image('small', $this->member->picture(), array(), TRUE),
          'type' => $this->member->type(),
          'position' => $this->member->profile->position,
          'birthday_formatted' => $this->member->profile->birthday_formatted(),
          'address' => $this->member->profile->address_printable() ? $this->member->profile->address_printable() : 'Unavailable',
          'school_year' => $this->member->profile->school_year ? $this->member->profile->school_year : 'Unavailable',
          'department' => $this->member->profile->department,
          'major' => $this->member->profile->major,
          'emergency_contacts' => array(
            array(
              'name' => $this->member->profile->emergency1_name,
              'phone_formatted' => format::phone($this->member->profile->emergency1_phone),
              'phone' => preg_replace('/\D/i', '', $this->member->profile->emergency1_phone)),
            array(
              'name' => $this->member->profile->emergency2_name,
              'phone_formatted' => format::phone($this->member->profile->emergency2_phone),
              'phone' => preg_replace('/\D/i', '', $this->member->profile->emergency2_phone)),
          ),
        );
      }
      response::json(TRUE, NULL, $response);
    }
    else {
      url::redirect('profile/'. $this->member->id);
    }
  }
  
  function search() {
    $members = ORM::factory('user')->search_names($this->input->get('q'));
    print json_encode($members);
    Event::run('system.shutdown');
    die;
  }
  
  function export($type = 'active') {
    $this->admin = A2::instance()->allowed('user', 'manage');
    $rows[] = array('Name', 'Phone', 'Email', 'Shirt Size', 'Position', 'Student ID', 'Scroll Number', 'School Year', 'School/Department', 'Major', 'Initiation Year', 'Type', 'Birthday', 'School Address', 'City', 'State', 'Zip', 'Home/Permanent Address', 'City', 'State', 'Zip');
    $this->users = ORM::factory('user')->search_profile(null, $type);
    foreach ($this->users as $user) {
      $rows[] = array(
        $user->name(),
        $user->phone(),
        $user->email,
        $user->shirt_size(FALSE),
        $user->profile->position, 
        $this->admin ? $user->profile->student_id : '-',
        $user->profile->scroll_number, 
        $user->profile->school_year, 
        $user->profile->department, 
        $user->profile->major, 
        $user->profile->initiation_year, 
        $user->type(),
        $user->profile->birthday_formatted(),
        $user->profile->address1 .' '. $user->profile->address2,
        $user->profile->city,
        $user->profile->state,
        $user->profile->zip,
        $user->profile->home_address1 .' '. $user->profile->home_address2,
        $user->profile->home_city,
        $user->profile->home_state,
        $user->profile->home_zip,
      );
    }
    response::csv($rows, 'chapterboard-members');
  }
  
  function autocomplete() {
    $members = ORM::factory('user')->search_names($this->input->get('q'));
    foreach ($members as $member) {
      printf("%s\n", $member['name']);
    }
    Event::run('system.shutdown');
    die;
  }
  
  function printable() {
    $this->template = 'print';
    $this->type = $this->input->get('type') ? $this->input->get('type') : 'all';
    $this->title = sprintf('%s Members', ucwords($this->type));

    $this->members = ORM::factory('user')->search_profile(
      $this->input->get('name'),
      $this->input->get('type')
    );
  }
  
  /**
   * Deliver response::html response to show member lists in thickbox popups.
   */
  function popup($group_id = null) {
    if ($group_id) {
      $this->group = ORM::factory('group', $group_id);
      $this->title = $this->group->name;
      $this->users = $this->group->users;
    }
    else {
      $this->title = 'All Members';
      $this->users = ORM::factory('user')->where('site_id', $this->site->id)->find_all();
    }
    response::html(View::factory('members/popup'));
  }
  
  /**
   * Redirect back to index for undefined methods.
   */
  function __call($method, $args) {
    return $this->index($method);
  }
}