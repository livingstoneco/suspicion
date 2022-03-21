<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTriggerColumnToSuspiciousRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suspicious_requests', function (Blueprint $table) {
            $table->string('trigger', 200)->after('method');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('suspicious_requests', function (Blueprint $table) {
            $table->dropColumn(['trigger']);
        });
    }
}
