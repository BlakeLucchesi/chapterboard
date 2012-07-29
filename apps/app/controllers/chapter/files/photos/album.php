<?php defined('SYSPATH') or die('No direct script access.');

class Album_Controller extends Files_Controller {
  
  public function index() {
    url::redirect('files/photos');
  }
  
  /**
   * Display photos from the album.
   */
  public function show($id) {
    $this->album = ORM::factory('album', $id);
    
    $this->pagination = new Pagination(array('total_items' => $this->album->count_photos()));
    $limit = $this->pagination->items_per_page;
    $offset = $this->pagination->sql_offset();
    $this->photos = $this->album->find_photos($limit, $offset);
    
    if ( ! $this->album->loaded)
      Event::run('system.404');
    
    $this->title = $this->album->title;
  }
  
  public function edit($id) {
    $this->album = ORM::factory('album', $id);
    if ( ! $this->album->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->album, 'edit'))
      Event::run('system.403');
    
    $this->title = sprintf('Editing Album: %s', $this->album->title);
    $this->view = 'files/photos/album/form';
    $this->form = $this->album->as_array();
    if ($post = $this->input->post()) {
      if ($this->album->validate($post, TRUE)) {
        message::add(TRUE, '%s album updated successfully.', $this->album->title);
        url::redirect('files/photos/album/'. $this->album->id);
      }
      else {
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_album');
      }
    }
  }
  
  public function delete($id) {
    $this->album = ORM::factory('album', $id);
    if ( ! $this->album->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->album, 'edit'))
      Event::run('system.403');
      
    message::add(TRUE, '%s album deleted succesfully.', $this->album->title);
    foreach ($this->album->photos as $photo) {
      $photo->delete();
    }
    $this->album->delete();
    url::redirect('files/photos');
  }
  
  /**
   * Add new album.
   */
  public function add() {
    $this->title = 'Create Album';
    $this->view = 'files/photos/album/form';
    
    if ($post = $this->input->post()) {
      $this->album = ORM::factory('album');
      if ($this->album->validate($post, TRUE)) {
        message::add(TRUE, 'Album created successfully. '. html::anchor('files/photos/album/upload/'. $this->album->id .'?KeepThis=true&amp;TB_iframe=true&amp;height=400&amp;width=600&amp;modal=true', 'Add photos', array('class' => 'thickbox')));
        url::redirect('files/photos/album/'. $this->album->id);
      }
      else {
        message::add(FALSE, 'Please fix the errors below.');
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_album');
      }
    }
  }
  
  /**
   * Popup upload window to upload files to the album.
   */
  public function upload($id) {
    $this->template = 'basic';
    $this->album = ORM::factory('album', $id);
    $this->title = sprintf('Upload Photos to %s', $this->album->title);
    
    // Allow uploads on the album page.
    css::add('styles/fileuploader.css');
    javascript::add('scripts/fileuploader.js');
    javascript::add("
      new qq.FileUploader({
        element: document.getElementById('upload-photos'),
        action: '/files/photos/upload/{$this->album->id}',
        allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
        sizeLimit: 1024 * 1024 * 5, // max size 3MB
        minSizeLimit: 0, // min size
        template: '<div class=\"qq-uploader\">' + 
                '<div class=\"qq-upload-drop-area\"><span>Drop photos here to upload</span></div>' +
                '<div class=\"qq-upload-button\">Upload a photo</div><div class=\"qq-upload-drop-notice\">or drag and drop files here.</div>' +
                '<ul class=\"qq-upload-list\"></ul>' + 
             '</div>',
        // debug: true
    });", 'inline');
  }
  
  public function photo($id) {
    $this->photo = ORM::factory('file', $id);
    if ( ! ($this->photo->loaded && $this->photo->object_type == 'album'))
      Event::run('system.404');
      
    javascript::add('scripts/photos.js');
    $this->next_photo = ORM::factory('album')->next_photo($this->photo);
    $this->previous_photo = ORM::factory('album')->previous_photo($this->photo);
    $this->album_order = ORM::factory('album')->album_order($this->photo);
    $this->title = sprintf('Photo: %s', $this->photo->name);
    
    if ($post = $this->input->post()) {
      $comment = ORM::factory('comment');
      $comment->object_type = 'file';
      $comment->object_id = $this->photo->id;
      if ($comment->validate($post, TRUE)) {
        message::add(TRUE, 'Your comment has been added.');
      }
      else {
        message::add(FALSE, 'Unable to post your comment, please fix the errors below and try again.');
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_comment');
      }
    }
  }
  
}