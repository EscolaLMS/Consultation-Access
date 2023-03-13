<?php

namespace EscolaLms\ConsultationAccess\Database\Factories;

use EscolaLms\Auth\Models\User;
use EscolaLms\ConsultationAccess\Enum\EnquiryStatusEnum;
use EscolaLms\ConsultationAccess\Models\Consultation;
use EscolaLms\ConsultationAccess\Models\ConsultationAccessEnquiry;
use EscolaLms\Consultations\Enum\ConsultationTermStatusEnum;
use EscolaLms\Consultations\Models\ConsultationUserPivot;
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
            'description' => $this->faker->text(),
        ];
    }

    public function approved(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => EnquiryStatusEnum::APPROVED,
                'consultation_user_id' => ConsultationUserPivot::factory()
                    ->state([
                        'consultation_id' => $attributes['consultation_id'],
                        'user_id' => $attributes['user_id'],
                        'executed_status' => ConsultationTermStatusEnum::APPROVED,
                    ]),
            ];
        });
    }
}
