<?php defined('SYSPATH') or die('No direct script access.');

class Add_signature extends Migration
{
	public function up()
	{
		$this->add_column ( "comments" , "signature" , 'string' );
	}

	public function down()
	{
		$this->remove_column ( "comments" , "signature" );
	}
}