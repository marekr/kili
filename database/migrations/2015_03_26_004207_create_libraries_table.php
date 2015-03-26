<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLibrariesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('libraries', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->text('file_path');
			$table->integer('package_id')->unsigned();
			$table->enum('type', ['eeschema', 'pcbnew']);
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
		Schema::drop('libraries');
	}

}
