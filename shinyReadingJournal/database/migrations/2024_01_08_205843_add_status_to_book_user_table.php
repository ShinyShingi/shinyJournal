<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('book_user', function (Blueprint $table) {
            $table->string('status')->default('unread'); // Default value set to 'unread'
        });
    }

    public function down()
    {
        Schema::table('book_user', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }

};
