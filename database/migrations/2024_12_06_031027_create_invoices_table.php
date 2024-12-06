<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice');
            $table->unsignedBigInteger('customer_id');
            $table->string('courier');
            $table->string('courier_service');
            $table->bigInteger('courier_cost');
            $table->integer('weight');
            $table->string('nama');
            $table->string('phone');
            $table->unsignedBigInteger('city_id');
            $table->unsignedBigInteger('province_id');
            $table->text('address');
            $table->enum('status', ['pending', 'success', 'expired', 'failed']);
            $table->bigInteger('grand_total');
            $table->string('snap_token')->nullable();
            $table->timestamps();

            //foreign key customer_id
            $table->foreign('customer_id')->references('id')->on('customers');

            //foreign key city_id
            $table->foreign('city_id')->references('id')->on('cities');

            //foreign key province_id
            $table->foreign('province_id')->references('id')->on('provinces');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
