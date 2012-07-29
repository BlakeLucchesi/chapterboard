<?php defined('SYSPATH') or die('No direct script access.');

class Example_create_table extends Migration
{
	public function up()
	{
 		$this->create_table
 		(
 			'articles',
			array
			(
				'title'          => array ('string[50]' ),
				'body'           => 'text',
				'date_published' => 'date',
				'author'         => array ('string[30]', 'default' => 'Anonymous'),
				'visible'        => 'boolean',
			)
		);

		$this->create_table
		(
			'comments',
			array
			(
				'body'   => 'text',
				'email'  => 'string'
			)
		);
	}

	public function down()
	{
		$this->drop_table('articles');
		$this->drop_table('comments');
	}
}

