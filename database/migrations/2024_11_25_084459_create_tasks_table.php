<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task');
            $table->text('description')->nullable();
            $table->foreignId('reseller_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_sales_id')->constrained()->onDelete('cascade');
            $table->enum('assigned_to', ['warehouse', 'maintenance', 'reseller'])->nullable();
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->string('photo_url')->nullable();
            $table->timestamp('deadline')->nullable(); // Deadline task
            $table->timestamp('upload_time')->nullable(); // Waktu upload foto
            $table->string('input_source')->default('manual_by_user'); // Hapus ->after('description')
            $table->timestamps();
        });
    }      

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
