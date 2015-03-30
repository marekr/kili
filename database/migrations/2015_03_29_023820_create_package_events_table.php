<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageEventsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('package_events', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('package_id')->unsigned();
			$table->integer('library_id')->unsigned()->nullable();
			$table->integer('component_id')->unsigned()->nullable();
			$table->string('type');
			$table->string('repository_bookmark');
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
		Schema::drop('package_events');
	}

}
