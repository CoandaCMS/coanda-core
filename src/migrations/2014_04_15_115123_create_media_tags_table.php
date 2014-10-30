<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaTagsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mediatags', function($table)
	    {
	        $table->increments('id');
			$table->string('tag')->default('');

	        $table->timestamps();
	    });

		Schema::create('media_media_tag', function($table)
	    {
			$table->increments('id');
	        $table->integer('media_id');
	        $table->integer('media_tag_id');

	    });

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('mediatags');
		Schema::drop('media_media_tag');
	}

}
