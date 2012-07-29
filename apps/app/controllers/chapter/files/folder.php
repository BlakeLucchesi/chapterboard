<?php defined('SYSPATH') or die('No direct script access.');

class Folder_Controller extends Files_Controller {
  
  public function index($id = NULL) {
    $this->folder = ORM::factory('folder')->where('status', TRUE)->where('id', $id)->find();
    if ( ! $this->folder->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->folder, 'view'))
      Event::run('system.403');
    
    $this->title = $this->folder->name;
    $groups = ORM::factory('group_rule')->get_groups_for_resource('folder', $this->folder->id, 'view');
    foreach ($groups as $group) {
      $this->allowed_groups[] = $group->name;
    }
  }
  
  public function add() {
    $this->view = 'files/folder/form';
    if ( ! A2::instance()->allowed('file', 'manage'))
      Event::run('system.403');
    
    $this->title = 'Add Folder';
    $this->folders = ORM::factory('folder')->select_list_parents($this->site->id);
    $this->form = $this->input->get();
    $this->_setup_form();
    
    if ($post = $this->input->post()) {
      $groups = array_filter($post['groups']);
      if ( ! empty($groups)) {
        $this->folder = ORM::factory('folder');
        if ($this->folder->validate($post, TRUE)) {
          $this->folder->update_timestamp();
          ORM::factory('group_rule')->set_rules_for_resource('folder', $this->folder->id, 'view', $groups);
          message::add(TRUE, '%s folder created successfully.', $this->folder->name);
          url::redirect('files/folder/'. $this->folder->id);
        }
        else {
          message::add(FALSE, 'Please fix the errors below and try again.');
          $this->form = $post->as_array();
          $this->errors = $post->errors('form_files_folder');
        }     
      }
      else {
        message::add(FALSE, 'Choose at least one group who will have access to this folder.');
        $this->form = $post;
      }
    }
  }
  
  public function edit($id) {
    $this->view = 'files/folder/form';
    $this->folder = ORM::factory('folder')->where('status', TRUE)->where('id', $id)->find();
    if ( ! $this->folder->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->folder, 'edit'))
      Event::run('system.403');
    
    $this->title = 'Editing Folder: '. $this->folder->name;
    $this->folders = ORM::factory('folder')->select_list_parents($this->site->id, $this->folder->id);
    $this->form = $this->folder->as_array();
    $this->_setup_form();
    
    if ($post = $this->input->post()) {
      $groups = array_filter($post['groups']);
      if ( ! empty($post['groups'])) {
        if ($this->folder->validate($post, TRUE)) {
          $this->folder->update_timestamp();
          ORM::factory('group_rule')->set_rules_for_resource('folder', $this->folder->id, 'view', $groups);
          message::add(TRUE, '%s folder saved succesfully.', $this->folder->name);
          url::redirect('files/folder/'. $this->folder->id);
        }
        else {
          message::add(FALSE, 'Please fix the errors below and try again.');
          $this->form = $post->as_array();
          $this->errors = $post->errors('file_files_folder');
        }
      }
      else {
        message::add(FALSE, 'Choose at least one group who will have access to this folder.');
        $this->form = $post;
      }
    }
  }
  
  public function _setup_form() {
    $this->groups = ORM::factory('group')->find_keyed_object();
    $this->form['groups'] = ORM::factory('group_rule')->get_rules_for_resource('folder', $this->folder->id);
    $permissions = ORM::factory('group_rule')->get_rules('folder', $this->site->id);
    javascript::add(array('permissions' => $permissions), 'setting');
    javascript::add('scripts/folder_perms.js');
  }
  
  public function delete($id) {
    $this->folder = ORM::factory('folder')->where('status', TRUE)->where('id', $id)->find();
    if ( ! $this->folder->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->folder, 'delete'))
      Event::run('system.403');
    
    $path = $this->folder->parent->loaded ? 'files/folder/'. $this->folder->parent->id : 'files';
    $this->folder->delete();
    url::redirect($path);
  }
  
}