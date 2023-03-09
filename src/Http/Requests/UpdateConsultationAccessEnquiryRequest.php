<?php

namespace EscolaLms\ConsultationAccess\Http\Requests;

use EscolaLms\ConsultationAccess\Dtos\ConsultationAccessEnquiryDto;
use EscolaLms\ConsultationAccess\Dtos\UpdateConsultationAccessEnquiryDto;
use EscolaLms\ConsultationAccess\Models\ConsultationAccessEnquiry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

/**
 * @OA\Schema(
 *      schema="UpdateConsultationAccessEnquiryRequest",
 *      required={"proposed_terms"},
 *      @OA\Property(
 *          property="proposed_terms",
 *          type="array",
 *          @OA\Items(
 *              type="string",
 *              format="date-time"
 *          )
 *      ),
 * )
 *
 */
class UpdateConsultationAccessEnquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('updateOwn', $this->getEnquiry());
    }

    public function rules(): array
    {
        return [
            'proposed_terms' => ['required', 'array', 'min:1'],
            'proposed_terms.*' => ['required', 'date', 'after_or_equal:now'],
        ];
    }

    public function getId(): int
    {
        return $this->route('id');
    }

    public function toDto(): UpdateConsultationAccessEnquiryDto
    {
        return UpdateConsultationAccessEnquiryDto::instantiateFromRequest($this);
    }

    private function getEnquiry(): ConsultationAccessEnquiry
    {
        return ConsultationAccessEnquiry::findOrFail($this->getId());
    }
}
