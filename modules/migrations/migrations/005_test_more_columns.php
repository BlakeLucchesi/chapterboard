<?php defined('SYSPATH') or die('No direct script access.');

class Test_More_Columns extends Migration
{
	public function up()
	{
		$this->rename_column('blogs', 'monkeys', 'poop');
		$this->change_column('blogs', 'test_first', 'string');
	}
	
	public function down()
	{
		$this->change_column('blogs', 'test_first', 'integer');
		$this->rename_column('blogs', 'poop', 'monkeys');
	}
}


