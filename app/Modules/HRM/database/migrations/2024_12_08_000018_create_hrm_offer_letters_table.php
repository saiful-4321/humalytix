<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hrm_offer_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('hrm_candidates')->onDelete('cascade');
            $table->foreignId('template_id')->nullable()->constrained('hrm_letter_templates')->onDelete('set null');
            $table->string('designation');
            $table->foreignId('department_id')->nullable()->constrained('hrm_departments')->onDelete('set null');
            $table->foreignId('branch_id')->nullable()->constrained('hrm_branches')->onDelete('set null');
            $table->decimal('salary', 15, 2);
            $table->date('joining_date');
            $table->date('offer_date');
            $table->date('expiry_date')->nullable();
            $table->text('content')->nullable(); // Generated offer letter content
            $table->string('status')->default('draft'); // draft, sent, accepted, rejected, expired
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->string('file_path')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['candidate_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hrm_offer_letters');
    }
};
