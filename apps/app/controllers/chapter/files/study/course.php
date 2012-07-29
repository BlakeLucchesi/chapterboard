<?php defined('SYSPATH') or die('No direct script access.');

class Course_Controller extends Files_Controller {

  public function index() {
    url::redirect('files/study');
  }
  
  public function show($id) {
    $this->course = ORM::factory('course', $id);
    
    if ( ! $this->course->loaded || $this->course->status == 0)
      Event::run('system.404');
    if ($this->course->site_id != $this->site->id)
      Event::run('system.403');

    $this->title = $this->course->title;
    $this->is_admin = A2::instance()->allowed($this->course, 'edit');
    
    // Check to see if a comment was posted.
    if ($post = $this->input->post()) {
      $this->comment = ORM::factory('comment');
      $this->comment->object_id = $this->course->id;
      $this->comment->object_type = 'course';
      if ($this->comment->validate($post, TRUE)) {
        // Comment has been saved, now save uploaded files.
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
        }
        $this->session->delete('uploads-'. $post['key']);
      }
      else {
        message::add('error', 'Sorry, you can\'t leave an empty comment. Please add a comment and try again.');
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_comment');
        $this->uploads = $this->session->get('uploads-'. $post['key']);
      }
    }
    else {
      $this->form['key'] = text::token();
    }
  }
  
  public function add() {
    $this->title = 'Add a Course';
    $this->view = 'files/study/course/form';
  
    if ($post = $this->input->post()) {
      $this->course = ORM::factory('course');
      if ($this->course->validate($post, TRUE)) {
        message::add(TRUE, '%s added succesfully.', $this->course->title);
        url::redirect('files/study/course/'. $this->course->id);
      }
      else {
        message::add(FALSE, 'Please fix the errors below.');
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_study_bank_course');
      }
    }
    
  }
  
  public function edit($id) {
    $this->course = ORM::factory('course', $id);
    if ( ! $this->course->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->course, 'edit'))
      Event::run('system.403');
    
    $this->title = 'Editing '. $this->course->title;
    $this->form = $this->course->as_array();
    $this->view = 'files/study/course/form';
    
    if ($post = $this->input->post()) {
      if ($this->course->validate($post, TRUE)) {
        message::add(TRUE, '%s updated succesfully.', $this->course->title);
        url::redirect('files/study/course/'. $this->course->id);
      }
      else {
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_study_bank_course');
      }
    }
    
  }
  
  public function delete($id) {
    $this->course = ORM::factory('course', $id);
    if ( ! $this->course->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->course, 'delete'))
      Event::run('system.403');
    
    $this->course->archive();
    message::add(TRUE, '%s has been deleted.', $this->course->title);
    url::redirect('files/study');
  }
  
}