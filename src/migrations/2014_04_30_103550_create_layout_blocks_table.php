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

			$table->timestamp('visible_from')->nullable();
			$table->timestamp('visible_to')->nullable();

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
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('layoutblock_regions');
		Schema::drop('layoutblocks');
		Schema::drop('layoutblockattributes');
	}

}
