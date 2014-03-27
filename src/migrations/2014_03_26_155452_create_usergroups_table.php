<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsergroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_groups', function($table)
		{
			$table->increments('id');
	        $table->string('name');
			$table->timestamps();
		});

		Schema::create('user_user_group', function($table)
		{
			$table->increments('id');
	        $table->integer('user_id');
	        $table->integer('user_group_id');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_groups');
		Schema::drop('user_user_group');
	}

}
