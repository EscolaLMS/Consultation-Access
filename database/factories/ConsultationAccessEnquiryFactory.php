<?php

namespace EscolaLms\ConsultationAccess\Database\Factories;

use EscolaLms\Auth\Models\User;
use EscolaLms\ConsultationAccess\Enum\EnquiryStatusEnum;
use EscolaLms\ConsultationAccess\Models\Consultation;
use EscolaLms\ConsultationAccess\Models\ConsultationAccessEnquiry;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConsultationAccessEnquiryFactory extends Factory
{
    protected $model = ConsultationAccessEnquiry::class;

    public function definition(): array
    {
        return [
            'consultation_id' => Consultation::factory(),
            'user_id' => User::factory(),
            'status' => EnquiryStatusEnum::PENDING,
        ];
    }
}