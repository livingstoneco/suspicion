<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReaddTriggerColumnToSuspiciousRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suspicious_requests', function (Blueprint $table) {
            $table->text('trigger')->after('class');
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
