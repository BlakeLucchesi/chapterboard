<?php defined('SYSPATH') or die('No direct script access.');

class Album_Model extends ORM implements ACL_Resource_Interface {
  
  protected $belongs_to = array('site', 'user');
  
  protected $has_many_polymorphic = array('files' => 'object');
  
  protected $has_one = array('file');
  
  protected $sorting = array('created' => 'DESC');
  
  public function find_by_site($site_id = NULL, $limit = NULL, $offset = NULL) {
    $site_id = is_null($site_id) ? kohana::config('chapterboard.site_id') : $site_id;
    return $this->where('albums.site_id', $site_id)->find_all($limit, $offset);
  }
  
  public function recent_photos($count = 10, $site_id = NULL) {
    $site_id = is_null($site_id) ? kohana::config('chapterboard.site_id') : $site_id;
    return ORM::factory('file')->where('site_id', $site_id)->where('object_type', 'album')->orderby('id', 'DESC')->find_all($count);
  }
  
  public function count_albums($site_id) {
    $site_id = is_null($site_id) ? kohana::config('chapterboard.site_id') : $site_id;
    return $this->db->query("SELECT COUNT(*) AS count FROM albums WHERE site_id = ?", array($site_id))->current()->count;
  }
  
  public function count_photos() {
    return $this->db->query("SELECT COUNT(*) AS count FROM files WHERE object_type = 'album' AND object_id = ?", array($this->id))->current()->count;
  }
  
  /**
   * Album navigation.
   */
  public function find_photos($limit = NULL, $offset = NULL) {
    return ORM::factory('file')
      ->where('object_type', 'album')
      ->where('object_id', $this->id)
      ->orderby('id', 'DESC')
      ->find_all($limit, $offset);
  }

  /**
   * @param loaded photo ORM object currently being viewed.
   */
  public function next_photo($photo) {
    return ORM::factory('file')->orderby('id', 'DESC')
      ->where('id <', $photo->id)
      ->where('object_type', 'album')
      ->where('object_id', $photo->object_id)
      ->find();
  }
  
  public function previous_photo($photo) {
    return ORM::factory('file')->orderby('id', 'ASC')
      ->where('id >', $photo->id)
      ->where('object_type', 'album')
      ->where('object_id', $photo->object_id)
      ->find();
  }
  
  /**
   * Calculate the current photo's order in the album.
   */
  public function album_order($photo) {
    $this->db->query("SET @rownum := 0;");
    $item_number = $this->db->query("
      SELECT * FROM (
      SELECT @rownum := @rownum+1 AS item_number, id
      FROM files WHERE object_id = ? AND object_type = 'album' 
      ORDER BY id DESC
      ) AS derived_table WHERE id = ?;
    ", array($photo->object_id, $photo->id))->current()->item_number;
    return sprintf('%s of %s', $item_number, $photo->album->count_photos());
  }
    
  /**
   * Validation.
   */
  public function validate(array &$array, $save = FALSE) {
    $array = Validation::factory($array)
    ->pre_filter('trim')
    ->add_rules('title', 'required')
    ->add_rules('description', 'blob');
    return parent::validate($array, $save);
  }
  
  public function before_insert() {
    $this->user_id = kohana::config('chapterboard.user_id');
    $this->site_id = kohana::config('chapterboard.site_id');
    $this->created = date::to_db();
    $this->updated = $this->created;
  }
  
  public function __get($column) {
    switch ($column) {
      case 'photos':
        return parent::__get('files');
      case 'thumbnail':
        // Always show the first photo in the folder as the thumbnail.
        if ($this->file_id) {
          return ORM::factory('file')->where('id', $this->file_id)->find()->filename;
        }
        else {
          // Auto select a thumbnail.
          if ($this->files->count()) {
            $this->file_id = ORM::factory('file')->where('object_type', 'album')->where('object_id', $this->id)->orderby('id', 'DESC')->find()->id;
            $this->save();
            return $this->__get('thumbnail');
          }
          return '_photo.gif';
        }
    }
    return parent::__get($column);
  }
  
  public function get_resource_id() {
    return 'album';
  }
}