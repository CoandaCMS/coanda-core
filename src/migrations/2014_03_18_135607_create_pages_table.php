<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pages', function ($table) {

			$table->increments('id');
			$table->string('type');
			$table->string('name');

			$table->integer('parent_page_id');
			$table->integer('order');
			$table->string('remote_id'); // Used to mark any imported pages/content

			$table->integer('current_version');

			$table->integer('created_by');
			$table->integer('edited_by');
			$table->timestamps();

		});

		Schema::create('pageversions', function ($table) {

			$table->increments('id');

			$table->integer('page_id');
			$table->integer('version');

			$table->text('slug');
			
			$table->string('status')->default('draft'); // draft/published/archived (maybe pending for sign off?)

			$table->integer('created_by');
			$table->integer('edited_by');
			$table->timestamps();

		});

		Schema::create('pageattributes', function ($table) {

			$table->increments('id');
			$table->integer('page_version_id');

			$table->string('identifier');
			$table->string('type');
			$table->integer('order'); // Just so that the form matches the array order

			$table->text('attribute_data'); // I think most attribute types can store everything they need in here..

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pages');
		Schema::drop('pageversions');
		Schema::drop('pageattributes');
	}

}
