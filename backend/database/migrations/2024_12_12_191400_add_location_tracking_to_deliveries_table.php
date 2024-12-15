<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationTrackingToDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('timeline');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->timestamp('last_updated_at')->nullable()->after('longitude');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'last_updated_at']);
        });
    }
}
