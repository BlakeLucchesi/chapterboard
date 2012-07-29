<?php defined('SYSPATH') or die('No direct script access.');

class Comment_Controller extends Forum_Controller {
  
  public function edit($id) {
    $this->title = 'Edit Comment';

    $this->comment = ORM::factory('comment', $id);
    if ( ! $this->comment->loaded)
      Event::run('system.404');
    if ( ! (A2::instance()->allowed($this->comment, 'edit') || A2::instance()->allowed($this->comment->topic->forum, 'admin')))
      Event::run('system.403');

    $this->topic = $this->comment->topic;
    if ($post = $this->input->post()) {
      if ($this->comment->validate($post, TRUE)) {
        ORM::factory('file')->save_uploads($post['key'], 'comment', $this->comment->id);
        message::add(TRUE, 'Comment has been updated.');
        url::redirect('forum/topic/'. $this->topic->id);
      }
      else {
        message::add('error', 'Sorry, you can\'t leave an empty comment. Please add a comment and try again.');
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_comment');
        $this->uploads = upload::files_from_session($form['key']);
      }
    }
    else {
      $this->form = $this->comment->as_array();
      $this->form['key'] = text::token();
      $this->uploads = ORM::factory('file')->find_by_parent('comment', $this->comment->id);
      upload::set_files($this->form['key'], $this->uploads);
    }
  }
  
  public function delete($id) {
    $this->comment = ORM::factory('comment', $id);
    if ( ! $this->comment->loaded)
      Event::run('system.404');
    if ( ! (A2::instance()->allowed($this->comment, 'delete') || A2::instance()->allowed($this->comment->topic->forum, 'admin')))
      Event::run('system.403');
      
    $this->comment->unpublish();
    message::add(TRUE, 'Comment removed successfully.');
    url::redirect('forum/topic/'. $this->comment->topic->id);
  }

  /**
   * Like/unlike a comment.
   */
  function like($id) {
    $this->vote_action($id, 'insert');
  }
  
  function unlike($id) {
    $this->vote_action($id, 'remove');
  }
  
  /**
   * Permission checking
   *
   * @param object id
   * @param ORM method: insert or remove
   */
  function vote_action($id, $action) {
    $this->comment = ORM::factory('comment', $id);
    
    if ( ! $this->comment->loaded)
      Event::run('system.404');
    if ( ! $this->comment->topic->loaded)
      Event::run('system.404');
    if ( ! $this->comment->allowed())
      Event::run('system.403');
    
    ORM::factory('vote')->$action('comment', $id, $this->user->id);
    url::redirect('forum/topic/'. $this->comment->topic->id .'#comment-'. $this->comment->id);
  }
}