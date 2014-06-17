<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTemplateIdentifierColumn extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('pageversions', function($table)
		{
		    $table->string('template_identifier')->default('')->after('publish_handler_data');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('pageversions', function($table)
		{
		    $table->dropColumn('template_identifier');
		});
	}

}
