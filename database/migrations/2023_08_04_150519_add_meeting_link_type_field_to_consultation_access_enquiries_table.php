<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMeetingLinkTypeFieldToConsultationAccessEnquiriesTable extends Migration
{
    public function up(): void
    {
        Schema::table('consultation_access_enquiries', function (Blueprint $table) {
            $table->string('meeting_link_type')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('consultation_access_enquiries', function (Blueprint $table) {
            $table->dropColumn('meeting_link_type');
        });
    }
}
