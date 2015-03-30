<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftdelete extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('libraries', function(Blueprint $table)
		{
			$table->softDeletes();
		});

		Schema::table('components', function(Blueprint $table)
		{
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('libraries', function(Blueprint $table)
		{
			$table->dropColumn('deleted_at');
		});

		Schema::table('components', function(Blueprint $table)
		{
			$table->dropColumn('deleted_at');
		});
	}

}
