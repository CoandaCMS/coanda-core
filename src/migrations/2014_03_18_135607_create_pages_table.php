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
            $table->string('sub_page_order')->default('manual');
            $table->string('path');

            $table->integer('order');

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

            $table->text('slug');

			$table->string('meta_page_title');
			$table->text('meta_description');

			$table->string('preview_key');
			
			$table->string('status')->default('draft'); // draft/published/archived (maybe pending for sign off?)
            $table->boolean('is_hidden')->default(0);
            $table->boolean('is_hidden_navigation')->default(0);

			$table->timestamp('visible_from')->nullable();
			$table->timestamp('visible_to')->nullable();

			$table->string('publish_handler');
			$table->text('publish_handler_data');

            $table->string('template_identifier')->default('');
			$table->string('layout_identifier');

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

            $table->index('page_version_id');
            $table->index('identifier');

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
