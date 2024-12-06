<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number')->nullable()->after('email');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('password'); // Account Status
            $table->string('timezone')->default('UTC')->after('status');
            $table->string('language')->default('en')->after('timezone'); // Add language column
            $table->string('address_line1')->nullable()->after('language'); // Address line 1
            $table->string('address_line2')->nullable()->after('address_line1'); // Address line 2
            $table->string('city')->nullable()->after('address_line2'); // City
            $table->string('state')->nullable()->after('city'); // State
            $table->string('postal_code')->nullable()->after('state'); // Postal code
            $table->string('country')->nullable()->after('postal_code'); // Country
            $table->string('default_currency')->default('USD')->after('country'); // Default currency column
            $table->string('avatar')->nullable()->after('default_currency'); // Avatar column
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'phone_number')) {
                $table->dropColumn('phone_number');
            }
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('users', 'timezone')) {
                $table->dropColumn('timezone');
            }
            if (Schema::hasColumn('users', 'language')) {
                $table->dropColumn('language');
            }
            if (Schema::hasColumn('users', 'address_line1')) {
                $table->dropColumn('address_line1');
            }
            if (Schema::hasColumn('users', 'address_line2')) {
                $table->dropColumn('address_line2');
            }
            if (Schema::hasColumn('users', 'city')) {
                $table->dropColumn('city');
            }
            if (Schema::hasColumn('users', 'state')) {
                $table->dropColumn('state');
            }
            if (Schema::hasColumn('users', 'postal_code')) {
                $table->dropColumn('postal_code');
            }
            if (Schema::hasColumn('users', 'country')) {
                $table->dropColumn('country');
            }
            if (Schema::hasColumn('users', 'default_currency')) {
                $table->dropColumn('default_currency'); // Drop default_currency column
            }
            if (Schema::hasColumn('users', 'avatar')) {
                $table->dropColumn('avatar');
            }
            $table->dropSoftDeletes(); // Removes deleted_at column
        });
    }
}
