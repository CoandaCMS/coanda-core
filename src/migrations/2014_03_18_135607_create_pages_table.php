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

			$table->string('type')->default('');
			$table->string('name')->default('');

            $table->integer('parent_page_id')->default(0);
            $table->string('sub_page_order')->default('manual');
            $table->string('path')->default('');

            $table->integer('order')->default(0);

			$table->string('remote_id')->nullable(); // Used to mark any imported pages/content

			$table->integer('current_version')->default(1);

			$table->integer('is_trashed')->default(0);
			$table->integer('is_home')->default(0);

			$table->integer('created_by')->nullable();
			$table->integer('edited_by')->nullable();
			$table->timestamps();

		});

		Schema::create('pageversions', function ($table) {

			$table->increments('id');

			$table->integer('page_id');
			$table->integer('version');

            $table->text('slug')->nullable();

			$table->string('meta_page_title')->nullable();
			$table->text('meta_description')->nullable();

			$table->string('preview_key')->nullable();
			
			$table->string('status')->default('draft'); // draft/published/archived (maybe pending for sign off?)
            $table->boolean('is_hidden')->default(0);
            $table->boolean('is_hidden_navigation')->default(0);

			$table->timestamp('visible_from')->nullable();
			$table->timestamp('visible_to')->nullable();

			$table->string('publish_handler')->nullable();
			$table->text('publish_handler_data')->nullable();

            $table->string('template_identifier')->default('');
			$table->string('layout_identifier')->default('');

			$table->integer('created_by')->nullable();
			$table->integer('edited_by')->nullable();
			$table->timestamps();

		});

		Schema::create('pageattributes', function ($table) {

			$table->increments('id');
			$table->integer('page_version_id');

			$table->string('identifier')->default('');
			$table->string('type')->default('');
			$table->integer('order'); // Just so that the form matches the array order

			$table->text('attribute_data')->nullable(); // I think most attribute types can store everything they need in here..

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
