<?php defined('SYSPATH') or die('No direct script access.');

class Poll_Model extends ORM {
  
  protected $belongs_to = array('topic');
  
  protected $has_many = array('poll_choices', 'poll_votes');
  
  /**
   * Whether or not poll results should be hidden from members.
   */
  public function is_private() {
    return (bool) $this->private;
  }
  
  /**
   * Save a users vote.
   */
  public function vote($choice_id, $user_id) {
    $existing = ORM::factory('poll_vote')->where('poll_id', $this->id)->where('user_id', $user_id)->find_all();
    foreach ($existing as $remove) {
      $remove->delete();
    }
    
    $vote = ORM::factory('poll_vote');
    $vote->poll_id = $this->id;
    $vote->poll_choice_id = $choice_id;
    $vote->user_id = $user_id;
    return $vote->save();
  }
  
  /**
   * Delete a user's vote.
   */
  public function remove_vote($user_id) {
    $vote = ORM::factory('poll_vote')->where('poll_id', $this->id)->where('user_id', $user_id)->find();
    $choice = $vote->poll_choice;
    $vote->delete();
    $choice->update_count();
    return $this->update_count();
  }
  
  /**
   * Set status to published.
   */
  public function before_insert() {
    $this->status = 1;
  }
  
  public function __get($column) {
    if ($column == 'choices') {
      return ORM::factory('poll_choice')->where('poll_id', $this->id)->find_all();
    }
    return parent::__get($column);
  }
  
  /**
   * Update Count
   */
  public function update_count() {
    $this->votes = $this->db->query("SELECT SUM(votes) votes FROM poll_choices WHERE poll_id = ? GROUP BY poll_id", $this->id)->current()->votes;
    $this->save();
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

    $this->db->where('poll_id', $this->id)->delete('poll_choices');
    $this->db->where('poll_id', $this->id)->delete('poll_votes');
		// Delete this object
		$this->db->where($this->primary_key, $id)->delete($this->table_name);

		return $this->clear();
	}
}