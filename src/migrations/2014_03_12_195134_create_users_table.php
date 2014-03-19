<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function($table)
		{
			$table->increments('id');

	        $table->string('email')->unique();
	        $table->string('first_name');
	        $table->string('last_name');
	        $table->string('password');

	        $table->dateTime('last_login');

			$table->timestamps();
		});

		// Add a demo admin user, so you can log in!
		Eloquent::unguard();
		\CoandaCMS\Coanda\Authentication\Eloquent\Models\User::create(['email' => 'demo@somewhere.com', 'password' => Hash::make('password'), 'first_name' => 'Demo', 'last_name' => 'Admin']);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
