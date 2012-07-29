<?php defined('SYSPATH') or die('No direct script access.');

class Department_Model extends ORM {
  
  protected $has_many = array('courses');
  
  protected $belongs_to = array('site_id');

	/**
	 * Creates a key/value array from all of the objects available. Uses find_all
	 * to find the objects.
	 *
	 * @param   string  key column
	 * @param   string  value column
	 * @return  array
	 */
	public function select_list($key = NULL, $val = NULL) {
	  $this->where('site_id', kohana::config('chapterboard.site_id'));
		if ($key === NULL)
		{
			$key = $this->primary_key;
		}

		if ($val === NULL)
		{
			$val = $this->primary_val;
		}

		// Return a select list from the results
		return $this->select($key, $val)->find_all()->select_list($key, $val);
	}
	
}