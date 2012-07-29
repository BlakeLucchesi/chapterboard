<?php defined('SYSPATH') or die('No direct script access.');

Class Sms_Controller extends Private_Controller {
  
  protected $secondary = 'menu/dashboard';

  public function _pre_controller() {
    if ( ! A2::instance()->allowed('sms', 'send')) {
      Event::run('system.404');
    }
  }
  
  public function index() {
    $this->title = 'Recent Text Messages';
    javascript::add('jquery/jquery.jeditable.js');
    
    $this->pagination = new Pagination(array('total_items' => ORM::factory('sms')->find_all()->count(), 'items_per_page' => 10));
    $count = $this->pagination->items_per_page;
    $start = $this->pagination->sql_offset();  
    
    $this->messages = ORM::factory('sms')->find_all($count, $start);
    $this->groups = ORM::factory('group')->find_all();
    $this->users = ORM::factory('user')->find_by_role(array('admin', 'sms'));
    
    if (request::is_ajax()) {
      $response = array(
        'groups' => array(),
        'messages' => array(),
        'number' => '(415) 800-3041',
        'number_plain' => '4158003041',
      );
      foreach ($this->groups as $group) {
        $response['groups'][] = array(
          'name' => $group->name,
          'sms_key' => $group->sms_key,
          'member_count' => format::plural($group->users->count(), '@count member', '@count members'),
        );
      }
      foreach ($this->messages as $message) {
        $response['messages'][] = array(
          'message' => $message->message,
          'author' => $message->user->name(),
          'picture' => theme::image('small', $message->user->picture(), array(), TRUE),
          'created' => date::display($message->created, 'M jS \a\t g:ia'),
          'send_count' => $message->send_count,
          'groups' => implode(', ', $message->groups),
        );
      }
      response::json(TRUE, null, $response);
    }
  }
  
  /**
   * Send text message using web interface.
   */
  public function send() {
    $this->title = 'Send Group Text Message';
    $this->groups = ORM::factory('group')->find_all();
    
    javascript::add("
      $('#message').keypress(function() {
        $('span', '#text-count').html(110 - $('#message').val().length);
      });", 'inline');
    
    if ($post = $this->input->post()) {
      if (empty($post['message'])) {
        message::add(FALSE, 'Please enter a message.');
      }
      // Group Validation and adding to message body.
      else if (empty($post['groups'])) {
        message::add(FALSE, 'Please select a group to send your message to.');
      }
      else {
        array_filter($post['groups']);
        $groups_added = ORM::factory('group')->in('id', $post['groups'])->find_all(); // site id clause already in find_all().
        foreach ($groups_added as $group) {
          $post['message'] = sprintf('@%s %s', $group->sms_key, $post['message']);
        }

        $this->sms = ORM::factory('sms');
        $post['sent_from'] = $this->user->profile->phone;
        if ($this->sms->validate($post, TRUE)) {
          message::add(TRUE, 'Your message has been sent and will be delivered shortly.');
          url::redirect('sms');
        }
      }
      $this->form = $post;
    }
    
  }
  
  public function update() {
    if ($post = $this->input->post()) {
      $post['sms_key'] = $post['value'];
      $this->group = ORM::factory('group')->where('site_id', $this->site->id)->find($post['id']);
      if ($this->group->loaded) {
        $this->group->validate_sms_key($post, TRUE);
        response::html($this->group->sms_key);
      }
      else {
        response::html('Error, invalid group id. Please refresh page.');
      }
    }
    else {
      url::redirect('sms');
    }
  }
  
  public function show($id) {
    $this->sms = ORM::factory('sms', $id);
    if ($this->sms->site_id != $this->site->id)
      Event::run('system.404');
  }
}