<?php defined('SYSPATH') or die('No direct script access.');

class Test_Indexes extends Migration
{
	public function up()
	{
		$this->add_index('blogs', 'some_index', array('poop', 'id'), 'unique');
	}
	
	public function down()
	{
		$this->remove_index('blogs', 'some_index');
	}
}