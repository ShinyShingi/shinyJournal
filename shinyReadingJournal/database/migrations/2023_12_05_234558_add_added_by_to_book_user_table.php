<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddedByToBookUserTable extends Migration
{
    public function up()
    {
        Schema::table('book_user', function (Blueprint $table) {
            $table->unsignedBigInteger('added_by_user_id')->nullable();
            $table->foreign('added_by_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('book_user', function (Blueprint $table) {
            $table->dropForeign(['added_by_user_id']);
            $table->dropColumn('added_by_user_id');
        });
    }
}
