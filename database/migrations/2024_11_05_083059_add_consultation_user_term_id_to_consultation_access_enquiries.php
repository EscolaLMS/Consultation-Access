<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('consultation_access_enquiries', function (Blueprint $table) {
            $table->foreignId('consultation_user_term_id')->nullable();

            $table->foreign('consultation_user_term_id')->references('id')->on('consultation_user_terms')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('consultation_access_enquiries', function (Blueprint $table) {
            $table->dropColumn('consultation_user_term_id');
        });
    }
};
