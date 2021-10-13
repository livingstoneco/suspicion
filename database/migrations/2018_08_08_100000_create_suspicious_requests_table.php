<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuspiciousRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suspicious_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('ip');
            $table->text('url');
            $table->json('input');
            $table->json('headers');
            $table->json('cookies');
            $table->text('userAgent');
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
        Schema::dropIfExists('suspicious_requests');
    }
}
