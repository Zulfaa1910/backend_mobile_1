<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('alamat');
            $table->string('nomor_telepon');
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan']); // Gender options
            $table->date('tanggal_lahir');
            $table->string('foto')->nullable(); // Path ke foto profile
            $table->decimal('latitude', 10, 7)->nullable(); // Latitude for maps
            $table->decimal('longitude', 10, 7)->nullable(); // Longitude for maps
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
        Schema::dropIfExists('user_profiles');
    }
}
