<?php defined('SYSPATH') or die('No direct script access.');
/**
 * 
 * Extend the Migrations core class so that we can store the current
 * schema revision in a 'versions' table.  This makes it easier
 * for maintaining multi-site installs and tracking changes
 * from dev to production.
 */

class Migrations extends Migrations_Core {

  protected $db;

  /**
   * __construct to get a db instance.
   */
   public function __construct() {
     parent::__construct();
     $this->db = Database::instance();
   }
   
  /**
	 * Retrieves current schema version
	 *
	 * @return	integer	Current Schema version
	 */
	public function get_schema_version()
	{
	  if (!$this->db->table_exists('version')) {
	    $this->db->query("CREATE TABLE version ( id INT NOT NULL, INDEX ( id ) )");
	    $this->db->query("INSERT INTO version (id) VALUES (0)");
	  }
  
	  $version = $this->db->query("SELECT id FROM version");
	  return $version->current()->id;
	}

	
	/**
	 * Stores the current schema version
	 *
	 * @param  integer  Schema version reached
	 */
	protected function update_schema_version($schema_version)
	{
    $version = $this->db->query("UPDATE version SET id = ?", $schema_version);
	}
}