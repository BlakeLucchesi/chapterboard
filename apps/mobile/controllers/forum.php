<?php defined('SYSPATH') or die('No direct script access.');

class Forum_Controller extends Web_Controller {
  
  public function _pre_controller() {
    Router::$routed_uri = 'forum';
  }
  
  public function index() {
    $this->title = 'Recent Topics';
    $this->link = html::anchor('forum/unread', 'Unread Topics');
    
    $this->pagination = new Pagination(array('total_items' => ORM::factory('topic')->topics_count()));
    $count = $this->pagination->items_per_page;
    $start = $this->pagination->sql_offset();  
    $this->topics = ORM::factory('topic')->recent_topics($count, $start);
  }
  
  public function unread() {
    $this->title = 'Unread Topics';
    $this->view = 'forum/index';
    $this->link = html::anchor('forum', 'Recent Topics');

    $this->pagination = new Pagination(array('total_items' => ORM::factory('topic')->topics_count()));
    $count = $this->pagination->items_per_page;
    $start = $this->pagination->sql_offset();
    $this->topics = ORM::factory('topic')->unread($count, $start);
  }
  
  public function topic($id) {
    $this->topic = ORM::factory('topic', $id);
    
    if ( ! $this->topic->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->topic->forum, 'view'))
      Event::run('system.403');
      
    $this->title = $this->topic->title;
    
    $topic_history = ORM::factory('topic_history')->where(array('user_id' => $this->user->id, 'topic_id' => $this->topic->id))->find();
    $this->last_viewed = $this->user->topic_history > $topic_history->last_viewed ? $this->user->topic_history : $topic_history->last_viewed;
    $this->topic->markread();

    if ($this->topic->poll) {
      $this->vote = ORM::factory('poll_vote')->where('user_id', $this->user->id)->where('poll_id', $this->topic->poll->id)->find();
    }
    
    // Check to see if a comment was posted.
    $post = $this->input->post();
    if ( ! empty($post) && ! $this->topic->locked) {
      $this->comment = ORM::factory('comment');
      $this->comment->object_id = $this->topic->id;
      $this->comment->object_type = 'topic';
      if ($this->comment->validate($post, TRUE)) {
        message::add('success', 'Your comment has been posted.');
      }
      else {
        message::add('error', 'Sorry, you can\'t leave an empty comment. Please add a comment and try again.');
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_comment');
        $this->uploads = $this->session->get('uploads-'. $post['key']);
      }
    }
      
  }
  
  /**
   * Accept a vote for a poll choice.
   */
  function vote() {
    $this->topic = ORM::factory('topic', $this->input->post('topic_id'));
    if ( ! $this->topic->poll->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->topic->forum, 'view'))
      Event::run('system.403');

    if ($this->input->post('choice_id') > 0) {
      $this->topic->poll->vote($this->input->post('choice_id'), $this->user->id);
      message::add(TRUE, 'Your vote has been cast!');
    }
    else {
      message::add(FALSE, 'Please choose an option to cast your vote.');
    }
    url::redirect('forum/topic/'. $this->topic->id);
  }
}