<?php defined('SYSPATH') or die('No direct script access.');

class Photos_Controller extends Files_Controller {

  public function index() {
    $this->title = 'Chapter Photos';
    
    $this->pagination = new Pagination(array('total_items' => ORM::factory('album')->count_albums()));
    $limit = $this->pagination->items_per_page;
    $offset = $this->pagination->sql_offset();
    $this->albums = ORM::factory('album')->find_by_site($this->site->id, $limit, $offset);
  }
  
  public function delete($id) {
    $this->photo = ORM::factory('file', $id);

    if ( ! $this->photo->loaded)
      Event::run('system.404');
    if ($this->photo->object_type != 'album')
      Event::run('system.404'); // Only delete photo files.
    if ( ! A2::instance()->allowed($this->photo, 'delete'))
      Event::run('system.403');

    // Make sure to reset the thumbnail for the album.
    $album = $this->photo->album;
    $album->file_id = 0;
    $album->save();
    message::add(TRUE, 'Photo deleted successfully.');
    $this->photo->delete();
    url::redirect('files/photos/album/'. $album->id);
  }
  
  /**
   * Handle bulk file uploads.
   */
  public function upload($id) {
    $this->album = ORM::factory('album', $id);
    
    // You can only upload files to albums belonging to your site.
    if ( ! ($this->album->loaded && $this->album->site_id == kohana::config('chapterboard.site_id'))) {
      $result['error'] = 'Access denied. '. $this->album->id;
    }
    else {
      // list of valid extensions, ex. array("jpeg", "xml", "bmp")
      $allowedExtensions = array('jpg', 'jpeg', 'gif', 'png');
      // max file size in bytes
      $sizeLimit = 5 * 1024 * 1024;

      $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
      $result = $uploader->handleUpload();

      // Check result to see if file uploaded to temp directory.
      // Move a resized photo to the original directory and insert into DB.
      if ($result['success']) {
        $temp = $result['filepath'];
        $extension = array_pop(explode('.', $temp));
        $new_filename = sprintf('%s.%s', file::unique_name($temp), $extension);
        $new_filepath = sprintf('%s/%s', Kohana::config('upload.directory'), $new_filename);
        copy($temp, $new_filepath);
        $post = array(
          'object_id' => $this->album->id,
          'object_type' => 'album',
          'mime' => file::mime($new_filepath),
          'size' => filesize($new_filepath),
          'extension' => $extension,
          'name' => $new_filename,
          'filepath' => $new_filepath,
          'filename' => $new_filename,
        );

        // Save to db and reset thumbnail or return error.
        $this->file = ORM::factory('file');
        if ($this->file->validate($post, TRUE)) {
          // Create large image and thumbnail.
          Image::factory($new_filepath)->thumbnail($new_filename, 'large');
          Image::factory($new_filepath)->thumbnail($new_filename, 'thumbnail');

          $this->album->file_id = 0;
          $this->album->save();
        }
        else {
          log::system('photos', print_r($post->errors(), TRUE));
          $result = array('error', 'Could not save image, please try again.');
        }
      }
    }
    
    // to pass data through iframe you will need to encode all html tags
    echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    die();
  }
}

class qqFileUploader {
  private $allowedExtensions = array();
  private $sizeLimit = 10485760;
  private $file;

  function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760){        
    $allowedExtensions = array_map("strtolower", $allowedExtensions);
        
    $this->allowedExtensions = $allowedExtensions;        
    $this->sizeLimit = $sizeLimit;
    
    $this->checkServerSettings();       

    if (isset($_GET['qqfile'])) {
      $this->file = new qqUploadedFileXhr();
    }
    elseif (isset($_FILES['qqfile'])) {
      $this->file = new qqUploadedFileForm();
    }
    else {
      $this->file = false; 
    }
  }
  
  private function checkServerSettings(){        
    $postSize = $this->toBytes(ini_get('post_max_size'));
    $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));        
    
    if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit) {
      $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
      die("{'error':'increase post_max_size and upload_max_filesize to $size'}");
    }        
  }
  
  private function toBytes($str){
    $val = trim($str);
    $last = strtolower($str[strlen($str)-1]);
    switch($last) {
      case 'g': $val *= 1024;
      case 'm': $val *= 1024;
      case 'k': $val *= 1024;        
    }
    return $val;
  }
  
  /**
   * Returns array('success'=>true) or array('error'=>'error message')
   */
  function handleUpload() {
    $directory = Kohana::config('upload.temp_directory') .'/';
      
    if ( ! is_writable($directory)){
      return array('error' => "Server error. Upload directory isn't writable.");
    }
    
    if ( ! $this->file){
      return array('error' => 'No files were uploaded.');
    }
    
    $size = $this->file->getSize();
    
    if ($size == 0) {
      return array('error' => 'File is empty');
    }
    
    if ($size > $this->sizeLimit) {
      return array('error' => 'File is too large');
    }
    
    $pathinfo = pathinfo($this->file->getName());
    $filename = $pathinfo['filename'];
    $ext = $pathinfo['extension'];

    if ($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
      $these = implode(', ', $this->allowedExtensions);
      return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
    }

    $filepath = $directory . $filename . '.' . $ext;
    if ($this->file->save($filepath)) {
      // Size down large images.
      list($width, $height) = getimagesize($filepath);
      if ($width > 960) {
        Image::factory($filepath)->resize(960, null, Image::AUTO)->save($filepath);
      }
      return array(
        'success' => TRUE,
        'filepath' => $directory . $filename . '.' . $ext
      );
    }
    else {
      return array('error'=> 'Could not save uploaded file.' .
            'The upload was cancelled, or server error encountered');
    }
  }    
}

/**
 * Handle file uploads via XMLHttpRequest
 */
class qqUploadedFileXhr {
  
  /**
   * Save the file to the specified path
   * @return boolean TRUE on success
   */
  function save($path) {    
    $input = fopen("php://input", "r");
    $temp = tmpfile();
    $realSize = stream_copy_to_stream($input, $temp);
    fclose($input);
    
    if ($realSize != $this->getSize()){            
      return false;
    }
    
    $target = fopen($path, "w");        
    fseek($temp, 0, SEEK_SET);
    stream_copy_to_stream($temp, $target);
    fclose($target);
    
    return true;
  }
  
  function getName() {
    return $_GET['qqfile'];
  }
  
  function getSize() {
    if (isset($_SERVER["CONTENT_LENGTH"])){
      return (int)$_SERVER["CONTENT_LENGTH"];            
    }
    else {
      throw new Exception('Getting content length is not supported.');
    }      
  }   
}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm {  
  /**
   * Save the file to the specified path
   * @return boolean TRUE on success
   */
  function save($path) {
    if ( ! move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)){
      return false;
    }
    return true;
  }
  function getName() {
    return $_FILES['qqfile']['name'];
  }
  function getSize() {
    return $_FILES['qqfile']['size'];
  }
}