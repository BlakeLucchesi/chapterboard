<?php defined('SYSPATH') or die('No direct script access.');

class Upload_Controller extends Private_Controller {
  
  /**
   * Provide a url to post file uploads to.  This method provides
   * us with a consistent way to support multiple file uploads prior
   * to attaching the files to an object (which often times hasn't been
   * created yet in the first place, e.g. a forum topic or comment).
   */
  public function file() {
    $this->view = 'upload/files'; // The view lists currently uploaded files.
    
    // Setup session storage data.
    $key = $this->input->post('key');
    if (is_string($key)) {
      $this->uploads = $this->session->get('uploads-'. $key);
      
      // Validate upload and save file if valid.
      $valid = upload::validate('attach');
      if ($valid->validate()) {
        $info = upload::info('attach');
        if ($fileinfo = upload::save('attach', $info['filename'])) {
          $this->uploads[upload::filehash($fileinfo['filepath'])] = array_merge($info, $fileinfo);
          $this->session->set('uploads-'. $key, $this->uploads);
        }
      }
      else {
        $this->upload_error = 'Error uploading file.  Unsupported file type or the file is too large.';
      }
    }
    response::html(View::factory('upload/files')->render());
  }

  
  /**
   * Provide a url for post requests to remove files from the set
   * of uploaded files.
   */
  public function remove() {
    $this->view = 'upload/files';
    
    $key = $this->input->post('key');
    $filehash = $this->input->post('filehash');
    if (is_string($key)) {
      $this->uploads = upload::files_from_session($key);
      unset($this->uploads[$filehash]);
      $this->session->set('uploads-'. $key, $this->uploads);
    }
    
    response::html(View::factory('upload/files')->render());
  }
}