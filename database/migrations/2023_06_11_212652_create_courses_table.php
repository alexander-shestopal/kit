<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('id_send_currency');
            $table->integer('id_recive_currency');
            $table->decimal('rate_send', $precision = 20, $scale = 8);
            $table->decimal('rate_recive', $precision = 20, $scale = 8);
            $table->integer('id_exchange_office');
            $table->date('created_at');

            $table->index(['id_send_currency', 'id_recive_currency']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courses');
    }
};
