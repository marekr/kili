<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComponentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('components', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('prefix');
			$table->boolean('draw_numbers');
			$table->boolean('draw_names');
			$table->integer('unit_count')->unsigned();
			$table->integer('pin_name_offset')->unsigned();
			$table->text('raw');
			$table->integer('library_id')->unsigned();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('components');
	}

}
