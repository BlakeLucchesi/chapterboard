<?php defined('SYSPATH') or die('No direct script access.');

/**
 * CodeIgniter MongoDB Active Record Library
 *
 * A library to interface with the NoSQL database MongoDB. For more information see http://www.mongodb.org
 *
 * @package		CodeIgniter
 * @author		Alex Bilbie | www.alexbilbie.com | alex@alexbilbie.com
 * @copyright	Copyright (c) 2010, Alex Bilbie.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://alexbilbie.com/code/
 * @version		Version 0.2
 */
class Mongo_DB_Core {

	private $connection;
	private $db;
	
	private $select = array();
	private $where = array();
	private $limit = NULL;
	private $offset = NULL;
	private $sort = array();

	function __construct()
	{
		if(!class_exists('Mongo'))
		{
			show_error('It looks like the MongoDB PECL extension isn\'t installed or enabled', 500);
		}
		return $this->connect();
	}
	
	/* Connect function
	 *
	 * Connect to a Mongo database
	 *
	 * Usage: $this->mongo_db->connect();
	 */ 
	function connect($host = 'localhost:27017', $db = "") {
		$config = Kohana::config('mongo_db');
		$host = $host ? $host : $config['host'];
		$db = $db ? $db : $config['database'];
    
    $this->connection = new Mongo("{$host}");
		
		if ( ! empty($db)) {
			$this->db = $db;
		}
		else {
			throw new Kohana_Exception('No Mongo database selected.');
		}
		
		return $this;
	}
	
	//! Get Functions
	
	/* Select function
	 *
	 * Select specific fields from a document
	 *
	 * Usage: $this->mongo_db->select(array('foo','bar'))->get('foobar');
	 */ 
	function select($what = array())
	{
		if(is_array($what) && count($what) > 0)
		{
			$this->select = $what;
		}
		elseif($what !== "")
		{
			$this->select = array();
			$this->select[] = $what;
		}
		
		return $this;
	}
	
	/* Where function
	 *
	 * Get documents where something
	 *
	 * Usage: $this->mongo_db->where(array('foo' => 1))->get('foobar');
	 */ 
	function where($where = array())
	{
		$this->where = $where;
		return $this;
	}
	
	/* Where_in function
	 *
	 * Get documents where something is in an array of something
	 *
	 * Usage: $this->mongo_db->where_in('foo', array(1,2,3))->get('foobar');
	 */ 
	function where_in($what = "", $in = array())
	{
		if(!isset($this->where[$what]))
		{
			$this->where[$what] = array();
		}
		$this->where[$what]['$in'] = $in;
		return $this;
	}
	
	/* Where_in function
	 *
	 * Get documents where something is in all of an array of something
	 *
	 * Usage: $this->mongo_db->where_in_all('foo', array(1,2,3))->get('foobar');
	 */
	function where_in_all($what = "", $in = array())
	{
		if(!isset($this->where[$what]))
		{
			$this->where[$what] = array();
		}
		$this->where[$what]['$all'] = $in;
		return $this;
	}
	
	/* Where_not_in function
	 *
	 * Get documents where something is not in an array of something
	 *
	 * Usage: $this->mongo_db->where_not_in('foo', array(1,2,3))->get('foobar');
	 */
	function where_not_in($what = "", $in)
	{
		if(!isset($this->where[$what]))
		{
			$this->where[$what] = array();
		}
		$this->where[$what]['$nin'] = $in;
		return $this;
	}
	
	/* Where_gt function
	 *
	 * Get documents where something is greater than something
	 *
	 * Usage: $this->mongo_db->where_gt('foo', 1)->get('foobar');
	 */
	function where_gt($what, $gt)
	{
		if(!isset($this->where[$what]))
		{
			$this->where[$what] = array();
		}
		$this->where[$what]['$gt'] = $gt;
		return $this;
	}
	
	/* Where_gte function
	 *
	 * Get documents where something is greater than or equal to something
	 *
	 * Usage: $this->mongo_db->where_gte('foo', 1)->get('foobar');
	 */
	function where_gte($what, $gte)
	{
		if(!isset($this->where[$what]))
		{
			$this->where[$what] = array();
		}
		$this->where[$what]['$gte'] = $gte;
		return $this;
	}
	
	/* Where_lt function
	 *
	 * Get documents where something is lee than something
	 *
	 * Usage: $this->mongo_db->where_lt('foo', 1)->get('foobar');
	 */
	function where_lt($what, $lt)
	{
		if(!isset($this->where[$what]))
		{
			$this->where[$what] = array();
		}
		$this->where[$what]['$lt'] = $lt;
		return $this;
	}
	
	/* Where_lte function
	 *
	 * Get documents where something is less than or equal to something
	 *
	 * Usage: $this->mongo_db->where_lte('foo', 1)->get('foobar');
	 */
	function where_lte($what, $lte)
	{
		if(!isset($this->where[$what]))
		{
			$this->where[$what] = array();
		}
		$this->where[$what]['$lte'] = $lte;
		return $this;
	}
	
	/* Where_lte function
	 *
	 * Get documents where something is not equal to something
	 *
	 * Usage: $this->mongo_db->where_not_equal('foo', 1)->get('foobar');
	 */
	function where_not_equal($what, $to)
	{
		if(!isset($this->where[$what]))
		{
			$this->where[$what] = array();
		}
		$this->where[$what]['$ne'] = $to;
		return $this;
	}
	
	/* Order_by function
	 *
	 * Order documents by something ascending (1) or descending (-1)
	 *
	 * Usage: $this->mongo_db->order_by('foo', 1)->get('foobar');
	 */
	function order_by($what, $order = "ASC")
	{
	  $order = ($order == 'ASC') ? 1 : -1;
		$this->sort[$what] = $order;
		return $this;
	}
	
	/* Limit function
	 *
	 * Limit the returned documents by something (and optionally an offset)
	 *
	 * Usage: $this->mongo_db->limit(5,5)->get('foobar');
	 */
	function limit($limit = NULL, $offset = NULL)
	{
		if($limit !== NULL && is_numeric($limit) && $limit >= 1)
		{
			$this->limit = $limit;
		}
		
		if($offset !== NULL && is_numeric($offset) && $offset >= 1)
		{
			$this->offset = $offset;
		}
		
		return $this;
	}
	
	/* Get_where function
	 *
	 * Get documents where something
	 *
	 * Usage: $this->mongo_db->get_where('foobar', array('foo' => 'bar'));
	 */
	function get_where($collection = "", $where = array())
	{
		return $this->where($where)->get($collection);
	}
	
	/* Get function
	 *
	 * Get documents from a collection
	 *
	 * Usage: $this->mongo_db->get('foobar');
	 */
	function get($collection = "")
	{
		if($collection !== "")
		{
			$results = array();
						
			// Initial query
			$documents = $this->connection->{$this->db}->{$collection}->find($this->where);
			
			// Sort the results
			if ( ! empty($this->sort)) {
			  $documents = $documents->sort($this->sort);
			}
			
			// Limit the results
			if($this->limit !== NULL) {
				$documents = $documents->limit($this->limit);
			}
			
			// Offset the results
			if($this->offset !== NULL) {
				$documents = $documents->skip($this->offset);
			}
			
			// Get the results
			while($documents->hasNext())
			{
				$document = $documents->getNext();
				if($this->select !== NULL && count($this->select) > 0)
				{
					foreach($this->select as $s)
					{
						if(isset($document[$s])){
							$results[][$s] = $document[$s];
						}
					}
				}
				else
				{
					$results[] = $document;
				}
				
			}
			
			return $results;
		}
		
		else
		{
			show_error('No Mongo collection selected to query', 500);
		}	
	}
	
	/* Count function
	 *
	 * Count the number of documents
	 *
	 * Usage: $this->mongo_db->where(array('foo' => 'bar'))->count('foobar');
	 */
	function count($collection = "")
	{
		if($collection !== "")
		{			
			// Initial query
			$documents = $this->connection->{$this->db}->{$collection}->find($this->where);
			
			// Limit the results
			if($this->limit !== NULL)
			{
				$documents = $documents->limit($this->limit);
			}
			
			// Offset the results
			if($this->offset !== NULL)
			{
				$documents = $documents->skip($this->offset);
			}
			
			return $documents->count();
		}
		
		else
		{
			show_error('No Mongo collection selected', 500);
		}	
	}
	
	//! Insert functions
	
	/* Insert function
	 *
	 * Insert a new document into a collection
	 *
	 * Usage: $this->mongo_db->insert('foobar', array('foo' => 'bar'));
	 */
	function insert($collection = "", $insert = array())
	{
		if($collection !== "")
		{
			show_error("No Mongo collection selected to insert into", 500);
		}
		
		if(count($insert) == 0)
		{
			show_error("Nothing to insert into Mongo collection", 500);
		}
		
		$this->connection->{$this->db}->{$collection}->insert($insert);
	}

}