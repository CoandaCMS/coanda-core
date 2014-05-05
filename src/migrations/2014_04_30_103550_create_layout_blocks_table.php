<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLayoutBlocksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('layoutblocks', function($table)
	    {
	        $table->increments('id');
	        $table->string('type');
			$table->string('name');

			$table->integer('current_version');

	        $table->timestamps();
	    });

		Schema::create('layoutblockversions', function($table)
	    {
	        $table->increments('id');
			$table->integer('layout_block_id');

			$table->string('status')->default('draft'); // draft/published/archived

			$table->integer('version');

	        $table->timestamps();
	    });

		Schema::create('layoutblockattributes', function($table)
	    {
	        $table->increments('id');
	        $table->integer('layout_block_version_id');

			$table->string('identifier');
			$table->string('type');
			$table->integer('order');

			$table->text('attribute_data');

	        $table->timestamps();
	    });

		Schema::create('layoutblockregions', function($table)
	    {
	        $table->increments('id');
	        $table->integer('layout_block_id');

			$table->string('layout_identifier');
			$table->string('region_identifier');

			$table->string('module');
			$table->string('module_identifier');
			$table->boolean('cascade');

			$table->integer('order');

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
		Schema::drop('layoutblocks');
		Schema::drop('layoutblockversions');
		Schema::drop('layoutblockattributes');
		Schema::drop('layoutblockregions');
	}

}
