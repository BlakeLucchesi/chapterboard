<?php defined('SYSPATH') or die('No direct script access.');

class Comment_Controller extends Files_Controller {
  
  public function edit($id) {
    $this->title = 'Edit Comment';

    $this->comment = ORM::factory('comment', $id);
    if ( ! $this->comment->loaded)
      Event::run('system.404');
    if ( ! (A2::instance()->allowed($this->comment, 'edit') || A2::instance()->allowed($this->comment->file, 'edit')))
      Event::run('system.403');
    
    $this->photo = $this->comment->file;
    if ($post = $this->input->post()) {
      if ($this->comment->validate($post, TRUE)) {
        url::redirect('files/photos/album/photo/'. $this->photo->id);
      }
      else {
        message::add('error', 'Sorry, you can\'t leave an empty comment. Please add a comment and try again.');
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_comment');
      }
    }
    else {
      $this->form = $this->comment->as_array();
    }
  }
  
  public function delete($id) {
    $this->comment = ORM::factory('comment', $id);
    if ( ! $this->comment->loaded)
      Event::run('system.404');
    if ( ! (A2::instance()->allowed($this->comment, 'delete') || A2::instance()->allowed($this->comment->file, 'delete')))
      Event::run('system.403');
      
    $this->comment->unpublish();
    url::redirect('files/photos/album/photo/'. $this->comment->file->id);
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
    if ( ! $this->comment->file->loaded)
      Event::run('system.404');
    if ( ! $this->comment->allowed())
      Event::run('system.403');
    
    ORM::factory('vote')->$action('comment', $id, $this->user->id);
    url::redirect('files/photos/album/photo/'. $this->comment->file->id .'#comment-'. $this->comment->id);
  }
  
}