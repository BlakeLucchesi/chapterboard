<?php defined('SYSPATH') or die('No direct script access.');

class Poll_choice_Model extends ORM {
  
  protected $belongs_to = array('poll');
  
  protected $has_many = array('poll_votes');
    
  /**
   * Update vote count.
   */
  public function update_count() {
    $this->votes = $this->db->query("SELECT COUNT(*) votes FROM poll_votes WHERE poll_choice_id = ?", $this->id)->current()->votes;
    $this->save();
    $this->poll->update_count();
    return TRUE;
  }

  public function percent($sig_digit = 0) {
    if ($this->poll->votes) {
      return number_format(($this->votes / $this->poll->votes) * 100, $sig_digit);
    }
    return 0;
  }
  
  /**
   * Validation.
   */
  public function validate(array &$array, $save = FALSE) {
    $array = Validation::factory($array)
    ->pre_filter('trim')
    ->add_rules('poll_id', 'digit')
    ->add_rules('text', 'length[1,255]');
    return parent::validate($array, $save);
  }
  
  /**
	 * Deletes the current object from the database. This does NOT destroy
	 * relationships that have been created with other objects.
	 *
	 * @chainable
	 * @return  ORM
	 */
	public function delete($id = NULL)
	{
		if ($id === NULL AND $this->loaded)
		{
			// Use the the primary key value
			$id = $this->object[$this->primary_key];
		}

    $poll = $this->poll;
    
    $this->db->where('poll_choice_id', $this->id)->delete('poll_votes');
		// Delete this object
		$this->db->where($this->primary_key, $id)->delete($this->table_name);

    $poll->update_count();

		return $this->clear();
	}
	
}