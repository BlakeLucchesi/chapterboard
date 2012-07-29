<?php defined('SYSPATH') or die('No direct script access.');

class Messages_Controller extends Private_Controller {
    
  /**
   * List a members inbox.
   */
  function inbox() {
    $this->title = 'Inbox';
    $this->pagination = new Pagination(array('items_per_page' => 15, 'total_items' => ORM::factory('message')->count_by_user($this->user->id)));
    $limit = $this->pagination->items_per_page;
    $offset = $this->pagination->sql_offset();
    $this->messages = ORM::factory('message')->find_by_user($this->user->id, $limit, $offset);
  }
  
  /**
   * View a message.
   */
  function show($id) {
    $this->message = ORM::factory('message')->with('comments')->find($id);
    
    if ( ! $this->message->loaded)
      Event::run('system.404');
    if ( ! $this->message->is_allowed($this->user->id))
      Event::run('system.403');
        
    $this->title = $this->message->subject;
    if ($post = $this->input->post()) {
      $this->comment = ORM::factory('comment');
      $this->comment->object_id = $this->message->id;
      $this->comment->object_type = 'message';
      if ($this->comment->validate($post, TRUE)) {
        // Topic has been saved, now save uploaded files.
        if ($uploads = $this->session->get('uploads-'. $post['key'])) {
          foreach ($uploads as $upload) {
            // Move temp files to upload directory and insert records into database.
            if ($fileinfo = upload::save($upload, $upload['filename'], Kohana::config('upload.directory'))) {
              $upload = array_merge($upload, $fileinfo);
            }
            $upload['object_type'] = 'comment';
            $upload['object_id'] = $this->comment->id;
            ORM::factory('file')->insert($upload);
          }
          $this->session->delete('uploads-'. $post['key']);
        }
      }
      else {
        message::add('error', 'Sorry, you can\'t leave an empty response. Please add a response and try again.');
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_comment');
        $this->uploads = $this->session->get('uploads-'. $post['key']);
      }
    }

    // Make sure to update the count in the top nav.
    // Perform after post so that commenter's message is marked read.
    $this->message->read();
    $this->_inbox();
  }
  
  /**
   * Move the message to the trash for the given user.
   */
  function delete($id) {
    $this->message = ORM::factory('message', $id);
    if ( ! $this->message->loaded)
      Event::run('system.404');
    if ( ! $this->message->is_allowed($this->user->id))
      Event::run('system.403');
      
    $this->message->delete($this->user->id);
    url::redirect('messages');
  }
  
  /**
   * Mark a message as unread.
   */
  function unread($id) {
    $this->message = ORM::factory('message', $id);
    if ( ! $this->message->loaded)
      Event::run('system.404');

    // Make sure the user is involved in the conversation.
    foreach ($this->message->users as $user) {
      if ($user->id == $this->user->id) {
        $this->message->keep_unread($this->user->id);
        url::redirect('messages');
      }
    }
    
    Event::run('system.403');
  }
  
  /**
   * Provide a create new message form.
   */
  function send() {
    $this->title = 'Send New Message';
    css::add('styles/token-input.css');
    css::add('styles/token-input-facebook.css');
    javascript::add('jquery/jquery.tokeninput.js');
    javascript::add('scripts/messages.js');
    javascript::add(array('messages_users_url' => '/messages/users'), 'setting');

    $this->groups = ORM::factory('group')->find_all();
    if ($post = $this->input->post()) {
      $this->message = ORM::factory('message');
      if ($this->message->validate($post, TRUE)) {
        message::add(TRUE, 'Message sent succesfully.');
        url::redirect('messages/'. $this->message->id);
      }
      else {
        message::add(FALSE, 'Please fix the errors below.');
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_message_send');
      }
    }
  }

  /**
   * Respond to a search names of members as recipients.
   */
  public function users() {
    $matches = array();
    if ($name = $this->input->get('q')) {
      $users = ORM::factory('user')->where('site_id', $this->site->id)->like('searchname', text::searchable($name))->where('status', 1)->find_all();
      foreach ($users as $user) {
        if ($user->id != $this->user->id) { // Can't send a message to yourself.
          $matches[] = (object) array('id' => $user->id, 'name' => $user->name());
        }
      }
    }
    print json_encode($matches);
    die();
  }
    
}