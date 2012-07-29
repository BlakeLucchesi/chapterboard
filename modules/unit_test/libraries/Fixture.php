<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Fixture Class
 * loads fixtures for testing
 *
 * @author     John Brennan
 * @link 	   http://www.janisb.com
 * @license    http://www.opensource.org/licenses/mit-license.php
 */

class Fixture_Core {

  protected $db;
  protected $fixture_path;
  
  function __construct($db, $fixure_path){
    if(!is_dir($fixure_path)){
      exit("{$fixure_path} is not a valid path to fixtures.  Please redefine in config.");
    }
    $this->db = $db;
    $this->fixture_path = $fixure_path;
  }

  /*
   * loads fixture data $fixt into corresponding table
   *
   * @param String $fixt Name of fixture file to load (without the extension). Found in the fixtures folder of the test directory
   */
  function load($fixt){
    $Spyc = new Spyc;
    $fixt_data = $Spyc->YAMLLoad($this->fixture_path.DIRECTORY_SEPARATOR.$fixt);
    var_dump($fixt_data);
    # $fixt_data is supposed to be an associative array outputted by spyc from YAML file
    foreach($fixt_data as $table => $data) {
      $this->db->query("TRUNCATE TABLE {$table}");
      foreach ($data as $col => $row) {
        $this->db->insert($table, $row);
      }
    }
  }
  
}