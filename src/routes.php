<?php

use EscolaLms\ConsultationAccess\Http\Controllers\Admin\ConsultationAccessEnquiryAdminApiController;
use EscolaLms\ConsultationAccess\Http\Controllers\ConsultationAccessEnquiryApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware(['auth:api'])->group(function () {
    Route::prefix('admin')->group(function () {
        Route::prefix('consultation-access-enquiries')->group(function () {
            Route::get(null, [ConsultationAccessEnquiryAdminApiController::class, 'index']);
            Route::post('approve/{proposedTermId}', [ConsultationAccessEnquiryAdminApiController::class, 'approve']);
            Route::post('disapprove/{id}', [ConsultationAccessEnquiryAdminApiController::class, 'disapprove']);
        });
    });

    Route::prefix('consultation-access-enquiries')->group(function () {
        Route::get(null, [ConsultationAccessEnquiryApiController::class, 'index']);
        Route::post(null, [ConsultationAccessEnquiryApiController::class, 'create']);
    });
});