<?php

namespace EscolaLms\ConsultationAccess\Http\Requests;

use EscolaLms\ConsultationAccess\Dtos\ConsultationAccessEnquiryDto;
use EscolaLms\ConsultationAccess\Models\ConsultationAccessEnquiry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

/**
 * @OA\Schema(
 *      schema="CreateConsultationAccessEnquiryRequest",
 *      required={"consultation_id"},
 *      @OA\Property(
 *          property="consultation_id",
 *          description="consultation_id",
 *          type="number"
 *      ),
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
class CreateConsultationAccessEnquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('createOwn', ConsultationAccessEnquiry::class);
    }

    public function rules(): array
    {
        return [
            'consultation_id' => ['required', 'integer', 'exists:consultations,id'],
            'proposed_terms' => ['required', 'array', 'min:1'],
            'proposed_terms.*' => ['required', 'date', 'after_or_equal:now'],
        ];
    }

    public function toDto(): ConsultationAccessEnquiryDto
    {
        return ConsultationAccessEnquiryDto::instantiateFromRequest($this);
    }
}
