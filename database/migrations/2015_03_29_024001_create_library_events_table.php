<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLibraryEventsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('library_events', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('library_id')->unsigned();
			$table->string('type');
			$table->timestamp('date_occurred');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('library_events');
	}

}
