<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenamePromoUrlTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::rename('promourls', 'redirecturls');

		Schema::table('redirecturls', function($table)
		{
			$table->string('redirect_type')->after('destination')->default('temp');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::rename('redirecturls', 'promourls');

		Schema::table('promourls', function ($table) {

			$table->dropColumn('redirect_type');


		});
	}

}
