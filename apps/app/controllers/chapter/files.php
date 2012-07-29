<?php defined('SYSPATH') or die('No direct script access.');

class Files_Controller extends Private_Controller {
  
  public $secondary = 'menu/files';
  
  public function index() {
    $this->folders = ORM::factory('folder')->find_by_site($this->site->id);
    $this->title = 'Chapter File Folders';
    $this->recent = ORM::factory('file')->find_recent_documents($this->site->id, 10);
  }
  
  public function upload() {
    $this->title = 'Upload File';
    $this->folders = ORM::factory('folder')->select_list_all($this->site->id);
    $this->form = $this->input->get();
    
    if ( ! A2::instance()->allowed('file', 'manage'))
      Event::run('system.403');
    
    if (count($this->folders) < 1) {
      message::add(TRUE, 'Create a folder using the form below before uploading your first file.');
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
          ORM::factory('folder', $this->file->object_id)->update_timestamp(); // Update the parent folders' updated field.
          url::redirect('files/folder/'. $this->file->object_id);
        }
        else {
          $this->form = $post->as_array();
          $this->errors = $post->errors('form_file');
          log::system('form_errors', 'File upload failed. (Chapter files section).', 'notice', array('errors' => $this->errors, 'form data' => $this->form));
        }
      }
      else {
        message::add(FALSE, 'Invalid filetype or no file selected. Please choose a valid file to upload.');
      }
    }
  }
  
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
        ORM::factory('folder', $this->file->object_id)->update_timestamp(); // Update the parent folders' updated field.        
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
  
  public function delete($id) {
    $this->file = ORM::factory('file')->where('object_type', 'folder')->where('id', $id)->find();
    if ( ! $this->file->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->file, 'delete'))
      Event::run('system.403');
      
    message::add(TRUE, '%s deleted succesfully.', $this->file->name);
    $folder_id = $this->file->object_id;
    $this->file->delete();
    url::redirect('files/folder/'. $folder_id);
  }
}