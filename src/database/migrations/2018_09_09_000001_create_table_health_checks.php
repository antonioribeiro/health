<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableHealthChecks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('health_checks', function (Blueprint $table) {
            $table->increments('id');

            $table->string('resource_name');

            $table->string('resource_slug')->index();

            $table->string('target_name');

            $table->string('target_slug')->index();

            $table->string('target_display');

            $table->boolean('healthy');

            $table->text('error_message')->nullable();

            $table->float('runtime');

            $table->string('value')->nullable();

            $table->string('value_human')->nullable();

            $table->timestamp('created_at', 0)->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('health_checks');
    }
}
