<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDocColumns extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('components', function(Blueprint $table)
		{
			$table->text('description');
			$table->text('keywords');
			$table->string('doc_filename');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('components', function(Blueprint $table)
		{
			$table->dropColumn('description');
			$table->dropColumn('keywords');
			$table->dropColumn('doc_filename');
		});
	}

}
