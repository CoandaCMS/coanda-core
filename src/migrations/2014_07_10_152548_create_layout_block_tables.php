<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLayoutBlockTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('layoutblocks', function ($table) {

			$table->increments('id');

			$table->string('name');
			$table->string('type');

			$table->text('block_data');

			$table->timestamps();

		});

		Schema::create('layoutblockregionassignments', function ($table) {

			$table->increments('id');

			$table->integer('block_id');
			$table->string('layout_identifier');
			$table->string('region_identifier');
			$table->string('module_identifier');
			$table->boolean('cascade');

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
		Schema::drop('layoutblockregionassignments');
	}

}
