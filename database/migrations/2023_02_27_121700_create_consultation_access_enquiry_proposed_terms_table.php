<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsultationAccessEnquiryProposedTermsTable extends Migration
{
    public function up(): void
    {
        Schema::create('consultation_access_enquiry_proposed_terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_access_enquiry_id')->constrained('consultation_access_enquiries')->cascadeOnDelete();
            $table->dateTime('proposed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultation_access_enquiry_proposed_terms');
    }
}
