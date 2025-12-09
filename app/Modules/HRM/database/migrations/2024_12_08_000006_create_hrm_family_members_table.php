<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hrm_family_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hrm_employees')->onDelete('cascade');
            $table->string('name');
            $table->string('relationship'); // spouse, father, mother, son, daughter, etc.
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->boolean('is_dependent')->default(false);
            $table->string('contact_number')->nullable();
            $table->string('occupation')->nullable();
            $table->boolean('is_emergency_contact')->default(false);
            $table->text('address')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrm_family_members');
    }
};
