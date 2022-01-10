<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeacceptTransactionHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weaccept_transaction_history', function (Blueprint $table) {
            $table->increments('id');
            $table->string('transaction_id');
            $table->string('order_id');
            $table->string('amount');
            $table->string('currency_code');
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
        Schema::dropIfExists('weaccept_transaction_history');
    }
}
