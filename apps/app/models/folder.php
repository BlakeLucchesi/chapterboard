<?php defined('SYSPATH') or die('No direct script access.');

class Folder_Model extends ORM_Tree implements ACL_Resource_Interface {
  
  protected $child_name = 'folders';
  
  protected $parent_key = 'parent_id';
  
  protected $belongs_to = array('site', 'chapter', 'user');
  
  protected $has_many_polymorphic = array('files' => 'object');
  
  protected $sorting = array('name' => 'ASC');
  
  protected $level_marker = '-'; // Used in select_options();

  public function find_by_site($site_id, $children = FALSE) {
    if ( ! $children) {
      $this->where('parent_id', 0);
    }
    $this->where('status', 1);
    return $this->where('site_id', $site_id)->find_all();
  }
  
  public function find_by_chapter($chapter_id, $children = FALSE) {
    if ( ! $children) {
      $this->where('parent_id', 0);
    }
    $this->where('status', 1)->where('national', TRUE);
    return $this->where('chapter_id', $chapter_id)->find_all();
  }
  
  /**
   * Returns an array representing the path to the current folder.
   */
  public function path($show_current = FALSE) {
    $path = array();
    if ($this->parent->loaded) {
      $path += $this->parent->path(TRUE);
    }
    if ($show_current) {
      $path += array($this->id => $this->name);
    }
    return $path;
  }
  
  /**
   * Validation.
   */
  public function validate(array &$array, $save = FALSE) {
    $array = Validation::factory($array)
    ->pre_filter('trim')
    ->add_rules('name', 'required')
    ->add_rules('description', 'blob')
    ->add_rules('national', 'numeric')
    ->add_rules('parent_id', 'numeric');
    return parent::validate($array, $save);
  }
  
  
  public function before_insert() {
    $this->site_id = Kohana::config('chapterboard.site_id');
    $this->user_id = Kohana::config('chapterboard.user_id');
    $this->chapter_id = Kohana::config('chapterboard.chapter_id');
    $this->created = date::to_db();
    $this->updated = $this->created;
    $this->status = 1;
  }
  
  /**
   * Update timestamp.
   *
   * Whenever a new file or folder is added a folder we need to
   * recurse up the tree updating each parent folder's updated
   * field. This is done outside of before_update hook because
   * of a concern for possible race condition.
   */
  public function update_timestamp() {
    if ($this->parent->loaded) {
      $this->parent->update_timestamp();
    }
    $this->updated = date::to_db();
    $this->save();
  }
  
	/**
	 * Creates a key/value array from all of the objects available. Uses find_all
	 * to find the objects.
	 *
	 * @param   string  key column
	 * @param   string  value column
	 * @return  array
	 */
	public function select_list($blank = TRUE, $current_id = NULL) {
		// Return a select list from the results
		$results = array();
		if ($blank) {
      $results[] = 'Top Level Folder';
		}
		$query = $this->where('status', 1)->where('parent_id', 0)->find_all();
		foreach ($query as $row) {
		  $results += $row->select_options($blank ? $this->level_marker : '', $current_id);
		}
		return $results;
	}
	
	public function select_options($level_marker, $current_id = NULL) {
	  $results = array();
	  if ($this->id == $current_id) {
	    return $results;
	  }
    $results[$this->id] = sprintf('%s %s', $level_marker, $this->name);
    foreach ($this->children as $child) {
      $results += $child->select_options($level_marker . $this->level_marker, $current_id);
    }
    return $results;
	}
	
	public function select_list_parents($site_id, $current_id = NULL) {
	  $this->where('site_id', $site_id);
	  return $this->select_list(TRUE, $current_id);
	}
	
	public function select_list_all($site_id, $current_id = NULL) {
	  $this->where('site_id', $site_id);
	  return $this->select_list(FALSE, $current_id);
	}

  /**
   * Delete a folder and all of its contents.
   */
  public function delete() {
    $this->status = 0;
    foreach ($this->children as $child) {
      $child->delete();
    }
    return $this->save();
  }

  public function get_resource_id() {
    return 'folder';
  }
  
  public function __get($column) {
    if ($column === 'children') {
			if (empty($this->related[$column])) {
				$model = ORM::factory(inflector::singular($this->child_name));

				if ($this->child_name === $this->table_name) {
					// Load children within this table
					$this->related[$column] = $model
						->where($this->parent_key, $this->object[$this->primary_key])
            ->where('status', TRUE)
						->find_all();
				}
				else {
					// Find first selection of children
					$this->related[$column] = $model
						->where($this->foreign_key(), $this->object[$this->primary_key])
						->where($this->parent_key, NULL)
						->find_all();
				}
			}

			return $this->related[$column];
		}
		return parent::__get($column);
  }
  
}