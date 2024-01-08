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
        // Fetch all records
        $records = DB::table('book_user')->get();

        foreach ($records as $record) {
            $capitalizedStatus = ucfirst($record->status); // Capitalize the first letter

            // Update the record
            DB::table('book_user')
                ->where('id', $record->id)
                ->update(['status' => $capitalizedStatus]);
        }
    }

    public function down()
    {
        // Optional: Revert status to lowercase if needed
    }
};
