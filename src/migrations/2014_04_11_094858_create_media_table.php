<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('media', function($table)
	    {
	        $table->increments('id');

			$table->string('original_filename');	        
	        $table->string('extension');
	        $table->string('mime');
	        $table->integer('size');

	        $table->integer('width');
	        $table->integer('height');

	        $table->string('filename');

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
		Schema::drop('media');
	}

}
