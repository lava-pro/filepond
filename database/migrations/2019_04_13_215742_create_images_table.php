<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('images', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('batch_id')->default(null)->index('batch_id');
			$table->string('resource', 160)->default('')->index('resource');
			$table->integer('user_id')->default(null)->index('user_id');
			$table->string('transfer_key', 60)->unique('transfer_key');
			$table->string('file_path', 60)->default('')->unique('file_path');
			$table->integer('position')->unsigned()->default(0);
			$table->string('extension', 5)->default('');
			$table->string('mime_type', 30)->nullable()->default('');
			$table->string('base_name', 60)->nullable()->default('');
			$table->integer('file_size')->unsigned()->nullable();
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
		Schema::drop('images');
	}

}
