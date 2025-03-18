<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->integer('id')->primary(); // Integer primary key
            $table->string('firstname');
            $table->string('lastname');
            $table->string('password');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
//add balance field, and balance should have a 
$table->timestamps();
        });

        // Create a trigger to generate a random 6-digit ID
        DB::statement('
            CREATE TRIGGER generate_random_id
            BEFORE INSERT ON customers
            FOR EACH ROW
            BEGIN
                SET NEW.id = FLOOR(RAND() * 900000) + 100000;
            END;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the trigger
        DB::statement('DROP TRIGGER IF EXISTS generate_random_id');

        Schema::dropIfExists('customers');
    }
};
