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

			$table->string('remote_id'); // Used to mark any imported pages/content

			$table->integer('current_version');

			$table->integer('is_trashed');
			$table->integer('is_home');

			$table->integer('created_by');
			$table->integer('edited_by');
			$table->timestamps();

		});

		Schema::create('pageversions', function ($table) {

			$table->increments('id');

			$table->integer('page_id');
			$table->integer('version');

			$table->string('meta_page_title');
			$table->text('meta_description');

			$table->string('preview_key');
			
			$table->string('status')->default('draft'); // draft/published/archived (maybe pending for sign off?)

			$table->timestamp('visible_from')->nullable();
			$table->timestamp('visible_to')->nullable();

			$table->string('publish_handler');
			$table->text('publish_handler_data');

			$table->string('layout_identifier');

			$table->integer('created_by');
			$table->integer('edited_by');
			$table->timestamps();

		});

		Schema::create('pageversionslugs', function ($table) {

			$table->increments('id');
			
			$table->integer('version_id');
			$table->integer('location_id');
			$table->text('slug');

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

		Schema::create('pagelocations', function ($table) {

			$table->increments('id');
			$table->integer('page_id');
			$table->integer('parent_page_id');
			$table->string('sub_location_order');
			$table->string('path');

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
		Schema::drop('pages');
		Schema::drop('pageversions');
		Schema::drop('pageversionslugs');
		Schema::drop('pageattributes');
		Schema::drop('pagelocations');
	}

}
