<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDataflowTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        if (!Schema::hasTable('dataflow'))
        {
            Schema::create('dataflow', function(Blueprint $table) {
                $table->integer('id_dataflow', true);
                $table->string('name', 60);
                $table->string('token', 45);
                $table->string('repository', 90);
                $table->string('separator_csv', 90);
                $table->text('columns', 65535);
                $table->text('where_clause', 65535);
            });
        }
    }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('dataflow');
	}

}
