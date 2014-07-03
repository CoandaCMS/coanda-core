<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHiddenOptionsToVersion extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('pageversions', function($table)
		{
		    $table->boolean('is_hidden')->default(0)->after('status');
		    $table->boolean('is_hidden_navigation')->default(0)->after('is_hidden');

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
		    $table->dropColumn('is_hidden');
		    $table->dropColumn('is_hidden_navigation');
		});
	}

}
