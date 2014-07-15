<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderToBlockRegionAssignments extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('layoutblockregionassignments', function($table)
		{
			$table->integer('order')->after('cascade');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('layoutblockregionassignments', function($table)
		{
			$table->dropColumn('order');

		});
	}

}
