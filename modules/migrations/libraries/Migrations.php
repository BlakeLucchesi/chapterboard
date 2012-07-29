<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Migrations
 *
 * An open source utility inspired by Ruby on Rails
 *
 * Reworked for Kohana by Jamie Madill
 *
 * @package		Migrations
 * @author		Matías Montes
 * @author      Jamie Madill
 */

class Migrations_Core
{
	protected $config;
	protected $group;
	protected $output;

	/**
	 * Intialize migration library
	 *
	 * @param   bool   Do we want output of migration steps?
	 * @param   string Database group
	 */
	public function __construct($output = FALSE, $group = 'default')
	{
		$this->config = Kohana::config('migrations');
		$this->group  = $group;
		$this->output = $output;

		if (!$this->config['enabled'])
		{
			throw new Kohana_Exception('migrations.disabled');
		}
		
		$this->config['path'] = $this->config['path'][$group];
		$this->config['info'] = $this->config['path'] . $this->config['info'] . '/';
	}

	/**
	 * Enable log output
	 */
	public function enable_output()
	{
		$this->output = TRUE;
	}
	
	/**
	 * Disable log output
	 */
	public function disable_output()
	{
		$this->output = FALSE;
	}

	/**
	 * Installs the schema up to the last version
	 * Outputs a report of the installation
	 */
	public function install()
	{
		$last_version = $this->last_schema_version();
		
		if ($last_version === FALSE)
		{
			throw new Kohana_Exception(Kohana::lang('migrations.none_found'));
		}
		
		$this->version($last_version);
	}

	/**
	 * Migrate to a schema version
	 *
	 * Calls each migration step required to get to the schema version of
	 * choice
	 * Outputs a report of the migration
	 *
	 * @param   integer   Target schema version
	 */
	public function version($version = 0)
	{
		if (!$this->config['enabled']) return;
		
		$schema_version = $this->get_schema_version();
		$last_version   = $this->last_schema_version();

		if ($version == $schema_version)
		{
			$this->log('Nothing to do, bye!');
			return;
		}

		$method = $version > $schema_version ? 'up' : 'down';

		$this->log("<p>Current schema version: $schema_version<br/>");
		$this->log("Moving $method to version $version</p>");
		$this->log('<hr/>');

		for ($i = $schema_version; $i != $version;)
		{
			if ($method == 'up') $i++;
			
			// This is what this is all about
			$migration = $this->load_migration($i);

			$this->log(get_class($migration).'<br />');
			$this->log('<blockquote>');
			$migration->$method();
			$this->log('</blockquote>');
      $this->log($migration->get_log());
			$this->log('<hr/>');

			if ($method == 'down') $i--;
		}

		$this->update_schema_version($version);
		$this->log("<p>All done. Schema is at version $version.</p>");
	}
	
	/**
	 * Loads a migration
	 *
	 * @param   integer   Migration version number
	 * @return  Migration_Core  Class object
	 */
	protected function load_migration($version)
	{
		$f = glob(sprintf($this->config['path'] . '%03d_*.php', $version));

		if ( count($f) > 1 ) // Only one migration per step is permitted
			throw new Kohana_Exception('migrations.multiple_versions', $version);

		if ( count($f) == 0 ) // Migration step not found
			throw new Kohana_Exception('migrations.not_found', $version);

		$file = basename($f[0]);
		$name = basename($f[0], EXT);

		// Filename validations
		if ( !preg_match('/^\d{3}_(\w+)$/', $name, $match) )
			throw new Kohana_Exception('migrations.invalid_filename', $file);

		$match[1] = strtolower($match[1]);

		include $f[0];
		$class = ucfirst($match[1]);

		if ( !class_exists($class) )
			throw new Kohana_Exception('migrations.class_doesnt_exist', $class);

		if ( !method_exists($class, 'up') OR !method_exists($class, 'down') )
			throw new Kohana_Exception('migrations.wrong_interface', $class);

		return new $class($this->output, $this->group);
	}
	
	/**
	 * Retrieves current schema version
	 *
	 * @return	integer	Current Schema version
	 */
	public function get_schema_version()
	{
		if ( !is_dir($this->config['path']))
			mkdir($this->config['path']);
			
		if ( !is_dir($this->config['info']) )
			mkdir($this->config['info']);

		if ( !file_exists($this->config['info'] . 'version') )
		{
			$fversion = fopen($this->config['info'] . 'version','w');
			fwrite($fversion, '0');
			fclose($fversion);
			return 0;
		}
		else
		{
			$fversion = fopen($this->config['info'] . 'version','r');
			$version = fread($fversion, 11);
			fclose($fversion);
			return $version;
		}

		return 0;
	}

	/**
	 * Retrieves available last version
	 *
	 * @return   integer   Last schema version, or 0 if none found.
	 */
	public function last_schema_version()
	{
		$migrations = glob($this->config['path'] . '*' . EXT);

		foreach ($migrations as $i => $file)
		{
			// Mark wrongly formatted files as FALSE for later filtering
			$name = basename($file, EXT);
			if (!preg_match('/^\d{3}_(\w+)$/', $name))
				unset($migrations[$i]);
		}

		if (!$migrations) return 0;

		sort($migrations);
		$last_migration = basename(end($migrations));

		// Calculate the last migration step from existing migrations
		$last_version = substr($last_migration, 0, 3);
		return intval($last_version, 10);
	}
	
	/**
	 * Stores the current schema version
	 *
	 * @param  integer  Schema version reached
	 */
	protected function update_schema_version($schema_version)
	{
		$fversion = fopen($this->config['info'] . 'version', 'w');
		fwrite($fversion,$schema_version);
		fclose($fversion);
	}

	/**
	 * Simple output, filtered through a class variable
	 *
	 * @param   string  Output message
	 */
	protected function log($msg)
	{
		if ($this->output)
			echo $msg;
	}
}
