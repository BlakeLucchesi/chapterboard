<?php defined('SYSPATH') or die('No direct script access.');

class Rename_articles extends Migration
{
	public function up()
	{
		$this->rename_table("articles", "blogs");
	}

	public function down()
	{
		$this->rename_table("blogs", "articles");
	}
}
