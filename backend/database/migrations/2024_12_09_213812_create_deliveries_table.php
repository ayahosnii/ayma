<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveriesTable extends Migration
{
    public function up()
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('tracking_code')->unique();
            $table->enum('status', ['Pending', 'In Transit', 'Delivered', 'Cancelled'])->default('In Transit'); // Enum for status
            $table->string('delivery_partner');
            $table->integer('current_step')->default(0);
            $table->json('timeline')->nullable();
            $table->timestamps(); // Created at and updated at
        });
    }

    public function down()
    {
        Schema::dropIfExists('deliveries');
    }
}
