<?php defined('SYSPATH') or die('No direct script access.');

class Files_Controller extends National_Controller {
  
  public $secondary = 'menu/files';

  /**
   * List all top level folders.
   */
  public function index() {
    $this->title = 'File Folders';
    $this->folders = ORM::factory('folder')->find_by_site($this->site->id);
    $this->recent = ORM::factory('file')->find_recent_documents($this->site->id);
  }
    
  /**
   * Upload a file.
   */
  public function upload() {
    $this->title = 'Upload File';
    $this->folders = ORM::factory('folder')->select_list_all($this->site->id);
    $this->form = $this->input->get();
    
    if (empty($this->folders)) {
      message::add(TRUE, 'Create a folder using the form below to get started.');
      url::redirect('files/folder/add');
    }
    
    if ($post = $this->input->post()) {
      $post['object_type'] = 'folder';
      // Validate upload and save file if valid.
      $valid = upload::validate('file');
      $info = upload::info('file');
      if ($valid->validate() && $fileinfo = upload::save('file', $info['filename'], Kohana::config('upload.directory'))) {
        $post = array_merge($fileinfo, $info, $post);
        $this->file = ORM::factory('file');
        if ($this->file->validate($post, TRUE)) {
          url::redirect('files/folder/'. $this->file->object_id);
        }
        else {
          $this->form = $post->as_array();
          $this->errors = $post->errors('form_file');
        }
      }
      else {
        message::add(FALSE, 'Invalid filetype or no file selected. Please choose a valid file to upload.');
      }
    }
  }
  
  /**
   * Edit a file.
   */
  public function edit($id) {
    $this->file = ORM::factory('file', $id);
    if ( ! $this->file->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->file, 'edit'))
      Event::run('system.403');
      
    $this->title = 'Editing file: '. $this->file->name;
    $this->form = $this->file->as_array();
    $this->folders = ORM::factory('folder')->select_list_all($this->site->id);
    
    if ($post = $this->input->post()) {
      if ($this->file->update_validate($post, TRUE)) {
        message::add(TRUE, 'File updated successfully.');
        url::redirect('files/folder/'. $this->file->object_id);
      }
      else {
        message::add(FALSE, 'Please fix the errors below.');
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_file');
      }
    }
  }
  
  /**
   * Delete a file.
   */
  public function delete($id) {
    $this->file = ORM::factory('file')->where('object_type', 'folder')->where('id', $id)->find();

    if ( ! $this->file->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->file, 'manage'))
      Event::run('system.403');

    $folder_id = $this->file->object_id;
    message::add(TRUE, '%s deleted successfully.', $this->file->name);
    $this->file->delete();
    url::redirect('files/folder/'. $folder_id);
  }
}