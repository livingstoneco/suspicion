<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameTriggerColumnToClassWithinSuspiciousRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suspicious_requests', function (Blueprint $table) {
            $table->renameColumn('trigger', 'class');
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
            $table->renameColumn('class', 'trigger');
        });
    }
}
