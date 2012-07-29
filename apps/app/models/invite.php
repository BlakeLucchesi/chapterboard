<?php defined('SYSPATH') or die('No direct script access.');

class Invite_Model extends ORM {
  
  protected $belongs_to = array('site', 'group');

  /**
   * Additional find method to see which invites need a reminder sent.
   */
  public function find_by_needs_reminder() {
    $date = date::to_db('-2 days');
    return $this->where('created <', $date)->where('reminder_sent', 0)->find_all();
  }

  /**
   * Record the invite to the database and send an email notification.
   */
  public function send_invite($data) {
    // Don't allow existing users or emails with invites to be entered again.
    $user = ORM::factory('user')->where('email', $data['email'])->find();
    if ($user->loaded)
      return FALSE;
    
    $existing_invite = ORM::factory('invite')->where('email', $data['email'])->find();
    if ($existing_invite->loaded)
      return FALSE;
    
    $invite = ORM::factory('invite');
    if ($invite->validate($data, TRUE)) {
      $email_data = array(
        'name' => $invite->site->user->name(),
        'phone' => $invite->site->user->phone(),
        'email' => $invite->site->user->email,
        'school' => $invite->site->school->name,
        'chapter_name' => $invite->site->chapter->name,
        'token' => $invite->token,
      );
      email::notify($invite->email, 'user_invite', $email_data, $invite->site->name());
      return TRUE;
    }
    return FALSE;
  }
  
  public function resend() {
    $email_data = array(
      'name' => $this->site->user->name(),
      'phone' => $this->site->user->phone(),
      'email' => $this->site->user->email,
      'school' => $this->site->school->name,
      'chapter_name' => $this->site->chapter->name,
      'token' => $this->token,
    );
    email::notify($this->email, 'user_invite_reminder', $email_data, $this->site->name());
  }
  
  /**
   * Show all open invitations for the site.
   */
  public function outstanding() {
    return $this->with('group')->where('invites.site_id', kohana::config('chapterboard.site_id'))->orderby('created', 'DESC')->find_all();
  }
  
  public function validate(array &$array, $save = FALSE) {
    $array = Validation::factory($array)
      ->pre_filter('trim')
      ->add_rules('group_id', 'required', 'digit')
      ->add_rules('email', 'required', 'email')
      ->add_callbacks('group_id', array('Group_Model', 'valid_group'))
      ->add_callbacks('email', array($this, '_unique_email'));
    return parent::validate($array, $save);
  }
  
  /**
   * Verify the uniqueness of the email being invited.
   */
  public function _unique_email(Validation $array, $field) {
    // check the database for existing records
    $user_email = (bool) ORM::factory('user')->where('email', $array[$field])->count_all();
    if ($user_email) {
     $array->add_error($field, 'email_exists');
    }
  }
  
  /**
   * Before insert hook.
   */
  public function before_insert() {
    $this->site_id = kohana::config('chapterboard.site_id');
    $this->created = date::to_db();
    $this->type = $this->group->static_key;
    do { // Make sure we are setting a unique token for the invite.
      $this->token = text::token();
      $existing = ORM::factory('invite')->where('token', $this->token)->find();
    } while ($this->token == $existing->token);
  }
  
}