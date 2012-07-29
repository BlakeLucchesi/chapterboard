
<?php defined('SYSPATH') or die('No direct script access.');

class Invite_Controller extends Members_Controller {
  
  public function __construct() {
    if ( ! A2::instance()->allowed('user', 'manage'))
      Event::run('system.403');
    parent::__construct();
  }
  
  public function index() {
    $this->title = 'Invite Members';
    css::add('styles/members.css');
    $this->groups = ORM::factory('group')->default_groups_select();
    
    if ($post = $this->input->post()) {
      // Make sure the group_id belongs to their site.
      
      $group = ORM::factory('group', $post['group_id']);
      if ($group->site_id == $this->site->id) {
        $invites = $this->_invite_list($post['emails']);
        foreach ($invites as $email) {
          $invite = array('email' => $email, 'group_id' => $post['group_id']);
          if (ORM::factory('invite')->send_invite($invite)) {
            $success++;
          }
          else {
            // Errors returned via validation object.
            $this->form['emails'] .= $email ."\r\n";
            $this->errors['emails'] = TRUE;
          }
        }

        // Manage success and errors.
        if ($success) {
          message::add(TRUE, format::plural($success, '@count invite was sent successfully.', '@count invites were sent successfully.'));
        }
        if ($this->errors) {
          $this->form['group_id'] = $post['group_id'];
          message::add('error', 'One or more of the email addresses you entered was either invalid or the user is already in our system. Please fix and try again.');
        }
      }
      else {
        message::add('error', 'An error occurred while sending your invitations. Please try again.');
      }
    }
    $this->invitations = ORM::factory('invite')->outstanding();
  }
  
  /**
   * @param string A string of email addresses that are separated by commas or white space.
   *
   * @return array An array of email addresses.
   */
  public function _invite_list($emails) {
    $emails = preg_replace('/[\s]+/i', ',', $emails);
    $emails = explode(',', $emails);
    return array_filter($emails);
  }
  
  /**
   * Revoke invitation.
   */
  public function revoke($id) {
    $this->invite = ORM::factory('invite', $id);
    if ( ! $this->invite->loaded)
      Event::run('system.404');

    message::add('success', 'Invitation to: %s has been revoked.', $this->invite->email);
    $this->invite->delete();
    url::redirect('members/invite');
  }
  
  /**
   * Resend invitation.
   */
  public function resend($id) {
    $this->invite = ORM::factory('invite', $id);
    if ( ! $this->invite->loaded)
      Event::run('system.404');
    if ($this->invite->site_id != $this->site->id)
      Event::run('system.404');
    
    message::add('success', 'Invitation to: %s has been resent.', $this->invite->email);
    $this->invite->resend();
    url::redirect('members/invite');
  }
  
  /**
   * Resend all invitations.
   */
  public function resend_all() {
    $this->invites = ORM::factory('invite')->where('site_id', $this->site->id)->find_all();
    foreach ($this->invites as $invite) {
      $invite->resend();
    }
    message::add('success', format::plural($this->invites->count(), 'Invitation reminders sent to @count member.', 'Invitation reminders sent to @count members.'));
    url::redirect('members/invite');
  }
}