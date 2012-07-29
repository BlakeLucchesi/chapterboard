<?php defined('SYSPATH') or die('No direct script access.');

class Folder_Controller extends Files_Controller {
  
  /**
   * Show the contents of a folder.
   */
  public function index($id = NULL) {
    if (is_null($id))
      Event::run('system.404');
    $this->folder = ORM::factory('folder')->where('status', 1)->where('id', $id)->find();
    $this->title = $this->folder->name;
    $this->parent_id = $this->folder->parent->loaded ? $this->folder->parent->id : $this->folder->id;
  }
  
  /**
   * Add a new folder.
   */
  public function add() {
    $this->title = 'Add Folder';
    $this->folders = ORM::factory('folder')->select_list_parents($this->site->id);
    $this->form = $this->input->get();
    
    if ($post = $this->input->post()) {
      $folder = ORM::factory('folder');
      if ($folder->validate($post, TRUE)) {
        message::add(TRUE, 'Folder added successfully.');
        url::redirect('files/folder/'. $folder->id);
      }
      else {
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_files_folder');
        message::add(FALSE, 'Please fix the errors below.');
      }
    }
  }
  
  /**
   * Delete a folder.
   */
  public function delete($id) {
    $this->folder = ORM::factory('folder', $id);
    if ( ! $this->folder->loaded)
      Event::run('system.404');
    if ($this->folder->site_id != $this->site->id)
      Event::run('system.403');
    
    message::add(TRUE, '%s folder removed succesfully.', $this->folder->name);
    $this->folder->delete();
    url::redirect('files');
  }
  
  /**
   * Edit folder meta data and permissions.
   */
  public function edit($id) {
    $this->folder = ORM::factory('folder', $id);
    if ( ! $this->folder->loaded)
      Event::run('system.404');
    if ($this->folder->site_id != $this->site->id)
      Event::run('system.403');
    
    $this->title = 'Editing: '. $this->folder->name;
    $this->view = 'files/folder/add';
    $this->form = $this->folder->as_array();
    $this->folders = ORM::factory('folder')->select_list_parents($this->site->id);
    unset($this->folders[$this->folder->id]);

    if ($post = $this->input->post()) {
      if ($this->folder->validate($post, TRUE)) {
        message::add(TRUE, 'Folder settings saved succesfully.');
        url::redirect('files/folder/'. $this->folder->id);
      }
      else {
        message::add(FALSE, 'Please fix the errors below.');
        $this->form = $post->as_array();
        $this->errors = $post->errors('file_files_folder');
      }
    }
  }
  
}