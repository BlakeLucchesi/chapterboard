<?php defined('SYSPATH') or die('No direct script access.');

class Test_Add_Column extends Migration
{
	public function up()
	{
		$this->add_column('blogs', 'monkeys', array('integer[big]', 'default' => 55, 'null' => FALSE, 'after' => 'id'));
		$this->add_column('blogs', 'test_first', array('integer', 'first'));
	}
	
	public function down()
	{
		$this->remove_column('blogs', 'monkeys');
		$this->remove_column('blogs', 'test_first');
	}
}