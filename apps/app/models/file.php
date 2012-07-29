<?php defined('SYSPATH') or die('No direct script access.');

class File_Model extends ORM implements ACL_Resource_Interface {
  
  protected $belongs_to = array('user');
  
  protected $sorting = array('name' => 'ASC');
  
  protected $belongs_to_polymorphic = array('folder' => 'id', 'album' => 'id');
  
  protected $has_many_polymorphic = array('comments' => 'object');
  
  /**
   * Find files based on the parent object id.
   */
  public function find_by_parent($object_type, $object_id) {
    return $this->where(array('object_type' => $object_type, 'object_id' => $object_id))->find_keyed_array();
  }
  
  public function find_recent_documents($site_id, $count = 25) {
    return $this->custom_join('folders', array('files.object_type' => '"folder"', 'files.object_id' => 'folders.id'), NULL, 'LEFT')
      ->where('files.site_id', $site_id)
      ->where('object_type', 'folder')
      ->where('folders.status', TRUE)
      ->orderby('files.id', 'DESC')
      ->find_all($count, 0);
  }
  
  /**
   * Find all published comments that belong to a topic.
   */
  public function comments() {
    // custom join to preload whether or not the user liked the comment.
    $join_on = array(
      'comments.id' => 'votes.object_id',
      'votes.object_type' => '"comment"',
      'votes.user_id' => kohana::config('chapterboard.user_id')
    );
    return ORM::factory('comment')->select('value AS liked, comments.*')->custom_join('votes', $join_on, null, 'LEFT')->where(array('comments.object_type' => 'file', 'comments.object_id' => $this->id, 'comments.status' => 1))->find_all();
  }
  
  /**
   * Return a url to download the original file.
   */
  public function url() {
    return sprintf('file/original/%s', $this->filename);
  }
  
  /**
   * Return a html-class friendly string based on the file extension.
   */
  public function type() {
    switch ($this->extension) {
      case 'msword':
      case 'vnd.openxmlforma':
        return 'word';
      case 'pdf':
        return 'pdf';
      case 'vnd.ms-excel':
        return 'excel';
      case 'jpg':
      case 'png':
      case 'gif':
      case 'jpeg':
        return 'image';
      case 'vnd.adobe.photos':
        return 'psd';
      default:
        return 'default';
    }
  }
  
  /**
   * Generic insert function to create new records.
   */
  public function insert($data) {
    if ($id = $data[$this->primary_key]) {
      $row = ORM::factory(inflector::singular($this->table_name), $id);
    }
    else {
      $row = ORM::factory(inflector::singular($this->table_name));
    }
    foreach ($data as $key => $value) {
      if (isset($this->table_columns[$key]))
        $row->$key = $value;
    }
    Kohana::log('debug', 'Inserting new file record', $row->as_array());
    return $row->save();
  }
  
  /**
   * Save uploaded files that have been stored in the user's session.
   */
  public function save_uploads($session_key, $object_type, $object_id) {
    // Comment has been saved, now save uploaded files.
    $session = Session::instance();
    $this->where('object_type', $object_type)->where('object_id', $object_id)->delete_all();
    if ($uploads = $session->get('uploads-'. $session_key)) {
      foreach ($uploads as $upload) {
        // Move temp files to upload directory and insert records into database.
        if ($fileinfo = upload::save($upload, $upload['filename'], Kohana::config('upload.directory'))) {
          $upload = array_merge($upload, $fileinfo);
        }
        $upload['object_type'] = $object_type;
        $upload['object_id'] = $object_id;
        ORM::factory('file')->insert($upload);
      }
      $session->delete($session_key);
    }
  }
  
  /**
   * Validation
   */
  public function validate(array &$array, $save = FALSE) {
   $array = Validation::factory($array)
       ->pre_filter('trim')
       ->add_rules('name', 'required')
       ->add_rules('description', 'blob')
       ->add_rules('object_id', 'required', 'numeric')
       ->add_rules('object_type', 'required', 'standard_text')
       ->add_rules('size', 'required', 'numeric')
       ->add_rules('mime', 'required')
       ->add_rules('extension', 'required')
       ->add_rules('filepath', 'required')
       ->add_rules('filename', 'required')
       ->add_callbacks('object_id', array($this, '_folder_check'));
    return parent::validate($array, $save);
  }
  
  public function update_validate(array &$array, $save = FALSE) {
    $array = Validation::factory($array)
      ->pre_filter('trim')
      ->add_rules('name', 'required')
      ->add_rules('description', 'blob')
      ->add_rules('object_id', 'required', 'numeric')
      ->add_callbacks('object_id', array($this, '_folder_check'));
    return parent::validate($array, $save);
  }
  
  /**
   * Ensure access to attach files to the requested folder.
   */
  public function _folder_check(Validation $array, $field) {
    
  }
  
  /**
   * ORM before_insert hook.
   */
  public function before_insert() {
    $this->site_id = kohana::config('chapterboard.site_id');
    $this->user_id = kohana::config('chapterboard.user_id');
    $this->created = date::to_db();
  }

  /**
   * Delete the original file when deleting the row.
   */
  public function delete($id = NULL) {
    unlink($this->filepath);
    return parent::delete($id);
  }
  
  public function get_resource_id() {
    return 'file';
  }
}