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
            $table->float('rating', 8, 2)->nullable()->change(); // Adjust precision as needed
        });
    }

    public function down()
    {
        Schema::table('book_user', function (Blueprint $table) {
            $table->integer('rating')->nullable()->change(); // revert back to integer
        });
    }
};
